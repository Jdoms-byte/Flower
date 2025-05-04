<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['username'])) {
    $item_id = $_POST['item_id'];
    $username = $_SESSION['username'];
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO reviews (item_id, username, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $item_id, $username, $rating, $comment);
    $stmt->execute();

    header("Location: user_product_dashboard.php");
    exit();
}
?>
