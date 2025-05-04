<?php
include 'db.php';

// Read raw request from PayMongo
$input = file_get_contents('php://input');
$payload = json_decode($input, true);

// Optional: verify PayMongo signature here for added security

if (isset($payload['data']['attributes']['status']) && $payload['data']['attributes']['status'] === 'paid') {
    $amount = $payload['data']['attributes']['amount'];
    $description = $payload['data']['attributes']['description']; // Optional
    $item_id = $payload['data']['attributes']['metadata']['item_id'];
    $quantity = $payload['data']['attributes']['metadata']['quantity'];

    if ($item_id && $quantity) {
        $stmt = $conn->prepare("UPDATE items SET available_quantity = available_quantity - ? WHERE id = ? AND available_quantity >= ?");
        $stmt->bind_param("iii", $quantity, $item_id, $quantity);
        $stmt->execute();
    }

    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or unpaid event']);
}
?>
