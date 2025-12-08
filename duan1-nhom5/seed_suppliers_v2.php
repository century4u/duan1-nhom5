<?php
require_once 'configs/env.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Inserting test row...\n";
    $pdo->exec("INSERT INTO suppliers (name, type, status) VALUES ('Test Supplier', 'hotel', 1)");
    echo "Success!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
