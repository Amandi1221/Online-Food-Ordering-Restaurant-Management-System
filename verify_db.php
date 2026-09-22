<?php
require 'config/db.php';
$conn = get_db_connection();
if (!$conn) {
    exit('NO_CONN');
}
$userResult = $conn->query("SELECT user_id, username FROM users WHERE username='copilot_test'");
$orderResult = $conn->query("SELECT order_id, total_amount FROM orders WHERE order_id='ORD-AE4B613C'");
echo 'USER_ROWS=' . $userResult->num_rows . PHP_EOL;
echo 'ORDER_ROWS=' . $orderResult->num_rows . PHP_EOL;
