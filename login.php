<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $usernameDB, $passwordDB, $role);
    $stmt->fetch();

    // Check password
    if (password_verify($password, $passwordDB)) {
        $_SESSION['username'] = $usernameDB;
        $_SESSION['role'] = $role;
        $_SESSION['show_welcome'] = true; // Set welcome message flag

        // Redirect based on user role
        if ($role === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: user_dashboard.php');
        }
        exit();
    } else {
        echo "Invalid login credentials.";
    }
}
?>
