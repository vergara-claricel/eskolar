<?php
session_start();

require_once "../classes/supa_semester.php";
require_once "../classes/supa_officers.php";
require_once "../classes/supa_activities.php";
require_once "../classes/supabase.php";

$config = require __DIR__ . "../api/supabase.php";

$api = new Supabase($config);

$semobj = new Semester($api);
$offobj = new Officer($api);
$actobj = new Activities($api);

$adminId = $_SESSION["userid"] ?? null;

/* SAFE: data only, no output */

$activeSem = $semobj->getActiveSemester();
$adminName = $offobj->getAdminName($adminId);

function active($keyword){
    $current = basename($_SERVER['PHP_SELF']);
    return (strpos($current, $keyword) !== false) ? 'active' : '';
}