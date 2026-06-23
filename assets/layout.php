<?php
// session_start();
// include "../classes/supa_semester.php";
// require_once "../classes/supa_officers.php";

// // Active menu highlighter
// $activeSem = $semobj->getActiveSemester();

// $adminId = $_SESSION["userid"];
// $offobj = new Officer($api);
// $adminName = $offobj->getAdminName($adminId);
// function active($keyword){
//     $current = basename($_SERVER['PHP_SELF']);
//     return (strpos($current, $keyword) !== false) ? 'active' : '';
// }
$activeSem = $activeSem ?? null;
$adminName = $adminName ?? 'Admin';
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
    
    <style>
    /* Profile Dropdown Styles */
    .profile-dropdown {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .profile-trigger {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: #f8f9fa;
        border-radius: 20px;
        font-weight: 500;
        color: #333;
        transition: background 0.2s ease;
        user-select: none;
    }

    .profile-trigger:hover {
        background: #f0f1f2;
    }

    .profile-trigger .arrow-icon {
        font-size: 11px;
        transition: transform 0.2s ease;
        color: #6f6f6f;
    }

    /* Dropdown Menu Box */
    .dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        background: #ffffff;
        min-width: 150px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 10px;
        border: 1px solid #eef0f2;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
        z-index: 1000;
    }

    /* Show dropdown on hover or via toggle */
    .profile-dropdown:hover .dropdown-menu,
    .profile-dropdown.open .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .profile-dropdown:hover .arrow-icon {
        transform: rotate(180deg);
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        color: #e63946; /* Destructive red for logout */
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: background 0.2s ease;
    }

    .dropdown-item:hover {
        background: #fff5f5;
    }
    </style>
</head>

<body>

    <div class="layout">

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

                <a href="admin_officers.php"
                    class="<?= active('admin_officers') ?>">Officers</a>
            </nav>
        </aside>

        <div class="main">

            <header class="topbar">
                <span id="currentsem">Current Sem: <?php
                    if ($activeSem !== null) {
                        echo htmlspecialchars($activeSem['semester_name']);
                    } else {
                        echo "No active semester found.";
                    }
                ?></span>
                
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="profile-trigger">
                        <span>👤 <?= htmlspecialchars($adminName) ?: "Admin" ?></span>
                        <span class="arrow-icon">▼</span>
                    </div>
                    <div class="dropdown-menu">
                        <a href="../logout.php" class="dropdown-item">
                            🚪 Logout
                        </a>
                    </div>
                </div>
            </header>

            <div class="main-content">

            <script>
                document.getElementById('profileDropdown').addEventListener('click', function(e) {
                    this.classList.toggle('open');
                    e.stopPropagation();
                });
                
                document.addEventListener('click', function() {
                    document.getElementById('profileDropdown').classList.remove('open');
                });
            </script>