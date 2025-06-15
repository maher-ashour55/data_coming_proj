<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

$conn = new mysqli("localhost", "root", "", "datacoming");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// إلغاء الطلب إذا تم إرسال POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_id = intval($_POST['cancel_order_id']);

    // تأكد أن الطلب مازال قابل للإلغاء (Pending أو Processing فقط)
    $sql_check_status = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
    $stmt_check = $conn->prepare($sql_check_status);
    $stmt_check->bind_param("ii", $cancel_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        if ($row['status'] === 'Pending' || $row['status'] === 'Processing') {
            $sql_cancel = "UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ?";
            $stmt_cancel = $conn->prepare($sql_cancel);
            if ($stmt_cancel) {
                $stmt_cancel->bind_param("ii", $cancel_id, $user_id);
                $stmt_cancel->execute();
                $stmt_cancel->close();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Error preparing cancel query: (" . $conn->errno . ") " . $conn->error;
            }
        } else {
            echo "<p style='color:red; text-align:center;'>This order cannot be cancelled.</p>";
        }
    }
    $stmt_check->close();
}

// جلب الطلبات الخاصة بالمستخدم
$sql_orders = "SELECT id, order_date, total, status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt_orders = $conn->prepare($sql_orders);

if (!$stmt_orders) {
    die("Prepare failed for orders: (" . $conn->errno . ") " . $conn->error);
}

$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="./styles/dashboard.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet' />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        .dashboard {
            max-width: 900px;
            margin: 40px auto;
            background-color: white;
            padding: 30px 40px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            text-align: center;
        }
        h1 {
            margin-bottom: 15px;
            color: #333;
        }
        .btn {
            display: inline-block;
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            margin: 10px 5px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #34495e;
        }
        .logout-btn {
            background-color: #e74c3c;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        p {
            font-size: 18px;
            color: #555;
        }
        table {
            margin: 30px auto 0;
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        th, td {
            padding: 10px 15px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #9265A6;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f3f3f3;
        }
        .status-completed {
            color: green;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .status-processing {
            color: #2980b9; /* أزرق */
            font-weight: bold;
        }
        .status-shipped {
            color: #8e44ad; /* بنفسجي */
            font-weight: bold;
        }
        .status-delivered {
            color: #27ae60; /* أخضر */
            font-weight: bold;
        }
        .status-cancelled {
            color: red;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .dashboard {
                padding: 20px;
                margin: 20px 10px;
            }

            .btn {
                font-size: 14px;
                padding: 8px 12px;
            }

            table {
                display: block;
                overflow-x: auto;
                width: 100%;
            }

            thead {
                display: none;
            }

            tr {
                display: block;
                margin-bottom: 15px;
                background: #fff;
                border: 1px solid #ccc;
                padding: 10px;
                text-align: left;
            }

            td {
                display: flex;
                justify-content: space-between;
                padding: 8px 10px;
                border: none;
                border-bottom: 1px solid #eee;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #555;
            }
        }
    </style>
</head>
<body>

<div class="dashboard">
    <h1>Hello, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>!</h1>

    <p>This is your dashboard where you can manage your account and track your activity.</p>

    <?php if ($role === 'admin'): ?>
        <a href="../admin/admin_dashboard.php" class="btn">Admin Control Panel</a>
    <?php endif; ?>
    <a href="index.php" class="btn">Browse</a>
    <a href="logout.php" class="btn logout-btn">Logout</a>

    <h2>My Orders</h2>
    <?php if ($result_orders->num_rows > 0): ?>
        <table>
            <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Total (₪)</th>
                <th>Order Status</th>
                <th>Products</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($order = $result_orders->fetch_assoc()): ?>
                <tr>
                    <td data-label="Order ID"><?php echo htmlspecialchars($order['id']); ?></td>
                    <td data-label="Order Date"><?php echo htmlspecialchars($order['order_date']); ?></td>
                    <td data-label="Total (₪)"><?php echo htmlspecialchars($order['total']); ?></td>
                    <td data-label="Order Status">
                        <?php
                        $status = htmlspecialchars($order['status']);
                        switch ($status) {
                            case 'Completed':
                                echo "<span class='status-completed'>Completed</span>";
                                break;
                            case 'Pending':
                                echo "<span class='status-pending'>Pending</span>";
                                break;
                            case 'Processing':
                                echo "<span class='status-processing'>Processing</span>";
                                break;
                            case 'Shipped':
                                echo "<span class='status-shipped'>Shipped</span>";
                                break;
                            case 'Delivered':
                                echo "<span class='status-delivered'>Delivered</span>";
                                break;
                            case 'Cancelled':
                                echo "<span class='status-cancelled'>Cancelled</span>";
                                break;
                            default:
                                echo $status;
                        }
                        ?>
                    </td>
                    <td data-label="Products">
                        <?php
                        $sql_products = "
                            SELECT p.name, oi.quantity, p.price
                            FROM order_items oi
                            JOIN product p ON oi.product_id = p.id
                            WHERE oi.order_id = ?
                        ";
                        $stmt_products = $conn->prepare($sql_products);

                        if (!$stmt_products) {
                            echo "Error preparing product query: (" . $conn->errno . ") " . $conn->error;
                            continue;
                        }

                        $stmt_products->bind_param("i", $order['id']);
                        $stmt_products->execute();
                        $result_products = $stmt_products->get_result();

                        if ($result_products->num_rows > 0) {
                            echo "<ul style='list-style: none; padding: 0; margin: 0; text-align: left;'>";
                            while ($product = $result_products->fetch_assoc()) {
                                echo "<li>" . htmlspecialchars($product['name']) . " (Qty: " . intval($product['quantity']) . ") - ₪" . htmlspecialchars($product['price']) . "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "No products found";
                        }
                        ?>
                    </td>
                    <td data-label="Action">
                        <?php
                        // السماح بالإلغاء فقط إذا كانت الحالة Pending أو Processing
                        if ($order['status'] === 'Pending' || $order['status'] === 'Processing') {
                            echo '
                                <form method="post" action="">
                                    <input type="hidden" name="cancel_order_id" value="' . $order['id'] . '">
                                    <button type="submit" class="btn" style="background-color:#e67e22;" onclick="return confirm(\'Are you sure you want to cancel this order?\');">Cancel</button>
                                </form>
                            ';
                        } elseif ($order['status'] === 'Cancelled') {
                            echo '<span style="color:gray;">N/A</span>';
                        } else {
                            echo '<span style="color:gray;">Not Allowed</span>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders found yet.</p>
    <?php endif; ?>
</div>
<!--<div style="margin-top: 50px; text-align:left;">-->
<!--    <h2>Chat with Admin</h2>-->
<!--    <div id="chat-box" style="height: 250px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px; background: #f9f9f9;"></div>-->
<!---->
<!--    <form id="chat-form">-->
<!--        <input type="hidden" name="receiver_id" value="1">  نفترض أن الادمن هو user_id = 1 -->
<!--        <textarea id="message" name="message" required style="width: 100%; height: 60px;" placeholder="Type your message here..."></textarea>-->
<!--        <button type="submit" class="btn" style="margin-top:10px;">Send</button>-->
<!--    </form>-->
<!---->
<!--</div>-->

<script>
    // تحديث الرسائل كل 3 ثواني
    setInterval(fetchMessages, 3000);

    function fetchMessages() {
        fetch('get_messages.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('chat-box').innerHTML = data;
                document.getElementById('chat-box').scrollTop = document.getElementById('chat-box').scrollHeight;
            });
    }

    fetchMessages(); // أول تحميل
</script>
<script>
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault(); // ما يرسل النموذج بشكل عادي

        const messageInput = document.getElementById('message');
        const message = messageInput.value.trim();

        if (message === "") return;

        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'message=' + encodeURIComponent(message)
        })
            .then(res => res.text())
            .then(data => {
                messageInput.value = ""; // نظف الحقل
                getMessages(); // رجع أحدث رسائل
            })
            .catch(err => console.error('Send Error:', err));
    });
</script>


</body>
</html>
