<?php
session_start();
include 'db.php'; // connect to your DB

// Fetch items (or a specific item if passed with ID)
$item_id = $_GET['id'] ?? null;

if ($item_id) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "No item selected.";
    exit();
}
?>

<h2>Buy <?php echo htmlspecialchars($item['description']); ?></h2>
<p>Price: ₱<?php echo number_format($item['price'], 2); ?></p>
<p>Available: <?php echo $item['available_quantity']; ?></p>

<?php if ($item['available_quantity'] > 0): ?>
<form method="post" action="create_gcash_payment.php">
    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
    <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
    <input type="hidden" name="description" value="<?php echo htmlspecialchars($item['description']); ?>">

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" min="1" max="<?php echo $item['available_quantity']; ?>" required>

    <button type="submit">Pay with GCash</button>
</form>
<?php else: ?>
    <p style="color: red;">Out of stock</p>
<?php endif; ?>

