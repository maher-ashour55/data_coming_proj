<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized access.";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datacoming";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection failed: " . $conn->connect_error;
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($conn->real_escape_string($_POST['product_name']));
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = trim($conn->real_escape_string($_POST['category']));
    $description = trim($conn->real_escape_string($_POST['description']));

    if (empty($name) || $price <= 0 || $stock < 0 || empty($category) || empty($description)) {
        http_response_code(400);
        echo "Please fill in all required fields with valid values.";
        exit();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed)) {
            http_response_code(400);
            echo "Only JPG, JPEG, PNG & GIF files are allowed.";
            exit();
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowed_mimes)) {
            http_response_code(400);
            echo "Invalid image file type.";
            exit();
        }

        $new_file_name = uniqid('img_') . '.' . $file_ext;
        $target_file = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $sql = "INSERT INTO product (name, category, description, price, image, stock) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                http_response_code(500);
                echo "Prepare statement failed: " . $conn->error;
                exit();
            }

            $stmt->bind_param("sssisi", $name, $category, $description, $price, $new_file_name, $stock);

            if ($stmt->execute()) {
                echo "Product added successfully!";
            } else {
                http_response_code(500);
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            http_response_code(500);
            echo "Failed to upload image.";
        }
    } else {
        http_response_code(400);
        echo "Image file is required.";
    }
} else {
    http_response_code(405);
    echo "Invalid request method.";
}

$conn->close();
?>
