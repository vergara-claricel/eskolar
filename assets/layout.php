<?php
// Active menu highlighter
function active($keyword){
    $current = basename($_SERVER['PHP_SELF']);
    return (strpos($current, $keyword) !== false) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? "eSkolar" ?></title>
    <link rel="stylesheet" href="/esko/assets/css/layout.css">
    <link rel="stylesheet" href="/esko/assets/css/table.css">
    <link rel="stylesheet" href="/esko/assets/css/activityform.css">
    <link rel="stylesheet" href="/esko/assets/css/activity.css">
    <link rel="stylesheet" href="/esko/assets/css/semester.css">
    <!-- <link rel="stylesheet" href="/esko/assets/css/form.css"> -->
    
    
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="logo">eSkolar</div>

            <nav class="menu">
                <a href="admin_dashboard.php" class="<?= active('admin_dashboard') ?>">Dashboard</a>

                <a href="admin_activities_attendance.php"
                    class="<?= active('admin_activities') ?>">Activities & Attendance</a>

                <a href="admin_scholars.php"
                    class="<?= active('admin_scholars') ?>">Scholars</a>

                <a href="admin_sem.php"
                    class="<?= active('admin_sem') ?>">Semester</a>
            </nav>
        </aside>

        <!-- MAIN -->
        <div class="main">

            <!-- TOP BAR -->
            <header class="topbar">
                <span>Welcome, Admin</span>
                <div class="profile-icon">👤</div>
            </header>

            <!-- CONTENT WRAPPER -->
            <div class="main-content">