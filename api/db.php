<?php
// api/db.php

header('Content-Type: application/json');

// Enable error reporting to be caught in JSON if we want, or just log
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

$conn = get_db_connection();
if (!$conn) {
    echo json_encode([
        'ok' => false,
        'message' => 'Database connection failed: ' . (get_db_error() ?: 'Unknown error')
    ]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        handleRegister($conn);
        break;
    case 'login':
        handleLogin($conn);
        break;
    case 'place_order':
        handlePlaceOrder($conn);
        break;
    default:
        echo json_encode([
            'ok' => false,
            'message' => 'Invalid action'
        ]);
        break;
}

function handleRegister($conn) {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $password = $_POST['password'] ?? '';
    $delivery_address = trim($_POST['delivery_address'] ?? '');

    if (empty($username) || empty($password) || empty($full_name) || empty($mobile)) {
        echo json_encode([
            'ok' => false,
            'message' => 'All fields (Username, Name, Mobile, Password) are required.'
        ]);
        exit;
    }

    if (strlen($username) < 3) {
        echo json_encode([
            'ok' => false,
            'message' => 'Username must be at least 3 characters long.'
        ]);
        exit;
    }

    if (strlen($password) < 4) {
        echo json_encode([
            'ok' => false,
            'message' => 'Password must be at least 4 characters long.'
        ]);
        exit;
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    if (!$stmt) {
        echo json_encode([
            'ok' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
        exit;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode([
            'ok' => false,
            'message' => 'Username is already taken.'
        ]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, full_name, mobile, password_hash, delivery_address) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode([
            'ok' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
        exit;
    }
    $stmt->bind_param("sssss", $username, $full_name, $mobile, $password_hash, $delivery_address);
    if ($stmt->execute()) {
        echo json_encode([
            'ok' => true,
            'message' => 'Registration successful!'
        ]);
    } else {
        echo json_encode([
            'ok' => false,
            'message' => 'Registration failed: ' . $stmt->error
        ]);
    }
    $stmt->close();
}

function handleLogin($conn) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode([
            'ok' => false,
            'message' => 'Username and password are required.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, username, full_name, mobile, password_hash, delivery_address FROM users WHERE username = ?");
    if (!$stmt) {
        echo json_encode([
            'ok' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
        exit;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'ok' => false,
            'message' => 'Invalid username or password.'
        ]);
        $stmt->close();
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    if (password_verify($password, $user['password_hash'])) {
        echo json_encode([
            'ok' => true,
            'user' => [
                'user_id' => (int)$user['user_id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'mobile' => $user['mobile'],
                'delivery_address' => $user['delivery_address']
            ]
        ]);
    } else {
        echo json_encode([
            'ok' => false,
            'message' => 'Invalid username or password.'
        ]);
    }
}

function handlePlaceOrder($conn) {
    $user_id = $_POST['user_id'] ?? null;
    if (empty($user_id) || $user_id === 'null') {
        $user_id = null;
    } else {
        $user_id = (int)$user_id;
    }

    $delivery_name = trim($_POST['delivery_name'] ?? '');
    $delivery_phone = trim($_POST['delivery_phone'] ?? '');
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $subtotal = (float)($_POST['subtotal'] ?? 0);
    $hospitality_charge = (float)($_POST['hospitality_charge'] ?? 0);
    $delivery_fee = (float)($_POST['delivery_fee'] ?? 0);
    $total_amount = (float)($_POST['total_amount'] ?? 0);
    $items_raw = $_POST['items'] ?? '[]';

    if (empty($delivery_name) || empty($delivery_phone) || empty($delivery_address)) {
        echo json_encode([
            'ok' => false,
            'message' => 'Delivery name, phone, and address are required.'
        ]);
        exit;
    }

    $items = json_decode($items_raw, true);
    if (!is_array($items) || empty($items)) {
        echo json_encode([
            'ok' => false,
            'message' => 'Order must contain at least one item.'
        ]);
        exit;
    }

    // Determine zone_id from address
    $zone_id = null;
    $zones_query = $conn->query("SELECT zone_id, zone_name FROM delivery_zones");
    if ($zones_query) {
        while ($row = $zones_query->fetch_assoc()) {
            if (stripos($delivery_address, $row['zone_name']) !== false) {
                $zone_id = (int)$row['zone_id'];
                break;
            }
        }
    }

    // Generate unique order ID: ORD-XXXXXXXX (8 hex chars)
    $order_id = '';
    $attempts = 0;
    while ($attempts < 10) {
        $candidate_id = 'ORD-' . strtoupper(bin2hex(random_bytes(4)));
        $check_stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_id = ?");
        $check_stmt->bind_param("s", $candidate_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows === 0) {
            $order_id = $candidate_id;
            $check_stmt->close();
            break;
        }
        $check_stmt->close();
        $attempts++;
    }

    if (empty($order_id)) {
        echo json_encode([
            'ok' => false,
            'message' => 'Failed to generate a unique order ID.'
        ]);
        exit;
    }

    // Start Transaction
    $conn->begin_transaction();

    try {
        // Insert into orders
        $stmt = $conn->prepare("INSERT INTO orders (order_id, user_id, zone_id, delivery_name, delivery_phone, delivery_address, payment_method, subtotal, hospitality_charge, delivery_fee, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Received')");
        if (!$stmt) {
            throw new Exception("Prepare failed for order: " . $conn->error);
        }
        $stmt->bind_param("siissssdddd", $order_id, $user_id, $zone_id, $delivery_name, $delivery_phone, $delivery_address, $payment_method, $subtotal, $hospitality_charge, $delivery_fee, $total_amount);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for order: " . $stmt->error);
        }
        $stmt->close();

        // Insert order items
        foreach ($items as $item) {
            $item_id = $item['item_id'] ?? '';
            $quantity = (int)($item['quantity'] ?? 1);
            $unit_price = (float)($item['unit_price'] ?? 0);
            $customizations = trim($item['customizations'] ?? 'Original Portion');

            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, unit_price, customizations) VALUES (?, ?, ?, ?, ?)");
            if (!$item_stmt) {
                throw new Exception("Prepare failed for order items: " . $conn->error);
            }
            $item_stmt->bind_param("ssids", $order_id, $item_id, $quantity, $unit_price, $customizations);
            if (!$item_stmt->execute()) {
                throw new Exception("Execute failed for order items: " . $item_stmt->error);
            }
            $item_stmt->close();
        }

        $conn->commit();

        echo json_encode([
            'ok' => true,
            'order_id' => $order_id
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'ok' => false,
            'message' => 'Failed to place order: ' . $e->getMessage()
        ]);
    }
}
