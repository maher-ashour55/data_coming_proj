<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit();
}

$user_id = $_SESSION['user_id'];
$message = trim($_POST['message'] ?? '');

if ($message === '') {
    http_response_code(400);
    exit('Empty message');
}

$conn = new mysqli("localhost", "root", "", "datacoming");
$conn->set_charset("utf8");
if ($conn->connect_error) {
    http_response_code(500);
    exit("DB Error");
}

$stmt = $conn->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, 1, ?)");
$stmt->bind_param("is", $user_id, $message);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Message sent";
