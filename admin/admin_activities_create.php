<?php
// $upd = $actobj->updateStatuses();
$title = "Activities & Attendance";
include "../assets/layout.php";
require_once "../classes/supabase.php";
$config = require __DIR__ . "esko/../api/supabase.php";
include "../classes/supa_activities.php";

$api = new Supabase($config);
$actobj = new Activities($api);
$activeSem = $semobj->getActiveSemester();

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
        $semid = $activeSem['sem_id'];

        $result = $actobj->createActivity($name, $status, $date, $start, $end, $venue, $classification, $barangay, $info, $semid);
        echo $result ? "Activity created successfully!" : "Failed to create activity.";
        header("location: /esko/admin/admin_activities_attendance.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

<h2>Create Activity</h2>
<div id="activity_form">
    <form method="POST" class="activity-form">
        <label for="activityname">Activity Name</label>
        <input type="text" name="activityname" id="activityname">
        <label for="last_name">Last Name</label>
        <select name="activity_status">
            <option value="Upcoming">Upcoming</option>
            <option value="Active">Active</option>
            <option value="Completed">Completed</option>
        </select>

        <label for="activitydate">Activity Date</label>
        <input type="date" name="activitydate" id="activitydate">

        <div class="time-row full">
            <div>
                <label for="start_time">Start Time</label>
                <input type="time" name="start_time" id="start_time">
            </div>

            <div>
                <label for="end_time">End Time</label>
                <input type="time" name="end_time" id="end_time">
            </div>
        </div>

        <label for="venue">Venue</label>
        <input type="text" name="venue" id="venue">

        <label for="classification">Classification</label>
        <select name="classification">
    
            <option name="clas" value="Municipal">Municipal</option>
            <option value="Barangay">Barangay</option>
        </select>

        <div id="barangayField" class="hidden full">
            <label for="barangay">Select Barangay</label>
            <select name="barangay">
                <option value="Atilano Ricardo">Atilano Ricardo</option>
                <option value="Bagumbayan">Bagumbayan</option>
                <option value="Banawang">Banawang</option>
                <option value="Binuangan">Binuangan</option>
                <option value="Binukawan">Binukawan</option>
                <option value="Ibis">Ibis</option>
                <option value="Ibaba">Ibaba</option>
                <option value="Pag-asa">Pag-asa</option>
                <option value="Parang">Parang</option>
                <option value="Paysawan">Paysawan</option>
                <option value="Quinawan">Quinawan</option>
                <option value="San Antonio">San Antonio</option>
                <option value="Saysain">Saysain</option>
                <option value="Tabing-Ilog">Tabing-Ilog</option>
            </select>
        </div>

        <label for="activityinfo">More Activity Info: </label>
        <textarea name="activityinfo" id="activityinfo"></textarea>

        <button type="submit" name="create_activity">CREATE</button>

    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const classification = document.querySelector('[name="classification"]');
    const barangayField = document.getElementById("barangayField");

    function toggleBarangay() {
        if (classification.value === "Barangay") {
            barangayField.classList.remove("hidden");
        } else {
            barangayField.classList.add("hidden");
        }
    }

    classification.addEventListener("change", toggleBarangay);
    toggleBarangay(); // run on load
});
</script>


<?php
include "../assets/layout_end.php";
?>