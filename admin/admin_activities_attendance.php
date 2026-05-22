<?php
include "../classes/activities.php";
include "../classes/semester.php";
$title = "Dashboard";
include "../assets/layout.php";

$activesem = $semobj->getActiveSemester();
$activeSemId = $activesem['sem_id'];
$allacts = $actobj->getActivitiesOfActiveSem($activeSemId);
?>

<h2>Activities & Attendance</h2>
<a href="/esko/admin/admin_activities_create.php">Create Activity</a>

<table class="table">
    <thead>
        <tr>
            <th>Activity Name </th>
            <th>Date</th>
            <th>Semester</th>
            <th>Status</th>
            <th>Classification</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($allacts as $s): ?>
            <tr>
                <td><?= $s['activityname'] ?></td>
                <td><?= $s['date']?></td>
                <td><?= $s['semester'] ?></td>
                <td><?= $s['status'] ?></td>
                <td><?= $s['classification'] ?></td>
                <td>
                    <a href="/esko/admin/admin_activities_view.php?actId=<?= $s['activity_id'] ?>">View</a>
                    <a href="">Edit</a>
            </td>
                

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include "../assets/layout_end.php"; ?>