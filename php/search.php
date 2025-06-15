<?php
header('Content-Type: application/json');

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "datacoming");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset("utf8");

// قراءة الكلمة المفتاحية من جافاسكربت
$data = json_decode(file_get_contents("php://input"), true);

// التحقق من وجود الكلمة المفتاحية
if (!isset($data['query'])) {
    echo json_encode(['success' => false, 'message' => 'No query provided']);
    exit;
}

$searchTerm = "%" . $data['query'] . "%";

// استعلام يشمل السعر قبل وبعد الخصم
$stmt = $conn->prepare("SELECT id, name, price, discount_price, image, stock FROM product WHERE name LIKE ?");
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// إرسال النتائج على شكل JSON
echo json_encode([
    'success' => true,
    'products' => $products
]);
?>
