<?php

require "../vendor/autoload.php";
require "../connection.php";

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// ---------------- INPUT ----------------
$iskolarno = $_POST['iskolarno'] ?? null;

if (!$iskolarno) {
    die("Missing iskolarno");
}

// ---------------- TOKEN ----------------
$qrToken = bin2hex(random_bytes(16));

$qrData = json_encode([
    "iskolarno" => $iskolarno,
    "token"     => $qrToken
]);

// ---------------- SAVE TO DB ----------------
$stmt = $pdo->prepare("
    UPDATE scholars 
    SET qr_token = :token 
    WHERE iskolarno = :id
");

$stmt->execute([
    ":token" => $qrToken,
    ":id"    => $iskolarno
]);

// ---------------- QR OPTIONS (FIXED) ----------------
$options = new QROptions([
    'version'    => 5,
    'scale'      => 6,
    'eccLevel'   => \chillerlan\QRCode\Common\EccLevel::L,
]);

// ---------------- GENERATE QR ----------------
$qrcode = new QRCode($options);

header('Content-Type: image/png');
echo $qrcode->render($qrData);