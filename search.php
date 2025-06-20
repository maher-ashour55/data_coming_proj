<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}
$conn->set_charset("utf8");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['query'])) {
    echo json_encode(['success' => false, 'message' => 'No query provided']);
    exit;
}

$searchTerm = "%" . $data['query'] . "%";

$stmt = $conn->prepare("SELECT id, name, price, discount_price, image, stock FROM product WHERE name LIKE ?");
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode([
    'success' => true,
    'products' => $products
]);
?>
