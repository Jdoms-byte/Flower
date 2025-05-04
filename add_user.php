<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.html'); // Redirect to login if not logged in or not admin
    exit();
}

// Handle form submission for adding a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPassword, $role);

    if ($stmt->execute()) {
        echo "<p>User added successfully!</p>";
        echo "<p><a href='admin_dashboard.php'>Return to Admin Dashboard</a></p>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New User</title>
    <style>
        /* Basic styling */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        form {
            max-width: 400px;
            margin: auto;
        }
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            background: #2ecc71;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>Add New User</h2>

<form action="add_user.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <label for="role">Role:</label>
    <select name="role" id="role" required>
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select>

    <button type="submit">Add User</button>
</form>

</body>
</html>
