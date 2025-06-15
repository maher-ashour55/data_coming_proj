<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';
    $order_status = isset($_POST['order_status']) ? $_POST['order_status'] : '';

    $valid_payment_statuses = ['Pending', 'Paid', 'Failed'];
    $valid_order_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Completed'];

    if ($order_id <= 0 || !in_array($payment_status, $valid_payment_statuses) || !in_array($order_status, $valid_order_statuses)) {
        die("Invalid input data.");
    }

    $conn = new mysqli("localhost", "root", "", "datacoming");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE orders SET payment_status = ?, status = ?, updated_at = NOW() WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    if (!$stmt->bind_param("ssi", $payment_status, $order_status, $order_id)) {
        die("Binding parameters failed: " . $stmt->error);
    }

    if ($stmt->execute()) {
        header("Location: view_orders.php?success=1");
        exit();
    } else {
        echo "Error updating order: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: view_orders.php");
    exit();
}
