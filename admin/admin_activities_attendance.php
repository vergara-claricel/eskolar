<?php
// include "../classes/supa_activities.php";
// include "../classes/semester.php";
$title = "Dashboard";
require_once "../test.php";
$adminId = $_SESSION['userid'];
// $config = require __DIR__ . "/../api/supabase.php";

$barangays = [
    'Atilano Ricardo',
    'Bagumbayan',
    'Banawang',
    'Binuangan',
    'Binukawan',
    'Ibis',
    'Ibaba',
    'Pag-asa',
    'Parang',
    'Paysawan',
    'Quinawan',
    'San Antonio',
    'Saysain',
    'Tabing-Ilog'
];

$activesem = $semobj->getActiveSemester();
$activeSemId = null;
$allacts = [];

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$barangay = $_GET['barangay'] ?? '';

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;
$totalRows = 0;

if (!empty($activesem)) {
    $activeSemId = $activesem['sem_id'];
    if ($search === '' && $status === '' && $barangay === '') {
        $allacts = $actobj->getActivitiesOfActiveSem(
            $activeSemId,
            $page,
            $limit
        );
        $totalRows = $actobj->countActivitiesOfActiveSem($activeSemId);
    } else {
        $allacts = $actobj->filterActivities(
            $activeSemId,
            $search,
            $status,
            $barangay,
            $limit,
            $offset
        );
        $totalRows = $actobj->countFilteredActivities(
            $search,
            $status,
            $barangay,
            $activeSemId
        );
    }
} else {
    $totalRows = 0;
}

$totalPages = ceil($totalRows / $limit);

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
    #createActivityBtn {
        background: #FFC857;
        color: white;
        padding: 8px 14px;
        border: 0;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: 0.2s;
        margin-left: auto;
        margin-right: 20px;
    }

    #createActivityBtn:hover {
        background: #1565c0;
    }

/* =========================
   FILTER BAR
========================= */

.filter-bar {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
    margin: 15px 0;
    width: 100%;
    box-sizing: border-box;
}


/* =========================
   SEARCH FORM
========================= */

#searchForm {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    flex-wrap: wrap;
}

#searchActs {
    width: 250px;
    height: 36px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

#searchForm button {
    height: 36px;
    padding: 8px 16px;
    border: none;
    background: #2d8cff;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    box-sizing: border-box;
}

#searchForm button:hover {
    background: #1f73d8;
}


/* =========================
   FILTER ITEMS
========================= */

.filter-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-item label {
    font-size: 13px;
    font-weight: 600;
    color: #555;
}

#statusFilter,
#barangayFilter {
    min-width: 170px;
    height: 36px;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    cursor: pointer;
    box-sizing: border-box;
}


/* =========================
   MOBILE
========================= */

@media (max-width: 480px) {
    /* Entire filter section */
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        gap: 12px;
    }

    /* Form becomes the mobile layout */
    #searchForm {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        gap: 12px;
    }

    /* Search input */
    #searchActs {
        width: 100%;
        height: 40px;
    }

    /* Search button */
    #searchForm button {
        width: 100%;
        height: 40px;
    }

    /* Filter items */
    .filter-item {
        width: 100%;
    }

    #statusFilter,
    #barangayFilter {
        width: 100%;
        min-width: 0;
        height: 40px;
    }

    /* Reset button */
    .action-icon {
        align-self: flex-start;
    }

    /* Create Activity */
    #createActivityBtn {
        width: 100%;
        margin: 0;
        box-sizing: border-box;
    }
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

        <div class="filter-item">
            <label for="statusFilter">Activity Status</label>
            <select name="status" id="statusFilter">
                <option value="">All Activities</option>
                <option value="Upcoming" <?= $status === 'Upcoming' ? 'selected' : '' ?>>Upcoming</option>
                <option value="Active" <?= $status === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Ended" <?= $status === 'Ended' ? 'selected' : '' ?>>Ended</option>
            </select>
        </div>

        <div class="filter-item">
            <label for="barangayFilter">Location</label>
            <select name="barangay" id="barangayFilter">
                <option value="">All Activities</option>
                <option value="Municipal" <?= $barangay === 'Municipal' ? 'selected' : '' ?>>Municipal</option>
                <option value="Barangay" <?= $barangay === 'Barangay' ? 'selected' : '' ?>>All Barangays</option>

                <?php foreach ($barangays as $brgy): ?>
                    <option value="<?= htmlspecialchars($brgy) ?>"
                        <?= $barangay === $brgy ? 'selected' : '' ?>>
                        <?= htmlspecialchars($brgy) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    <a href="/esko/admin/admin_activities_attendance.php"
        class="action-icon"
        title="Reset Filters">
        <i class="fa-solid fa-filter-circle-xmark"></i>
    </a>

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

<div class="pagination">

    <?php
    $query = http_build_query([
        'search' => $search,
        'status' => $status,
        'barangay' => $barangay
    ]);
    ?>

    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&<?= $query ?>">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&<?= $query ?>"
            class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&<?= $query ?>">Next</a>
    <?php endif; ?>

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
                        Activity Name*
                    </label>
                    <input type="text" name="activityname" id="activityname">
                </div>


                <!-- Date + Venue -->
                <div class="form-group">
                    <label class="form-label" for="activitydate">
                        Activity Date*
                    </label>
                    <input type="date" name="activitydate" id="activitydate" required>
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
                            TIME IN START*
                        </label>
                        <input type="time" name="timein_start" id="timein_start" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="timein_end">
                            TIME IN END*
                        </label>
                        <input type="time" name="timein_end" id="timein_end" required>
                    </div>

                </div>


                <!-- Time Out -->
                <div class="time-row full">

                    <div class="form-group">
                        <label class="form-label" for="timeout_start">
                            TIME OUT START*
                        </label>
                        <input type="time" name="timeout_start" id="timeout_start" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="timeout_end" required>
                            TIME OUT END*
                        </label>
                        <input type="time" name="timeout_end" id="timeout_end">
                    </div>

                </div>


                <!-- Classification + Barangay -->
                <div class="form-group">
                    <label class="form-label" for="classification">
                        Classification
                    </label>

                    <select name="classification" id="classification" required>
                        <option value="Municipal">Municipal</option>
                        <option value="Barangay">Barangay</option>
                    </select>
                </div>


                <div id="barangayField" class="form-group hidden">
                    <label class="form-label" for="barangay">
                        Select Barangay
                    </label>

                    <select name="barangay" id="barangay" required>
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
                        id="activityinfo"></textarea>
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
    document.getElementById('statusFilter').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });

    document.getElementById('barangayFilter').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });

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