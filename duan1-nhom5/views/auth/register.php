<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Đăng ký tài khoản' ?></title>
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
            <div class="col-md-6 col-lg-5">
                <div class="auth-card">
                    <div class="auth-header">
                        <h4>Tạo tài khoản mới</h4>
                        <p>Tham gia cùng chúng tôi ngay hôm nay</p>
                    </div>

                    <div class="auth-body pt-0">
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

                        <form method="POST" action="<?= BASE_URL ?>?action=register/process" id="registerForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i
                                            class="bi bi-envelope text-secondary"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= htmlspecialchars($_SESSION['old_data']['email'] ?? '') ?>"
                                        placeholder="example@email.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i
                                            class="bi bi-lock text-secondary"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Tối thiểu 6 ký tự" minlength="6" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="guide_code" class="form-label">Mã HDV (Dành cho Hướng dẫn viên)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i
                                            class="bi bi-person-badge text-secondary"></i></span>
                                    <input type="text" class="form-control" id="guide_code" name="guide_code"
                                        value="<?= htmlspecialchars($_SESSION['old_data']['guide_code'] ?? '') ?>"
                                        placeholder="Nhập mã HDV nếu có">
                                </div>
                                <div class="form-text text-muted">Nếu bạn là HDV, hãy nhập mã được cấp để xác thực.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirm" class="form-label">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light"><i
                                            class="bi bi-check2-square text-secondary"></i></span>
                                    <input type="password" class="form-control" id="password_confirm"
                                        name="password_confirm" placeholder="Nhập lại mật khẩu" minlength="6" required>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary text-uppercase">Đăng ký</button>
                            </div>
                        </form>
                    </div>

                    <div class="auth-footer">
                        <p class="mb-0 text-secondary small">Đã có tài khoản? <a
                                href="<?= BASE_URL ?>?action=login">Đăng nhập ngay</a></p>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>" class="text-secondary small text-decoration-none"><i
                                    class="bi bi-arrow-left me-1"></i>Về trang chủ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }
        });

        document.getElementById('password_confirm').addEventListener('input', function () {
            const password = document.getElementById('password').value;
            const passwordConfirm = this.value;

            if (passwordConfirm && password !== passwordConfirm) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
    <?php unset($_SESSION['old_data']); ?>
</body>

</html>