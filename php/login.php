<?php
// بدء الجلسة
session_start();

// التحقق إذا كان المستخدم قد سجل الدخول مسبقًا
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // إذا كان المستخدم مسجلاً بالفعل، التوجه إلى الـ Dashboard
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // الاتصال بقاعدة البيانات
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "datacoming";

    // إنشاء الاتصال
    $conn = new mysqli($servername, $username, $password, $dbname);

    // التحقق من الاتصال
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // استلام البيانات من الفورم
    $email = $_POST['email'];
    $password = $_POST['password'];

    // تأمين البيانات ضد هجمات SQL Injection
    $email = $conn->real_escape_string($email);

    // استعلام للتحقق من البيانات المدخلة مع جلب دور المستخدم
    $sql = "SELECT * FROM users WHERE email = '$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // التحقق من كلمة المرور باستخدام دالة password_verify
        if (password_verify($password, $user['password'])) {
            // تخزين بيانات المستخدم في الجلسة
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role']; // تخزين الدور في الجلسة

            // إعادة التوجيه إلى صفحة الـ Dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "كلمة المرور غير صحيحة!";
        }
    } else {
        $error = "البريد الإلكتروني غير موجود!";
    }

    // إغلاق الاتصال
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
        <div class="title_login">Login</div>
    </div>
    <?php
    // عرض رسالة الخطأ إذا كانت موجودة
    if (isset($error)) {
        echo "<div class='error-message'>$error</div>";
    }
    ?>
    <form class="login-form" method="POST" action="">
        <div class="input-box">
            <input type="email" class="input-field" name="email" type="email" id="log-email" placeholder="Email" required>
            <i class='bx bx-envelope icon'></i>
        </div>
        <div class="input-box">
            <input type="password" class="input-field" name="password" id="log-password" placeholder="Password" required>
            <i class='bx bx-lock-alt icon'></i>
        </div>
        <div class="forgot"><a href="forgot_password.php">Forgot password?</a></div>
        <button type="submit" class="login-btn">Sign In <i class='bx bx-log-in'></i></button>
        <div class="register">Don't have an account? <a href="sign.php">Sign up</a></div>
        <div class="social-media">
            <div class="social-icons">
                <a href="https://www.facebook.com/profile.php?id=61556946718232" target="_blank"><i class='bx bxl-facebook'></i></a>
                <a href="https://www.instagram.com/datac0ming?igsh=MThhMWs5ZHA5MTZneA==" target="_blank"><i class='bx bxl-instagram'></i></a>
                <a href="https://wa.me/+972566720728" target="_blank"><i class='bx bxl-whatsapp'></i></a>
            </div>
        </div>
    </form>
</div>

</body>
</html>
