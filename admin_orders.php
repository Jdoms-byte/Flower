<?php
session_start();
include 'db.php';

// Optional: Restrict to admin only
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: index.html');
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
echo "<h2>All User Orders</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Order ID</th><th>User</th><th>Item</th><th>Quantity</th><th>Total</th><th>Status</th><th>Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['username']}</td>
            <td>{$row['item_name']}</td>
            <td>{$row['quantity']}</td>
            <td>₱" . number_format($row['total_amount'], 2) . "</td>
            <td>{$row['status']}</td>
            <td>{$row['created_at']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "No orders found.";
}
if ($_SESSION['username'] === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM orders ORDER BY created_at DESC");
} else {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $_SESSION['username']);
}

?>

<!-- Back to Admin Dashboard -->
<div style="margin-top: 20px;">
    <a href="admin_dashboard.php" style="display:inline-block; background:#2c3e50; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;">← Back to Dashboard</a>
</div>
