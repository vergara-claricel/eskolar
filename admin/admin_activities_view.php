<?php
// session_start();
$title = "View Activity";
// include "../assets/layout.php";
// include "../classes/supa_activities.php";
// $config = require __DIR__ . "/../api/supabase.php";
// $adminId = $_SESSION["userid"];
// $actobj = new Activities($api);
require_once "../test.php";
$adminId = $adminId ?? null;

$activeSem = $semobj->getActiveSemester();
$actId = $_GET['actId'];
$keyword = $_GET['search'] ?? '';
$actDetails = $actobj->getActivityDetails($actId);

// pagination limit
$page = max(1, (int)($_GET['page'] ?? 1));
$limit =5;
$search = $_GET['search'] ?? '';

$ar = $actobj->getActivityAttendanceReport(
    $actId,
$actDetails['classification'],
    $actDetails['barangay'],
    $search,
    $page,
    $limit
);

$totalRows = $actobj->countActivityAttendanceReport(
    $actId,
$actDetails['classification'],
    $actDetails['barangay'],
    $search
);

$totalPages = ceil($totalRows / $limit);
// $ar = $actobj->getActivityAttendanceReport(
//     $actId,
//     $actDetails['classification'],
//     $actDetails['barangay'],
//     $keyword
// );

$totalRecords = count($ar);


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
    $timeinStart = $_POST['timein_start'];
    $timeinEnd = $_POST['timein_end'];
    $timeoutStart = $_POST['timeout_start'];
    $timeoutEnd = $_POST['timeout_end'];
    $venue = $_POST['venue'];
    $info = $_POST['info'];

    try {
        $result = $actobj->updateActivity(
            $actId,
            $name,
            $classification,
            $barangay,
            $date,
            $timeinStart,
            $timeinEnd,
            $timeoutStart,
            $timeoutEnd,
            $venue,
            $info
        );


        if ($result) {
            // print_r($result);
            // exit;
            header("Location: /esko/admin/admin_activities_view.php?actId=$actId");
            exit;
        } else {
            echo "Update failed.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
include "../assets/layout.php";
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

.ar-header {
    display: flex;
    justify-content: space-between;
    align-items: end;
    margin: 30px 0 18px;
    gap: 20px;
}

.ar-title h3 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    color: #222;
}

.ar-title p {
    margin: 4px 0 0;
    color: #666;
    font-size: 14px;
}

#searchForm {
    display: flex;
    align-items: center;
    gap: 10px;
}

#searchAttendance {
    width: 260px;
    padding: 9px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
}

#searchAttendance:focus {
    border-color: #1e88e5;
}

#searchForm button {
    padding: 9px 16px;
    border: none;
    background: #1e88e5;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
}

#searchForm button:hover {
    background: #1565c0;
}


    .pagination{
    margin-top:20px;
    display:flex;
    justify-content:center;
    gap:8px;
}

.pagination a{
    padding:8px 14px;
    border:1px solid #ddd;
    border-radius:6px;
    text-decoration:none;
    color:#333;
}

.pagination a.active{
    background:#2d8cff;
    color:white;
    border-color:#2d8cff;
}
</style>

<div class="activity-view">

    <div class="activity-info">

        <!-- classification -->
        <div class="activity-classification">
            <?= $actDetails['classification'] ?: '-'?>
            <?php if ($actDetails['classification'] === 'Barangay' && !empty($actDetails['barangay'])): ?>
                - <?= $actDetails['barangay'] ?: 'N/A'?>
            <?php endif; ?>
        </div>

        <!-- header -->
        <div class="activity-header">

            <div class="activity-title">
                <div class="activity-name">
                    <?= htmlspecialchars($actDetails['activityname']) ?: '' ?>
                </div>

                <span class="activity-status">
                    <?= htmlspecialchars($actDetails['computed_status']) ?: 'Not set'?>
                </span>
            </div>

            <a href="javascript:void(0)"
                class="activity-edit"
                data-id="<?= $actDetails['activity_id'] ?>"
                data-name="<?= htmlspecialchars($actDetails['activityname'], ENT_QUOTES) ?>"
                data-classification="<?= $actDetails['classification'] ?>"
                data-barangay="<?= $actDetails['barangay'] ?? '' ?>"
                data-venue="<?= htmlspecialchars($actDetails['venue'] ?? '', ENT_QUOTES) ?>"
                data-date="<?= $actDetails['activitydate'] ?? '' ?>"
                data-timein-start="<?= $actDetails['timein_start'] ?? '' ?>"
                data-timein-end="<?= $actDetails['timein_end'] ?? '' ?>"
                data-timeout-start="<?= $actDetails['timeout_start'] ?? '' ?>"
                data-timeout-end="<?= $actDetails['timeout_end'] ?? '' ?>"
                data-info="<?= htmlspecialchars($actDetails['info'] ?? '', ENT_QUOTES) ?>"
                onclick="openDrawerFromElement(this)">
                Edit
            </a>

        </div>

        <!-- DETAILS GRID -->
        <div class="activity-grid">

            <div class="label">Date </div>
            <div class="value">
                <?= !empty($actDetails['activitydate'])
            ? date("F j, Y", strtotime($actDetails['activitydate']))
            : '-' ?>
            </div>

            <div class="label">TIMEIN CUTOFF</div>
            <div class="value"> <?= (!empty($actDetails['timein_start']) && !empty($actDetails['timein_end']))
            ? date("g:i A", strtotime($actDetails['timein_start'])) .
              " - " .
              date("g:i A", strtotime($actDetails['timein_end']))
            : 'TBA' ?>
            </div>

            <div class="label">Venue</div>
            <div class="value"><?= htmlspecialchars($actDetails['venue']) ?: 'TBA'?></div>


            <div class="label">TIMEOUT CUTOFF</div>
            <div class="value"> <?= (!empty($actDetails['timeout_start']) && !empty($actDetails['timeout_end']))
            ? date("g:i A", strtotime($actDetails['timeout_start'])) .
              " - " .
              date("g:i A", strtotime($actDetails['timeout_end']))
            : 'TBA' ?>
            </div>

            <div class="label">Info</div>

            <div class="value"  style="grid-column: span 3;">
                <?= !empty($actDetails['info'])
            ? nl2br(htmlspecialchars($actDetails['info']))
            : 'No additional information.' ?></div>

        </div>

    </div>



    <div class="ar-header">
        <div class="ar-title">
            <h4>Attendance Records</h4>
            <p>View and manage the activity's attendance records.</p>
        </div>

        <form method="GET" id="searchForm">
            <input type="text" name="search" id="searchAttendance" placeholder="Search scholar.." value="<?= $_GET['search'] ?? '' ?>">
            <button type="submit">search</button>
        </form>
    </div>

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
                                class="action-icon edit-btn"
                                data-ar_id="<?= $s['ar_id'] ?>"
                                data-user_id="<?= $s['user_id'] ?? '' ?>"
                                data-iskolarno="<?= $s['username'] ?? '' ?>"
                                data-fullname="<?= $s['last_name'] . ', ' . $s['first_name'] ?>"
                                data-timein="<?= $s['timein'] ?? '' ?>"
                                data-timeout="<?= $s['timeout'] ?? '' ?>"
                                data-scanned_by="<?= $s['scanned_by'] ?>"
                                data-officer_fullname="<?= $s['officer_fullname'] ?? '' ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">

<?php if ($page > 1): ?>
<a href="?actId=<?= $actId ?>&page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">
    Previous
</a>
<?php endif; ?>

<?php for($i=1;$i<=$totalPages;$i++): ?>

<a href="?actId=<?= $actId ?>&page=<?= $i ?>&search=<?= urlencode($search) ?>"
   class="<?= $i==$page ? 'active' : '' ?>">
    <?= $i ?>
</a>

<?php endfor; ?>

<?php if ($page < $totalPages): ?>
<a href="?actId=<?= $actId ?>&page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">
    Next
</a>
<?php endif; ?>

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

                <input type="hidden" id="page_activity_id" value="<?= $actId ?>">

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
                        <label>Timein Start</label>
                        <input type="time" name="timein_start" id="timein_start">
                    </div>

                    <div class="field">
                        <label>Timein End</label>
                        <input type="time" name="timein_end" id="timein_end">
                    </div>

                    <div class="field">
                        <label>Timeout Start</label>
                        <input type="time" name="timeout_start" id="timeout_start">
                    </div>

                    <div class="field">
                        <label>Timeout End</label>
                        <input type="time" name="timeout_end" id="timeout_end">
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
    const btn = e.target.closest('.edit-btn');
    if (!btn) return;

    document.getElementById('editDrawer').classList.add('open');

    document.getElementById('ar_id').value = btn.dataset.ar_id;
    document.getElementById('user_id').value = btn.dataset.user_id;
    document.getElementById('iskolarno').value = btn.dataset.iskolarno;
    document.getElementById('fullname').value = btn.dataset.fullname;

    document.getElementById('timein').value =
        btn.dataset.timein ? btn.dataset.timein.substring(0, 5) : '';

    document.getElementById('timeout').value =
        btn.dataset.timeout ? btn.dataset.timeout.substring(0, 5) : '';

    document.getElementById('officer_fullname').value =
        btn.dataset.officer_fullname;

    document.getElementById('scanned_by').value =
        btn.dataset.scanned_by;
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
            document.getElementById('timein_start').value = el.dataset.timeinStart;
            document.getElementById('timein_end').value = el.dataset.timeinEnd;
            document.getElementById('timeout_start').value = el.dataset.timeoutStart;
            document.getElementById('timeout_end').value = el.dataset.timeoutEnd;
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
        let timer;
        const tableBody = document.getElementById('attendanceRecordsTable');
        const originalHTML = tableBody.innerHTML;

        document.getElementById('searchAttendance').addEventListener('input', function() {
            clearTimeout(timer);

            const keyword = this.value.trim();
            const actId = "<?= $actId ?>";

            if (keyword === "") {
                tableBody.innerHTML = originalHTML;
                return;
            }

            timer = setTimeout(() => {
                fetch(`admin_attendance_search.php?actId=${actId}&search=${encodeURIComponent(keyword)}`)
                    .then(res => res.text())
                    .then(data => {
                        document.getElementById('attendanceRecordsTable').innerHTML = data;
                    });
            }, 300);
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