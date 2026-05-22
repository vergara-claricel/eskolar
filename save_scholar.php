<?php
include "../classes/activities.php";
include "../classes/scholar.php";

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
        echo $result ? "Scholar created successfully!" : "Failed to add scholar.";

   } catch(Exception $e) {
     //    $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        }
     header("location: /esko/admin/admin_scholars.php");
}
?>