<?php
session_start();
header('Content-Type: application/json');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// قراءة البيانات من الطلب
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$product_id = intval($data['product_id']);
$user_id = $_SESSION['user_id'];

// الاتصال بقاعدة البيانات
$conn = new mysqli('localhost', 'root', '', 'datacoming');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// حذف العنصر من السلة
$stmt = $conn->prepare("DELETE FROM cart_items WHERE product_id = ? AND user_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}

$stmt->close();
$conn->close();
?>
