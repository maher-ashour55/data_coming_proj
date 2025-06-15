<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$product_id = intval($data['product_id']);
$quantity = intval($data['quantity']);
$user_id = $_SESSION['user_id'];

// تأكد من أن الكمية أكبر من صفر
if ($quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be positive']);
    exit;
}

// الاتصال بقاعدة البيانات
$conn = new mysqli('localhost', 'root', '', 'datacoming');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// تحديث الكمية
$stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE product_id = ? AND user_id = ?");
$stmt->bind_param("iii", $quantity, $product_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Query failed']);
}

$stmt->close();
$conn->close();
