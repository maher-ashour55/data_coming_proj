<?php
session_start();

// تأكد أن المستخدم مسجل دخوله وصلاحيته admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    // لو ما في id يوجه لصفحة إدارة المنتجات
    header("Location: manage_products.php");
    exit();
}

$product_id = intval($_GET['id']); // تحويل الـ id لرقم صحيح للسلامة

$host = "localhost";
$username = "root";
$password = "";
$dbname = "datacoming";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// حذف المنتج من قاعدة البيانات
$sql = "DELETE FROM product WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
if ($stmt->execute()) {
    // إذا تم الحذف بنجاح
    $stmt->close();
    $conn->close();
    header("Location: manage_products.php?msg=deleted");
    exit();
} else {
    // في حالة حدوث خطأ في الحذف
    $stmt->close();
    $conn->close();
    echo "Error deleting product: " . $conn->error;
}
?>
