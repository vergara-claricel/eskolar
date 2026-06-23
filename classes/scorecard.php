<?php 
// require_once('../localcon.php');
require "../connection.php";
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
            FROM (
                SELECT 
                    CASE 
                        WHEN ar.timein IS NOT NULL AND ar.timeout IS NOT NULL THEN 1
                        ELSE 0
                    END AS is_present
                FROM attendance_record ar
                JOIN activities a ON ar.activityid = a.activity_id
                WHERE ar.user_id = :scholarUserId
                AND a.semester = :sem_id
            ) t
            WHERE is_present = 1
        ");
        $stmt->execute([
            ':scholarUserId' => $scholarUserId,
            ':sem_id' => $semId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getMunicipalEvents($semId){
            $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM activities 
            WHERE classification = 'Municipal'
            AND semester = ?
        ");
        $stmt->execute([$semId]);
        return (int)$stmt->fetchColumn();
    }

    function getTotalBarangayEvents($barangay, $semId){
             $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM activities 
            WHERE classification = 'Barangay'
            AND barangay = ?
            AND semester = ?
        ");
        $stmt->execute([$barangay, $semId]);
        return (int)$stmt->fetchColumn();
    }

    function getTotalEvents($brgyEvents, $municipalEvents){
        return $brgyEvents + $municipalEvents;
    }

    function computeQuotaPercent($totalAttended, $totalEvents){

        if ($totalEvents == 0) {
            return 0; // or return null or "-"
        }

        $percent = ($totalAttended / $totalEvents) * 100;
        return min($percent, 100);
}

    function attendanceRemark($totalPercentage){
        if($totalPercentage >= 80){
            return 'PASSED';
        } else if ($totalPercentage < 80){
            return 'FAILED';
        } else if($totalPercentage == null){
            return '-';
        }
    }
}

$scObj = new Scorecard();
?>
