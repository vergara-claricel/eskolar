<?php
$title = "Dashboard";
require_once "../test.php";
// include "../classes/supa_activities.php";

// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $actobj = new Activities($api);
$activeSem = $semobj->getActiveSemester();
$totalscholars = $schoobj->getAllActiveScholars();
$totalevents = $actobj->getActivitiesOfActiveSem($activeSem['sem_id']);
$upcomingEvents = $actobj->upcomingActivities($activeSem['sem_id']);
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

/* Specific accent borders for the metrics to match your vibe */
.metric-card:nth-child(1) { border-left: 5px solid #0909ae; }
.metric-card:nth-child(2) { border-left: 5px solid #0909ae; }
.metric-card:nth-child(3) { border-left: 5px solid #0909ae }

/* Activities Section */
.activities-card {
    background: #ffffff;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}

.activities-card h2 {
    font-size: 18px;
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
    font-weight: 600;
}

.activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #FBF0D2;
    padding: 20px 25px;
    border-radius: 20px;
    border: 2px solid #F2E2B8;
    margin-bottom: 18px;
    text-decoration: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.activity-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.activity-info {
    width: 80%;
    overflow: hidden;
}

.activity-info h3 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.activity-info p {
    margin-top: 6px;
    margin-bottom: 0;
    color: #6f6f6f;
    font-size: 11px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.activity-date {
    background: #E4E0FF;
    padding: 12px 18px;
    border-radius: 18px;
    text-align: center;
    font-size: 16px;
    min-width: 80px;
}

.activity-date span {
    display: block;
    color: #2D2D2D;
    font-weight: 500;
}

.activity-date strong {
    color: #2D2D2D;
    font-size: 16px;
}

.view-link {
    display: inline-block;
    margin-top: 10px;
    color: #1a0dab;
    font-weight: 500;
    text-decoration: none;
}

.view-link:hover {
    text-decoration: underline;
}
</style>

<div class="dashboard-container">
    <h1 class="dashboard-title">Dashboard</h1>

    <div class="metrics-row">
        <div class="metric-card">
            <div class="metric-label">Total Scholars</div>
            <div class="metric-value"><?= count($totalscholars) ?></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Total Activities</div>
            <div class="metric-value"><?= count($totalevents) ?></div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Overall Attendance</div>
            <div class="metric-value">84%</div>
        </div>
    </div>

    <div class="activities-card">
        <h2>Upcoming Activities</h2>
        
        <?php foreach ($upcomingEvents as $ue):
            $date = strtotime($ue['activitydate']); ?>
            
            <a class="activity-item" href="/esko/admin/admin_activities_view.php?actId=<?= $ue['activity_id'] ?>">
        
                <div class="activity-date">
                    <span><?= date("M d", $date) ?></span>
                    <strong><?= date("Y", $date) ?></strong>
                </div>
                <div class="activity-info">
                    <h3><?= htmlspecialchars($ue['activityname']) ?></h3>
                    <p><?= htmlspecialchars($ue['info']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
        
        <a href="/esko/admin/admin_activities_attendance.php" class="view-link">View all activities →</a>
    </div>
</div>

<?php include "../assets/layout_end.php"; ?>