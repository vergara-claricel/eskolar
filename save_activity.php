<?php
// require_once "../connection.php";
include "../classes/semester.php";
include "../classes/activities.php";


if (isset($_POST['create_activity'])) {

    $name = $_POST['activityname'];
    $status = $_POST['activity_status'];
    $date = $_POST['activitydate'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $venue = $_POST['venue'];
    $classification = $_POST['classification'];
    $barangay = $_POST['barangay'] ?? null;
    $info = $_POST['activityinfo'];

    if ($classification !== "Barangay") {
        $barangay = null; // force null
    }
    try {
        // $semid = $activeSemID;

        $result = $actobj->createActivity($name, $status, $date, $start, $end, $venue, $classification, $barangay, $info, $activeSemID);
        echo $result ? "Activity created successfully!" : "Failed to create activity.";
        header("location: /esko/admin/admin_activities_attendance.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}