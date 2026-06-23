<?php

require_once('../connection.php');
// require_once('../localcon.php');
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
        $stmt = $this->db->prepare("SELECT * FROM activities where semester = $semid ORDER BY date desc");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getActivityDetails($actId)
    {
        $stmt = $this->db->prepare("SELECT * FROM activities WHERE activity_id = :actId");
        $stmt->execute(['actId' => $actId]);

        return $stmt->fetch(PDO::FETCH_ASSOC); // single record
    }

    //     function getAttendanceRecords($actId)
    // {
    //     $stmt = $this->db->prepare("
    //         SELECT 
    //             s.user_id,
    //             u.id,
    //             u.first_name,
    //             u.last_name,
    //             u.username AS iskolarno,
    //             ar.ar_id,
    //             ar.timein,
    //             ar.timeout,
    //             ar.scanned_by,
    //             CONCAT(ou.first_name, ' ', ou.last_name) AS officer_fullname,

    //             CASE 
    //                 WHEN ar.timein IS NOT NULL AND ar.timeout IS NOT NULL THEN 'present'
    //                 ELSE 'absent'
    //             END AS attendance_status

    //         FROM scholars s

    //         LEFT JOIN users u 
    //             ON u.id = s.user_id

    //         LEFT JOIN attendance_record ar 
    //             ON ar.user_id = s.user_id
    //             AND ar.activityid = :activity_id

    //          LEFT JOIN users ou
    //             ON ou.id = ar.scanned_by 

    //         WHERE u.is_active = 1

    //         ORDER BY u.last_name, u.first_name
    //     ");

    //     $stmt->execute([
    //         ':activity_id' => $actId
    //     ]);

    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
    function getAttendanceRecords($actId)
    {
        $stmt = $this->db->prepare("
        SELECT 
            s.user_id,
            u.id,
            u.first_name,
            u.last_name,
            u.username AS iskolarno,
            ar.ar_id,
            ar.timein,
            ar.timeout,
            ar.scanned_by,
            CONCAT(ou.first_name, ' ', ou.last_name) AS officer_fullname,

            CASE 
                WHEN ar.timein IS NOT NULL AND ar.timeout IS NOT NULL THEN 'present'
                ELSE 'absent'
            END AS attendance_status

        FROM scholars s

        LEFT JOIN users u 
            ON u.id = s.user_id

        LEFT JOIN attendance_record ar 
            ON ar.user_id = s.user_id
            AND ar.activityid = :activity_id

        LEFT JOIN users ou
            ON ou.id = ar.scanned_by

        INNER JOIN activities a
            ON a.activity_id = :activity_id

        WHERE u.is_active = 1
          AND (
                a.classification = 'Municipal'
                OR s.barangay = a.barangay
          )

        ORDER BY u.last_name, u.first_name
    ");

        $stmt->execute([
            ':activity_id' => $actId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function updateAttendance($ar_id, $activity_id, $user_id, $iskolarno, $date, $timein, $timeout, $scanned_by)
    {
        // 1. Check if record exists
        $check = $this->db->prepare("
        SELECT ar_id 
        FROM attendance_record 
        WHERE ar_id = :ar_id
    ");

        $check->execute([':ar_id' => $ar_id]);
        $exists = $check->fetch(PDO::FETCH_ASSOC);

        if ($exists) {

            // 2. UPDATE
            $stmt = $this->db->prepare("
            UPDATE attendance_record
            SET 
                timein = :timein,
                timeout = :timeout,
                scanned_by = :scanned_by
            WHERE ar_id = :ar_id
        ");

            return $stmt->execute([
                ':ar_id' => $ar_id,
                ':timein' => $timein,
                ':timeout' => $timeout,
                ':scanned_by' => $scanned_by
            ]);
        } else {

            // 3. INSERT
            $stmt = $this->db->prepare("
            INSERT INTO attendance_record 
            (activityid, user_id, iskolarno, scanned_date, timein, timeout, scanned_by, sync_status)
            VALUES
            (:activityid, :user_id, :iskolarno, :date, :timein, :timeout, :scanned_by, :sync_status)
        ");

            return $stmt->execute([
                ':activityid' => $activity_id,
                ':user_id' => $user_id,
                ':iskolarno' => $iskolarno,
                ':date' => $date,
                ':timein' => $timein,
                ':timeout' => $timeout,
                ':scanned_by' => $scanned_by,
                ':sync_status' => 'manual override'
            ]);
        }
    }

    function updateActivity(
        $activity_id,
        $name,
        $classification,
        $barangay,
        $date,
        $start_time,
        $end_time,
        $venue,
        $info
    ) {
        $stmt = $this->db->prepare("
            UPDATE activities
            SET 
                activityname = :name,
                classification = :classification,
                barangay = :barangay,
                date = :date,
                start_time = :start_time,
                end_time = :end_time,
                venue = :venue,
                info = :info
            WHERE activity_id = :activity_id
        ");

        return $stmt->execute([
            ':activity_id' => $activity_id,
            ':name' => $name,
            ':classification' => $classification,
            ':barangay' => ($classification === 'Barangay') ? $barangay : null,
            ':date' => $date,
            ':start_time' => $start_time,
            ':end_time' => $end_time,
            ':venue' => $venue,
            ':info' => $info
        ]);
    }

    function upcomingActivities($semId)
    {
        $stmt = $this->db->prepare("SELECT * FROM activities
            where semester = :semid && status = 'upcoming'");
        $stmt->execute([
            'semid' => $semId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function searchActivities($keyword, $semid)
    {
        $search = "%" . $keyword . "%";

        $stmt = $this->db->prepare("SELECT *
            FROM activities
            WHERE (
            activityname LIKE :search
            OR barangay LIKE :search
            OR classification LIKE :search
            )
            AND semester = :semid
            ORDER BY date desc");
        $stmt->execute([
            ':search' => $search,
            ':semid' => $semid
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function searchAttendanceRecords($keyword, $actId)
    {
        $search = "%" . $keyword . "%";

        $stmt = $this->db->prepare("
            SELECT 
            s.user_id,
            u.first_name,
            u.last_name,
            u.username AS iskolarno,
            ar.ar_id,
            ar.timein,
            ar.timeout,
            ar.scanned_by,
            CONCAT(ou.first_name, ' ', ou.last_name) AS officer_fullname,

            CASE 
                WHEN ar.timein IS NOT NULL AND ar.timeout IS NOT NULL THEN 'present'
                ELSE 'absent'
            END AS attendance_status

        FROM scholars s

        LEFT JOIN users u 
            ON u.id = s.user_id

        LEFT JOIN attendance_record ar 
            ON ar.user_id = s.user_id
            AND ar.activityid = :activity_id

         LEFT JOIN users ou
            ON ou.id = ar.scanned_by 

        WHERE (
            (CONCAT(u.first_name, ' ', u.last_name) LIKE :search
            OR u.username LIKE :search)
            AND u.is_active = 1 
            )
        ORDER BY u.last_name, u.first_name"); // retrieves active only

        $stmt->execute([
            ':activity_id' => $actId,
            ':search' => $search
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


$actobj = new Activities();
