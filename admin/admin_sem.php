<?php

$title = "Semester";
include "../assets/layout.php";

$config = require __DIR__ . "/../api/supabase.php";

$api = new Supabase($config);

$semobj = new Semester($api);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_sem'])) {

    $semester_name = $_POST['semester_name'];
    $semester_start = $_POST['semester_start'];
    $semester_end = $_POST['semester_end'];
    $semester_status = $_POST['semester_status'];

       try {
        $result = $semobj->saveSemester(
            $semester_name,
            $semester_start,
            $semester_end,
            $semester_status
        );
        header("location: /esko/admin/admin_sem.php");
        exit;

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// change active sem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_sem'])){
    $sem_id = $_POST['activesem'];

    try {
        $newsem = $semobj->updateActiveSem($sem_id);
         if ($newsem) {
            echo "Active semester updated!";
            header("location: /esko/admin/admin_sem.php");
            exit;
        } else {
            echo "Update failed.";
        }
    } catch(Exception $e){
        echo "Error: " . $e->getMessage();
    }
}
?>

<h2>Semester</h2>

<div class="sem-page">

    <!-- LEFT / MAIN AREA -->
    <div class="sem-left">

        <!-- SET ACTIVE SEM -->
        <div id="setsem">
            <h3>Set Active Semester</h3>
            <form method="POST">
                <?php $allsem = $semobj->getAllSemester(); ?>

                <select name="activesem" id="activesem">
                    <?php foreach ($allsem as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>"
                            <?= ($sem['semester_status'] == 'active') ? 'selected' : '' ?>>
                            <?= $sem['semester_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="update_sem" id="update_sem">Update</button>
            </form>
        </div>

        <!-- SEM LIST -->
        <div id="semlist">
            <table class="table">
                <thead>
                    <tr>
                        <th>Semester Name</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($allsem as $row): ?>
                    <tr>
                        <td><?= $row['semester_name'] ?></td>
                        <td>
                            <?= (new DateTime($row['semester_start']))->format('M d, Y') ?>
                            -
                            <?= (new DateTime($row['semester_end']))->format('M d, Y') ?>
                        </td>
                        <td><?= $row['semester_status'] ?></td>
                        <td>
                            <!-- <button>Edit</button> -->
                            <button>Archive</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- RIGHT SIDE / CREATE FORM -->
    <div class="sem-right">

        <div id="semform">
            <form method="POST" class="sem-form">

            <h4>Create New Semester</h4>

                <div class="semform-item">
                    <label>Semester Name</label>
                <input type="text" name="semester_name" required>
                </div>

                <div class="semform-item">
                    <label>Start Date</label>
                <input type="date" name="semester_start" required>
                </div>

                <div class="semform-item">
                    <label>End Date</label>
                <input type="date" name="semester_end" required>
                </div>

                <div class="semform-item">
                    <label>Status</label>
                    <div id="rb-sem">
                        <label><input type="radio" name="semester_status" value="inactive"> Inactive</label>
                        <label><input type="radio" name="semester_status" value="active"> Active</label>
                    </div>
                </div>
                

                <button type="submit" name="create_sem">CREATE SEM</button>
            </form>
        </div>

    </div>

</div>
<script>
    const select = document.getElementById('activesem');
    let originalValue = select.value;

    // store original value when page loads
    document.addEventListener('DOMContentLoaded', () => {
        originalValue = select.value;
    });

    document.getElementById('update_sem').addEventListener('click', function(e) {
        const selectedText = select.options[select.selectedIndex].text;

        const confirmMsg = `Change active semester to "${selectedText}"?`;

        if (!confirm(confirmMsg)) {
            e.preventDefault();

            // revert to original selection
            select.value = originalValue;
        }
    });
</script>
<?php include "../assets/layout_end.php"; ?>