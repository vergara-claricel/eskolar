<?php
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: /esko/login.php");
    exit;
}

$adminId = $_SESSION['userid'];