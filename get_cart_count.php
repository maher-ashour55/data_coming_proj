<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "cart_count" => 0]);
    exit;
}

$conn = new mysqli("localhost", "user", "pass", "db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "cart_count" => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode([
    "success" => true,
    "cart_count" => $row['total'] ?? 0
]);
