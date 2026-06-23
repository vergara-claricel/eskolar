<?php

require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";
include "../classes/supa_activities.php";
include "../classes/supa_semester.php";

$api = new Supabase($config);
$actobj = new Activities($api);
$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$barangay = $_GET['barangay'] ?? '';
$allacts = $actobj->filterActivities($search, $status, $barangay);

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
            <td>{$s['date']}</td>
            <td>{$s['activityname']}</td>
            <td>{$s['status']}</td>
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