<?php
require_once '../classes/supabase.php';
$config = require __DIR__ . "esko/../api/supabase.php";
require_once "../test.php";

$api = new Supabase($config);

$token = $_GET['token'];

$result = $api->get(
    "scholars",
    "?verification_token=eq." . urlencode($token)
);

if (empty($result)) {
    die("Invalid verification token.");
}

$scholar = $result[0];

//

$actobj = new Activities($api);
$offObj = new Officer($api);


$adminId = $_SESSION['userid'];
$adminName = $offObj->getAdminName($adminId);
$scholarUserId = $scholar['user_id'];


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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Digital Scorecard</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }


        .scorecard-header {
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        }

        .header-inner {
            max-width: 1000px;
            min-height: 68px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }


        /* Brand */

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #0f172a;
            color: #ffffff;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .brand-info strong {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
        }

        .brand-info span {
            font-size: 0.68rem;
            color: #64748b;
        }

        .header-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
        }


        .page {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px 40px;
        }


        .page-title {
            margin-bottom: 20px;
        }

        .page-title h1 {
            margin: 0;
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
        }

        .page-title p {
            margin: 4px 0 0;
            font-size: 0.82rem;
            color: #64748b;
        }

        .card {
            margin-bottom: 18px;
            padding: 20px 22px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow:
                0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .card-title {
            margin: 0 0 17px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
        }


        #scholarInfo {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px 30px;
        }

        .infoField {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .infoField p {
            margin: 0;
        }

        .infoField p:first-child {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .infoField p:last-child {
            font-size: 0.86rem;
            font-weight: 600;
            color: #1e293b;
        }

        .status-passed,
        .status-failed {
            display: inline-flex;
            width: fit-content;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem !important;
            font-weight: 700 !important;
        }

        .status-passed {
            background: #dcfce7;
            color: #15803d !important;
        }

        .status-failed {
            background: #fee2e2;
            color: #b91c1c !important;
        }


        #activitiesInfo {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        #activitiesInfo .infoField {
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        #activitiesInfo .infoField p:first-child {
            margin-bottom: 5px;
        }

        #activitiesInfo .infoField p:last-child {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
        }

        .table th {
            padding: 11px 13px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 0.68rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table td {
            padding: 12px 13px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.78rem;
            color: #334155;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .table td:nth-child(1) {
            font-weight: 600;
            color: #1e293b;
        }

        .empty-row {
            text-align: center !important;
            padding: 25px !important;
            color: #94a3b8 !important;
        }


        .scorecard-footer {
            margin-top: 30px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
        }

        .scorecard-footer p {
            margin: 0 0 3px;
            font-size: 0.72rem;
        }

        .scorecard-footer strong {
            color: #64748b;
            font-weight: 700;
        }

        .scorecard-footer span {
            font-size: 0.65rem;
        }

        @media (max-width: 600px) {

            .header-inner {
                min-height: 60px;
                padding: 0 14px;
            }

            .header-label {
                display: none;
            }

            .brand-logo {
                width: 34px;
                height: 34px;
            }

            .page {
                padding: 22px 12px 30px;
            }

            .page-title h1 {
                font-size: 1.3rem;
            }

            .card {
                padding: 17px;
            }

            #scholarInfo {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            #activitiesInfo {
                grid-template-columns: 1fr;
            }

            .table-container {
                border-radius: 7px;
            }
        }
    </style>

</head>
<body>



<header class="scorecard-header">
    <div class="header-inner">
        <div class="brand">
            <div class="brand-logo">
                eSko
            </div>
            <div class="brand-info">
                <strong>eSkolar</strong>
                <span>Iskolar ng Bagac</span>
            </div>
        </div>
        <div class="header-label">
            Digital Scorecard
        </div>

    </div>

</header>




<main class="page">
    <div class="page-title">
        <h1>eScorecard</h1>
        <p>Public view of scholar's attendance record.</p>
    </div>

    <section class="card">
        <h2 class="card-title">Scholar Information</h2>
        <div id="scholarInfo">
            <div class="infoField">
                <p>Name</p>
                <p>
                    <?= htmlspecialchars(
                        $scholar_info['users']['first_name']
                        . ' '
                        . $scholar_info['users']['last_name']
                    ) ?>
                </p>
            </div>

            <div class="infoField">
                <p>Iskolar No.</p>
                <p><?= htmlspecialchars($scholar_info['username']) ?></p>
            </div>
            <div class="infoField">
                <p>Barangay</p>
                <p><?= htmlspecialchars($scholar_info['barangay']) ?></p>
            </div>
            <div class="infoField">
                <p>Semester</p>
                <p><?= htmlspecialchars($activeSem['semester_name']) ?></p>
            </div>

            <div class="infoField">
                <p>Remarks</p>
                <p class="status-<?= strtolower($passFail) ?>">
                    <?= htmlspecialchars($passFail) ?>
                </p>
            </div>
        </div>
    </section>

    <section class="card">
        <h2 class="card-title">Activity Summary</h2>

        <div id="activitiesInfo">
            <div class="infoField">
                <p>Activities Attended</p>
                <p><?= $scholarAttended . ' / ' . $totalEvents ?></p>
            </div>
            <div class="infoField">
                <p>Quota</p>
                <p><?= $quotaPercentage . '%' ?></p>

            </div>
        </div>
    </section>

    <section class="card">
        <h2 class="card-title">Activities Information</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Activity Name</th>
                        <th>Date </th>
                        <th>Classification</th>
                        <th>Time In / Out </th>
                        <th>Remarks</th>
                        <th>Verified By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($scholar_ar)): ?>
                        <?php foreach ($scholar_ar as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['activityname']) ?></td>
                                <td><?= htmlspecialchars($s['activitydate']) ?></td>
                                <td><?= htmlspecialchars($s['classification']) ?></td>
                                <td>
                                    <?= htmlspecialchars(
                                        $s['timein']
                                        . ' / '
                                        . $s['timeout']
                                    ) ?>
                                </td>
                                <td><?= htmlspecialchars($s['attendance_status']) ?></td>
                                <td>
                                    <?= !empty($s['officer_fullname'])
                                        ? htmlspecialchars($s['officer_fullname'])
                                        : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-row">
                                No activity records available.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>


    <footer class="scorecard-footer">
        <p>Powered by <strong>eSkolar</strong></p>

        <span>Integrated Scholar QR Attendance &amp; Digital Scorecard System
        </span>
    </footer>
</main>


</body>

</html>