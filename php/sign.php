<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datacoming";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['reg-firstname'];
    $lastname = $_POST['reg-lastname'];
    $email = $_POST['reg-email'];
    $password = $_POST['reg-password'];
    $confirm_password = $_POST['reg-confirm'];

    if ($password == $confirm_password) {
        $firstname = $conn->real_escape_string($firstname);
        $lastname = $conn->real_escape_string($lastname);
        $email = $conn->real_escape_string($email);
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql_check_email = "SELECT * FROM users WHERE email = '$email'";
        $result_check = $conn->query($sql_check_email);

        if ($result_check->num_rows > 0) {
            $error_message = "البريد الإلكتروني هذا مسجل بالفعل. الرجاء استخدام بريد آخر.";
        } else {
            $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES ('$firstname', '$lastname', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                echo "تم التسجيل بنجاح!";
                header("Location: login.php");
                exit();
            } else {
                echo "خطأ في التسجيل: " . $conn->error;
            }
        }
    } else {
        $error_message = "كلمة المرور غير متطابقة!";
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Sign up</title>
    <link rel="stylesheet" href="../styles/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .error-message {
            background-color: #ffe0e0;
            color: #c0392b;
            padding: 15px;
            border: 1px solid #e74c3c;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

    </style>
</head>
<body>

<div class="outer">
    <div class="title">
        <div class="title_login">Register</div>
    </div>
    <?php

    if (isset($error_message)) {
        echo '<div class="error-message">' . $error_message . '</div>';
    }
    ?>

    <form action="sign.php" method="POST">
        <div class="name-box">
            <div class="input-box half">
                <input type="text" class="input-field" name="reg-firstname" placeholder="First Name">
                <i class='bx bx-user icon'></i>
            </div>
            <div class="input-box half">
                <input type="text" class="input-field" name="reg-lastname" placeholder="Last Name">
                <i class='bx bx-user icon'></i>
            </div>
        </div>

        <div class="input-box">
            <input  class="input-field" required type="email" name="reg-email" placeholder="Email">
            <i class='bx bx-envelope icon'></i>
        </div>
        <div class="input-box">
            <input type="password" class="input-field" name="reg-password" placeholder="Password">
            <i class='bx bx-lock-alt icon'></i>
        </div>
        <div class="input-box">
            <input type="password" class="input-field" name="reg-confirm" placeholder="Confirm Password">
            <i class='bx bx-lock icon'></i>
        </div>
        <button class="login-btn">Register <i class='bx bx-user-plus'></i></button>
        <div class="register">Already have an account? <a href="login.php">Login</a></div>
    </form>
</div>

</body>
</html>
