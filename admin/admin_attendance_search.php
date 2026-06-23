<?php
include "../classes/activities.php";
include "../classes/semester.php";

$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];

$keyword = $_GET['search'] ?? '';
$actId = $_GET['actId'];
// if ($keyword === '') {
//     exit; // return no rows
// }
$ar = $actobj->searchAttendanceRecords($keyword, $actId);
var_dump($actId);

foreach ($ar as $s) {
    echo "
        <tr>
             <td>{$s['iskolarno']}</td>
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
                    data-iskolarno='" . ($s['iskolarno'] ?? '') . "'
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