<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// بدء التسجيل في ملف log
file_put_contents("log.txt", "========== New Request ==========\n", FILE_APPEND);
file_put_contents("log.txt", "[" . date("Y-m-d H:i:s") . "] Script started\n", FILE_APPEND);

header('Content-Type: application/json');
session_start();

// تحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    file_put_contents("log.txt", "User not logged in\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
file_put_contents("log.txt", "User ID from session: $user_id\n", FILE_APPEND);

// الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$database = "datacoming";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    file_put_contents("log.txt", "Database connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}
file_put_contents("log.txt", "Database connected\n", FILE_APPEND);

// قراءة البيانات المرسلة
$rawData = file_get_contents("php://input");
file_put_contents("log.txt", "Raw input: $rawData\n", FILE_APPEND);

$data = json_decode($rawData, true);
file_put_contents("log.txt", "Decoded data: " . print_r($data, true) . "\n", FILE_APPEND);

// التحقق من وجود جميع البيانات المطلوبة
$requiredFields = ['fname', 'lname', 'address', 'email', 'phone', 'city', 'payment', 'total'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        file_put_contents("log.txt", "Missing field: $field\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
        exit;
    }
}

// استخراج البيانات
$fname = $data['fname'];
$lname = $data['lname'];
$address = $data['address'];
$email = $data['email'];
$phone = $data['phone'];
$city = $data['city'];
$payment = $data['payment'];
$total = $data['total'];

$order_date = date("Y-m-d H:i:s");
$status = "Pending";

file_put_contents("log.txt", "Inserting order...\n", FILE_APPEND);

// إدخال الطلب
$stmt = $conn->prepare("INSERT INTO orders (user_id, total, payment_method, address, order_date, status, fname, lname, email, phone, city) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    file_put_contents("log.txt", "Order prepare failed: " . $conn->error . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed', 'error' => $conn->error]);
    exit;
}

$stmt->bind_param("idsssssssss", $user_id, $total, $payment, $address, $order_date, $status, $fname, $lname, $email, $phone, $city);
if (!$stmt->execute()) {
    file_put_contents("log.txt", "Order execution failed: " . $stmt->error . "\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Order insert failed', 'error' => $stmt->error]);
    exit;
}

$order_id = $stmt->insert_id;
file_put_contents("log.txt", "Order inserted successfully with ID: $order_id\n", FILE_APPEND);
$stmt->close();

// جلب محتويات السلة
file_put_contents("log.txt", "Fetching cart items...\n", FILE_APPEND);
$cart_query = $conn->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();

// إدخال عناصر الطلب
file_put_contents("log.txt", "Inserting order items...\n", FILE_APPEND);
while ($row = $cart_result->fetch_assoc()) {
    $product_id = $row['product_id'];
    $quantity = $row['quantity'];

    file_put_contents("log.txt", "Adding product_id: $product_id, quantity: $quantity\n", FILE_APPEND);

    $insert_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
    if (!$insert_item) {
        file_put_contents("log.txt", "Item prepare failed for product $product_id: " . $conn->error . "\n", FILE_APPEND);
        continue;
    }
    $insert_item->bind_param("iii", $order_id, $product_id, $quantity);
    if (!$insert_item->execute()) {
        file_put_contents("log.txt", "Failed to insert item for product $product_id: " . $insert_item->error . "\n", FILE_APPEND);
    } else {
        file_put_contents("log.txt", "Item inserted for product $product_id successfully\n", FILE_APPEND);
    }
    $insert_item->close();

    // تحديث المخزون
    $conn->query("UPDATE product SET stock = stock - $quantity WHERE id = $product_id");
}
$cart_query->close();

// حذف محتويات السلة
file_put_contents("log.txt", "Clearing cart items...\n", FILE_APPEND);
$delete_cart = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
$delete_cart->bind_param("i", $user_id);
$delete_cart->execute();
$delete_cart->close();

file_put_contents("log.txt", "Order process finished successfully\n", FILE_APPEND);

// التحقق من نجاح الإدخال قبل إرسال النجاح
if (!isset($order_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to insert order.']);
    exit;
}

// الرد النهائي عند النجاح
echo json_encode(['status' => 'success']);
exit;
?>
