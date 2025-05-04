<?php
$type = $_POST['payment_method']; // 'gcash', 'grab_pay', 'maya'

$fields = [
    'data' => [
        'attributes' => [
            'amount' => (int)$amount,
            'redirect' => [
                'success' => "http://yourdomain.com/payment_success.php?item_id=$item_id&qty=$quantity",
                'failed' => "http://yourdomain.com/payment_failed.php"
            ],
            'type' => $type,
            'currency' => 'PHP'
        ]
    ]
];
?>
