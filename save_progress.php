<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $message = $_POST['message'];
    $is_user = $_POST['is_user'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO chat_history (user_id, message, is_user) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $message, $is_user);
    $stmt->execute();
    $stmt->close();
}
?>
