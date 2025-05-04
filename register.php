<?php
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if there are already users in the database
    $result = $conn->query("SELECT COUNT(*) AS count FROM users");
    $row = $result->fetch_assoc();
    $isFirstUser = $row['count'] == 0; // If there are no users, this is the first user

    // Assign role: admin if first user, else user
    $role = $isFirstUser ? 'admin' : 'user';

    // Insert user into the database with the selected role
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!-- Registration Form -->
<form method="POST" action="register.php">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
    </div>
    
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
    </div>

    <button type="submit">Register</button>
</form>
