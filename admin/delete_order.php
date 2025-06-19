<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $conn = new mysqli("localhost", "root", "", "datacoming");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 1. Get all items in the order
    $stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // 2. Return stock for each item
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        $updateStmt = $conn->prepare("UPDATE product SET stock = stock + ? WHERE id = ?");
        $updateStmt->bind_param("ii", $quantity, $product_id);
        $updateStmt->execute();
        $updateStmt->close();
    }
    $stmt->close();

    // 3. Delete from order_items
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // 4. Delete from orders
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    // 5. Redirect with success
    header("Location: view_orders.php?deleted=1");
    exit();

} else {
    header("Location: view_orders.php");
    exit();
}
