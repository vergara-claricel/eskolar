<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host     = "db.yaksrweqnidfpvceoqzo.supabase.co";      
$port     = "5432";                  
$dbname   = "postgres";                
$user     = "postgres";                
$password = "Capstonedefendedcuti3!";        

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "sumakses k tol!";
} catch (PDOException $e) {
    die("yawaaa: " . $e->getMessage());
}
?>