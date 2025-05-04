<?php
session_start();
include 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}

// Fetch items
$sql = "SELECT * FROM items ORDER BY created_at DESC";
$result = $conn->query($sql);
$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            margin: 0;
        }

        .sidebar {
            width: 240px;
            background-color: #ffffff;
            padding: 2rem 1rem;
            border-right: 1px solid #e0e0e0;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            color: #207cd7;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.4rem;
        }

        .nav-link {
            font-weight: 500;
            color: #34495e;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: #f0f4ff;
            color: #2e6de4 !important;
        }

        .main-content {
            margin-left: 240px;
            padding: 20px;
            flex: 1;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .dashboard-header h1 {
            margin: 0;
            color: #2c3e50;
        }

        .welcome {
            font-weight: 500;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            width: 280px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card-content {
            padding: 15px;
            flex: 1;
        }

        .description {
            font-size: 16px;
            margin-bottom: 8px;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
            margin-bottom: 5px;
        }

        .available-quantity {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            padding: 0 15px 15px;
        }

        .btn {
            border: none;
            padding: 7px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-update {
            background-color: #f39c12;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-update:hover {
            background-color: #d68910;
        }

        .add-new {
            margin-top: 30px;
        }

        .add-new a {
            text-decoration: none;
            background-color: #2980b9;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .add-new a:hover {
            background-color: #21618c;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Flower Shop</h2>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_dashboard.php"><i class="fas fa-box-open me-2"></i> Manage Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_orders.php"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="manage_users.php"><i class="fas fa-users me-2"></i> Customers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
        </li>
    </ul>
</div>


<!-- Main Content -->
<div class="main-content">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <div class="welcome">
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!
        </div>
    </div>

    <div class="grid">
        <?php foreach ($items as $item): ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image">
            <div class="card-content">
                <div class="description"><?php echo htmlspecialchars($item['description']); ?></div>
                <div class="price">₱<?php echo number_format($item['price'], 2); ?></div>
                <div class="available-quantity">Available: <?php echo $item['available_quantity']; ?></div>
            </div>
            <div class="actions">
                <form method="post" action="delete_item.php">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <button type="submit" class="btn btn-delete">Delete</button>
                </form>
                <form method="get" action="edit_item.php">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <button type="submit" class="btn btn-update">Update</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="add-new text-center mt-4">
        <a href="add_item.php">+ Add New Item</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
