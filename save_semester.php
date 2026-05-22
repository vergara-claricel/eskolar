<?php
// require_once "../connection.php";
require_once "../localcon.php";
include "../classes/semester.php";

//create new sem
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

        echo $result ? "Semester saved successfully!" : "Failed to save semester.";

    } catch (PDOException $e) {
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
        } else {
            echo "Update failed.";
        }
    } catch(PDOException $e){
        echo "Error: " . $e->getMessage();
    }
}
?>