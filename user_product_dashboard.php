<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

// Fetch items
$sql = "SELECT * FROM items ORDER BY created_at DESC";
$result = $conn->query($sql);
$items = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

// Inside the add-to-cart logic in user_product_dashboard.php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $quantity = intval($_POST['quantity']);

    $check = $conn->prepare("SELECT available_quantity, image FROM items WHERE id = ?");
    $check->bind_param("i", $item_id);
    $check->execute();
    $result = $check->get_result();
    $item = $result->fetch_assoc();

    if ($quantity > $item['available_quantity']) {
        echo "<script>alert('Not enough stock available.');</script>";
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $item_id) {
                $cart_item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $item_id,
                'name' => $item_name,
                'price' => $item_price,
                'quantity' => $quantity,
                'image' => $item['image']  // Add the image to the session
            ];
        }

        echo "<script>alert('Item added to cart!');</script>";
    }
}


// Handle Buy Now
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_now'])) {
    $item_id = $_POST['item_id'];
    $quantity = intval($_POST['quantity']);

    $check = $conn->prepare("SELECT available_quantity FROM items WHERE id = ?");
    $check->bind_param("i", $item_id);
    $check->execute();
    $result = $check->get_result();
    $item = $result->fetch_assoc();

    if ($quantity > $item['available_quantity']) {
        echo "<script>alert('Not enough stock available.');</script>";
    } else {
        header("Location: purchase_item.php?id=$item_id&quantity=$quantity");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 0 10px;
        }
        .add-to-cart-btn {
    background-color: #27ae60; /* Green color */
    color: white;
    border: none;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 160px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: inline-block;
    width: 100%;
}

.add-to-cart-btn:hover {
    background-color: #2ecc71; /* Lighter green for hover */
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.add-to-cart-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}



        .box {
            flex: 1 1 calc(100% - 40px);
            max-width: 300px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.2s;
        }

        @media (min-width: 576px) {
            .box {
                flex: 1 1 calc(50% - 40px);
            }
        }

        @media (min-width: 768px) {
            .box {
                flex: 1 1 calc(33.333% - 40px);
            }
        }

        @media (min-width: 992px) {
            .box {
                flex: 1 1 calc(25% - 40px);
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f0f2f5;
            background-image: url(image/productbackground.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed; /* This makes the background image fixed */
        }


        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2c3e50;
            color: #fff;
            border-radius: 5px;
            margin-bottom: 25px;
        }

        .header a {
            color: #ecf0f1;
            text-decoration: none;
            font-weight: bold;
        }

        .grid {
            margin-top: 110px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
        }

        .box {
    flex: 1 1 calc(100% - 40px);
    max-width: 300px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    padding: 15px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

    .box:hover {
    background: rgba(255, 255, 255, 0.8); /* Slightly transparent background when hovering */
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}



.box img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 12px;
}

.box-content {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-grow: 1;
}

.description {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.price {
    font-size: 17px;
    color: #27ae60;
    font-weight: 600;
}

.available {
    font-size: 14px;
    color: #999;
}


        .description {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .price {
            color: #27ae60;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .available {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        form {
            margin-bottom: 10px;
        }

        .quantity-input {
            width: 60px;
            padding: 5px;
            margin-bottom: 10px;
        }

        .btn {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }


        .buy-btn {
      background-color: #e67e22;
      color: #fff;
      border: none;
      padding: 12px 24px;
      font-size: 16px;
      font-weight: 600;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      min-width: 160px;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: inline-block;
      width: 100%;
    }

    .description {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.price {
    font-size: 17px;
    color: #27ae60;
    font-weight: 600;
}

.available {
    font-size: 14px;
    color: #999;
}

    .buy-btn:hover {
      background-color: #cf711f;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .buy-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

        .cart-link {
            margin-top: 30px;
            display: inline-block;
            background: #2ecc71;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        
    </style>
</head>
<body>

<!-- Bootstrap 5 CSS & JS -->
 <link rel="stylesheet" href="src/style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<header class="header">
    <nav class="nav-container d-flex justify-content-between align-items-center p-3">
        <!-- Logo Section -->
        <div class="logo">
            <img src="image/logo in flower.png" alt="Logo" class="logo-img">
        </div>
        
        <!-- Mobile Menu Toggle Icon -->
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Navigation Links -->
        <ul class="nav-links d-flex list-unstyled mb-0">
            <li><a href="user_dashboard.php" class="nav-link active">Home</a></li>
            <li><a href="user_product_dashboard.php" class="nav-link">Product</a></li>
            <li><a href="user_dashboard.php" class="nav-link">Blogs</a></li>
            <li><a href="user_dashboard.php" class="nav-link">About Us</a></li>
            <li><a href="user_dashboard.php" class="nav-link">Contact</a></li>
            <!-- Display User's Name -->
            <li>welcome <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
        </ul>

        <!-- Enhanced Logout Button -->
        <div class="nav-actions">
            <a href="index.html" class="btn btn-outline-dark rounded-pill px-4 py-2 logout-btn">Log-out</a>
        </div>
    </nav>
</header>


<div class="grid">
    <?php foreach ($items as $item): ?>
    <a href="product_detail.php?id=<?php echo $item['id']; ?>" style="text-decoration: none; color: inherit;">
        <div class="box">
            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image">
            <div class="box-content">
                <div class="description"><?php echo htmlspecialchars($item['description']); ?></div>
                <div class="price">₱<?php echo number_format($item['price'], 2); ?></div>
                <div class="available">Available: <?php echo $item['available_quantity']; ?></div>

                <form method="POST" action="user_product_dashboard.php">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item['description']); ?>">
                    <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="<?php echo $item['available_quantity']; ?>" required>
                    <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                </form>

                <form action="create_gcash_payment.php" method="POST">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="description" value="<?php echo htmlspecialchars($item['description']); ?>">
                    <input type="hidden" name="amount" value="<?php echo $item['price'] * 100; ?>"> <!-- In centavos -->
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="buy-btn" id="checkoutbtn">Buy Now </button>
                </form>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<div>
    <a class="cart-link" href="cart.php">Go to Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?> items)</a>
</div>

</body>
</html>
