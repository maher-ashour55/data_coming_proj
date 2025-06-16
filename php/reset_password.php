<?php
session_start();

$conn = new mysqli("localhost", "root", "", "datacoming");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['token'])) {
    die("Invalid request! No token provided.");
}

$token = $_GET['token'];
$message = "";
$show_form = true;

$stmt = $conn->prepare("SELECT email, expires FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Invalid or expired token!");
}

$row = $res->fetch_assoc();
$email = $row['email'];
$expires = $row['expires'];

if (strtotime($expires) < time()) {
    die("The reset link has expired.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $password1 = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirm'] ?? '';

    if ($password1 !== $password2) {
        $message = "Passwords do not match!";
    } elseif (strlen($password1) < 6) {
        $message = "Password must be at least 6 characters!";
    } else {
        $hashedPassword = password_hash($password1, PASSWORD_DEFAULT);

        $updateSQL = "UPDATE users SET password = ? WHERE email = ?";
        $updateStmt = $conn->prepare($updateSQL);
        $updateStmt->bind_param("ss", $hashedPassword, $email);
        if ($updateStmt->execute()) {
            $delStmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $delStmt->bind_param("s", $token);
            $delStmt->execute();

            $message = "Password updated successfully! You can now <a href='login.php'>login</a>.";
            $show_form = false;
        } else {
            $message = "Error updating password. Please try again.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial; background-color: #f7f7f7; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .input-box { margin-bottom: 15px; position: relative; }
        input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; }
        button { padding: 12px 20px; background-color: #9265A6; color: white; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background-color: #7a5290; }
        .message { background: #e8f4ff; border: 1px solid #3c8dbc; padding: 15px; border-radius: 6px; margin-bottom: 15px; }
        a { color: #9265A6; }
    </style>
</head>
<body>
<div id="messageBox" style="display:none; padding:10px; margin-bottom:15px; border-radius:6px; color:white;"></div>


<div class="container">
    <h2>Reset Password</h2>

    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

    <?php if ($show_form): ?>
        <form method="POST">
            <div class="input-box">
                <input type="password" name="password" placeholder="Enter new password" required minlength="6">
            </div>
            <div class="input-box">
                <input type="password" name="password_confirm" placeholder="Confirm new password" required minlength="6">
            </div>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

</body>
<script>
    function showMessage(text, success = true) {
        const messageBox = document.getElementById('messageBox');
        messageBox.textContent = text;
        messageBox.style.backgroundColor = success ? '#4CAF50' : '#f44336';
        messageBox.style.display = 'block';
        setTimeout(() => {
            messageBox.style.display = 'none';
        }, 3000);
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        const pass1 = this.password.value.trim();
        const pass2 = this.password_confirm.value.trim();

        if (pass1 !== pass2) {
            e.preventDefault();
            showMessage('Passwords do not match!', false);
        }
    });
</script>

</html>
