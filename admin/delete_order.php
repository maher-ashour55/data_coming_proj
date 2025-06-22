<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // ✅ 1. Get order status first
    $stmt_status = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt_status->bind_param("i", $order_id);
    $stmt_status->execute();
    $result_status = $stmt_status->get_result();
    $order_data = $result_status->fetch_assoc();
    $stmt_status->close();

    $current_status = $order_data['status'] ?? '';

    // ✅ 2. Return stock ONLY if the order is NOT already cancelled
    if ($current_status !== 'Cancelled') {
        $stmt_items = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();

        while ($row = $result_items->fetch_assoc()) {
            $product_id = $row['product_id'];
            $quantity = $row['quantity'];

            $updateStmt = $conn->prepare("UPDATE product SET stock = stock + ? WHERE id = ?");
            $updateStmt->bind_param("ii", $quantity, $product_id);
            $updateStmt->execute();
            $updateStmt->close();
        }

        $stmt_items->close();
    }

    // ✅ 3. Delete order_items
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    // ✅ 4. Delete order
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    // ✅ 5. Redirect with success
    header("Location: view_orders.php?deleted=1");
    exit();

} else {
    header("Location: view_orders.php");
    exit();
}
