<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // First, get the image path from database
    $stmt = $conn->prepare("SELECT image FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    // Delete the image file if it exists
    if (!empty($imagePath) && file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Then delete the record from the database
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to dashboard after deleting
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error deleting item: " . $stmt->error;
    }
}
?>
