<?php
// Lấy giao thức
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

// Lấy host + port hiện tại
$host = $_SERVER['HTTP_HOST']; // ví dụ: localhost:8080

// Tự động lấy thư mục project từ SCRIPT_NAME
$scriptName = $_SERVER['SCRIPT_NAME']; // ví dụ: /duan1-nhom5/index.php
$dir = str_replace(basename($scriptName), '', $scriptName); // /duan1-nhom5/

// BASE_URL động
define('BASE_URL', "$protocol://$host$dir");

// URL cho assets upload
define('BASE_ASSETS_UPLOADS', BASE_URL . 'assets/uploads/');

// Đường dẫn tuyệt đối trong server
define('PATH_ROOT', __DIR__ . '/../');
define('PATH_VIEW', PATH_ROOT . 'views/');
define('PATH_VIEW_MAIN', PATH_ROOT . 'views/main.php');
define('PATH_ASSETS_UPLOADS', PATH_ROOT . 'assets/uploads/');
define('PATH_CONTROLLER', PATH_ROOT . 'controllers/');
define('PATH_MODEL', PATH_ROOT . 'models/');

// Cấu hình Database
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'nhom5');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
