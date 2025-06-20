<?php
session_start();

$host = 'localhost';
$db   = 'u251541401_datacoming';
$user = 'u251541401_maher_user';
$pass = 'Datacoming12345';
$charset = 'utf8mb4';

// إعداد PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

use master\src\PHPMailer;
use master\src\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+30 minutes'));

        // حذف أي رموز قديمة لنفس البريد
        $conn->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
        $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)")->execute([$email, $token, $expires]);

        $reset_link = "https://datacoming.store/data_coming_proj/php/reset_password.php?token=$token";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'maherashoor48@gmail.com';
            $mail->Password = 'zsfpmrackwswronn';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('maherashoor48@gmail.com', 'Data Coming');
            $mail->addAddress($email);
            $mail->Subject = 'Reset Your Password';
            $mail->isHTML(true);

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2 style='color: #9265A6;'>Password Reset Request</h2>
                    <p>Hello,</p>
                    <p>We received a request to reset your password. Click the button below to proceed:</p>
                    <a href='$reset_link' style='
                        display: inline-block;
                        padding: 12px 24px;
                        background-color: #9265A6;
                        color: white;
                        text-decoration: none;
                        border-radius: 6px;
                        font-size: 16px;
                        margin: 20px 0;
                    '>Reset Password</a>
                    <p>This link will expire in <strong>30 minutes</strong>.</p>
                    <hr style='margin-top: 40px;'>
                    <p style='font-size: 14px; color: #777;'>If you didn’t request a password reset, you can safely ignore this email.</p>
                    <p style='font-size: 13px; color: #aaa;'>– Data Coming Team</p>
                </div>
            ";

            $mail->send();
            $feedback = "<p class='success'>✅ A reset link has been sent to your email.</p>";
        } catch (Exception $e) {
            $feedback = "<p class='error'>❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
        }
    } else {
        $feedback = "<p class='error'>❌ Email is not registered.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        }

        .reset-form {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .reset-form h2 {
            margin-bottom: 20px;
            color: #9265A6;
        }

        .reset-form input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
        }

        .reset-form button {
            background-color: #9265A6;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .reset-form button:hover {
            background-color: #7a4f8a;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="reset-form">
    <h2>Forgot Your Password?</h2>
    <?= $feedback ?>
    <form method="post" novalidate>
        <input type="email" name="email" required placeholder="Enter your email">
        <button type="submit">Send Reset Link</button>
    </form>
</div>

</body>
</html>
