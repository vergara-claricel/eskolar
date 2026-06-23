<?php

require_once('../connection.php');
// require_once('../localcon.php');


class Semester{
    private $db;

    function __construct($pdo)
    {
        $this->db = $pdo;
    }

    function getAllSemester(){
        $stmt = $this->db->prepare("SELECT * FROM semester");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getActiveSemester(){
        $stmt2 = $this->db->prepare("SELECT * FROM semester WHERE semester_status = 'active' LIMIT 1");
        $stmt2->execute();

       $semester = $stmt2->fetch(PDO::FETCH_ASSOC);

    // handle false result
    if ($semester === false) {
        return null;
    }

    return $semester;
    }

    
    function saveSemester($semname, $semstart, $semend, $semstatus){
        try {
            $this->db->beginTransaction();
            // inactive all active if active sem created
            if ($semstatus === 'active') {
                $stmt = $this->db->prepare(
                    "UPDATE semester SET semester_status = 'inactive' WHERE semester_status = 'active'"
                );
                $stmt->execute();
            }

            $stmt = $this->db->prepare("INSERT INTO semester
                (semester_name, semester_start, semester_end, semester_status)
                VALUES (:semname, :semstart, :semend, :semstatus)");

            $result = $stmt->execute([
                ':semname' => $semname,
                ':semstart' => $semstart,
                ':semend' => $semend,
                ':semstatus' => $semstatus
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

    function updateActiveSem($sem_id) {

        try {
            $this->db->beginTransaction();

            // set sem inactive
            $stmt1 = $this->db->prepare("
                UPDATE semester 
                SET semester_status = 'inactive'
            ");
            $stmt1->execute();

            $stmt2 = $this->db->prepare("
                UPDATE semester 
                SET semester_status = 'active'
                WHERE sem_id = :sem_id
            ");

            $stmt2->execute([
                ':sem_id' => $sem_id
            ]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}

$semobj = new Semester($pdo);
$activeSem = $semobj->getActiveSemester();
$activeSemID = $activeSem['sem_id'] ?? null;
$activeSemName = $activeSem['semester_name'] ?? null;

?>