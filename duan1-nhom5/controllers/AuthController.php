<?php

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Hiển thị form đăng nhập
     */
    /**
     * Hiển thị form đăng nhập
     */
    public function login()
    {
        // Nếu đã đăng nhập, chuyển về trang phù hợp
        if (isset($_SESSION['user_id'])) {
            $role = strtoupper($_SESSION['role'] ?? '');
            if ($role === 'ADMIN') {
                header('Location: ' . BASE_URL . '?action=statistics');
            } elseif (in_array($role, ['HDV', 'HVD', 'GUIDE'])) {
                header('Location: ' . BASE_URL . '?action=hvd');
            } else {
                header('Location: ' . BASE_URL);
            }
            exit;
        }

        $title = 'Đăng nhập';
        require_once PATH_VIEW . 'auth/login.php';
    }

    /**
     * Xử lý đăng nhập
     */
    public function processLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validate
        $errors = [];
        if (empty($username)) {
            $errors[] = 'Vui lòng nhập tên đăng nhập hoặc email!';
        }
        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu!';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_username'] = $username;
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        // Xác thực
        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            // Lưu thông tin user vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Remember me (lưu cookie - tùy chọn)
            if ($remember) {
                // TODO: Implement remember me
            }

            $_SESSION['success'] = 'Đăng nhập thành công!';

            // Xử lý chuyển hướng dựa trên Role
            $redirect = $_GET['redirect'] ?? null;

            if ($redirect) {
                header('Location: ' . $redirect);
                exit;
            }

            // Role redirection
            $role = strtoupper($user['role']);
            if ($role === 'ADMIN') {
                header('Location: ' . BASE_URL . '?action=statistics');
            } elseif ($role === 'HDV' || $role === 'HVD' || $role === 'GUIDE') {
                header('Location: ' . BASE_URL . '?action=hvd');
            } else {
                header('Location: ' . BASE_URL);
            }
            exit;
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
            $_SESSION['old_username'] = $username;
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    /**
     * Hiển thị form đăng ký
     */
    public function register()
    {
        // Nếu đã đăng nhập, chuyển về trang chủ
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL);
            exit;
        }

        $title = 'Đăng ký';
        require_once PATH_VIEW . 'auth/register.php';
    }

    /**
     * Xử lý đăng ký
     */
    /**
     * Xử lý đăng ký
     */
    public function processRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        // Auto-generate username from email
        $usernameParts = explode('@', $email);
        $username = $usernameParts[0];
        // Sanitize username to allow only alphanumeric and underscore (matching current validation)
        $username = preg_replace('/[^a-zA-Z0-9_]/', '_', $username);

        // Ensure minimum length
        if (strlen($username) < 3) {
            $username .= '_' . rand(100, 999);
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => $username // Use username as default display name
        ];

        // Validate
        $errors = $this->validateRegister($data);

        // Check Guide Code if provided
        $guideCode = trim($_POST['guide_code'] ?? '');
        $guideToLink = null;

        if (!empty($guideCode)) {
            require_once PATH_MODEL . 'GuideModel.php';
            $guideModel = new GuideModel();
            $guideToLink = $guideModel->findUnclaimedByCode($guideCode);

            if (!$guideToLink) {
                $errors[] = 'Mã HDV không hợp lệ hoặc tài khoản này đã được liên kết!';
            } else {
                // Set role intent if guide code is valid
                $data['role'] = 'HDV'; // Or GUIDE, assuming HDV is supported
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            // Preserve guide code in old data if needed, key 'guide_code' is not in $data array above
            $_SESSION['old_data']['guide_code'] = $guideCode;
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }

        // Kiểm tra username/email đã tồn tại chưa
        if ($this->userModel->exists($data['username'], $data['email'])) {
            if ($this->userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Email đã tồn tại!';
            } elseif ($this->userModel->findByUsername($username)) {
                $data['username'] .= '_' . rand(1000, 9999);
                if ($this->userModel->exists($data['username'], $data['email'])) {
                    $_SESSION['error'] = 'Tên đăng nhập hoặc email đã tồn tại!';
                }
            } else {
                $_SESSION['error'] = 'Tên đăng nhập hoặc email đã tồn tại!';
            }

            if (isset($_SESSION['error'])) {
                $_SESSION['old_data'] = $data;
                $_SESSION['old_data']['guide_code'] = $guideCode;
                header('Location: ' . BASE_URL . '?action=register');
                exit;
            }
        }

        // Mã hóa mật khẩu
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Tạo user mới
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'full_name' => $data['full_name'],
            'role' => $data['role'] ?? 'USER',
            'status' => 1
        ];

        $userId = $this->userModel->create($userData);

        if ($userId) {
            // Link guide if applicable
            if ($guideToLink) {
                $guideModel->linkUser($guideToLink['id'], $userId);
            }

            $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi đăng ký!';
            $_SESSION['old_data'] = $data;
            $_SESSION['old_data']['guide_code'] = $guideCode;
            header('Location: ' . BASE_URL . '?action=register');
            exit;
        }
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        // Xóa tất cả session
        session_unset();
        session_destroy();

        // Bắt đầu session mới
        session_start();

        $_SESSION['success'] = 'Đăng xuất thành công!';
        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Quên mật khẩu
     */
    public function forgotPassword()
    {
        $title = 'Quên mật khẩu';
        $view = 'auth/forgot-password';
        require_once PATH_VIEW_MAIN;
    }

    /**
     * Validate dữ liệu đăng ký
     */
    private function validateRegister($data)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'Tên đăng nhập không được để trống!';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự!';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới!';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email không được để trống!';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        }

        if (empty($data['password'])) {
            $errors[] = 'Mật khẩu không được để trống!';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        }

        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Mật khẩu xác nhận không khớp!';
        }

        if (empty($data['full_name'])) {
            $errors[] = 'Họ và tên không được để trống!';
        }

        return $errors;
    }
}
