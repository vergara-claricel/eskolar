<?php
$title = "View Scholar";
// include "../classes/scholar.php";
// include "../classes/activities.php";
// include "../classes/scorecard.php";
require_once "../test.php";
$adminId = $_SESSION['userid'];
$actobj = new Activities($api);
$offObj = new Officer($api);


$adminId = $_SESSION['userid'];
$adminName = $offObj->getAdminName($adminId);
$scholarUserId = $_GET['scholarid'];


$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'] ?? null;

$scholar_info = [];
$scholar_ar = [];
$scholarAttended = 0;
$totalBrgyEvents =0;
$totalEvents = 0;
$totalMunicipalEvents = 0;
$quotaPercentage = 0;
$passFail = 'N/A';


if($semId != null){
    $scholar_info = $schoobj->getScholarInfo($scholarUserId);
    $scholar_ar = $schoobj->getScholarRecord($scholarUserId, $semId);
    $scholarBrgy = $scholar_info['barangay'];
    $scholarAttended = $scObj->getAttendedEventsPerScholar($scholarUserId, $semId);
    $totalBrgyEvents = $scObj->getTotalBarangayEvents($scholarBrgy, $semId);
    $totalMunicipalEvents = $scObj->getMunicipalEvents($semId);
    $totalEvents = $scObj->getTotalEvents($totalBrgyEvents, $totalMunicipalEvents);
    $quotaPercentage = $scObj->computeQuotaPercent($scholarAttended, $totalEvents);
    $passFail = $scObj->attendanceRemark($quotaPercentage);
} else {
    $scholar_info = $schoobj->getScholarInfo($scholarUserId);
}



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
</style>


<a href="/esko/admin/admin_scholars.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back</a>

<div class="container">
    <div class="scholarinfo">
        <!-- LEFT COLUMN: Profile & Registration Details -->
        <div class="profile-side">
            <h2>
                <?= $scholar_info['users']['first_name'] . ' ' . $scholar_info['users']['last_name'] ?>
                <span class="scholar-id">#<?= $scholar_info['username'] ?></span>
            </h2>
            <div class="meta-details">
                <span>Barangay: <b><?= $scholar_info['barangay'] ?? 'N/A'?></b></span>
                <span>Contact: <b><?= $scholar_info['phonenumber'] ?? 'N/A' ?></b></span>
                <span>Email: <b><?= $scholar_info['email'] ?? 'N/A' ?></b></span>
                <span>Semester: <b><?= $activeSem['semester_name'] ?? 'Not found.' ?></b></span>
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
                            class="action-btn edit-btn"
                            data-ar_id="<?= $s['ar_id'] ?>"
                            data-user_id="<?= $s['user_id'] ?: $_GET['scholarid'] ?>"
                            data-activityid="<?= $s['activity_id'] ?? '' ?>"
                            data-activityname="<?= $s['activityname'] ?? '' ?>"
                            data-scanned_date="<?= $s['activitydate'] ?? '' ?>"
                            data-timein="<?= $s['timein'] ?? '' ?>"
                            data-timeout="<?= $s['timeout'] ?? '' ?>"
                            data-scanned_by="<?= $s['scanned_by'] ?? '' ?>"
                            data-officer_fullname="<?= trim($s['officer_fullname'] ?? '') !== '' ? $s['officer_fullname'] : $adminName ?>">
                        <i class="fa-solid fa-pen-to-square" style="color: black;"></i>
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