<?php
// Connect to database
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "flowershopdb";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form inputs
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Escape to prevent SQL Injection
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// Insert into users table
$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

if ($conn->query($sql) === TRUE) {
    echo "<script>
        alert('Registration successful! You can now login.');
        window.location.href = 'index.html';
    </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
