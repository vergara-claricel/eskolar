<?php

require_once('../localcon.php');
require_once "../vendor/autoload.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;

$GLOBALS['connection'] = $pdo;

class Scholar
{
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
                INSERT INTO users (username, password, role)
                VALUES (:iskolarno, :password, :role)
            ");

            $stmt->execute([
                ':iskolarno' => $iskolarno,
                ':password'  => password_hash($password, PASSWORD_DEFAULT),
                ':role'      => 'scholar'
            ]);

            $userId = $this->db->lastInsertId();

            $stmt = $this->db->prepare("
                INSERT INTO scholars
                (user_id, first_name, last_name, barangay, phonenumber, email)
                VALUES (:user_id, :firstname, :lastname, :brgy, :phonenumber, :email)
            ");

            $stmt->execute([
                ':user_id'     => $userId,
                ':firstname'   => $firstname,
                ':lastname'    => $lastname,
                ':brgy'        => $brgy,
                ':phonenumber' => $phonenumber,
                ':email'       => $email,
            ]);

            // generate QR (NOW SECURE)
            $this->generateScholarQR($userId, $iskolarno);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    function getAllScholars()
    {
        $stmt = $this->db->prepare("SELECT 
            u.id,
            u.username,
            s.*
        FROM users u
        JOIN scholars s ON s.user_id = u.id;");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getScholarInfo($user_id){
        $stmt = $this->db->prepare("SELECT
        u.username,
        s.*
        FROM users u
        JOIN scholars s ON s.user_id = u.id
        where user_id = :user_id");
        $stmt->execute([
            ':user_id' => $user_id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getScholarRecord($user_id, $semId){
        $stmt = $this->db->prepare("SELECT 
        act.*,
        ar.*
        FROM attendance_record ar
        JOIN activities act ON act.activity_id = ar.activityid
        where user_id = :user_id && semester = :semester");
        $stmt->execute([
            ':user_id' => $user_id,
            ':semester' => $semId]
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$schoobj = new Scholar();
