<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set('Asia/Jerusalem');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];

$host = "localhost";
$dbname = "u251541401_datacoming";
$username = "u251541401_maher_user";
$password = "Datacoming12345";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// عدد المستخدمين
$user_count = 0;
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);
if ($result_users && $row = $result_users->fetch_assoc()) {
    $user_count = $row['total_users'];
}

// عدد المنتجات
$product_count = 0;
$sql_products = "SELECT COUNT(*) AS total_products FROM product";
$result_products = $conn->query($sql_products);
if ($result_products && $row = $result_products->fetch_assoc()) {
    $product_count = $row['total_products'];
}

// إحصائيات الطلبات
$sql_orders_stats = "SELECT 
    COUNT(*) AS total_orders,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
    SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_orders,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_orders
FROM orders";
$result_orders_stats = $conn->query($sql_orders_stats);
$order_stats = ['total' => 0,'completed' => 0, 'cancelled' => 0, 'pending' => 0];
if ($result_orders_stats && $row = $result_orders_stats->fetch_assoc()) {
    $order_stats['total'] = $row['total_orders'];
    $order_stats['completed'] = $row['completed_orders'];
    $order_stats['cancelled'] = $row['cancelled_orders'];
    $order_stats['pending'] = $row['pending_orders'];
}

// آخر 5 طلبات
$recent_orders = [];
$sql_recent_orders = "SELECT id, fname, lname, status, order_date FROM orders ORDER BY order_date DESC LIMIT 5";
$result_recent_orders = $conn->query($sql_recent_orders);
if ($result_recent_orders) {
    while ($row = $result_recent_orders->fetch_assoc()) {
        $recent_orders[] = $row;
    }
}

// المنتجات منخفضة المخزون (الكمية أقل من 5)
$low_stock_products = [];
$sql_low_stock = "SELECT id, name, stock FROM product WHERE stock < 5 AND is_active = 1 ORDER BY stock ASC";
$result_low_stock = $conn->query($sql_low_stock);
if ($result_low_stock) {
    while ($row = $result_low_stock->fetch_assoc()) {
        $low_stock_products[] = $row;
    }
}

// المنتجات غير متاحة (is_active = 0)
$unavailable_products = [];
$sql_unavailable = "SELECT id, name FROM product WHERE is_active = 0 ORDER BY name ASC";
$result_unavailable = $conn->query($sql_unavailable);
if ($result_unavailable) {
    while ($row = $result_unavailable->fetch_assoc()) {
        $unavailable_products[] = $row;
    }
}

// إجمالي المبيعات (المجموع الكلي) حسب الفترة (اليوم، الأسبوع، الشهر)
$sales = ['today' => 0, 'week' => 0, 'month' => 0];

// اليوم
$sql_sales_today = "SELECT IFNULL(SUM(total), 0) AS total_sales FROM orders WHERE DATE(order_date) = CURDATE() AND status = 'Completed' AND payment_status = 'Paid'";
$result_sales_today = $conn->query($sql_sales_today);
if ($result_sales_today && $row = $result_sales_today->fetch_assoc()) {
    $sales['today'] = $row['total_sales'];
}

// الأسبوع (من أول يوم في الأسبوع الحالي)
$sql_sales_week = "SELECT IFNULL(SUM(total), 0) AS total_sales FROM orders WHERE YEARWEEK(order_date, 1) = YEARWEEK(CURDATE(), 1) AND status = 'Completed' AND payment_status = 'Paid'";
$result_sales_week = $conn->query($sql_sales_week);
if ($result_sales_week && $row = $result_sales_week->fetch_assoc()) {
    $sales['week'] = $row['total_sales'];
}

// الشهر الحالي
$sql_sales_month = "SELECT IFNULL(SUM(total), 0) AS total_sales FROM orders WHERE YEAR(order_date) = YEAR(CURDATE()) AND MONTH(order_date) = MONTH(CURDATE()) AND status = 'Completed' AND payment_status = 'Paid'";
$result_sales_month = $conn->query($sql_sales_month);
if ($result_sales_month && $row = $result_sales_month->fetch_assoc()) {
    $sales['month'] = $row['total_sales'];
}
// إجمالي المبيعات الكلية
$sql_sales_total = "SELECT IFNULL(SUM(total), 0) AS total_sales FROM orders WHERE status = 'Completed' AND payment_status = 'Paid'";
$result_sales_total = $conn->query($sql_sales_total);
$sales['total'] = 0;
if ($result_sales_total && $row = $result_sales_total->fetch_assoc()) {
    $sales['total'] = $row['total_sales'];
}

$admin_count = 0;
$sql_admins = "SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'";
$result_admins = $conn->query($sql_admins);
if ($result_admins && $row = $result_admins->fetch_assoc()) {
    $admin_count = $row['total_admins'];
}


$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            min-height: 100vh;
            color: #333;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #9265A6, #6d4c82);
            color: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            font-weight: bold;
        }

        .menu-links {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .logout a:hover {
            background-color: #c0392b;
        }
        .menu-links a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 10px;
            transition: background 0.3s ease;
            font-size: 16px;
        }

        .menu-links a:hover,
        .menu-links a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .menu-links a i {
            margin-right: 10px;
            font-size: 20px;
        }

        /* === bottom links: home/customer and logout === */
        .bottom-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: auto;
        }

        .home-customer {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 16px;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .home-customer:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f44336;
            color: white;
            font-weight: bold;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .logout-btn i {
            margin-right: 8px;
            font-size: 20px;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }


        .sidebar h2 {
            margin-bottom: 40px;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
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
            margin-right: 12px;
            font-size: 22px;
        }
        .main {
            flex-grow: 1;
            padding: 40px;
            background-color: #f8f9fa;
            overflow-x: auto;
            padding-left: 280px;
        }
        .main-header {
            font-size: 30px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #444;
        }
        .cards {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }
        .card {
            background-color: white;
            padding: 30px 25px;
            flex: 1 1 280px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }
        .card i {
            font-size: 40px;
            color: #9265A6;
            margin-bottom: 15px;
        }
        .card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #3a235a;
        }
        .card p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        table th, table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        table th {
            background-color: #9265A6;
            color: white;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .status {
            display: inline-block;
            text-transform: capitalize;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            color: white;
            background-color: gray;
            min-width: 80px;
            text-align: center;
            vertical-align: middle;
            line-height: 1.4;
        }


        .status.pending {
            background-color: #f0ad4e;
            color: white;
        }

        .status.completed {
            background-color: #5cb85c;
            color: white;
        }

        .status.cancelled {
            background-color: #d9534f;
            color: white;
        }

        .status.processing {
            background-color: #5bc0de;
            color: white;
        }




        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main {
                padding: 20px;
            }
            .cards {
                flex-direction: column;
            }
            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>

    <div class="menu-links">
        <a href="#" class="active"><i class='bx bx-home'></i> HOME</a>
        <a href="add.html"><i class='bx bx-plus-circle'></i> Add Product</a>
        <a href="manage_products.php"><i class='bx bx-edit'></i> Edit Products</a>
        <a href="view_orders.php"><i class='bx bx-cart'></i> View Orders</a>
        <a href="manage_users.php"><i class='bx bx-group'></i> Manage Users</a>
    </div>

    <div class="bottom-links">
        <a href="../index.php" class="home-customer"><i class='bx bx-home'></i> home/customer</a>
        <a href="../logout.php" class="logout-btn"><i class='bx bx-log-out'></i> Logout</a>
    </div>
</div>



<div class="main">
    <div class="main-header">
        Welcome Admin, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
    </div>

    <div class="cards">
        <div class="card">
            <i class='bx bx-user'></i>
            <h3>Users</h3>
            <p>Total users: <strong><?php echo $user_count; ?></strong></p>
            <p>Admins: <strong><?php echo $admin_count; ?></strong></p>
        </div>
        <div class="card">
            <i class='bx bx-package'></i>
            <h3>Products</h3>
            <p>Total products: <strong><?php echo $product_count; ?></strong></p>
        </div>
        <div class="card">
            <i class='bx bx-shopping-bag'></i>
            <h3>Orders</h3>
            <p>Total orders: <strong><?php echo $order_stats['total']; ?></strong></p>
            <p>Completed orders: <strong><?php echo $order_stats['completed']; ?></strong></p>
            <p>Cancelled orders: <strong><?php echo $order_stats['cancelled']; ?></strong></p>
            <p>Pending orders: <strong><?php echo $order_stats['pending']; ?></strong></p>
        </div>
        <div class="card">
            <i class='bx bx-dollar-circle'></i>
            <h3>Sales</h3>
            <p>Today: <strong><?php echo number_format($sales['today'], 2); ?> $</strong></p>
            <p>This Week: <strong><?php echo number_format($sales['week'], 2); ?> $</strong></p>
            <p>This Month: <strong><?php echo number_format($sales['month'], 2); ?> $</strong></p>
            <p>Total Sales: <strong><?php echo number_format($sales['total'], 2); ?> $</strong></p>
        </div>

    </div>

    <h3>Latest 5 Orders</h3>
    <table>
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Order Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($recent_orders) === 0): ?>
            <tr><td colspan="4" style="text-align:center;">No orders found.</td></tr>
        <?php else: ?>
            <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['fname'] . ' ' . $order['lname']); ?></td>
                    <td class="status <?php echo strtolower(htmlspecialchars($order['status'])); ?>">
                        <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                    </td>

                    <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($order['order_date']))); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <h3>Low Stock Products (stock &lt; 5)</h3>
    <div style="max-height: 240px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 40px;">
        <table>
            <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Stock</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($low_stock_products) === 0): ?>
                <tr><td colspan="3" style="text-align:center;">No low stock products.</td></tr>
            <?php else: ?>
                <?php foreach ($low_stock_products as $prod): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prod['id']); ?></td>
                        <td><?php echo htmlspecialchars($prod['name']); ?></td>
                        <td><?php echo htmlspecialchars($prod['stock']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>



    <h3>Unavailable Products</h3>
    <table>
        <thead>
        <tr>
            <th>Product ID</th>
            <th>Name</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($unavailable_products) === 0): ?>
            <tr><td colspan="2" style="text-align:center;">All products are active.</td></tr>
        <?php else: ?>
            <?php foreach ($unavailable_products as $prod): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prod['id']); ?></td>
                    <td><?php echo htmlspecialchars($prod['name']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>
