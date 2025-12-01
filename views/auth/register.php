<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['error'] ?>
                            <?php unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
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
                            <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?= htmlspecialchars($_SESSION['old_data']['full_name'] ?? '') ?>" 
                                   placeholder="Nhập họ và tên" required>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($_SESSION['old_data']['username'] ?? '') ?>" 
                                   placeholder="Tối thiểu 3 ký tự, chỉ chứa chữ, số và _" 
                                   pattern="[a-zA-Z0-9_]+" minlength="3" required>
                            <small class="text-muted">Chỉ được chứa chữ cái, số và dấu gạch dưới</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_SESSION['old_data']['email'] ?? '') ?>" 
                                   placeholder="example@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Tối thiểu 6 ký tự" minlength="6" required>
                            <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                   placeholder="Nhập lại mật khẩu" minlength="6" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-success btn-lg">Đăng ký</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    <p class="mb-0">Đã có tài khoản? <a href="<?= BASE_URL ?>?action=login" class="text-decoration-none">Đăng nhập ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Kiểm tra mật khẩu xác nhận
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Mật khẩu xác nhận không khớp!');
        return false;
    }
});

// Hiển thị/ẩn mật khẩu (tùy chọn)
document.getElementById('password_confirm').addEventListener('input', function() {
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

