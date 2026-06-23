<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "../classes/supa_semester.php";
require_once "../classes/supa_officers.php";
require_once "../classes/supa_activities.php";
require_once "../classes/supabase.php";
require_once "../classes/supa_scholar.php";
require_once "../classes/supa_scorecard.php";

$config = require __DIR__ . "../api/supabase.php";

$api = new Supabase($config);
$semobj = new Semester($api);
$offobj = new Officer($api);
$actobj = new Activities($api);
$scObj = new Scorecard($api);
$schoobj = new Scholar($api);

$adminId = $_SESSION["userid"] ?? null;

/* SAFE: data only, no output */

$activeSem = $semobj->getActiveSemester();
$adminName = $offobj->getAdminName($adminId);

function active($keyword){
    $current = basename($_SERVER['PHP_SELF']);
    return (strpos($current, $keyword) !== false) ? 'active' : '';
}