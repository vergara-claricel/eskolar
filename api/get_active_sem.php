<?php
require_once "../connection.php";

header("Content-Type: application/json");

try {
    $stmt = $pdo->prepare("SELECT * FROM semester WHERE semester_status = 'active' LIMIT 1");
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "active_semester" => $data
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>