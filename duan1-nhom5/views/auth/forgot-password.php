<div class="col-12">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0">Quên mật khẩu</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $_SESSION['success'] ?>
                            <?php unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= $_SESSION['error'] ?>
                            <?php unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted">Nhập email của bạn để nhận link đặt lại mật khẩu.</p>

                    <form method="POST" action="<?= BASE_URL ?>?action=forgot-password/process">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_SESSION['old_email'] ?? '') ?>" 
                                   placeholder="Nhập email đăng ký" required autofocus>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-warning btn-lg">Gửi yêu cầu</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    <p class="mb-0">
                        <a href="<?= BASE_URL ?>?action=login" class="text-decoration-none">Quay lại đăng nhập</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php unset($_SESSION['old_email']); ?>

