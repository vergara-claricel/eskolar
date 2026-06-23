<?php
// include "../classes/supa_officers.php";

// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $officerId = $_GET['officerId'];

// $offobj = new Officer($api);

require_once "../test.php";



// $del = $offobj->deactivateOfficer($officerId);

// header("Location: admin_officers.php");
// exit;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $officerid = $_POST['id'];
    $del = $offobj->deactivateOfficer($officerid);

    header("Location: admin_officers.php?deactivate=success");
    exit;
}

include "../assets/layout.php";

?>