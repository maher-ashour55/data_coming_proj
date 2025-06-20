<?php
session_start();
if (!isset($_SESSION['user_id'])) exit();

$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
$conn->set_charset("utf8");

$sql = "SELECT * FROM chat_messages 
        WHERE (sender_id = ? OR receiver_id = ?) 
        AND (sender_id = 1 OR receiver_id = 1)
        ORDER BY timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $who = ($row['sender_id'] == $user_id) ? "You" : "Admin";
    echo "<p><strong>$who:</strong> " . htmlspecialchars($row['message']) . " <span style='color:gray;font-size:11px;'>(" . $row['timestamp'] . ")</span></p>";
}
$stmt->close();
$conn->close();
