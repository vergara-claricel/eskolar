<?php
include "../classes/supa_activities.php";
include "../classes/supa_scholar.php";
require_once "../classes/supabase.php";
$config = require __DIR__ . "/esko/../api/supabase.php";

$api = new Supabase($config);
$schoobj = new Scholar($api);
$activeSem = $semobj->getActiveSemester();
$semId = $activeSem['sem_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $barangay = $_POST['barangay'];
    $phone = $_POST['phonenumber'];
    $email = $_POST['email'];
    $iskolarno = $_POST['iskolarno'];
    $password = $_POST['password'];

   try { 

        $result = $schoobj->addScholar($first_name, $last_name, $barangay, $phone, $email, $iskolarno, $password);

   } catch(Exception $e) {
     //    $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        }
     header("location: /esko/admin/admin_scholars_view.php?semid=$semId&scholarid=$result");
}
?>