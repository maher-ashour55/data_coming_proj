<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$dbname = "datacoming";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق من صلاحيات الأدمن
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// جلب التصنيفات لعرضها في القائمة المنسدلة
$category_sql = "SELECT DISTINCT category FROM product";
$category_result = $conn->query($category_sql);

// التحقق من وجود تصنيف مُختار
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';

if ($selected_category !== '') {
    $stmt = $conn->prepare("SELECT * FROM product WHERE category = ?");
    $stmt->bind_param("s", $selected_category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
}

// بيانات الأدمن من السيشن
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
?>

<!-- HTML page starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Products</title>
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

        .main {
            flex-grow: 1;
            padding: 40px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .main-header {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #444;
        }

        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            padding: 0px;
            overflow-y: auto;
            max-height: 600px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            background-color: #9265A6;
            color: white;
            font-weight: 700;
            padding: 14px 10px;
            text-align: center;
            word-wrap: break-word;

            /* تثبيت الرأس */
            position: sticky;
            top: 0;
            z-index: 5;
        }

        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        tbody td img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.2s ease-in-out;
            display: inline-block;
        }

        .edit {
            background-color: #5c9ded;
        }

        .edit:hover {
            background-color: #4584d6;
        }

        .delete {
            background-color: #e74c3c;
        }

        .delete:hover {
            background-color: #c0392b;
        }

        .id-col { width: 7%; }
        .name-col { width: 25%; }
        .price-col { width: 10%; }
        .category-col { width: 15%; }
        .image-col { width: 15%; }
        .actions-col { width: 20%; }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                overflow-x: auto;
                padding: 15px 10px;
            }

            .sidebar h2 {
                flex-shrink: 0;
                margin-right: 20px;
                margin-bottom: 0;
                font-size: 20px;
                align-self: center;
            }

            .sidebar a {
                padding: 8px 12px;
                margin: 0 5px;
                font-size: 14px;
            }

            .logout {
                padding-top: 0;
                margin-left: auto;
                display: flex;
                align-items: center;
            }

            .main {
                padding: 20px;
            }

            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tbody tr {
                margin-bottom: 20px;
                background: #fff;
                padding: 15px;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            }

            tbody td {
                border: none;
                position: relative;
                padding-left: 50%;
                white-space: normal;
                text-align: right;
            }

            tbody td::before {
                position: absolute;
                top: 15px;
                left: 15px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                color: #555;
                content: attr(data-label);
                text-align: left;
            }

            .action-buttons {
                justify-content: flex-start;
            }

    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class='bx bx-home'></i> HOME</a>
    <a href="add.html"><i class='bx bx-plus-circle'></i> Add Product</a>
    <a href="manage_products.php" class="active"><i class='bx bx-edit'></i> Edit Products</a>
    <a href="view_orders.php"><i class='bx bx-cart'></i> View Orders</a>
    <a href="manage_users.php"><i class='bx bx-group'></i> Manage Users</a>

    <div class="logout">
        <a href="../php/logout.php"><i class='bx bx-log-out'></i> Logout</a>
    </div>
</div>

<div class="main">

    <form method="GET" style="margin-bottom: 20px;">
        <label for="category" style="font-weight: bold;">Filter by Category:</label>
        <select name="category" id="category" onchange="this.form.submit()" style="padding: 8px 12px; margin-left: 10px; border-radius: 6px;">
            <option value="">All Categories</option>
            <?php while ($cat_row = $category_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($cat_row['category']); ?>" <?php echo ($selected_category == $cat_row['category']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat_row['category']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th class="id-col">ID</th>
                <th class="name-col">Name</th>
                <th class="price-col">Price ($)</th>
                <th class="category-col">Category</th>
                <th class="stock-col">Stock</th>
                <th class="image-col">Image</th>
                <th class="actions-col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID" class="id-col"><?php echo $row['id']; ?></td>
                    <td data-label="Name" class="name-col"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td data-label="Price" class="price-col"><?php echo $row['price']; ?></td>
                    <td data-label="Category" class="category-col"><?php echo htmlspecialchars($row['category']); ?></td>
                    <td data-label="Stock" class="stock-col"><?php echo $row['stock']; ?></td> <!-- السطر الجديد -->
                    <td data-label="Image" class="image-col">
                        <img src="uploads/<?php echo $row['image']; ?>" alt="Product Image" />
                    </td>
                    <td data-label="Actions" class="actions-col">
                        <div class="action-buttons">
                            <a class="btn edit" href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a class="btn delete" href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </div>
                    </td>

                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
