<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . uniqid() . '_' . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $available_quantity = $_POST['available_quantity'] ?? 0;

    // Insert into database, now with available_quantity
    $stmt = $conn->prepare("INSERT INTO items (image, description, price, available_quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $imagePath, $description, $price, $available_quantity); // i = integer for quantity

    if ($stmt->execute()) {
        echo "<p>Item added successfully!</p>";
        echo "<p><a href='admin_dashboard.php'>Return to Dashboard</a></p>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!-- HTML Form -->
<form action="add_item.php" method="post" enctype="multipart/form-data">
    <!-- Image Upload -->
    <label for="image">Upload Image:</label>
    <input type="file" name="image" id="image" required><br><br>

    <!-- Description -->
    <label for="description">Description:</label><br>
    <textarea name="description" id="description" rows="4" required></textarea><br><br>

    <!-- Price -->
    <label for="price">Price (₱):</label><br>
    <input type="number" name="price" id="price" step="0.01" required><br><br>

    <!-- Available Quantity -->
    <label for="available_quantity">Available Quantity:</label><br>
    <input type="number" name="available_quantity" id="available_quantity" min="0" required><br><br>

    <!-- Submit Button -->
    <button type="submit">Add Item</button>
</form>
