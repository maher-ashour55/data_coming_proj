<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// استيراد PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './php/PHPMailer-master/src/Exception.php';
require './php/PHPMailer-master/src/PHPMailer.php';
require './php/PHPMailer-master/src/SMTP.php';

$servername = "localhost";
$username = "u251541401_maher_user";
$password = "Datacoming12345";
$dbname = "u251541401_datacoming";

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

    if ($password !== $confirm_password) {
        $error_message = "كلمة المرور غير متطابقة!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // تحقق من البريد مسبقاً
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error_message = "البريد الإلكتروني هذا مسجل بالفعل. الرجاء استخدام بريد آخر.";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $firstname, $lastname, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                $user_id = $stmt_insert->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;

                // إرسال إيميل الترحيب
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.hostinger.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'datacoming@datacoming.store';
                    $mail->Password = 'Datacoming_1212';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('datacoming@datacoming.store', 'Data Coming');
                    $mail->addAddress($email, $firstname . ' ' . $lastname);

                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Datacoming';

                    $mail->Body = '
<div dir="rtl" style="font-family: Arial, sans-serif; color: #333; direction: rtl; text-align: right;">
    <h2 style="color: #9265A6;">اهلاً ' . htmlspecialchars($firstname) . '!</h2>
    <p>شكراً لتسجيلك معنا. نتمنى لك تجربة تسوق ممتعة.</p>
    <p>نحن سعداء بانضمامك إلينا!</p>
    <p>إذا كان لديك أي أسئلة، لا تتردد بالتواصل معنا.</p>

    <a href="https://datacoming.store" style="background:#9265A6; color:#fff; padding:10px 15px; text-decoration:none; border-radius:5px;">زر موقعنا</a>
</div>
';




                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                    // ممكن تعطي رسالة للمستخدم أو لا، حسب اختيارك
                }

                header("Location: index.php");
                exit();
            } else {
                $error_message = "خطأ في التسجيل: " . $conn->error;
            }

            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign up</title>
    <link rel="stylesheet" href="./styles/login.css">
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

    <?php if (isset($error_message)): ?>
        <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form action="sign.php" method="POST">
        <div class="name-box">
            <div class="input-box half">
                <input type="text" class="input-field" name="reg-firstname" placeholder="First Name" required>
                <i class='bx bx-user icon'></i>
            </div>
            <div class="input-box half">
                <input type="text" class="input-field" name="reg-lastname" placeholder="Last Name" required>
                <i class='bx bx-user icon'></i>
            </div>
        </div>

        <div class="input-box">
            <input type="email" class="input-field" name="reg-email" placeholder="Email" required>
            <i class='bx bx-envelope icon'></i>
        </div>

        <div class="input-box">
            <input type="password" class="input-field" name="reg-password" placeholder="Password" required>
            <i class='bx bx-lock-alt icon'></i>
        </div>

        <div class="input-box">
            <input type="password" class="input-field" name="reg-confirm" placeholder="Confirm Password" required>
            <i class='bx bx-lock icon'></i>
        </div>

        <button class="login-btn" type="submit">Register <i class='bx bx-user-plus'></i></button>

        <div class="register">Already have an account? <a href="login.php">Login</a></div>
    </form>
</div>

</body>
</html>
