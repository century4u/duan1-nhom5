<?php

if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        $targetDir = PATH_ASSETS_UPLOADS . $folder . '/';

        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $targetFile = $folder . '/' . time() . '-' . $file["name"];

        if (move_uploaded_file($file["tmp_name"], PATH_ASSETS_UPLOADS . $targetFile)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công!');
    }
}

if (!function_exists('isLoggedIn')) {
    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Kiểm tra user có phải admin không
     */
    function isAdmin()
    {
        return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
    }
}

if (!function_exists('requireLogin')) {
    /**
     * Yêu cầu đăng nhập, nếu chưa đăng nhập thì chuyển về trang login
     */
    function requireLogin()
    {
        if (!isLoggedIn()) {
            $redirect = BASE_URL . '?action=login&redirect=' . urlencode($_SERVER['REQUEST_URI']);
            header('Location: ' . $redirect);
            exit;
        }
    }
}

if (!function_exists('requireAdmin')) {
    /**
     * Yêu cầu quyền admin
     */
    function requireAdmin()
    {
        requireLogin();
        if (!isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}

if (!function_exists('isHvd')) {
    /**
     * Kiểm tra user có phải HDV không
     */
    function isHvd()
    {
        if (!isLoggedIn() || !isset($_SESSION['role']))
            return false;
        $r = strtoupper(trim($_SESSION['role'] ?? ''));
        return in_array($r, ['HDV', 'HVD'], true);
    }
}

if (!function_exists('requireHvd')) {
    /**
     * Yêu cầu quyền HDV
     */
    function requireHvd()
    {
        requireLogin();
        if (!isHvd()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}