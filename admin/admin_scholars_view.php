<?php
$title = "View Scholar";
// include "../classes/scholar.php";
// include "../classes/activities.php";
// include "../classes/scorecard.php";
require_once "../test.php";
//   include "../assets/layout.php";

// require_once "../classes/supabase.php";
// include "../classes/supa_scholar.php";
// include "../classes/supa_scorecard.php";
// include "../classes/supa_activities.php";

// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $schoobj = new Scholar($api);
// $scObj = new Scorecard($api);
$actobj = new Activities($api);
$offObj = new Officer($api);


$adminId = $_SESSION['userid'];
$adminName = $offObj->getAdminName($adminId);
$scholarUserId = $_GET['scholarid'];


$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];

$scholar_info = $schoobj->getScholarInfo($scholarUserId);
$scholar_ar = $schoobj->getScholarRecord($scholarUserId, $semId);

$scholarBrgy = $scholar_info['barangay'];
$scholarAttended = $scObj->getAttendedEventsPerScholar($scholarUserId, $semId);
$totalBrgyEvents = $scObj->getTotalBarangayEvents($scholarBrgy, $semId);
$totalMunicipalEvents = $scObj->getMunicipalEvents($semId);
$totalEvents = $scObj->getTotalEvents($totalBrgyEvents, $totalMunicipalEvents);
$quotaPercentage = $scObj->computeQuotaPercent($scholarAttended, $totalEvents);
$passFail = $scObj->attendanceRemark($quotaPercentage);

if (isset($_POST['updateAttendanceRecord'])) {

    $ar_id = $_POST['ar_id'];
    $activity_id = $_POST['activityid'];
    $user_id = $scholarUserId;
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

        print_r($result);
        if ($result) {
            // echo "success";
            header("Location: /esko/admin/admin_scholars_view.php?semid=$semId&scholarid=$user_id");
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
    /* Container matches your 900px table */
    .container {
        max-width: 950px;
        margin: 0 auto 24px auto;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* The Main Header Split Box */
    .scholarinfo {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 24px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);

        display: flex;
        justify-content: space-between;
        /* Spreads profile left, metrics right */
        align-items: center;
        /* Keeps everything vertically centered */
        gap: 40px;
    }

    /* LEFT SIDE: Identity & Info */
    .scholarinfo .profile-side {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
        /* Takes up the available left space naturally */
    }

    .scholarinfo h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .scholar-id {
        font-size: 0.8rem;
        font-weight: 500;
        background: #f1f5f9;
        color: #475569;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    /* Horizontal alignment for Barangay & Semester */
    .scholarinfo .meta-details {
        display: flex;
        gap: 20px;
        font-size: 0.85rem;
        color: #64748b;
    }

    .scholarinfo .meta-details span b {
        color: #334155;
        font-weight: 500;
    }


    /* RIGHT SIDE: Compact Metrics Column */
    .scholarinfo .metrics-side {
        display: grid;
        grid-template-columns: repeat(3, auto);
        /* Dynamically shrinks to fit text width */
        gap: 24px;
        background: #f8fafc;
        /* Light grey backdrop to group metrics together */
        padding: 12px 20px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
    }

    /* Individual Stat Layout */
    .scholarinfo p {
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }

    .scholarinfo p b {
        font-size: 1.05rem;
        /* Larger font size for core metrics */
        text-transform: none;
        letter-spacing: normal;
        color: #0f172a;
        font-weight: 600;
    }

    /* Color indicators for Remarks status */
    .status-pass {
        color: #16a34a !important;
    }

    .status-fail {
        color: #dc2626 !important;
    }

    
    .scholar-ar-header {
    display: flex;
    justify-content: space-between;
    align-items: end;
    margin: 30px 20px 18px;
    gap: 20px;
}

.ar-title h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #222;
}

.ar-title p {
    margin: 4px 0 0;
    color: #666;
    font-size: 14px;
}

#exportSCbtn {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 10px 16px;
    background: #dc2626;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: .2s ease;
}

#exportSCbtn:hover {
    background: #b91c1c;
}

#exportSCbtn i {
    font-size: 15px;
}

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
</style>



<div class="container">

    <div class="scholarinfo">

        <!-- LEFT COLUMN: Profile & Registration Details -->
        <div class="profile-side">
            <h2>
                <?= $scholar_info['users']['first_name'] . ' ' . $scholar_info['users']['last_name'] ?>
                <span class="scholar-id">#<?= $scholar_info['username'] ?></span>
            </h2>
            <div class="meta-details">
                <span>Barangay: <b><?= $scholar_info['barangay'] ?></b></span>
                <span>Semester: <b><?= $activeSem['semester_name'] ?></b></span>
                <span>Contact: <b><?= $scholar_info['phonenumber'] ?></b></span>
            </div>
        </div>

        <!-- RIGHT COLUMN: Performance & Attendance Tracker -->
        <div class="metrics-side">
            <p>Events Attended <b><?= $scholarAttended . ' / ' . $totalEvents ?></b></p>
            <p>Attendance <b><?= $quotaPercentage . '%' ?></b></p>
            <p>Remarks <b class="status-<?= strtolower($passFail) ?>"><?= $passFail ?></b></p>
        </div>

    </div>
</div>


<div class="scholar-ar-header">
    <div class="ar-title">
        <h4>Activity Information</h4>
        <p>View and manage the scholar's attendance records.</p>
    </div>
    <a id="exportSCbtn" href="export_scholar_pdf.php?scholarid=<?= $scholarUserId ?>" target="_blank">
        Export PDF
    </a>
</div>
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Activity Name</th>
                <th>Date</th>
                <th>Classification</th>
                <th>Timein / Timeout</th>
                <th>Remarks</th>
                <th>Verified By</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($scholar_ar as $s):  ?>
                <!-- <?php
                        $officerName =
                            isset($s['first_name'])
                            ? $s['first_name'] . ' ' . $s['last_name']
                            : '-';
                        ?> -->
                <tr>
                    <td><?= $s['activityname'] ?></td>
                    <!-- date directly from attendance record -->
                    <td><?= $s['activitydate'] ?></td>
                    <td><?= $s['classification'] ?></td>
                    <td><?= $s['timein'] . ' / ' . $s['timeout'] ?></td>
                    <td><?= $s['attendance_status'] ?></td>
                    <td><?= $s['officer_fullname'] ?: '-' ?></td>
                    <td>
                        <!-- <a href="/esko/admin/admin_activities_view.php?Id=<?= $s['ar_id'] ?>">Edit</a> -->
                        <a href="javascript:void(0)"
                            class="edit-btn"
                            data-ar_id="<?= $s['ar_id'] ?>"
                            data-user_id="<?= $s['user_id'] ?: $_GET['scholarid'] ?>"
                            data-activityid="<?= $s['activity_id'] ?? '' ?>"
                            data-activityname="<?= $s['activityname'] ?? '' ?>"
                            data-scanned_date="<?= $s['activitydate'] ?? '' ?>"
                            data-timein="<?= $s['timein'] ?? '' ?>"
                            data-timeout="<?= $s['timeout'] ?? '' ?>"
                            data-scanned_by="<?= $s['scanned_by'] ?? '' ?>"
                            data-officer_fullname="<?= trim($s['officer_fullname'] ?? '') !== '' ? $s['officer_fullname'] : $adminName ?>">
                            Edit
                        </a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="editDrawer" class="edit-drawer">
        <div class="drawer-content">
            <span class="drawer-close" onclick="closeDrawer()">×</span>

            <h2>Edit Attendance</h2>

            <form id="editForm" method="POST">

                <input type="hidden" name="ar_id" id="ar_id">

                <label>Activity Name</label>
                <input type="text" id="activityname" disabled>

                <label>Date</label>
                <input type="text" id="scanned_date" disabled>

                <label>Time In</label>
                <input type="time" name="timein" id="timein">

                <label>Time Out</label>
                <input type="time" name="timeout" id="timeout">

                <label>Verified By</label>
                <input type="text" name="officer_fullname" id="officer_fullname" disabled>
                <input type="hidden" name="activityid" id="activityid">
                <input type="hidden" name="user_id" id="user_id">
                <input type="hidden" name="scanned_by" id="scanned_by">
                <button type="submit" name="updateAttendanceRecord">Save Changes</button>

            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('editDrawer').classList.add('open');
                document.getElementById('ar_id').value = this.dataset.ar_id;
                document.getElementById('activityname').value = this.dataset.activityname;
                document.getElementById('activityid').value = this.dataset.activityid;
                document.getElementById('user_id').value = this.dataset.user_id;
                document.getElementById('scanned_date').value = this.dataset.scanned_date;
                document.getElementById('timein').value = this.dataset.timein.substring(0, 5) || '';
                document.getElementById('timeout').value = this.dataset.timeout.substring(0, 5) || '';
                document.getElementById('scanned_by').value = this.dataset.scanned_by;
                document.getElementById('officer_fullname').value = this.dataset.officer_fullname;
            });
        });

        function closeDrawer() {
            document.getElementById('editDrawer').classList.remove('open');
        }
    </script>

    <?php include "../assets/layout_end.php"; ?>