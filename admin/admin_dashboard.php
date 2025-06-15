<?php
session_start();

// Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "datacoming";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Query to count users
$user_count = 0;
$sql_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = $conn->query($sql_users);
if ($result_users && $row = $result_users->fetch_assoc()) {
    $user_count = $row['total_users'];
}

// Query to count products
$product_count = 0;
$sql_products = "SELECT COUNT(*) AS total_products FROM product";
$result_products = $conn->query($sql_products);
if ($result_products && $row = $result_products->fetch_assoc()) {
    $product_count = $row['total_products'];
}

// Query to count completed & paid orders
$order_count = 0;
$sql = "SELECT COUNT(*) as total_orders FROM orders WHERE status = 'Completed' AND payment_status = 'Paid'";
$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $order_count = $row['total_orders'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
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
            margin-bottom: 10px;
        }

        .card p {
            font-size: 16px;
            color: #555;
        }

        .logout {
            margin-top: auto;
            padding-top: 40px;
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

        .logout a:hover {
            background-color: #c0392b;
        }

        .logout i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar navigation -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#" class="active"><i class='bx bx-home'></i> HOME</a>
    <a href="add.html"><i class='bx bx-plus-circle'></i> Add Product</a>
    <a href="manage_products.php"><i class='bx bx-edit'></i> Edit Products</a>
    <a href="view_orders.php"><i class='bx bx-cart'></i> View Orders</a>
    <a href="manage_users.php"><i class='bx bx-group'></i> Manage Users</a>
    <div class="logout">
        <a href="../php/logout.php"><i class='bx bx-log-out'></i> Logout</a>
    </div>
</div>

<!-- Main dashboard content -->
<div class="main">
    <div class="main-header">
        Welcome Admin, <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
    </div>

    <!-- Cards for overview -->
    <div class="cards">
        <div class="card">
            <i class='bx bx-user'></i>
            <h3>Users</h3>
            <p>Total registered users: <strong><?php echo $user_count; ?></strong></p>
        </div>
        <div class="card">
            <i class='bx bx-package'></i>
            <h3>Products</h3>
            <p>Total products: <strong><?php echo $product_count; ?></strong></p>
        </div>
        <div class="card">
            <i class='bx bx-shopping-bag'></i>
            <h3>Orders</h3>
            <p>Total orders: <strong><?php echo $order_count; ?></strong></p>
        </div>
    </div>
</div>

</body>
</html>
