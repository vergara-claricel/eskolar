<?php
require_once '../classes/supabase.php';
$config = require __DIR__ . "esko/../api/supabase.php";

$api = new Supabase($config);

$token = $_GET['token'];

$result = $api->get(
    "scholars",
    "?verification_token=eq." . urlencode($token)
);

if (empty($result)) {
    die("Invalid verification token.");
}

$scholar = $result[0];
print_r($scholar);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>HELLO</h1>
</body>
</html>