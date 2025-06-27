<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Handle account update
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_info'])) {
    $new_first_name = trim($_POST['first_name']);
    $new_last_name = trim($_POST['last_name']);
    $new_email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);

    if (!empty($new_first_name) && !empty($new_last_name) && !empty($new_email)) {
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_first_name, $new_last_name, $new_email, $user_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['first_name'] = $new_first_name;
        $_SESSION['last_name'] = $new_last_name;
        $_SESSION['email'] = $new_email;

        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $user_id);
            $stmt->execute();
            $stmt->close();
            $success_message = "Password and details updated successfully.";
        } else {
            $success_message = "Details updated successfully.";
        }
    } else {
        $error_message = "All fields are required.";
    }
}


$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$first_name = $row['first_name'];
$last_name = $row['last_name'];
$email = $row['email'];
$stmt->close();

// Handle cancel order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancel_id = intval($_POST['cancel_order_id']);
    $sql_check = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("ii", $cancel_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (in_array(strtolower($row['status']), ['pending', 'processing'])) {
            $stmt_stock = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $stmt_stock->bind_param("i", $cancel_id);
            $stmt_stock->execute();
            $res_stock = $stmt_stock->get_result();
            while ($item = $res_stock->fetch_assoc()) {
                $conn->query("UPDATE product SET stock = stock + {$item['quantity']} WHERE id = {$item['product_id']}");
            }
            $update = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
            $update->bind_param("i", $cancel_id);
            $update->execute();
        }
    }
    $stmt->close();
}

$sql = "SELECT id, order_date, total, status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #9265A6;
            --light-bg: #f5f7fa;
            --danger: #e74c3c;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light-bg);
        }

        header {
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 60px 20px 100px;
            color: white;
            background: linear-gradient(135deg, #9265A6 0%, #7f55a3 50%, #9b74b8 100%);
        }

        header h1, header p {
            position: relative;
            z-index: 1;
        }

        header div {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
        }

        .top-bar {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: -40px;
        }

        .btn {
            background: var(--primary);
            color: white;
            padding: 10px 22px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: background 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-danger {
            background: var(--danger);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .dashboard-flex {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            padding: 24px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid #e4e2ee;
        }

        .card h2 {
            font-size: 1.4rem;
            margin-bottom: 18px;
            color: #553f72;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-info {
            flex: 1 1 300px;
            max-width: 300px;
        }

        .card-orders {
            flex: 2 1 600px;
        }

        .card-info p {
            margin-bottom: 12px;
            font-size: 14.5px;
            color: #333;
        }

        .card-info .btn {
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: var(--primary);
            color: white;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        ul {
            margin: 0;
            padding-left: 18px;
        }
        .status {
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 10px;
            display: inline-block;
            font-size: 13px;
            text-transform: capitalize;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #b45309;
        }
        .status-processing {
            background-color: #e0f2fe;
            color: #1d4ed8;
        }
        .status-shipped {
            background-color: #ede9fe;
            color: #7c3aed;
        }
        .status-delivered {
            background-color: #dcfce7;
            color: #15803d;
        }
        .status-cancelled {
            background-color: #fef2f2;
            color: #991b1b;
        }
        .status-completed {
            background-color: #ecfdf5;
            color: #065f46;
        }

    </style>

</head>
<body>
<header>
    <div style="top: -60px; left: -40px; width: 200px; height: 200px; background: rgba(255,255,255,0.07);"></div>
    <div style="bottom: -40px; right: -60px; width: 200px; height: 200px; background: rgba(255,255,255,0.04);"></div>

    <h1>Welcome, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?> ✨</h1>
    <p>Your personal dashboard</p>

    <svg viewBox="0 0 1440 150" preserveAspectRatio="none" style="position:absolute; bottom:0; left:0; width:100%; height:100px; display:block; z-index: 1;" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,64 C480,120 960,0 1440,80 L1440,150 L0,150 Z" fill="#f5f7fa"></path>
    </svg>
</header>
<br><br><br>
<div style="display: flex; justify-content: center; gap: 15px; margin-top: -60px; z-index: 2; position: relative; flex-wrap: wrap;">
    <a href="index.php" class="btn">🏠 Home</a>
    <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
    <?php if ($role === 'admin'): ?>
        <a href="admin/admin_dashboard.php" class="btn" style="background-color:#4a2b63;">🛠 Admin Panel</a>
    <?php endif; ?>
</div>

<div class="container" style="display: flex; gap: 30px; flex-wrap: wrap;">
    <div class="card" style="flex: 1 1 300px; max-width: 350px; background: linear-gradient(135deg, #ffffff, #f9f9f9); border-left: 6px solid var(--primary);">
        <h2 style="font-size: 20px; color: var(--primary); margin-bottom: 16px;">👤 Account Info</h2>
        <?php if ($success_message): ?><p style="color:green; font-size: 14px; font-weight: bold;">✔ <?php echo $success_message; ?></p><?php endif; ?>
        <?php if ($error_message): ?><p style="color:red; font-size: 14px; font-weight: bold;">✖ <?php echo $error_message; ?></p><?php endif; ?>

        <form method="post" class="account-form" style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; flex-direction: column;">
                <label for="first_name" style="color:#555; font-weight: 600;">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px;">
            </div>

            <div style="display: flex; flex-direction: column;">
                <label for="last_name" style="color:#555; font-weight: 600;">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px;">
            </div>

            <div style="display: flex; flex-direction: column;">
                <label for="email" style="color:#555; font-weight: 600;">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px;">
            </div>

            <div style="display: flex; flex-direction: column;">
                <label for="new_password" style="color:#555; font-weight: 600;">New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="••••••••" style="padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 15px;">
            </div>

            <button type="submit" name="update_user_info" class="btn" style="margin-top: 10px; width: 100%;">Save Changes</button>
        </form>
    </div>



    <div class="card orders-card">
        <h2><i class="fas fa-box"></i> My Orders</h2>
        <table class="styled-table">
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
            <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td>₪<?php echo number_format($order['total'], 2); ?></td>
                    <td>
                        <?php
                        $status = strtolower($order['status']);
                        echo '<span class="status status-' . $status . '">' . ucfirst($status) . '</span>';
                        ?>
                    </td>
                    <td>
                        <?php
                        $stmt_p = $conn->prepare("SELECT p.name, oi.quantity FROM order_items oi JOIN product p ON oi.product_id = p.id WHERE oi.order_id = ?");
                        $stmt_p->bind_param("i", $order['id']);
                        $stmt_p->execute();
                        $res_p = $stmt_p->get_result();
                        echo "<ul style='padding-left: 18px;'>";
                        while ($prod = $res_p->fetch_assoc()) {
                            echo "<li>" . htmlspecialchars($prod['name']) . " × " . $prod['quantity'] . "</li>";
                        }
                        echo "</ul>";
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($order['status'] === 'Pending' || $order['status'] === 'Processing') {
                            echo '
                            <form method="post" action="">
                                <input type="hidden" name="cancel_order_id" value="' . $order['id'] . '">
                                <button type="submit" class="btn" style="background-color:#e67e22;" onclick="return confirm(\'Are you sure you want to cancel this order?\');">Cancel</button>
                            </form>';
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
    </div>

</div>
</body>
</html>