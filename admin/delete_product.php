<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$product_id = intval($_GET['id']);

$host = "localhost";
$username = "root";
$password = "";
$dbname = "datacoming";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DELETE FROM product WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: manage_products.php?msg=deleted");
    exit();
} else {
    $stmt->close();
    $conn->close();
    echo "Error deleting product: " . $conn->error;
}
?>
