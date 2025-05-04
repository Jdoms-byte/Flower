<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: user_product_dashboard.php');
    exit();
}

$secretKey = 'sk_test_H7ofdpu9iQ4NRRPpRfKevNK5';
$username = $_SESSION['username'];
$cart = $_SESSION['cart'];

$line_items = [];
$total_amount = 0;

foreach ($cart as $item) {
    $amount = intval($item['price'] * 100); // Convert to cents
    $quantity = intval($item['quantity']);
    $total_amount += ($amount * $quantity);

    $line_items[] = [
        'currency' => 'PHP',
        'amount' => $amount,
        'name' => $item['name'],
        'quantity' => $quantity
    ];
}

// Create PayMongo checkout session
$data = [
    'data' => [
        'attributes' => [
            'line_items' => $line_items,
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
    $_SESSION['checkout_total'] = $total_amount;
    $_SESSION['cart_checkout'] = $cart; // Store cart to use in success page
    header('Location: ' . $responseData['data']['attributes']['checkout_url']);
    exit();
} else {
    echo "Payment Failed:<br><pre>";
    print_r($responseData);
    echo "</pre>";
}
?>
