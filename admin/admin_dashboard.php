<?php
$title = "Dashboard";
require_once "../test.php";
// include "../classes/supa_activities.php";

// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $actobj = new Activities($api);
$activeSem = $semobj->getActiveSemester();
$totalscholars = $schoobj->activeScholarsCount();

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

.dashboard-title {
    font-size: 24px;
    color: #2D2D2D;
    margin-bottom: 25px;
    font-weight: 700;
}

/* Metric Cards Top Row */
.metrics-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.metric-card {
    background: #ffffff;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.08);
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
    box-shadow: 0 4px 25px rgba(0,0,0,0.06);
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
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
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
</style>

<div class="dashboard-container">
    <h1 class="dashboard-title">Dashboard</h1>

    <div class="metrics-row">

        <div class="metric-card">
            <div class="metric-label">Total Scholars</div>
            <div class="metric-value">
                <?= count($totalscholars) ?>
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

            <a href="/esko/admin/admin_activities_attendance.php"
            class="view-link">
                View all activities →
            </a>
        </div>

        <?php if (!empty($upcomingEvents)): ?>

            <div class="activities-grid">

                <?php foreach ($upcomingEvents as $ue):
                    $date = strtotime($ue['activitydate']);
                ?>

                    <a
                        class="activity-card"
                        href="/esko/admin/admin_activities_view.php?actId=<?= $ue['activity_id'] ?>"
                    >

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
</div>


<?php include "../assets/layout_end.php"; ?>