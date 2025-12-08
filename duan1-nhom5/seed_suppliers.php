<?php
require_once 'configs/env.php';
require_once 'models/BaseModel.php';
require_once 'models/SupplierModel.php';

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$supplierModel = new SupplierModel();
// Inject PDO manually if needed or rely on UserModel's parent constructor if it does it (BaseModel usually does)
// Checking BaseModel... usually it creates connection. 
// But here I'm using the model directly. Let's just use raw SQL to be safe and quick, or instantiate if possible.
// Actually, looking at previous code, models extend BaseModel.
// Let's assume BaseModel handles connection if we include env.php.
// But to be 100% sure in a standalone script, I'll just use the PDO instance I created.

$suppliers = [
    [
        'name' => 'Nhà xe Thành Bưởi',
        'type' => 'vehicle',
        'contact_person' => 'Nguyễn Văn A',
        'phone' => '0901234567',
        'email' => 'contact@thanhbuoi.com',
        'address' => '266 Lê Hồng Phong',
        'status' => 1
    ],
    [
        'name' => 'Xe du lịch Minh Trí',
        'type' => 'vehicle',
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

echo "Seeding suppliers...\n";

$sql = "INSERT INTO suppliers (name, type, contact_person, phone, email, address, status) VALUES (:name, :type, :contact_person, :phone, :email, :address, :status)";
$stmt = $pdo->prepare($sql);

foreach ($suppliers as $s) {
    // Check if exists
    $check = $pdo->prepare("SELECT id FROM suppliers WHERE name = ?");
    $check->execute([$s['name']]);
    if ($check->rowCount() > 0) {
        echo "Skipped: " . $s['name'] . " (Already exists)\n";
        continue;
    }

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
}

echo "Done.";
