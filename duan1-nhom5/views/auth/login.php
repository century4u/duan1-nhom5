<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Đăng nhập' ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="auth-card">
                    <div class="auth-header">
                        <h4>Chào mừng trở lại!</h4>
                        <p>Vui lòng đăng nhập để tiếp tục</p>
                    </div>
                    
                    <div class="auth-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                                <?php unset($_SESSION['success']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
                                <?php unset($_SESSION['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['errors'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($_SESSION['errors'] as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php unset($_SESSION['errors']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?= BASE_URL ?>?action=login/process">
                            <div class="mb-4">
                                <label for="username" class="form-label">Tên đăng nhập / Email</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="bi bi-person text-secondary"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= htmlspecialchars($_SESSION['old_username'] ?? '') ?>" 
                                           placeholder="Nhập username hoặc email" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i class="bi bi-lock text-secondary"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Nhập mật khẩu" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                    <label class="form-check-label text-secondary small" for="remember">Ghi nhớ</label>
                                </div>
                                <a href="<?= BASE_URL ?>?action=forgot-password" class="text-decoration-none small">Quên mật khẩu?</a>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary text-uppercase">Đăng nhập</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="auth-footer">
                        <p class="mb-0 text-secondary small">Chưa có tài khoản? <a href="<?= BASE_URL ?>?action=register">Đăng ký ngay</a></p>
                        <div class="mt-3">
                             <a href="<?= BASE_URL ?>" class="text-secondary small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Về trang chủ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php unset($_SESSION['old_username']); ?>
</body>
</html>
