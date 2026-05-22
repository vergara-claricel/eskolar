<?php

// require_once('../connection.php');
require_once('../localcon.php');
$GLOBALS['connection'] = $pdo;

class Activities
{
    private $db;

    function __construct()
    {
        $this->db = $GLOBALS['connection'];
    }

    function createActivity($name, $status, $date, $starttime, $endtime, $venue, $classification, $barangay, $info, $semester)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO activities
                (activityname, status, date, start_time, end_time, venue, classification, barangay, info, semester)
                VALUES (:name, :status, :date, :starttime, :endtime, :venue, :classification, :barangay, :info, :semester)");

            $result = $stmt->execute([
                ':name' => $name,
                ':status' => $status,
                ':date' => $date,
                ':starttime' => $starttime,
                ':endtime' => $endtime,
                ':venue' => $venue,
                ':classification' => $classification,
                ':barangay' => $barangay,
                ':info' => $info,
                ':semester' => $semester
            ]);

            if ($result) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    function getActivitiesOfActiveSem($semid)
    {
        $stmt = $this->db->prepare("SELECT * FROM activities where semester = $semid");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getActivityDetails($actId)
    {
        $stmt = $this->db->prepare("SELECT * FROM activities WHERE activity_id = :actId");
        $stmt->execute(['actId' => $actId]);

        return $stmt->fetch(PDO::FETCH_ASSOC); // single record
    }

    function getAttendanceRecords($actId)
    {
        $stmt = $this->db->prepare("SELECT 
    
    s.first_name,
    s.last_name,
    u.username AS iskolarno,
    ar.ar_id,
    ar.timein,
    ar.timeout,
    ar.attendance_status,
    ar.scanned_by
FROM attendance_record ar
JOIN users u ON ar.user_id = u.id
JOIN scholars s ON s.user_id = u.id
WHERE ar.activityid = :activity_id;");

            $stmt->execute([
                'activity_id' => $actId
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$actobj = new Activities();
