<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

// Fetch the item to edit
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $description = $_POST['description'];
    $price = $_POST['price'];
    $available_quantity = $_POST['available_quantity'];
    $uploadDir = 'uploads/';
    $newImagePath = '';

    // If a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = basename($_FILES['image']['name']);
        $newImagePath = $uploadDir . uniqid() . '_' . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $newImagePath);

        // Update with new image
        $stmt = $conn->prepare("UPDATE items SET image = ?, description = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $newImagePath, $description, $price, $id);
    } else {
        // Update without changing image
        $stmt = $conn->prepare("UPDATE items SET description = ?, price = ? WHERE id = ?");
        $stmt->bind_param("sdi", $description, $price, $id);
    }

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating item: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        form {
            max-width: 400px;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        input[type="file"] {
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
        .back-link {
            margin-top: 20px;
            display: inline-block;
        }
    </style>
</head>
<body>

<h2>Edit Item</h2>

<form action="edit_item.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
    
    <label>Current Image:</label><br>
    <img src="<?php echo htmlspecialchars($item['image']); ?>" width="150" style="margin-bottom:10px;"><br>

    <label>Change Image (optional):</label>
    <input type="file" name="image">

    <label>Description:</label>
    <textarea name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea>

    <label>Price ($):</label>
    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($item['price']); ?>" required>

    <label for="available_quantity">Available Quantity:</label>
<input type="number" name="available_quantity" min="0" required>


    <button type="submit">Update Item</button>
</form>

<a href="dashboard.php" class="back-link">← Back to Dashboard</a>

</body>
</html>
