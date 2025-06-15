<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once "../php/fpdf186/fpdf.php";

$host = "localhost";
$username = "root";
$password = "";
$dbname = "datacoming";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['order_id'])) {
    die("No order ID specified.");
}

$order_id = intval($_GET['order_id']);

// جلب بيانات الطلب
$order_sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

// جلب عناصر الطلب
$item_sql = "SELECT oi.quantity, p.name, p.price FROM order_items oi JOIN product p ON oi.product_id = p.id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($item_sql);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

$stmt->close();
$stmt_items->close();
$conn->close();

// إنشاء PDF باستخدام FPDF
class PDF extends FPDF {
    // رأس الصفحة
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Invoice', 0, 1, 'C');
        $this->Ln(5);
    }

    // تذييل الصفحة
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// بيانات العميل
$pdf->Cell(40, 10, 'Order ID: ' . $order['id']);
$pdf->Ln();
$pdf->Cell(40, 10, 'Customer Name: ' . $order['fname'] . ' ' . $order['lname']);
$pdf->Ln();
$pdf->Cell(40, 10, 'Email: ' . $order['email']);
$pdf->Ln();
$pdf->Cell(40, 10, 'Phone: ' . $order['phone']);
$pdf->Ln();
$pdf->Cell(40, 10, 'City: ' . $order['city']);
$pdf->Ln();
$pdf->Cell(40, 10, 'Address: ' . $order['address']);
$pdf->Ln(15);

// جدول العناصر
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Product');
$pdf->Cell(30, 10, 'Quantity', 0, 0, 'C');
$pdf->Cell(40, 10, 'Price', 0, 0, 'R');
$pdf->Cell(40, 10, 'Total', 0, 1, 'R');

$pdf->SetFont('Arial', '', 12);
$totalPrice = 0;
foreach ($items as $item) {
    $lineTotal = $item['quantity'] * $item['price'];
    $totalPrice += $lineTotal;

    $pdf->Cell(80, 10, $item['name']);
    $pdf->Cell(30, 10, $item['quantity'], 0, 0, 'C');
    $pdf->Cell(40, 10, '$' . number_format($item['price'], 2), 0, 0, 'R');
    $pdf->Cell(40, 10, '$' . number_format($lineTotal, 2), 0, 1, 'R');
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(150, 10, 'Grand Total:', 0, 0, 'R');
$pdf->Cell(40, 10, '$' . number_format($totalPrice, 2), 0, 1, 'R');

$pdf->Output('I', 'invoice_order_' . $order['id'] . '.pdf');
?>
