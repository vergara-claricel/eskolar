<?php
include "../classes/scholar.php";
include "../classes/semester.php";
$title = "Scholars";
$allscho = $schoobj->getAllScholars();
$activeSem = $semobj->getActiveSemester();
include "../assets/layout.php"; // HEADER + SIDEBAR + TOPBAR
?>

<h2>Scholars</h2>
<a href= "admin_scholars_create.php" id="nav_addscholar">Add Scholar</a>

<table class="table">
    <thead>
        <tr>
            <th>Iskolar No. </th>
            <th>Full Name</th>
            <th>Barangay</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($allscho as $s): ?>
            <tr>
                <td><?= $s['username'] ?></td>
                <td><?= $s['last_name'] . ', ' . $s['first_name'] ?></td>
                <td><?= $s['barangay'] ?></td>
                <td>
                    <a href="/esko/admin/admin_scholars_view.php?semid=<?= urlencode($activeSem['sem_id']) ?>&scholarid=<?= urlencode($s['user_id']) ?>">View</a>                  
                    <a href="">Edit</a>
            </td>
                

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include "../assets/layout_end.php"; ?>