<?php
require_once 'configs/env.php';

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$suppliers = [
    [
        'name' => 'Nhà xe Thành Bưởi',
        'type' => 'transport',
        'contact_person' => 'Nguyễn Văn A',
        'phone' => '0901234567',
        'email' => 'contact@thanhbuoi.com',
        'address' => '266 Lê Hồng Phong',
        'status' => 1
    ],
    [
        'name' => 'Xe du lịch Minh Trí',
        'type' => 'transport',
        'contact_person' => 'Trần Văn B',
        'phone' => '0909888777',
        'address' => 'Hà Nội',
        'status' => 1
    ],
    [
        'name' => 'Khách sạn Mường Thanh',
        'type' => 'hotel',
        'contact_person' => 'Lễ tân',
        'phone' => '0243123456',
        'address' => 'Đà Nẵng',
        'status' => 1
    ],
    [
        'name' => 'Khách sạn Novotel',
        'type' => 'hotel',
        'contact_person' => 'Manager',
        'phone' => '0243999888',
        'address' => 'Nha Trang',
        'status' => 1
    ],
    [
        'name' => 'Nhà hàng Biển Đông',
        'type' => 'restaurant',
        'contact_person' => 'Chủ quán',
        'phone' => '0912341234',
        'address' => 'Vũng Tàu',
        'status' => 1
    ],
    [
        'name' => 'Cơm niêu Sài Gòn',
        'type' => 'restaurant',
        'contact_person' => 'Quản lý',
        'phone' => '0998887776',
        'address' => 'TP.HCM',
        'status' => 1
    ]
];

echo "Seeding suppliers v3...\n";

$stmt = $pdo->prepare("INSERT INTO suppliers (name, type, contact_person, phone, email, address, status) VALUES (:name, :type, :contact_person, :phone, :email, :address, :status)");

foreach ($suppliers as $s) {
    // Check if exists
    $check = $pdo->prepare("SELECT id FROM suppliers WHERE name = ?");
    $check->execute([$s['name']]);
    if ($check->rowCount() > 0) {
        echo "Skipped: " . $s['name'] . " (Already exists)\n";
        continue;
    }

    try {
        $stmt->execute([
            'name' => $s['name'],
            'type' => $s['type'],
            'contact_person' => $s['contact_person'],
            'phone' => $s['phone'],
            'email' => $s['email'] ?? null,
            'address' => $s['address'],
            'status' => $s['status']
        ]);
        echo "Inserted: " . $s['name'] . "\n";
    } catch (Exception $e) {
        echo "Error inserting " . $s['name'] . ": " . $e->getMessage() . "\n";
    }
}

echo "Done.";
