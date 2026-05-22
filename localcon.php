<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host     = "localhost";                       
$dbname   = "eskolar";                
$user     = "root";                
$password = "";        

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>