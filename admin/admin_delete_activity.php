<?php
require_once "../test.php";

if (isset($_GET['actId'])) {
    $actobj->deleteActivity($_GET['actId']);
}

header("Location: /esko/admin/admin_activities_attendance.php?success=deleted");
exit;
?>