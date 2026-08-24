<?php
$title = "Dashboard";
require_once "../test.php";
$adminId = $_SESSION['userid'];
// include "../classes/supa_activities.php";


// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $actobj = new Activities($api);
$activeSem = $semobj->getActiveSemester();
$totalscholars = $schoobj->getActiveScholarCount();
$adminName = $offobj->getAdminName($adminId);
$totalevents = 0;
$upcomingEvents = [];

if (!empty($activeSem['sem_id'])) {
    $totalevents = $actobj->countActivitiesOfActiveSem($activeSem['sem_id']);
    $upcomingEvents = $actobj->upcomingActivities($activeSem['sem_id']);
}
// $totalevents = $actobj->getActivitiesOfActiveSem($activeSem['sem_id']);
// $upcomingEvents = $actobj->upcomingActivities($activeSem['sem_id']);
include "../assets/layout.php";
?>

<style>
    /* Dashboard Layout Container */
    .dashboard-container {
        font-family: "Inter", sans-serif;
        max-width: 1000px;
        margin: auto;
        padding: 0 20px;
    }

    /* Metric Cards Top Row */
    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .metric-card {
        background: #ffffff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
    }

    .metric-label {
        font-size: 11px;
        color: #6f6f6f;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .metric-value {
        font-size: 22px;
        font-weight: 700;
        color: #333333;
    }

    .metric-card:nth-child(1),
    .metric-card:nth-child(2) {
        border-left: 5px solid #4C7742;
    }


    /* Activities Section */
    .activities-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 18px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid #f0f0f0;
    }

    .activities-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        margin-right: 50px;
    }

    .activities-card h2 {
        margin: 0;
        font-size: 18px;
        color: #333;
        font-weight: 600;
    }

    .view-link {
        color: #4C7742;
        font-weight: 500;
        font-size: 13px;
        text-decoration: underline;
        margin: 0;
    }

    .view-link:hover {
        text-decoration: none;
        font-weight: bold;
    }


    /* Upcoming Activities Grid */
    .activities-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }


    /* Individual Activity Card */
    .activity-card {
        display: flex;
        flex-direction: column;
        min-height: 160px;
        padding: 20px;
        background: rgb(244, 248, 239);
        border: 1px solid #e5ecdf;
        border-radius: 16px;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        width: 210px;
        height: 155px;
        box-sizing: border-box;
    }

    .activity-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }


    /* Date */
    .activity-date {
        color: #4C7742;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }


    /* Activity Name */
    .activity-card h3 {
        margin: 0 0 8px 0;
        font-size: 16px;
        line-height: 1.3;
        color: #2D2D2D;
        font-weight: 700;
    }


    /* Activity Info */
    .activity-card p {
        margin: 0;
        color: #6f6f6f;
        font-size: 11px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }


    /* View Activity */
    .activity-card span {
        margin-top: auto;
        padding-top: 15px;
        color: #4C7742;
        font-size: 11px;
        font-weight: 600;
    }


    /* Empty State */
    .no-activities {
        padding: 25px;
        text-align: center;
        color: #777;
        background: #f8f8f8;
        border-radius: 12px;
        font-size: 13px;
    }

    /* ================================
   SYSTEM INFORMATION
================================ */

    .system-info-card {
        margin-top: 25px;
        margin-bottom: 30px;
        background: #ffffff;
        padding: 20px 30px;
        border-radius: 18px;
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
    }

    .system-info-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .system-info-header i {
        width: 34px;
        height: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #EEF5EC;
        color: #4C7742;

        border-radius: 9px;

        font-size: 15px;
    }

    .system-info-header h2 {
        margin: 0;

        font-size: 18px;
        font-weight: 600;

        color: #333;
    }

    .system-info-content {
        display: grid;
        grid-template-columns: 1fr 1fr;

        gap: 18px 40px;
    }

    .system-info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .system-info-label {
        font-size: 11px;

        color: #7A7A7A;

        text-transform: uppercase;
        letter-spacing: 0.5px;

        font-weight: 600;
    }

    .system-info-value {
        font-size: 14px;

        color: #2D2D2D;

        font-weight: 500;
    }

    .system-info-notice {
        grid-column: 1 / -1;

        display: flex;
        align-items: flex-start;
        gap: 10px;

        margin-top: 5px;
        padding: 12px 14px;

        background: #F5F8F4;

        border: 1px solid #E3EBDD;

        border-radius: 10px;

        color: #647060;

        font-size: 12px;

        line-height: 1.5;
    }

    .system-info-notice i {
        color: #6B8F61;

        margin-top: 2px;
    }


    /* Mobile */

    @media (max-width: 600px) {

        .system-info-card {
            padding: 20px;
        }

        .system-info-content {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .system-info-notice {
            grid-column: auto;
        }
    }


    /* Mobile */
    @media (max-width: 600px) {

        .activities-card {
            padding: 20px;
        }

        .activities-grid {
            grid-template-columns: 1fr;
        }

        .activity-card {
            min-height: 145px;
        }
    }

    .dashboard-title{
        font-size: 22px;
        font-weight: 600;
        color: #4C7742;
        margin-bottom: 10px;
    }
</style>

<div class="dashboard-container">
    <h1 class="dashboard-title">Dashboard</h1>
    <div class="metrics-row">

        <div class="metric-card">
            <div class="metric-label">Total Scholars</div>
            <div class="metric-value">
                <?= $totalscholars ?>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-label">Total Activities</div>
            <div class="metric-value">
                <?= $totalevents ?>
            </div>
        </div>

    </div>
    <div class="activities-card">

        <div class="activities-header">
            <h2>Upcoming Activities</h2>

            <!-- <a href="/esko/admin/admin_activities_attendance.php"
                class="view-link">
                View all activities →
            </a> -->
                <?php if (count($upcomingEvents) > 3): ?>

        <div class="see-more">
            <a href="/esko/admin/admin_activities_attendance.php"
            class="view-link">
                See more activities →
            </a>
        </div>

    <?php endif; ?>
        </div>

        <?php if (!empty($upcomingEvents)): ?>

            <div class="activities-grid">

                <?php foreach (array_slice($upcomingEvents, 0, 3) as $ue):
                    $date = strtotime($ue['activitydate']);
                ?>

                    <a
                        class="activity-card"
                        href="/esko/admin/admin_activities_view.php?actId=<?= $ue['activity_id'] ?>">

                        <div class="activity-date">
                            <?= date("M d, Y", $date) ?>
                        </div>

                        <h3>
                            <?= htmlspecialchars($ue['activityname']) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($ue['info']) ?>
                        </p>

                        <span>
                            View activity →
                        </span>

                    </a>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="no-activities">
                No upcoming activities available.
            </div>

        <?php endif; ?>

    </div>

    <div class="system-info-card">

        <div class="system-info-header">

            <i class="fa-solid fa-circle-info"></i>

            <h2>System Information</h2>

        </div>


        <div class="system-info-content">

            <div class="system-info-item">

                <span class="system-info-label">
                    Current Administrator
                </span>

                <span class="system-info-value">
                    <?= htmlspecialchars($adminName) ?>
                </span>

            </div>


            <div class="system-info-item">

                <span class="system-info-label">
                    Role
                </span>

                <span class="system-info-value">
                    President / Administrator
                </span>

            </div>


            <div class="system-info-notice">

                <i class="fa-solid fa-circle-exclamation"></i>

                <span>
                    For changes to the designated administrator or
                    organization president, please contact the
                    system administrator at <span style="text-decoration: underline;">eskolarhelp@gmail.com</span>.
                </span>
            </div>
        </div>
    </div>
</div>


<?php include "../assets/layout_end.php"; ?>