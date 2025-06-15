<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "datacoming");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
$conn->set_charset("utf8");

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION['user_id'];
$product_id = intval($data['product_id']);
$quantity = intval($data['quantity']);
$price = floatval($data['price']); // السعر بعد الخصم المرسل من الجافاسكريبت

// تحقق إذا المنتج موجود بالسلة
$sql_check = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // تحديث الكمية والسعر (السعر يكون آخر سعر تم إضافته)
    $sql_update = "UPDATE cart_items SET quantity = quantity + ?, price = ? WHERE user_id = ? AND product_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("diii", $quantity, $price, $user_id, $product_id);
    $stmt_update->execute();
} else {
    // إضافة المنتج للسلة مع السعر
    $sql_insert = "INSERT INTO cart_items (user_id, product_id, quantity, price, added_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiid", $user_id, $product_id, $quantity, $price);
    $stmt_insert->execute();
}

echo json_encode(["success" => true]);
?>
