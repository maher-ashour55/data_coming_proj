<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}
$conn->set_charset("utf8");

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION['user_id'];
$product_id = intval($data['product_id']);
$quantity = intval($data['quantity']);
$price = floatval($data['price']);

// تحقق هل المنتج موجود مسبقًا
$sql_check = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // ✅ المنتج موجود مسبقاً
    echo json_encode(["success" => false, "message" => "Product already in cart"]);
    exit;
} else {
    // ✅ أضف المنتج
    $sql_insert = "INSERT INTO cart_items (user_id, product_id, quantity, price, added_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiid", $user_id, $product_id, $quantity, $price);
    $stmt_insert->execute();

    // ✅ احسب عدد العناصر بعد الإضافة
    $stmt_count = $conn->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();
    $cart_count = $row_count['total'] ?? 0;

    echo json_encode([
        "success" => true,
        "message" => "Product added to cart",
        "cart_count" => $cart_count
    ]);
    exit;
}
?>
