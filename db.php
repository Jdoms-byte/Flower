<?php
$servername = "localhost";
$username = "root"; // (default for XAMPP/MAMP)
$password = "";     // (default for XAMPP/MAMP)
$database = "flowershopdb"; // <-- your existing database

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
