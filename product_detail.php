<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

if (!isset($_GET['id'])) {
    die('Product not found.');
}

$item_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die('Product not found.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            overflow-x: hidden;
        }

        .transparent-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* Transparent background */
            z-index: -1;
            transition: background 0.3s ease;
        }

        .product-container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            cursor: pointer; /* Allow image to be clicked */
        }

        .product-info {
            padding-left: 20px;
        }

        .product-info h1 {
            font-size: 32px;
            font-weight: 500;
        }

        .price {
            font-size: 24px;
            font-weight: 600;
            color: #27ae60;
        }

        .description {
            font-size: 16px;
            color: #555;
            margin: 20px 0;
        }

        .available {
            font-size: 14px;
            color: #999;
        }

        .buy-btn, .add-to-cart-btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .buy-btn:hover, .add-to-cart-btn:hover {
            background-color: #2ecc71;
        }

        .buy-btn:active, .add-to-cart-btn:active {
            background-color: #2c3e50;
        }

        .review-section {
            margin-top: 40px;
        }

        .review-section h3 {
            font-size: 24px;
            font-weight: 500;
        }

        .review {
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 8px;
        }

        .review .username {
            font-weight: bold;
            color: #333;
        }

        .review .rating {
            color: #f39c12;
        }

        .review .comment {
            margin-top: 10px;
            color: #555;
        }

        .review-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .review-form select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        .back-btn {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #3498db;
        }

        .back-btn:active {
            background-color: #1d5d91;
        }
    </style>
</head>
<body>

<div class="transparent-background" id="transparent-background"></div>

<!-- Product Detail Card -->
<div class="product-container">
    <a href="user_product_dashboard.php">
        <button class="back-btn">X</button>
    </a>

    <div class="row">
        <div class="col-md-6">
            <!-- Product Image with click event to show review -->
            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image" class="product-image" id="product-image">
        </div>
        <div class="col-md-6 product-info">
            <h1><?php echo htmlspecialchars($item['description']); ?></h1>
            <p class="price">₱<?php echo number_format($item['price'], 2); ?></p>
            <p class="available">Available: <?php echo $item['available_quantity']; ?></p>
            <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>

            <!-- Add to cart form -->
            <form method="POST" action="user_product_dashboard.php">
                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item['description']); ?>">
                <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
                Quantity: <input type="number" name="quantity" min="1" max="<?php echo $item['available_quantity']; ?>" required>
                <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
            </form>

            <!-- Buy Now form -->
         
            <form action="create_gcash_payment.php" method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="description" value="<?php echo htmlspecialchars($item['description']); ?>">
                    <input type="hidden" name="amount" value="<?php echo $item['price'] * 100; ?>"> <!-- In centavos -->
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="buy-btn">Buy Now </button>
                </form>
        </div>
    </div>

    <!-- Review Section -->
    <div class="review-section">
        <h3>Recent Reviews</h3>
        <?php
        $reviewStmt = $conn->prepare("SELECT username, rating, comment, created_at FROM reviews WHERE item_id = ? ORDER BY created_at DESC LIMIT 5");
        $reviewStmt->bind_param("i", $item_id);
        $reviewStmt->execute();
        $reviewResult = $reviewStmt->get_result();

        if ($reviewResult->num_rows > 0) {
            while ($review = $reviewResult->fetch_assoc()) {
                echo "<div class='review'>";
                echo "<p class='username'>" . htmlspecialchars($review['username']) . " <span class='rating'>" . str_repeat("★", $review['rating']) . "</span></p>";
                echo "<p class='comment'>" . htmlspecialchars($review['comment']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No reviews yet.</p>";
        }
        ?>

        <!-- Review form -->
        <h3>Leave a Review</h3>
        <form class="review-form" method="POST" action="submit_review.php">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            Rating:
            <select name="rating" required>
                <?php for ($i=1; $i<=5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?> ★</option>
                <?php endfor; ?>
            </select><br>
            <textarea name="comment" placeholder="Write your review..." required></textarea><br>
            <button type="submit">Submit Review</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Make the background transparent when clicking on the image for reviews
    document.getElementById("product-image").addEventListener("click", function() {
        document.getElementById("transparent-background").style.background = "rgba(0, 0, 0, 0.5)"; // Keep it semi-transparent
    });
</script>

</body>
</html>
