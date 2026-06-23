<?php

require_once('../connection.php');
// require_once('../localcon.php');
$GLOBALS['connection'] = $pdo;

class Officer
{
    private $db;

    function __construct()
    {
        $this->db = $GLOBALS['connection'];
    }

    function getOfficers(){
        $stmt = $this->db->prepare("SELECT u.*, o.barangay, o.phonenumber
        FROM users u
        JOIN officers o ON o.user_id = u.id
        WHERE u.role = 'officer' AND u.is_active = 1");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getOfficerInfo($officerId){
        $stmt = $this->db->prepare("SELECT
        u.*,
        o.*
        FROM users u
        JOIN officers o ON o.user_id = u.id
        WHERE u.role = 'officer' AND u.id = $officerId");

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

      function updateOfficer(
    $userid,
    $firstname,
    $lastname,
    $barangay, $phonenumber, $email,
    $username,
    $password = null
) {
    try {

        $this->db->beginTransaction();

        // 1. update users table
        $stmt1 = $this->db->prepare("
            UPDATE users
            SET 
                first_name = :firstname,
                last_name = :lastname,
                username = :username
            WHERE id = :id
        ");

        $stmt1->execute([
            ':firstname' => $firstname,
            ':lastname'  => $lastname,
            ':username'  => $username,
            ':id'        => $userid
        ]);

        // 2. optional password update
        if (!empty($password)) {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt2 = $this->db->prepare("
                UPDATE users
                SET password = :password
                WHERE id = :id
            ");

            $stmt2->execute([
                ':password' => $hashed,
                ':id'       => $userid
            ]);
        }

        // 3. update officers table
        $stmt3 = $this->db->prepare("
            UPDATE officers
            SET
                barangay = :barangay,
                phonenumber = :phonenumber,
                email = :email
            WHERE user_id = :userid
        ");

        $stmt3->execute([
            ':barangay' => $barangay,
            ':phonenumber' => $phonenumber,
            ':email' => $email,
            ':userid'   => $userid
        ]);

        $this->db->commit();
        return true;

    } catch (PDOException $e) {
        $this->db->rollBack();
        throw $e;
    }
}

    function deactivateOfficer($officerId) {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_active = 0
            WHERE id = :userid
        ");

        return $stmt->execute([
            ':userid' => $officerId
        ]);
    }

function createOfficer($firstname, $lastname, $brgy, $phonenumber, $email, $officerno, $password)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO users (first_name, last_name, username, password, role)
                VALUES (:first_name, :last_name, :username, :password, :role)
            ");

            $stmt->execute([
               ':first_name' => $firstname,
               ':last_name' => $lastname,
                ':username' => $officerno,
                ':password'  => password_hash($password, PASSWORD_DEFAULT),
                ':role'      => 'officer'
            ]);

            $userId = $this->db->lastInsertId();

            $stmt = $this->db->prepare("
                INSERT INTO officers
                (user_id, officerno, barangay, phonenumber, email)
                VALUES (:user_id, :officerno, :brgy, :phonenumber,:email)
            ");

            $stmt->execute([
                ':user_id'     => $userId,
                ':officerno'   => $officerno,
                ':brgy'        => $brgy,
                ':phonenumber' => $phonenumber,
                ':email'       => $email,
            ]);
            

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

