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
    /**
     * Upload file vào thư mục chỉ định
     * @param string $folder Thư mục con trong assets/uploads (vd: 'tours', 'guides')
     * @param array $file Mảng $_FILES['field_name']
     * @return string Đường dẫn tương đối của file đã upload
     * @throws Exception Nếu upload thất bại
     */
    function upload_file($folder, $file)
    {
        // Kiểm tra file có được upload không
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('File không hợp lệ!');
        }

        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File vượt quá kích thước cho phép trong php.ini',
                UPLOAD_ERR_FORM_SIZE => 'File vượt quá kích thước cho phép',
                UPLOAD_ERR_PARTIAL => 'File chỉ được upload một phần',
                UPLOAD_ERR_NO_FILE => 'Không có file nào được upload',
                UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
                UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file vào đĩa',
                UPLOAD_ERR_EXTENSION => 'Extension PHP đã dừng upload file',
            ];
            throw new Exception($errorMessages[$file['error']] ?? 'Lỗi không xác định khi upload file!');
        }

        // Tạo (hoặc chuẩn hóa) đường dẫn upload
        $folder = trim($folder, "/ \\;");
        $uploadDir = rtrim(PATH_ASSETS_UPLOADS, "/\\") . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Không thể tạo thư mục upload! Kiểm tra quyền ghi.');
            }
        }

        // Kiểm tra quyền ghi
        if (!is_writable($uploadDir)) {
            throw new Exception('Thư mục upload không có quyền ghi!');
        }

        // Validate loại file (chỉ cho phép ảnh)
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Chỉ cho phép upload file ảnh (JPG, PNG, GIF, WEBP)!');
        }

        // Validate kích thước file (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            throw new Exception('File không được vượt quá 5MB!');
        }

        // Tạo tên file unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '-' . uniqid() . '.' . strtolower($extension);
        $relativePath = $folder . '/' . $filename;
        $fullPath = $uploadDir . $filename;

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return str_replace('\\', '/', $relativePath);
        }

        throw new Exception('Upload file không thành công! Kiểm tra quyền ghi thư mục.');
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
        return in_array($r, ['HDV', 'HVD', 'GUIDE'], true);
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

/**
 * Format duration to display as "X ngày Y đêm"
 */
if (!function_exists('formatDuration')) {
    function formatDuration($days)
    {
        $days = (int) $days;
        $nights = max(0, $days - 1);
        return "{$days} ngày {$nights} đêm";
    }
}

/**
 * Format date range for tour display
 * Example: formatDateRange('2024-12-23', '2024-12-25') => "23-25/12/2024"
 */
if (!function_exists('formatDateRange')) {
    function formatDateRange($startDate, $endDate)
    {
        try {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);

            if ($start->format('Y-m') === $end->format('Y-m')) {
                // Cùng tháng: "23-25/12/2024"
                return $start->format('d') . '-' . $end->format('d/m/Y');
            } else if ($start->format('Y') === $end->format('Y')) {
                // Cùng năm: "30/12-02/01/2024"
                return $start->format('d/m') . '-' . $end->format('d/m/Y');
            } else {
                // Khác năm: "30/12/2023-02/01/2024"
                return $start->format('d/m/Y') . '-' . $end->format('d/m/Y');
            }
        } catch (Exception $e) {
            return $startDate . ' - ' . $endDate;
        }
    }
}
