<?php
$title = "View Activity";
include "../assets/layout.php";
include "../classes/activities.php";


$actId = $_GET['actId'];
$actDetails = $actobj->getActivityDetails($actId);

$ar = $actobj->getAttendanceRecords($actId);

?>

<div class="activity-view">

    <div class="activity-info">
        <h2>Activity Overview</h2>

        <p><b>Name:</b> <?= htmlspecialchars($actDetails['activityname']) ?></p>
        <p><b>Date:</b> <?= htmlspecialchars($actDetails['date']) ?></p>
        <p><b>Venue:</b> <?= htmlspecialchars($actDetails['semester']) ?></p>
        <p><b>Status:</b> <?= htmlspecialchars($actDetails['status']) ?></p>
        <p><b>Info:</b><br><?= nl2br(htmlspecialchars($actDetails['info'])) ?></p>
    </div>



  <h3>Attendance Records</h3>
    <div class="table-container">
        <table class="table">
    <thead>
        <tr>
            <th>Iskolar No.</th>
            <th>Full Name</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Remark</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($ar as $s): ?>
            <tr>
                <td><?= $s['iskolarno'] ?></td>
                <td><?= $s['last_name'] . ', ' . $s['first_name'] ?></td>
                <td><?= $s['timein'] ?></td>
                <td><?= $s['timeout'] ?></td>
                <td><?= $s['attendance_status'] ?></td>
                <td>
                    <a href="/esko/admin/admin_activities_view.php?Id=<?= $s['ar_id'] ?>">Edit</a>
                </td>
                

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>


<?php include "../assets/layout_end.php"; ?>