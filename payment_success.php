<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$username = $_SESSION['username'];

if (isset($_SESSION['cart_checkout']) && count($_SESSION['cart_checkout']) > 0) {
    // Handle multiple items from cart
    $cart = $_SESSION['cart_checkout'];

    foreach ($cart as $item) {
        $item_id = $item['id'];
        $item_name = $item['name'];
        $quantity = $item['quantity'];
        $total_amount = $item['price'] * $quantity;

        // Insert into orders
        $stmt = $conn->prepare("INSERT INTO orders (username, item_id, item_name, quantity, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, 'gcash', 'paid')");
        $stmt->bind_param("sisid", $username, $item_id, $item_name, $quantity, $total_amount);
        $stmt->execute();

        // Reduce stock
        $reduceStock = $conn->prepare("UPDATE items SET available_quantity = available_quantity - ? WHERE id = ?");
        $reduceStock->bind_param("ii", $quantity, $item_id);
        $reduceStock->execute();
    }

    // Clear cart session
    unset($_SESSION['cart'], $_SESSION['cart_checkout'], $_SESSION['checkout_total']);

} elseif (
    isset($_SESSION['last_item_id']) &&
    isset($_SESSION['last_item_name']) &&
    isset($_SESSION['last_quantity']) &&
    isset($_SESSION['last_total_amount'])
) {
    // Handle single item purchase
    $item_id = $_SESSION['last_item_id'];
    $item_name = $_SESSION['last_item_name'];
    $quantity = $_SESSION['last_quantity'];
    $total_amount = $_SESSION['last_total_amount'];

    // Insert into orders
    $stmt = $conn->prepare("INSERT INTO orders (username, item_id, item_name, quantity, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, 'gcash', 'paid')");
    $stmt->bind_param("sisid", $username, $item_id, $item_name, $quantity, $total_amount);
    $stmt->execute();

    // Reduce stock
    $reduceStock = $conn->prepare("UPDATE items SET available_quantity = available_quantity - ? WHERE id = ?");
    $reduceStock->bind_param("ii", $quantity, $item_id);
    $reduceStock->execute();

    // Clear single item session
    unset($_SESSION['last_item_id'], $_SESSION['last_item_name'], $_SESSION['last_quantity'], $_SESSION['last_total_amount']);
} else {
    // No valid purchase found
    header('Location: user_product_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: '✅ Payment Successful!',
                text: 'Redirecting to your product dashboard...',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });

            setTimeout(function () {
                window.location.href = 'user_product_dashboard.php';
            }, 3000);
        });
    </script>
</head>
<body style="background-color:#f0f9f7;">
</body>
</html>
<script>
  gtag('event', 'purchase', {
    transaction_id: '<?= uniqid() ?>',  // unique ID for the order
    value: <?= $totalAmount ?>,         // PHP variable for total amount
    currency: 'PHP',
    items: [
      {
        item_id: '<?= $itemId ?>',
        item_name: '<?= $itemName ?>',
        quantity: <?= $quantity ?>,
        price: <?= $price ?>
      }
    ]
  });
</script>
