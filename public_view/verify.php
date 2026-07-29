<?php
require_once '../classes/supabase.php';
$config = require __DIR__ . "esko/../api/supabase.php";
require_once "../test.php";

$api = new Supabase($config);

$token = $_GET['token'];

$result = $api->get(
    "scholars",
    "?verification_token=eq." . urlencode($token)
);

if (empty($result)) {
    die("Invalid verification token.");
}

$scholar = $result[0];
print_r($scholar);

//

$actobj = new Activities($api);
$offObj = new Officer($api);


$adminId = $_SESSION['userid'];
$adminName = $offObj->getAdminName($adminId);
$scholarUserId = $scholar['user_id'];


$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];

$scholar_info = $schoobj->getScholarInfo($scholarUserId);
$scholar_ar = $schoobj->getScholarRecord($scholarUserId, $semId);

$scholarBrgy = $scholar_info['barangay'];
$scholarAttended = $scObj->getAttendedEventsPerScholar($scholarUserId, $semId);
$totalBrgyEvents = $scObj->getTotalBarangayEvents($scholarBrgy, $semId);
$totalMunicipalEvents = $scObj->getMunicipalEvents($semId);
$totalEvents = $scObj->getTotalEvents($totalBrgyEvents, $totalMunicipalEvents);
$quotaPercentage = $scObj->computeQuotaPercent($scholarAttended, $totalEvents);
$passFail = $scObj->attendanceRemark($quotaPercentage);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<header>

</header>
<body>
    <h1>Digital Scorecard</h1>
    <div class="container">
        <h2>Scholar Information</h2>
        <div id="scholarInfo">
            <div class="infoField">
                <p>Name: </p><p><?= $scholar_info['users']['first_name'] . ' ' . $scholar_info['users']['last_name'] ?></p>
            </div>
           <div class="infoField">
                <p>Barangay: </p><p><?= $scholar_info['barangay'] ?></p>
            </div>
            <div class="infoField">
                <p>Iskolar No.: </p><p><?= $scholar_info['username'] ?></p>
            </div>
            <div class="infoField">
                <p>Semester: </p><p><?= $activeSem['semester_name'] ?></p>
            </div>
            <div class="infoField">
                <p>Remarks: </p><p class="status-<?= strtolower($passFail) ?>"><?= $passFail ?></p>
            </div>
        </div>
    </div>

    <h2>Activities Information</h2>
        <div id="activitiesInfo">
            <div class="infoField">
                <p>Activities Attended: </p><p><?= $scholarAttended. ' / ' . $totalEvents ?></p>
            </div>
            <div class="infoField">
                <p>Quota: </p><p><?= $quotaPercentage . '%' ?></p>
            </div>
     </div>

     <div class="table-container">
                <table class="table">
                <thead>
                    <tr>
                        <th>Activity Name</th>
                        <th>Date</th>
                        <th>Classification</th>
                        <th>Timein / Timeout</th>
                        <th>Remarks</th>
                        <th>Verified By</th>
                    </tr>
                </thead>
                    <tbody>
        <?php foreach ($scholar_ar as $s):  ?>
            <!-- <?php
                $officerName =
            isset($s['first_name'])
            ? $s['first_name'] . ' ' . $s['last_name']
            : '-';
                ?> -->
            <tr>
                <td><?= $s['activityname'] ?></td>
                <!-- date directly from attendance record -->
                <td><?= $s['activitydate'] ?></td> 
                <td><?= $s['classification'] ?></td>
                <td><?= $s['timein'] .' / ' . $s['timeout'] ?></td>
                <td><?= $s['attendance_status'] ?></td>
                <td><?= $s['officer_fullname'] ?: '-' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
     </div>
</body>
</html>