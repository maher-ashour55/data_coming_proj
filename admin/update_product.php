<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "datacoming");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'msg' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// استلام البيانات مع التحقق
$id = intval($_POST['id']);
$name = $_POST['product_name'] ?? '';
$price = floatval($_POST['price'] ?? 0);
$discount_price = floatval($_POST['discount_price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$category = $_POST['category'] ?? '';
$description = $_POST['description'] ?? '';

// تحقق من وجود بيانات أساسية
if (!$id || !$name) {
    echo json_encode(['success' => false, 'msg' => 'Missing required fields.']);
    exit;
}

$imageName = null;
$targetDir = "uploads/";

// التحقق من رفع صورة جديدة
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2 ميجابايت

    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    $fileSize = $_FILES['image']['size'];

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'msg' => 'Invalid image type. Allowed: JPG, PNG, GIF.']);
        exit;
    }
    if ($fileSize > $maxFileSize) {
        echo json_encode(['success' => false, 'msg' => 'Image size exceeds 2MB limit.']);
        exit;
    }

    $imageName = uniqid() . '_' . basename($_FILES["image"]["name"]);
    $imagePath = $targetDir . $imageName;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
        echo json_encode(['success' => false, 'msg' => "Failed to upload image."]);
        exit;
    }
}

// تجهيز استعلام التحديث مع prepared statement
if ($imageName !== null) {
    $stmt = $conn->prepare("UPDATE product SET name=?, price=?, discount_price=?, stock=?, category=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("sddisssi", $name, $price, $discount_price, $stock, $category, $description, $imageName, $id);
} else {
    $stmt = $conn->prepare("UPDATE product SET name=?, price=?, discount_price=?, stock=?, category=?, description=? WHERE id=?");
    $stmt->bind_param("sddissi", $name, $price, $discount_price, $stock, $category, $description, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'msg' => "Product updated successfully!"]);
} else {
    echo json_encode(['success' => false, 'msg' => "Error updating product: " . $stmt->error]);
}

$stmt->close();
$conn->close();
exit;
?>
