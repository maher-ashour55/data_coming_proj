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

    $conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get current order status before updating
    $stmt_current = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt_current->bind_param("i", $order_id);
    $stmt_current->execute();
    $result = $stmt_current->get_result();
    $current_status = $result->fetch_assoc()['status'] ?? '';
    $stmt_current->close();

    // Update order
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ?, status = ?, updated_at = NOW() WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    if (!$stmt->bind_param("ssi", $payment_status, $order_status, $order_id)) {
        die("Binding parameters failed: " . $stmt->error);
    }

    if ($stmt->execute()) {

        // ✅ Restore stock if status changed to Cancelled (and it was not Cancelled before)
        if ($order_status === 'Cancelled' && $current_status !== 'Cancelled') {
            $stmt_items = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();

            while ($item = $result_items->fetch_assoc()) {
                $stmt_update_stock = $conn->prepare("UPDATE product SET stock = stock + ? WHERE id = ?");
                $stmt_update_stock->bind_param("ii", $item['quantity'], $item['product_id']);
                $stmt_update_stock->execute();
                $stmt_update_stock->close();
            }

            $stmt_items->close();
        }

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
