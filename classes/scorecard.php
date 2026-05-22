<?php 
require_once('../localcon.php');
$GLOBALS['connection'] = $pdo;

class Scorecard{
    private $db;

    function __construct()
    {
        $this->db = $GLOBALS['connection'];
    }
    
    function getAttendedEventsPerScholar($scholarUserId, $semId){
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS attended_count
            FROM attendance_record ar
            JOIN activities a ON ar.activityid = a.activity_id
            WHERE ar.user_id = :scholarUserId
            AND a.semester = :sem_id AND ar.attendance_status = 'present'
        ");
        $stmt->execute([
            ':scholarUserId' => $scholarUserId,
            ':sem_id' => $semId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$scObj = new Scorecard();
?>
