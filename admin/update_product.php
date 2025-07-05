<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'msg' => 'Database connection failed']);
    exit;
}

$id = intval($_POST['id']);
$name = $_POST['product_name'];
$price = floatval($_POST['price']);
$discount_price = isset($_POST['enable_discount']) ? floatval($_POST['discount_price']) : 0;
$stock = intval($_POST['stock']);
$category = $_POST['category'];
$description = $_POST['description'];
$is_featured_offer = isset($_POST['is_featured_offer']) ? 1 : 0;
$condition = isset($_POST['condition']) ? $_POST['condition'] : null;


$imageName = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['image']['tmp_name'];
    $imageName = basename($_FILES['image']['name']);
    $uploadDir = 'uploads/';
    $uploadPath = $uploadDir . $imageName;

    if (!move_uploaded_file($tmpName, $uploadPath)) {
        echo json_encode(['success' => false, 'msg' => 'Image upload failed']);
        exit;
    }
}

// 🔍 اطبع كل القيم المستلمة للتأكد
$debug = [
    'id' => $id,
    'name' => $name,
    'price' => $price,
    'discount_price' => $discount_price,
    'stock' => $stock,
    'category' => $category,
    'description' => $description,
    'image' => $imageName ?? 'NO IMAGE',
    'is_featured_offer' => $is_featured_offer,
    'condition' => $condition
];

file_put_contents("debug_update.txt", print_r($debug, true));

if ($imageName !== null) {
    $stmt = $conn->prepare("UPDATE product SET name=?, price=?, discount_price=?, stock=?, category=?, description=?, image=?, is_featured_offer=?, `condition`=? WHERE id=?");
    $stmt->bind_param("sddisssisi", $name, $price, $discount_price, $stock, $category, $description, $imageName, $is_featured_offer, $condition, $id);

} else {
    $stmt = $conn->prepare("UPDATE product SET name=?, price=?, discount_price=?, stock=?, category=?, description=?, is_featured_offer=?, `condition`=? WHERE id=?");
    $stmt->bind_param("sddissisi", $name, $price, $discount_price, $stock, $category, $description, $is_featured_offer, $condition, $id);

}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'msg' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'msg' => 'Update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
