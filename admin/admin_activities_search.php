<?php
require_once "../test.php";

$adminId = $_SESSION['userid'];
$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];


$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$barangay = $_GET['barangay'] ?? '';
$allacts = $actobj->filterActivities($search, $status, $barangay, $semId);

if(empty($allacts)){
    echo"
        <tr>
            <td>No activities found.</td>
        </tr>
    ";
} else{
    foreach ($allacts as $s) {

    $classification = $s['classification'];

    if ($s['classification'] === 'Barangay' && !empty($s['barangay'])) {
        $classification .= ' - ' . $s['barangay'];
    }
    echo "
        <tr>
            <td>{$s['activitydate']}</td>
            <td>{$s['activityname']}</td>
            <td>{$s['computed_status']}</td>
            <td>{$classification}</td>
            <td>
                <a href='/esko/admin/admin_activities_view.php?actId={$s['activity_id']}'>View</a>
                <a href='/esko/admin/admin_activities_edit.php?actId={$s['activity_id']}'>Edit</a>
            </td>
        </tr>
    ";
    }
}

?>