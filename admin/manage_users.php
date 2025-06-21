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

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    if ($delete_id !== $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $delete_id");
        header("Location: manage_users.php");
        exit();
    } else {
        $error = "You cannot delete your own account!";
    }
}
if (isset($_POST['update_role']) && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];

    if ($user_id !== $_SESSION['user_id']) {
        if (in_array($new_role, ['user', 'admin'])) {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $new_role, $user_id);
            $stmt->execute();
            $stmt->close();

            header("Location: manage_users.php");
            exit();
        } else {
            $error = "Invalid role selected.";
        }
    } else {
        $error = "You cannot change your own role.";
    }
}

$sql = "SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Users - Admin Panel</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .table-wrapper {
            max-height: 630px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
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

            position: sticky;
            top: 0;
            z-index: 10;
        }

        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
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

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .error-msg {
            background-color: #ffdddd;
            border-left: 6px solid #f44336;
            padding: 10px 15px;
            margin-bottom: 15px;
            color: #a33;
            border-radius: 6px;
        }

        a.back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #9265A6;
            text-decoration: none;
            font-weight: 600;
        }

        a.back-link:hover {
            text-decoration: underline;
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


        .logout i {
            margin-right: 8px;
        }

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
            display: flex;
            flex-direction: column;
        }

        h1 {
            margin-bottom: 20px;
        }
        .logout a:hover {
             background-color: #c0392b;
         }
    </style>
    <script>
        function confirmDelete(userId, userName) {
            if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                window.location.href = `manage_users.php?delete_id=${userId}`;
            }
        }
    </script>
</head>
<body>
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class='bx bx-home'></i> HOME</a>
    <a href="add.html"><i class='bx bx-plus-circle'></i> Add Product</a>
    <a href="manage_products.php"><i class='bx bx-edit'></i> Edit Products</a>
    <a href="view_orders.php"><i class='bx bx-cart'></i> View Orders</a>
    <a href="#" class="active"><i class='bx bx-group'></i> Manage Users</a>
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

<div class="main">

    <h1>Manage Users</h1>

    <?php if (!empty($error)) : ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result && $result->num_rows > 0) : ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['first_name']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <?php if ($row['id'] !== $_SESSION['user_id']) : ?>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                    <select name="role" style="padding:5px; border-radius:5px; border:1px solid #ccc;">
                                        <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="update_role" style="padding:5px 10px; margin-left:5px; background:#9265A6; color:#fff; border:none; border-radius:5px; cursor:pointer;">Update</button>
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($row['role']) ?> (Your Role)
                            <?php endif; ?>
                        </td>
                        <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if ($row['id'] !== $_SESSION['user_id']) : ?>
                                <button class="btn-delete" onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>')">Delete</button>
                            <?php else: ?>
                                <em>Your Account</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No users found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
