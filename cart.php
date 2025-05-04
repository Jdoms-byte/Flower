<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .cart-container {
            margin-top: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            display: flex;
            align-items: center;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 8px;
        }

        .cart-item .item-details {
            flex: 1;
        }

        .cart-item .item-details h5 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .cart-item .item-details p {
            color: #777;
        }

        .cart-item .item-details .price {
            font-size: 16px;
            color: #27ae60;
            font-weight: bold;
        }

        .total-section {
            border-top: 2px solid #ddd;
            padding-top: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }

        .btn-custom {
            width: 200px;
            padding: 10px;
            margin-top: 20px;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn-success-custom {
            background-color: #27ae60;
            color: white;
        }

        .btn-success-custom:hover {
            background-color: #2ecc71;
        }

        .btn-danger-custom {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger-custom:hover {
            background-color: #c0392b;
        }

        .btn-secondary-custom {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary-custom:hover {
            background-color: #7f8c8d;
        }

        .empty-cart {
            text-align: center;
            font-size: 18px;
            padding: 50px 0;
        }
    </style>
</head>
<body>

<div class="container cart-container">

    <h2 class="mb-4">Shopping Cart</h2>

    <?php if (count($cart) > 0): ?>
        <?php foreach ($cart as $item): ?>
            <?php $subtotal = $item['price'] * $item['quantity']; ?>
            <?php $total += $subtotal; ?>

            <div class="cart-item">
    <!-- Use a default image if the product image is missing -->
    <img src="<?php echo isset($item['image']) ? htmlspecialchars($item['image']) : 'path/to/default-image.jpg'; ?>" alt="Product Image">
    <div class="item-details">
        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
        <p><?php echo isset($item['description']) ? htmlspecialchars($item['description']) : 'No description available'; ?></p>
        <div class="price">₱<?php echo number_format($item['price'], 2); ?></div>
        <div class="quantity">Quantity: <?php echo $item['quantity']; ?></div>
        <div class="subtotal">Subtotal: ₱<?php echo number_format($subtotal, 2); ?></div>
    </div>
</div>

        <?php endforeach; ?>

        <div class="total-section">
            <p>Total: ₱<?php echo number_format($total, 2); ?></p>
        </div>

        <div class="d-flex justify-content-between">
            <a href="checkout.php" class="btn btn-custom btn-success-custom">Proceed to Checkout</a>
            <a href="clear_cart.php" class="btn btn-custom btn-danger-custom">Clear Cart</a>
        </div>
        <a href="user_product_dashboard.php" class="btn btn-custom btn-secondary-custom">Continue Shopping</a>
    <?php else: ?>
        <div class="empty-cart">
            <p>Your cart is empty.</p>
            <a href="user_product_dashboard.php" class="btn btn-primary">Go Back to Products</a>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
