<?php
// session_start();
$title = "View Activity";
include "../assets/layout.php";
include "../classes/supa_activities.php";
$config = require __DIR__ . "/../api/supabase.php";
$adminId = $_SESSION["userid"];
$actobj = new Activities($api);
$activeSem = $semobj->getActiveSemester();
$actId = $_GET['actId'];
$actDetails = $actobj->getActivityDetails($actId);

$ar = $actobj->getActivityAttendanceReport(
    $actId,
    $actDetails['classification'],
    $actDetails['barangay']
);
// print_r($ar);
// exit;


if (isset($_POST['updateAttendanceRecord'])) {

        $ar_id = $_POST['ar_id'];
        $activity_id = $actId;
        $user_id = $_POST['user_id'];
        $date = date('Y-m-d');
        $timein = !empty($_POST['timein']) && $_POST['timein'] !== '00:00' 
        ? $_POST['timein'] 
        : null;

        $timeout = !empty($_POST['timeout']) && $_POST['timeout'] !== '00:00' 
            ? $_POST['timeout'] 
            : null;
        $scanned_by = $adminId;

        try {
            $result = $actobj->upsertAttendance(
            $ar_id,
            $activity_id,
            $user_id,
            $date,
            $timein,
            $timeout,
            $scanned_by
        );
            if ($result) {   
                // echo "success";
                header("Location: /esko/admin/admin_activities_view.php?actId=$actId");
                exit;
            } else {
                echo "Update failed.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


if (isset($_POST['updateActivityDetails'])) {

    $name = $_POST['activityname'];
    $classification = $_POST['classification'];
    $barangay = $_POST['barangay'] ?? null;
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $venue = $_POST['venue'];
    $info = $_POST['info'];

    try {
        $result = $actobj->updateActivity(
            $actId,
            $name,
            $classification,
            $barangay,
            $date,
            $start_time,
              $end_time,
            $venue,
            $info
        );


        if ($result) {
            header("Location: /esko/admin/admin_activities_view.php?actId=$actId");
            exit;
        } else {
            echo "Update failed.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<style>
    /* Overlay background */
    .edit-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: 0;
        height: 100vh;
        background: rgba(0, 0, 0, 0.45);
        overflow: hidden;
        transition: width 0.3s ease;
        z-index: 9999;
    }

    /* Drawer box */
    .edit-drawer .drawer-content {
        position: absolute;
        top: 0;
        right: 0;
        width: 360px;
        height: 100%;
        background: #ffffff;
        padding: 25px;
        box-shadow: -3px 0 10px rgba(0, 0, 0, 0.2);
        overflow-y: auto;
    }

    /* When open */
    .edit-drawer.open {
        width: 360px;
    }

    /* Close button */
    .drawer-close {
        font-size: 28px;
        cursor: pointer;
        float: right;
        color: #444;
        margin-bottom: 10px;
    }

    .drawer-close:hover {
        color: #000;
    }

    /* Title */
    .edit-drawer h2 {
        margin: 10px 0 20px;
        font-size: 20px;
        font-weight: 600;
    }

    /* Labels */
    .edit-drawer form label {
        display: block;
        margin: 12px 0 5px;
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }

    /* Inputs */
    .edit-drawer form input {
        width: 100%;
        padding: 10px;
        border: 1px solid #bbb;
        border-radius: 6px;
        font-size: 15px;
    }

    /* Submit Button */
    .edit-drawer form button {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        background: #1e88e5;
        color: white;
        border: none;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .edit-drawer form button:hover {
        background: #1565c0;
    }

    /* ACTIVTY DRAWER CSS */

    .activity-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: 0;
        height: 100vh;
        background: rgba(0, 0, 0, 0.45);
        overflow: hidden;
        transition: 0.3s ease;
        z-index: 9999;
    }

    .activity-drawer.open {
        width: 100%;
    }

    .activity-drawer .drawer-content {
        position: absolute;
        right: 0;
        top: 0;
        width: 420px;
        height: 100%;
        background: #fff;
        padding: 20px;
        overflow-y: auto;
    }

    .drawer-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .drawer-close {
        font-size: 26px;
        cursor: pointer;
    }

    .drawer-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 15px;
    }

    .field {
        display: flex;
        flex-direction: column;
    }

    .field.full {
        grid-column: 1 / -1;
    }

    .field label {
        font-size: 12px;
        margin-bottom: 4px;
        color: #555;
    }

    .field input,
    .field select,
    .field textarea {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }

    .drawer-btn {
        margin-top: 15px;
        width: 100%;
        padding: 10px;
        background: #1e88e5;
        color: white;
        border: none;
        border-radius: 6px;
    }
</style>

<div class="activity-view">

    <div class="activity-info">

        <!-- classification -->
        <div class="activity-classification">
            <?= $actDetails['classification'] ?>
            <?php if ($actDetails['classification'] === 'Barangay' && !empty($actDetails['barangay'])): ?>
                - <?= $actDetails['barangay'] ?>
            <?php endif; ?>
        </div>

        <!-- header -->
        <div class="activity-header">

            <div class="activity-title">
                <div class="activity-name">
                    <?= htmlspecialchars($actDetails['activityname']) ?>
                </div>

                <span class="activity-status">
                    <?= htmlspecialchars($actDetails['status']) ?>
                </span>
            </div>

            <a href="javascript:void(0)"
                class="activity-edit"
                data-id="<?= $actDetails['activity_id'] ?>"
                data-name="<?= htmlspecialchars($actDetails['activityname'], ENT_QUOTES) ?>"
                data-classification="<?= $actDetails['classification'] ?>"
                data-barangay="<?= $actDetails['barangay'] ?? '' ?>"
                data-venue="<?= htmlspecialchars($actDetails['venue'] ?? '', ENT_QUOTES) ?>"
                data-date="<?= $actDetails['acytivitydate'] ?? '' ?>"
                data-start_time="<?= $actDetails['start_time'] ?? '' ?>"
                data-end_time="<?= $actDetails['end_time'] ?? '' ?>"
                data-info="<?= htmlspecialchars($actDetails['info'] ?? '', ENT_QUOTES) ?>"
                onclick="openDrawerFromElement(this)">
                Edit
            </a>

        </div>

        <!-- DETAILS GRID -->
        <div class="activity-grid">

            <div class="label">Date / Time </div>
            <div class="value"><?= htmlspecialchars($actDetails['activitydate']) . ' / ' . htmlspecialchars($actDetails['start_time']) . ' - ' . htmlspecialchars($actDetails['end_time'])?></div>


            <div class="label">Venue</div>
            <div class="value"><?= htmlspecialchars($actDetails['venue']) ?></div>

            <div class="label">Info</div>

            <div class="value"><?= nl2br(htmlspecialchars($actDetails['info'])) ?></div>

        </div>

    </div>



    <h3>Attendance Records</h3>

    <form method="GET" id="searchForm">
    <input type="text" name="search" id="searchAttendance" placeholder="Search…" value="<?= $_GET['search'] ?? '' ?>">
    <button type="submit">search</button>
    </form>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Iskolar No.</th>
                    <th>Full Name</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Remark</th>
                    <th>Verified By</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="attendanceRecordsTable">
                <?php foreach ($ar as $s): ?>
                    <tr>
                        <td><?= $s['username'] ?></td>
                        <td><?= $s['last_name'] . ', ' . $s['first_name'] ?></td>
                        <td><?= $s['timein'] ? $s['timein'] : '-' ?></td>
                        <td><?= $s['timeout'] ? $s['timeout'] : '-' ?></td>
                        <td><?= $s['attendance_status'] ? $s['attendance_status'] : 'absent' ?></td>
                        <td><?= $s['officer_fullname'] ?? '-'  ?></td>
                        <td>
                            <!-- <a href="/esko/admin/admin_activities_view.php?Id=<?= $s['ar_id'] ?>">Edit</a> -->
                            <a href="javascript:void(0)"
                            class="edit-btn"
                            data-ar_id="<?= $s['ar_id'] ?>"
                            data-user_id="<?= $s['user_id'] ?? '' ?>"
                            data-iskolarno="<?= $s['username'] ?? ''?>"
                            data-fullname="<?= $s['last_name'] . ', ' . $s['first_name'] ?>"
                            data-timein="<?= $s['timein'] ?? '' ?>"
                            data-timeout="<?= $s['timeout'] ?? '' ?>"
                            data-scanned_by="<?= $s['scanned_by']?>"
                            data-officer_fullname="<?= $s['officer_fullname'] ?? '' ?>">
                            Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- REUSABLE EDIT DRAWER -->
    <div id="editDrawer" class="edit-drawer">
        <div class="drawer-content">
            <span class="drawer-close" onclick="closeDrawer()">×</span>

            <h2>Edit Attendance</h2>

            <form id="editForm" method="POST">

                <input type="hidden" name="ar_id" id="ar_id">

                <label>Iskolar No.</label>
                <input type="text" id="iskolarno" name="iskolarno" readonly>

                <label>Name</label>
                <input type="text" id="fullname" disabled>

                <label>Time In</label>
                <input type="time" name="timein" id="timein">

                <label>Time Out</label>
                <input type="time" name="timeout" id="timeout">

                <label>Verified By</label>
                <input type="text" name="officer_fullname" id="officer_fullname" readonly>
                <!-- <input type="hidden" name="activity_id" id="activity_id"> -->
                <input type="hidden" name="user_id" id="user_id">
                <input type="hidden" name="scanned_by" id="scanned_by">
                <button type="submit" name="updateAttendanceRecord">Save Changes</button>

            </form>
        </div>
    </div>


    <div id="activityDrawer" class="activity-drawer">
        <div class="drawer-content">

            <div class="drawer-header">
                <h2>Edit Activity</h2>
                <span class="drawer-close" onclick="closeActivityDrawer()">×</span>
            </div>

            <form method="POST">

                <input type="hidden" id="page_activity_id" value="<?= $actId?>">

                <div class="drawer-grid">

                    <div class="field full">
                        <label>Activity Name</label>
                        <input type="text" name="activityname" id="activityname">
                    </div>

                    <div class="field">
                        <label>Classification</label>
                        <select name="classification" id="classification">
                            <option value="Municipal">Municipal</option>
                            <option value="Barangay">Barangay</option>
                        </select>
                    </div>

                    <div class="field" id="barangayField">
                        <label>Barangay</label>
                        <select name="barangay" id="barangay" required>
                            <option value=""></option>
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

                    <div class="field full">
                        <label>Date</label>
                        <input type="date" name="date" id="date">
                    </div>
                
                    <div class="field">
                        <label>Start Time</label>
                        <input type="time" name="start_time" id="start_time">
                    </div>

                    <div class="field">
                        <label>End Time</label>
                        <input type="time" name="end_time" id="end_time">
                    </div>

                    <div class="field">
                        <label>Venue</label>
                        <input type="text" name="venue" id="venue">
                    </div>

                    <div class="field full">
                        <label>Info</label>
                        <textarea name="info" id="info"></textarea>
                    </div>

                </div>

                <button type="submit" class="drawer-btn" name="updateActivityDetails">Save Changes</button>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('edit-btn')) return;

            document.getElementById('editDrawer').classList.add('open');
            document.getElementById('ar_id').value = e.target.dataset.ar_id;
            document.getElementById('user_id').value = e.target.dataset.user_id;
            document.getElementById('iskolarno').value = e.target.dataset.iskolarno;
            document.getElementById('fullname').value = e.target.dataset.fullname;

            document.getElementById('timein').value =
                e.target.dataset.timein ? e.target.dataset.timein.substring(0,5) : '';

            document.getElementById('timeout').value =
                e.target.dataset.timeout ? e.target.dataset.timeout.substring(0,5) : '';

            document.getElementById('officer_fullname').value =
                e.target.dataset.officer_fullname;

            document.getElementById('scanned_by').value =
                e.target.dataset.scanned_by;
        });
    
        function closeDrawer() {
            document.getElementById('editDrawer').classList.remove('open');
        }

        function openDrawerFromElement(el) {
            document.getElementById('activityDrawer').classList.add('open');

            document.getElementById('page_activity_id').value = el.dataset.activity_id;
            document.getElementById('activityname').value = el.dataset.name;
            document.getElementById('classification').value = el.dataset.classification;
            document.getElementById('barangay').value = el.dataset.barangay;
            document.getElementById('venue').value = el.dataset.venue;
            document.getElementById('date').value = el.dataset.date;
            document.getElementById('start_time').value = el.dataset.start_time;
            document.getElementById('end_time').value = el.dataset.end_time;
            document.getElementById('info').value = el.dataset.info;

            toggleBarangayField();
        }

        function closeActivityDrawer() {
            document.getElementById('activityDrawer').classList.remove('open');
        }

        /* show/hide barangay automatically */
        document.addEventListener("DOMContentLoaded", function() {
            const classification = document.getElementById("classification");
            toggleBarangayField();
            classification.addEventListener("change", toggleBarangayField);
        });

        function toggleBarangayField() {
            const classification = document.getElementById("classification");
            const barangayField = document.getElementById("barangayField");
            const barangay = document.getElementById("barangay");
            if (classification.value === "Barangay") {
                barangayField.style.display = "flex";
                barangay.required = true;
            } else {
                barangayField.style.display = "none";
                barangay.required = false;
            }
        }

        // search logic as user types
    document.getElementById('searchAttendance').addEventListener('keyup', function() {
        let keyword = this.value;
        let actId = "<?= $actId ?>";
        fetch(`admin_attendance_search.php?actId=${actId}&search=${encodeURIComponent(keyword)}`)
            .then(res => res.text())
            .then(data => {
                document.getElementById('attendanceRecordsTable').innerHTML = data;
            });
    });

    
    // prevent reload
        const searchInput = document.getElementById('searchAttendance');
        const form = document.getElementById('searchForm');

        form.addEventListener('submit', e => e.preventDefault());

        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') e.preventDefault();
        });
    </script>
    <?php include "../assets/layout_end.php"; ?>