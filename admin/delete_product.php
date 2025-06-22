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
$dbname = "u251541401_datacoming";
$username = "u251541401_maher_user";
$password = "Datacoming12345";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// تحقق إذا المنتج مرتبط بأي طلبات في order_items
$stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM order_items WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row['cnt'] > 0) {
    // المنتج مرتبط بطلبات، نعرض alert ونرجع
    echo "<script>
        alert('لا يمكن حذف المنتج لأنه مرتبط بطلبات سابقة.');
        window.location.href = 'manage_products.php';
    </script>";
    $conn->close();
    exit();
} else {
    // المنتج غير مرتبط بطلبات، يمكن حذفه
    $stmt = $conn->prepare("DELETE FROM product WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        // بعد الحذف نرجع مع رسالة alert
        echo "<script>
            alert('تم حذف المنتج بنجاح.');
            window.location.href = 'manage_products.php?msg=deleted';
        </script>";
        exit();
    } else {
        $stmt->close();
        $conn->close();
        echo "<script>
            alert('حدث خطأ أثناء حذف المنتج: " . addslashes($conn->error) . "');
            window.location.href = 'manage_products.php';
        </script>";
        exit();
    }
}
?>
