<?php
// include "../classes/supa_scholar.php";

// require_once "../classes/supabase.php";
// $config = require __DIR__ . "esko/../api/supabase.php";

// $api = new Supabase($config);
// $schoobj = new Scholar($api);
require_once "../test.php";

// $scholarid = $_GET['scholarid'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $scholarid = $_POST['scholarid'];
    $del = $schoobj->reactivateScholar($scholarid);

    header("Location: admin_scholars.php?reactivate=success");
    exit;
}


?>