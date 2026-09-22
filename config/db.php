<?php
// config/db.php

mysqli_report(MYSQLI_REPORT_OFF);

$host = '127.0.0.1';
$user = 'root';
$dbname = 'seapearl_bistro';
$conn = null;
$db_error = null;

$connection_attempts = [
    ['host' => '127.0.0.1', 'port' => 3306, 'password' => ''],
    ['host' => '127.0.0.1', 'port' => 3307, 'password' => ''],
    ['host' => '127.0.0.1', 'port' => 3306, 'password' => 'root'],
    ['host' => '127.0.0.1', 'port' => 3307, 'password' => 'root'],
    ['host' => 'localhost', 'port' => 3306, 'password' => ''],
    ['host' => 'localhost', 'port' => 3306, 'password' => 'root'],
];

foreach ($connection_attempts as $attempt) {
    $host_to_try = $attempt['host'] ?? $host;
    $port = (int) $attempt['port'];
    $password = $attempt['password'];

    $temp_conn = @mysqli_init();
    if ($temp_conn) {
        $temp_conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 2);
        $connected = @$temp_conn->real_connect($host_to_try, $user, $password, $dbname, $port);

        if ($connected) {
            $conn = $temp_conn;
            $conn->set_charset('utf8mb4');
            break;
        }

        $db_error = $temp_conn->connect_error ?: 'Unable to connect to the database server.';
    }
}

if ($conn === null) {
    $db_error = $db_error ?: 'Unable to connect to the database server.';
}

if (!function_exists('get_db_error')) {
    function get_db_error()
    {
        global $db_error;
        return $db_error;
    }
}

if (!function_exists('get_db_connection')) {
    function get_db_connection()
    {
        global $conn;
        return $conn;
    }
}
