<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE username = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Your Orders</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Item</th><th>Quantity</th><th>Total</th><th>Status</th><th>Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['item_name']}</td>
            <td>{$row['quantity']}</td>
            <td>₱" . number_format($row['total_amount'], 2) . "</td>
            <td>{$row['status']}</td>
            <td>{$row['created_at']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "You have no orders.";
}
?>

<!-- Back to Dashboard Button -->
<div style="margin-top: 20px;">
    <a href="admin_dashboard.php" style="display:inline-block; background:#3498db; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;">← Back to Dashboard</a>
</div>
