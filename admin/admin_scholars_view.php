<?php
$title = "View Scholar";
include "../assets/layout.php";
include "../classes/scholar.php";
include "../classes/semester.php";
include "../classes/scorecard.php";
$scholarUserId = $_GET['scholarid'];

$activeSem = $semobj->getActiveSemester();
$scholar_info = $schoobj->getScholarInfo($scholarUserId);
print_r($scholar_info);
$scholar_ar = $schoobj->getScholarRecord($scholarUserId, $activeSem['sem_id']);

$scholarAttended = $scObj->getAttendedEventsPerScholar($scholarUserId, $activeSem['sem_id']);
?>

<h2>Scholar's Information </h2>
<button>Settings</button>
<p>Name: <b><?=  $scholar_info['first_name'] . ' ' . $scholar_info['last_name']?> </b></p>
<p>Barangay: <b><?= $scholar_info['barangay'] ?></b></p>
<p>Iskolar No.: <b><?= $scholar_info['username'] ?></b></p>
<p>Semester: <b><?= $activeSem['semester_name'] ?></b></p>
<p>Events Attended: <b> <?= $scholarAttended['attended_count'] ?></b></p>
<p>Attendance Percentage: <b></b></p>
<p>Remarks: <b></b></p>



<h3>Activities Information</h3>
    <div class="table-container">
        <table class="table">
    <thead>
        <tr>
            <th>Activity Name</th>
            <th>Date</th>
            <th>Classification</th>
            <th>Remarks</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($scholar_ar as $s): ?>
            <tr>
                <td><?= $s['activityname'] ?></td>
                <td><?= $s['scanned_date'] ?></td>
                <td><?= $s['classification'] ?></td>
                <td><?= $s['attendance_status'] ?></td>
                <td>
                    <a href="/esko/admin/admin_activities_view.php?Id=<?= $s['ar_id'] ?>">Edit</a>
                </td>
                

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include "../assets/layout_end.php"; ?>
