<?php

// require_once('../localcon.php');
require "../connection.php";
require_once "../vendor/autoload.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;

$GLOBALS['connection'] = $pdo;

// class Scholar
// {
    private $db;

    function __construct()
    {
        $this->db = $GLOBALS['connection'];
    }

    function generateScholarQR($userId, $iskolarno)
    {
        // folder
        $folder = "../qrcodes/";

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        // 🔥 generate secure token
        $qrToken = $iskolarno . "_" . bin2hex(random_bytes(16));

        // QR data (SECURE)
        $qrData = json_encode([
            "iskolarno" => $iskolarno,
            "token"     => $qrToken
        ]);

        $fileName = "qr_" . $iskolarno . ".png";
        $filePath = $folder . $fileName;

        // ---------------- QR SETTINGS ----------------
        $options = new QROptions([
            'version'  => 5,
            'scale'    => 6,
            'eccLevel' => EccLevel::L,
        ]);

        $qrcode = new QRCode($options);

        // save QR image
        file_put_contents($filePath, $qrcode->render($qrData));


        $stmt = $this->db->prepare("
            UPDATE scholars
            SET qr_path = :qr_path,
                qr_token = :qr_token
            WHERE user_id = :user_id
        ");

        $stmt->execute([
            ':qr_path'  => $fileName,
            ':qr_token'    => $qrToken,
            ':user_id'  => $userId
        ]);

        return $filePath;
    }

    function addScholar($firstname, $lastname, $brgy, $phonenumber, $email, $iskolarno, $password)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO users (first_name, last_name, username, password, role)
                VALUES (:first_name, :last_name, :iskolarno, :password, :role)
            ");

            $stmt->execute([
               ':first_name' => $firstname,
               ':last_name' => $lastname,
                ':iskolarno' => $iskolarno,
                ':password'  => password_hash($password, PASSWORD_DEFAULT),
                ':role'      => 'scholar'
            ]);

            $userId = $this->db->lastInsertId();

            $stmt = $this->db->prepare("
                INSERT INTO scholars
                (user_id, barangay, phonenumber, username, email)
                VALUES (:user_id, :brgy, :phonenumber, :username, :email)
            ");

            $stmt->execute([
                ':user_id'        => $userId,
                ':brgy'        => $brgy,
                ':phonenumber' => $phonenumber,
                ':username' => $iskolarno,
                ':email'       => $email,
            ]);
            
    
            // generate QR (NOW SECURE)
            $this->generateScholarQR($userId, $iskolarno);

            $this->db->commit();
            return $userId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    function getAllActiveScholars()
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.first_name,
                u.last_name,
                u.username,
                u.role,
                s.*
            FROM scholars s
            LEFT JOIN users u ON u.id = s.user_id
            WHERE u.is_active = 1
            ORDER BY u.username;
        ");
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function searchScholars($keyword){
        $search = "%".$keyword."%";
        $stmt = $this->db->prepare("SELECT
            u.id,
            u.first_name,
            u.last_name,
            u.username,
            u.role,
            s.*
            FROM scholars s
            LEFT JOIN users u ON u.id = s.user_id
            WHERE 
            (u.first_name || ' ' || u.last_name) LIKE :search
            OR u.first_name LIKE :search
            OR u.last_name LIKE :search
            OR s.username LIKE :search
            OR s.barangay LIKE :search
            ORDER BY u.last_name, u.first_name;");
        $stmt->execute([':search' => $search]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getScholarInfo($user_id){
        $stmt = $this->db->prepare("SELECT
        u.*,
        s.*
        FROM users u
        JOIN scholars s ON s.user_id = u.id
        where u.id = :user_id");
        $stmt->execute([
            ':user_id' => $user_id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // function getScholarInfo($scholar_id)
    // {
    //     $stmt = $this->db->prepare("
    //         SELECT *
    //         FROM scholars
    //         WHERE scholar_id = :scholar_id
    //     ");

    //     $stmt->execute([
    //         ':scholar_id' => $scholar_id
    //     ]);

    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    function getScholarRecord($user_id, $semId){
        $stmt = $this->db->prepare("SELECT 
        act.*,
        ar.*,
        CONCAT(ou.first_name, ' ', ou.last_name) AS officer_fullname,

        CASE 
                WHEN ar.timein IS NOT NULL AND ar.timeout IS NOT NULL THEN 'present'
                ELSE 'absent'
            END AS attendance_status

        FROM attendance_record ar
        JOIN activities act ON act.activity_id = ar.activityid
        LEFT JOIN users ou 
            ON ou.id = ar.scanned_by

        where ar.user_id = :user_id AND semester = :semester");
        $stmt->execute([
            ':user_id' => $user_id,
            ':semester' => $semId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateScholar(
    $user_id,
    $first_name,
    $last_name,
    $barangay,
    $phonenumber,
    $email,
    $iskolarno,
    $password = null
) {
    try {

        $this->db->beginTransaction();

        // 1. update scholars table
        $stmt1 = $this->db->prepare("
            UPDATE scholars
            SET 
                barangay = :barangay,
                phonenumber = :phonenumber,
                email = :email,
                username = :iskolarno
            WHERE user_id = :user_id
        ");

        $stmt1->execute([
            ':barangay'    => $barangay,
            ':phonenumber' => $phonenumber,
            ':email'       => $email,
            ':iskolarno'   => $iskolarno,
            ':user_id'  => $user_id
        ]);

        // 2. update users table (username = iskolarno)
        $stmt2 = $this->db->prepare("
            UPDATE users
            SET 
            first_name = :first_name,
            last_name = :last_name,
            username = :iskolarno
            WHERE id = :user_id
        ");

        $stmt2->execute([
            ':first_name'  => $first_name,
            ':last_name'   => $last_name,
            ':iskolarno'  => $iskolarno,
            ':user_id' => $user_id
        ]);

        // 3. optional password update
        if (!empty($password)) {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt3 = $this->db->prepare("
                UPDATE users
                SET password = :password
                WHERE id = :user_id
            ");

            $stmt3->execute([
                ':password'   => $hashed,
                ':user_id' => $user_id
            ]);
        }

        $this->db->commit();
        return true;

    } catch (PDOException $e) {
        $this->db->rollBack();
        throw $e;
    }
}


    function deactivateScholar($scholarId) {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_active = 0
            WHERE id = :userid
        ");

        return $stmt->execute([
            ':userid' => $scholarId
        ]);
    }

        function reactivateScholar($scholarId) {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_active = 1
            WHERE id = :userid
        ");

        return $stmt->execute([
            ':userid' => $scholarId
        ]);
    }

function filterScholars($search = '', $status = '', $barangay = '')
{
    $sql = "
        SELECT
            u.id,
            u.first_name,
            u.last_name,
            u.username,
            u.is_active,
            s.*
        FROM scholars s
        JOIN users u ON u.id = s.user_id
        WHERE 1=1
    ";

    $params = [];

    // Search
    if (!empty($search)) {
        $sql .= "
            AND (
                CONCAT(u.first_name, ' ', u.last_name) LIKE :search
                OR u.username LIKE :search
                OR s.barangay LIKE :search
            )
        ";

        $params[':search'] = "%{$search}%";
    }

    // Status filter
    if ($status !== '') {
        $sql .= " AND u.is_active = :is_active";
        $params[':is_active'] = $status;
    } 

    // Barangay filter
    if (!empty($barangay)) {
        $sql .= " AND s.barangay = :barangay";
        $params[':barangay'] = $barangay;
    }

    $sql .= " ORDER BY u.last_name, u.first_name";

    // var_dump($sql);
    // var_dump($params);
    // exit;
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// function updateScholar(
//     $user_id,
//     $first_name,
//     $last_name,
//     $barangay,
//     $phonenumber,
//     $email,
//     $iskolarno,
//     $password = null
// ) {
//     try {

//         $this->db->beginTransaction();

//         // 1. update scholars table
//         $stmt1 = $this->db->prepare("
//             UPDATE scholars
//             SET first_name = :first_name,
//                 last_name = :last_name,
//                 barangay = :barangay,
//                 phonenumber = :phonenumber,
//                 email = :email
//             WHERE user_id = :user_id
//         ");

//         $stmt1->execute([
//             ':first_name' => $first_name,
//             ':last_name' => $last_name,
//             ':barangay' => $barangay,
//             ':phonenumber' => $phonenumber,
//             ':email' => $email,
//             ':user_id' => $user_id
//         ]);

//         // 2. update users table (username = iskolarno)
//         $stmt2 = $this->db->prepare("
//             UPDATE users
//             SET username = :iskolarno
//             WHERE id = :user_id
//         ");

//         $stmt2->execute([
//             ':iskolarno' => $iskolarno,
//             ':user_id' => $user_id
//         ]);

//         // 3. optional password update
//         if (!empty($password)) {

//             $hashed = password_hash($password, PASSWORD_DEFAULT);

//             $stmt3 = $this->db->prepare("
//                 UPDATE users
//                 SET password = :password
//                 WHERE id = :user_id
//             ");

//             $stmt3->execute([
//                 ':password' => $hashed,
//                 ':user_id' => $user_id
//             ]);
//         }

//         $this->db->commit();
//         return true;

//     } catch (PDOException $e) {
//         $this->db->rollBack();
//         throw $e;
//     }
// }


}

// $schoobj = new Scholar();
