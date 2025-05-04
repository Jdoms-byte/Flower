<?php
session_start();
include 'db.php'; // Make sure this file connects to your DB

// Get username from session
$username = $_SESSION['username'];

// Fetch user ID from database
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Store user ID in session
if ($user) {
    $_SESSION['user_id'] = $user['id'];
} else {
    echo "User not found.";
    exit();
}

// Store order-related session variables
$_SESSION['last_item_id'] = $_POST['item_id'];
$_SESSION['last_item_name'] = $_POST['description'];
$_SESSION['last_quantity'] = $_POST['quantity'];
$_SESSION['last_total_amount'] = ($_POST['amount'] / 100) * $_POST['quantity'];

// Prepare PayMongo checkout session
$secretKey = 'sk_test_H7ofdpu9iQ4NRRPpRfKevNK5';

$data = [
    'data' => [
        'attributes' => [
            'line_items' => [[
                'currency' => 'PHP',
                'amount' => intval($_POST['amount']),
                'name' => $_POST['description'],
                'quantity' => intval($_POST['quantity']),
            ]],
            'payment_method_types' => ['gcash'],
            'success_url' => 'http://localhost:8080/Flower%20shop/payment_success.php',
            'cancel_url' => 'http://localhost:8080/Flower%20shop/payment_cancelled.php'
        ]
    ]
];

$ch = curl_init('https://api.paymongo.com/v1/checkout_sessions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($secretKey . ':'), 
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseData = json_decode($response, true);

if ($http_status === 200 || $http_status === 201) {
    header('Location: ' . $responseData['data']['attributes']['checkout_url']);
    exit();
} else {
    echo "Payment Failed:<br><pre>";
    print_r($responseData);
    echo "</pre>";
}
?>
