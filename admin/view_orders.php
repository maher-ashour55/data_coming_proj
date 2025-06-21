<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$host = "localhost";
$dbname = "u251541401_datacoming";
$username = "u251541401_maher_user";
$password = "Datacoming12345";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);


function getOrderItems($conn, $order_id) {
    $stmt = $conn->prepare("SELECT oi.quantity, p.name AS product_name, p.price, p.id AS product_id
                            FROM order_items oi
                            JOIN product p ON oi.product_id = p.id
                            WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
    return $items;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Orders - Admin Panel</title>

    <style>
        :root {
            --main-color: #9265a6;
            --accent-color: #ffb74d;
            --bg-light: #f9f9f9;
            --text-color: #333;
            --border-radius: 12px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            color: var(--text-color);
        }
        .sidebar {
            width: 250px;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            background: linear-gradient(180deg, #9265A6, #6d4c82);
            padding: 30px 20px;
            flex-shrink: 0;
            margin-top: auto;
        }
        .sidebar h2 {
            letter-spacing: 1.3px;
            text-align: center;
            user-select: none;
            margin-bottom: 40px;
            font-size: 26px;
            font-weight: bold;
            color: white;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 8px;
            transition: background 0.3s ease;
            font-size: 16px;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .sidebar a i {
            margin-right: 14px;
            font-size: 22px;
        }
        .logout{
            margin-top: auto;
        }
        .logout a {
            background-color: #e74c3c;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        .logout i {
            margin-right: 8px;
        }
        .logout a:hover {
            background-color: #c0392b;
        }
        .main-content {
            margin-left: 250px;
            padding: 40px 60px;
            flex-grow: 1;
            max-width: calc(100% - 280px);
        }
        .main-content h1 {
            color: var(--main-color);
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 30px;
            user-select: none;
        }
        .order {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 25px 30px;
            margin-bottom: 28px;
            transition: box-shadow 0.3s ease;
        }
        .order:hover {
            box-shadow: 0 8px 18px rgba(146, 101, 166, 0.3);
        }
        .order h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #4b3a6b;
        }
        .order p {
            margin: 5px 0;
            font-size: 15.5px;
            color: #555;
        }
        .items {
            margin-top: 18px;
            padding-left: 20px;
            border-left: 3px solid var(--main-color);
        }
        .items h4 {
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--main-color);
            user-select: none;
        }
        .item {
            font-size: 15px;
            color: #444;
            margin-bottom: 7px;
        }
        .back {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 26px;
            background-color: var(--main-color);
            color: white;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 700;
            transition: background-color 0.3s ease;
            user-select: none;
        }
        .back:hover {
            background-color: #7a4d8d;
        }
        form.status-update {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        form.status-update select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        form.status-update button {
            padding: 6px 14px;
            background-color: var(--main-color);
            border: none;
            color: white;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form.status-update button:hover {
            background-color: #7a4d8d;
        }
        .invoice-link {
            margin-top: 12px;
            display: inline-block;
            background: var(--accent-color);
            color: #222;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            user-select: none;
            transition: background-color 0.3s ease;
        }
        .invoice-link:hover {
            background-color: #d99936;
        }
        @media (max-width: 900px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 20px 15px;
                display: flex;
                flex-direction: row;
                justify-content: space-around;
            }
            .sidebar h2 {
                display: none;
            }
            .logout a {
                margin-top: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 25px 20px;
                max-width: 100%;
            }
            .order {
                padding: 20px 15px;
            }
        }
    </style>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class='bx bx-home'></i> HOME</a>
    <a href="add.html"><i class='bx bx-plus-circle'></i> Add Product</a>
    <a href="manage_products.php"><i class='bx bx-edit'></i> Edit Products</a>
    <a href="view_orders.php" class="active"><i class='bx bx-cart'></i> View Orders</a>
    <a href="manage_users.php"><i class='bx bx-group'></i> Manage Users</a>

    <div class="logout">
        <a href="../index.php" class="home-link" style="
    background-color: transparent;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    font-family: 'Cairo', sans-serif;
    font-size: 16px;
    transition: background-color 0.3s ease;
"
           onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.2)';"
           onmouseout="this.style.backgroundColor='transparent';"
        >
            <i class='bx bx-home' style="font-size: 22px;"></i> home/customer
        </a>
        <a href="../logout.php"><i class='bx bx-log-out'></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <h1>All Orders</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <div class="order">
                <h3>Order #<?= $order['id'] ?> — <?= htmlspecialchars($order['fname'] . ' ' . $order['lname']) ?></h3>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <p><strong>City:</strong> <?= htmlspecialchars($order['city']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
                <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>

                <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'Not specified') ?></p>

                <p><strong>Payment Status:</strong>
                    <?= ($order['payment_method'] === 'visa') ? 'Paid' : htmlspecialchars($order['payment_status'] ?? 'Pending') ?>
                </p>

                <p><strong>Current Order Status:</strong> <?= htmlspecialchars($order['status'] ?? 'Pending') ?></p>

                <form class="status-update" method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>" />

                    <label><strong>Payment Status:</strong></label>
                    <select name="payment_status" required>
                        <?php
                        $paymentStatuses = ['Pending', 'Paid', 'Failed'];
                        foreach ($paymentStatuses as $status) {
                            $selected = ($order['payment_status'] === $status) ? 'selected' : '';
                            echo "<option value=\"$status\" $selected>$status</option>";
                        }
                        ?>
                    </select>

                    <label><strong>Order Status:</strong></label>
                    <select name="order_status" required>
                        <?php
                        $orderStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Completed'];
                        foreach ($orderStatuses as $status) {
                            $selected = ($order['status'] === $status) ? 'selected' : '';
                            echo "<option value=\"$status\" $selected>$status</option>";
                        }
                        ?>
                    </select>

                    <button type="submit">Update</button>
                </form>


                <p><strong>Last Updated:</strong> <?= $order['updated_at'] ?? 'N/A' ?></p>

                <div class="items">
                    <h4>Order Items:</h4>
                    <?php
                    $items = getOrderItems($conn, $order['id']);
                    if (count($items) > 0):
                        foreach ($items as $item):
                            ?>
                            <p class="item"><?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> — $<?= number_format($item['price'], 2) ?></p>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <p class="item">No items found.</p>
                    <?php endif; ?>
                </div>

                <a class="invoice-link" href="generate_invoice.php?order_id=<?= $order['id'] ?>" target="_blank">Download Invoice PDF</a>
                <form method="POST" action="delete_order.php" onsubmit="return confirm('Are you sure you want to delete this order?');" style="margin-top: 15px;">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>" />
                    <button type="submit" style="background-color: #e74c3c; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                        Delete Order
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>

</div>

</body>
</html>

<?php
$conn->close();
?>
