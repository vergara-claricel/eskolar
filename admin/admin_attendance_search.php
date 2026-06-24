<?php
// include "../classes/activities.php";
// include "../classes/semester.php";
require_once "../test.php";


$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];

$keyword = $_GET['search'] ?? '';
$actId = $_GET['actId'];
// if ($keyword === '') {
//     exit; // return no rows
// }
$actDetails = $actobj->getActivityDetails($actId);
$ar = $actobj->getActivityAttendanceReport(
    $actId,
    $actDetails['classification'],
    $actDetails['barangay'], $keyword
);

var_dump($ar);

foreach ($ar as $s) {
    echo "
        <tr>
             <td>{$s['username']}</td>
            <td>{$s['last_name']}, {$s['first_name']}</td>
            <td>" . ($s['timein'] ?: '-') . "</td>
            <td>" . ($s['timeout'] ?: '-') . "</td>
            <td>" . ($s['attendance_status'] ?: 'absent') . "</td>
            <td>" . ($s['officer_fullname'] ?? '-') . "</td>
            <td>
                <a href='javascript:void(0)'
                    class='edit-btn'
                    data-ar_id='{$s['ar_id']}'
                    data-user_id='" . ($s['user_id'] ?? '') . "'
                    data-username='" . ($s['username'] ?? '') . "'
                    data-fullname='{$s['last_name']}, {$s['first_name']}'
                    data-timein='" . ($s['timein'] ?? '') . "'
                    data-timeout='" . ($s['timeout'] ?? '') . "'
                    data-scanned_by='{$s['scanned_by']}'
                    data-officer_fullname='" . ($s['officer_fullname'] ?? '') . "'>
                Edit
                </a>
            </td>
        </tr>
    ";
}
?>