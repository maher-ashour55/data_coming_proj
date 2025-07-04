<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once "../php/fpdf186/fpdf.php";

$host = "localhost";
$dbname = "u251541401_datacoming";
$username = "u251541401_maher_user";
$password = "Datacoming12345";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['order_id'])) {
    die("No order ID specified.");
}

$order_id = intval($_GET['order_id']);

$order_sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

$item_sql = "SELECT oi.quantity, p.name, p.price, p.discount_price FROM order_items oi JOIN product p ON oi.product_id = p.id WHERE oi.order_id = ?";
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

class PDF extends FPDF {
    function Header() {
        // الصورة على اليسار
        $this->Image('../img/data2-removebg-preview.png', 10, 6, 30);

        // عنوان الفاتورة في المنتصف
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Data Coming Invoice', 0, 1, 'C');

        // السطر الفارغ
        $this->Ln(5);

        // التاريخ واسم المتجر على اليمين
        $this->SetFont('Arial', '', 12);
        $this->SetXY(130, 15);
        $this->Cell(0, 6, 'Date: ' . date("Y-m-d"), 0, 1, 'R');
        $this->SetXY(130, 21);
        $this->Cell(0, 6, 'Store: Data Coming', 0, 1, 'R');

        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// دالة لاقتطاع النصوص الطويلة
function truncateText($text, $maxLength = 200) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength - 3) . '...';
    }
    return $text;
}

// دالة لحساب عدد الأسطر التي سيشغلها النص في عمود بعرض معين
function getNumLines($pdf, $text, $width) {
    $cw = $pdf->GetStringWidth($text);
    if ($cw == 0) return 1; // تجنب القسمة على صفر
    $avgCharWidth = $cw / strlen($text);
    $maxCharsPerLine = floor($width / $avgCharWidth);
    return ceil(strlen($text) / $maxCharsPerLine);
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// معلومات الطلب
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Order Information:');
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(80, 8, 'Order ID: ' . $order['id']);
$pdf->Ln();
$pdf->Cell(80, 8, 'Customer: ' . $order['fname'] . ' ' . $order['lname']);
$pdf->Ln();
$pdf->Cell(80, 8, 'Email: ' . $order['email']);
$pdf->Ln();
$pdf->Cell(80, 8, 'Phone: ' . $order['phone']);
$pdf->Ln();
$pdf->Cell(80, 8, 'City: ' . $order['city']);
$pdf->Ln();
$pdf->Cell(80, 8, 'Address: ' . $order['address']);
$pdf->Ln(10);

// جدول المنتجات
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 250);
$pdf->Cell(80, 10, 'Product', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Price', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Total', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 12);
$totalPrice = 0;
foreach ($items as $item) {
    // نستخدم الخصم إذا موجود
    $unitPrice = (!empty($item['discount_price']) && $item['discount_price'] > 0) ? $item['discount_price'] : $item['price'];

    $lineTotal = $item['quantity'] * $unitPrice;
    $totalPrice += $lineTotal;

    $productName = truncateText($item['name'], 200);


    $numLines = getNumLines($pdf, $productName, 80); // عرض عمود المنتج 80
    $lineHeight = 6; // ارتفاع السطر
    $rowHeight = $lineHeight * $numLines;

    // نرسم الخانة الأولى بـ MultiCell (لتدعم تعدد الأسطر)
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(80, $lineHeight, $productName, 1);

    // نرسم بقية الخانات بنفس ارتفاع الصف (نرسمها كنوافذ ثم نكتب النص)
    $pdf->SetXY($x + 80, $y);
    $pdf->Cell(30, $rowHeight, $item['quantity'], 1, 0, 'C');

    $pdf->Cell(40, $rowHeight, '' . number_format($unitPrice, 2), 1, 0, 'R');
    $pdf->Cell(40, $rowHeight, '' . number_format($lineTotal, 2), 1, 1, 'R');
}

$pdf->Ln(5);

// المجموع الفرعي للمنتجات
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(150, 10, 'Subtotal (Products):', 1, 0, 'R');
$pdf->Cell(40, 10, number_format($totalPrice, 2), 1, 1, 'R');

// رسوم التوصيل
$deliveryFee = $order['total'] - $totalPrice;
$pdf->Cell(150, 10, 'Delivery Fee:', 1, 0, 'R');
$pdf->Cell(40, 10, number_format($deliveryFee, 2), 1, 1, 'R');

// المجموع الكلي
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(150, 10, 'Grand Total:', 1, 0, 'R', true);
$pdf->Cell(40, 10, number_format($order['total'], 2), 1, 1, 'R', true);


// إخراج الملف
$pdf->Output('I', 'invoice_order_' . $order['id'] . '.pdf');
?>
