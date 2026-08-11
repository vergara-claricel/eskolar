<?php
// include "../classes/supa_activities.php";
// include "../classes/semester.php";
$title = "Dashboard";
require_once "../test.php";
// $config = require __DIR__ . "/../api/supabase.php";

// $actobj = new Activities($api);
// $activesem =[];
// $activesem = $semobj->getActiveSemester();
// $activeSemId = $activesem['sem_id'];
// $allacts =[];
// if(!empty($activesem)){
//     $allacts = $actobj->getActivitiesOfActiveSem($activeSemId);
// }

$activesem = $semobj->getActiveSemester();
$activeSemId = null;
$allacts = [];

if (!empty($activesem)) {
    $activeSemId = $activesem['sem_id'];
    $allacts = $actobj->getActivitiesOfActiveSem($activeSemId);
}

if (isset($_POST['create_activity'])) {

    $name = $_POST['activityname'];
    $date = $_POST['activitydate'];
    $timeinStart = $_POST['timein_start'];
    $timeinEnd = $_POST['timein_end'];
    $timeoutStart = $_POST['timeout_start'];
    $timeoutEnd = $_POST['timeout_end'];
    $venue = $_POST['venue'];
    $classification = $_POST['classification'];
    $barangay = $_POST['barangay'] ?? null;
    $info = $_POST['activityinfo'];

    if ($classification !== "Barangay") {
        $barangay = null; // force null
    }
    try {
        $semid = $activeSemId;

        $result = $actobj->createActivity($name, $date, $timeinStart, $timeinEnd, $timeoutStart, $timeoutEnd, $venue, $classification, $barangay, $info, $semid);
        echo $result ? "Activity created successfully!" : "Failed to create activity.";
        header("location: /esko/admin/admin_activities_attendance.php?success=added");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
include "../assets/layout.php";
?>
<style>
    /* ADD BUTTON */
    #create_activitybtn {
        background: #FFC857;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: 0.2s;
        margin-left: auto;
        margin-right: 20px;
    }

    #create_activitybtn:hover {
        background: #1565c0;
    }

    .filter-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin: 15px 0;
    }

    #searchForm {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #searchActs {
        width: 250px;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #searchForm button {
        padding: 8px 16px;
        border: none;
        background: #2d8cff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    #searchForm button:hover {
        background: #1f73d8;
    }

    #statusFilter,
    #barangayFilter {
        min-width: 170px;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
    }

    .success-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
        border-left: 5px solid #43a047;
        padding: 12px 16px;
        margin-bottom: 15px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
    }

    .success-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .close-notif {
        background: none;
        border: none;
        color: #2e7d32;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        line-height: 1;
        padding: 0;
    }

    .close-notif:hover {
        color: #1b5e20;
    }



</style>

<div class="page-header">
    <h2>Activities & Attendance</h2>
    <p> Manage activity and attendance records. </p>
</div>

<?php if (isset($_GET['success'])): ?>

    <div class="success-box" id="successBox">
        <div class="success-content">
            <i class="fa-solid fa-circle-check"></i>

            <span>
                <?php
                if ($_GET['success'] === 'added') {
                    echo "Activity added successfully.";
                } elseif ($_GET['success'] === 'deleted') {
                    echo "Activity deleted successfully.";
                }
                ?>
            </span>
        </div>

        <button class="close-notif"
                onclick="document.getElementById('successBox').remove()">
            &times;
        </button>
    </div>

<?php endif; ?>

<div class="filter-bar">
    <form method="GET" id="searchForm">
        <input type="text" name="search" id="searchActs" placeholder="Search…" value="<?= $_GET['search'] ?? '' ?>">
        <button type="submit">search</button>
    </form>

    <select name="status" id="statusFilter">
        <option value="">All Events</option>
        <option value="Upcoming">Upcoming</option>
        <option value="Active">Active</option>
        <option value="Ended">Ended</option>
    </select>

    <select name="barangay" id="barangayFilter">
        <option value="">All Barangays</option>
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

    <button id="createActivityBtn" onclick="checkActiveSemester()">&plus; Create Activity</button>
    <!-- <a href="/esko/admin/admin_activities_create.php" id="create_activitybtn" onclick="return checkActiveSemester();">Create Activity</a> -->
</div>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Activity Name </th>
                <th>Date</th>
                <th>Status</th>
                <th>Classification</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody id="activitiesTable">
            <?php if (!empty($allacts)): ?>
                <?php foreach ($allacts as $s): ?>
                    <tr>
                        <td><?= $s['activityname'] ?></td>
                        <td><?= $s['activitydate'] ?></td>
                        <td><?= $s['computed_status'] ?></td>
                        <td>
                            <?= $s['classification'] ?>
                            <?php if ($s['classification'] === 'Barangay' && !empty($s['barangay'])): ?>
                                - <?= $s['barangay'] ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/esko/admin/admin_activities_view.php?actId=<?= $s['activity_id'] ?>"
                                class="action-icon"
                                title="View Activity Information">
                                <i class="fa-solid fa-eye"></i></a>
                            <a href="/esko/admin/admin_delete_activity.php?actId=<?= $s['activity_id'] ?>" class="action-icon"
                                onclick="return confirm('Are you sure you want to delete this activity?');"
                                title="Delete Activity">
                                <i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 12px;">
                        No activities found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="createActivityModal" class="modal">
    <div class="modal-content">

        <span class="close-modal" onclick="closeModal()">&times;</span>

        <h3>New Activity</h3>

        <div id="activity_form">

            <form method="POST" class="activity-form">

                <!-- Activity Name -->
                <div class="form-group full">
                    <label class="form-label" for="activityname">
                        Activity Name
                    </label>
                    <input type="text" name="activityname" id="activityname">
                </div>


                <!-- Date + Venue -->
                <div class="form-group">
                    <label class="form-label" for="activitydate">
                        Activity Date
                    </label>
                    <input type="date" name="activitydate" id="activitydate">
                </div>

                <div class="form-group">
                    <label class="form-label" for="venue">
                        Venue
                    </label>
                    <input type="text" name="venue" id="venue">
                </div>


                <!-- Time In -->
                <div class="time-row full">

                    <div class="form-group">
                        <label class="form-label" for="timein_start">
                            TIME IN START
                        </label>
                        <input type="time" name="timein_start" id="timein_start">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="timein_end">
                            TIME IN END
                        </label>
                        <input type="time" name="timein_end" id="timein_end">
                    </div>

                </div>


                <!-- Time Out -->
                <div class="time-row full">

                    <div class="form-group">
                        <label class="form-label" for="timeout_start">
                            TIME OUT START
                        </label>
                        <input type="time" name="timeout_start" id="timeout_start">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="timeout_end">
                            TIME OUT END
                        </label>
                        <input type="time" name="timeout_end" id="timeout_end">
                    </div>

                </div>


                <!-- Classification + Barangay -->
                <div class="form-group">
                    <label class="form-label" for="classification">
                        Classification
                    </label>

                    <select name="classification" id="classification">
                        <option value="Municipal">Municipal</option>
                        <option value="Barangay">Barangay</option>
                    </select>
                </div>


                <div id="barangayField" class="form-group hidden">
                    <label class="form-label" for="barangay">
                        Select Barangay
                    </label>

                    <select name="barangay" id="barangay">
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


                <!-- Additional Info -->
                <div class="form-group full">
                    <label class="form-label" for="activityinfo">
                        More Activity Info
                    </label>

                    <textarea
                        name="activityinfo"
                        id="activityinfo"
                    ></textarea>
                </div>


                <!-- Submit -->
                <div class="form-actions full">
                    <button type="submit" name="create_activity">
                        CREATE
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<script>
    let timer;

    const tableBody = document.getElementById('activitiesTable');
    const originalHTML = tableBody.innerHTML;

    function loadActivities() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            let search = document.getElementById('searchActs').value.trim();
            let status = document.getElementById('statusFilter').value;
            let barangay = document.getElementById('barangayFilter').value;
            if (search === "" && status == "" && barangay == "") {
                tableBody.innerHTML = originalHTML;
                return;
            }

            fetch(`admin_activities_search.php?search=${encodeURIComponent(search)}&status=${status}&barangay=${encodeURIComponent(barangay)}`)
                .then(res => res.text())
                .then(data => {
                    tableBody.innerHTML = data;
                });

        }, 300);
    }

    // live search
    document.getElementById('searchActs').addEventListener('input', loadActivities);
    document.getElementById('statusFilter').addEventListener('change', loadActivities);
    document.getElementById('barangayFilter').addEventListener('change', loadActivities);

    function checkActiveSemester() {
        const semId = <?= json_encode($activeSemId ?? null) ?>;

        if (!semId) {
            alert("There is no active semester. Please set an active semester first.");
            return false; // prevents navigation
        }

        document.getElementById("createActivityModal").style.display = "flex";
        return true; // allows navigation
    }

    function closeModal() {
        document.getElementById("createActivityModal").style.display = "none";
    }

    document.addEventListener("DOMContentLoaded", function() {
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


<?php include "../assets/layout_end.php"; ?>