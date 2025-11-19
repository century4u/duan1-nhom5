<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tạo HDV Mới</h2>
        <a href="<?= BASE_URL ?>?action=guides" class="btn btn-secondary">Quay lại</a>
    </div>

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

    <form method="POST" action="<?= BASE_URL ?>?action=guides/store" enctype="multipart/form-data">
        <!-- Thông tin cơ bản -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">1. Thông tin cơ bản</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Mã HDV <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['code'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['full_name'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="birthdate" class="form-label">Ngày sinh</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['birthdate'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">-- Chọn --</option>
                            <option value="male" <?= (isset($_SESSION['old_data']['gender']) && $_SESSION['old_data']['gender'] === 'male') ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= (isset($_SESSION['old_data']['gender']) && $_SESSION['old_data']['gender'] === 'female') ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= (isset($_SESSION['old_data']['gender']) && $_SESSION['old_data']['gender'] === 'other') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="avatar" class="form-label">Ảnh đại diện</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">2. Thông tin liên hệ</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($_SESSION['old_data']['address'] ?? '') ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_card" class="form-label">CMND/CCCD</label>
                        <input type="text" class="form-control" id="id_card" name="id_card" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['id_card'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="passport" class="form-label">Hộ chiếu</label>
                        <input type="text" class="form-control" id="passport" name="passport" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['passport'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Chuyên môn và kỹ năng -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">3. Chuyên môn và kỹ năng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="specialization" class="form-label">Chuyên môn <span class="text-danger">*</span></label>
                        <select class="form-select" id="specialization" name="specialization" required>
                            <option value="">-- Chọn chuyên môn --</option>
                            <?php foreach ($specializations as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (isset($_SESSION['old_data']['specialization']) && $_SESSION['old_data']['specialization'] === $key) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="experience_years" class="form-label">Số năm kinh nghiệm</label>
                        <input type="number" class="form-control" id="experience_years" name="experience_years" 
                               min="0" value="<?= $_SESSION['old_data']['experience_years'] ?? 0 ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngôn ngữ sử dụng</label>
                    <div class="row">
                        <?php foreach ($languages as $key => $label): ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="languages[]" 
                                           value="<?= $key ?>" id="lang_<?= $key ?>"
                                           <?= (isset($_SESSION['old_data']['languages']) && in_array($key, $_SESSION['old_data']['languages'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="lang_<?= $key ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="certificates" class="form-label">Chứng chỉ chuyên môn</label>
                    <textarea class="form-control" id="certificates" name="certificates" rows="3" 
                              placeholder="Ví dụ: Chứng chỉ hướng dẫn viên quốc tế, Chứng chỉ tiếng Anh IELTS 7.0..."><?= htmlspecialchars($_SESSION['old_data']['certificates'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="experience_description" class="form-label">Mô tả kinh nghiệm</label>
                    <textarea class="form-control" id="experience_description" name="experience_description" rows="4" 
                              placeholder="Mô tả chi tiết về kinh nghiệm dẫn tour, các tour đã dẫn, điểm mạnh..."><?= htmlspecialchars($_SESSION['old_data']['experience_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Đánh giá và sức khỏe -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">4. Đánh giá và sức khỏe</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="performance_rating" class="form-label">Đánh giá năng lực (0-5)</label>
                        <input type="number" class="form-control" id="performance_rating" name="performance_rating" 
                               min="0" max="5" step="0.1" 
                               value="<?= $_SESSION['old_data']['performance_rating'] ?? '' ?>">
                        <small class="text-muted">Đánh giá từ 0 đến 5 điểm</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="health_status" class="form-label">Tình trạng sức khỏe</label>
                        <select class="form-select" id="health_status" name="health_status">
                            <option value="good" <?= (isset($_SESSION['old_data']['health_status']) && $_SESSION['old_data']['health_status'] === 'good') ? 'selected' : '' ?>>Tốt</option>
                            <option value="fair" <?= (isset($_SESSION['old_data']['health_status']) && $_SESSION['old_data']['health_status'] === 'fair') ? 'selected' : '' ?>>Bình thường</option>
                            <option value="poor" <?= (isset($_SESSION['old_data']['health_status']) && $_SESSION['old_data']['health_status'] === 'poor') ? 'selected' : '' ?>>Kém</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="health_notes" class="form-label">Ghi chú về sức khỏe</label>
                    <textarea class="form-control" id="health_notes" name="health_notes" rows="2" 
                              placeholder="Ghi chú về tình trạng sức khỏe, bệnh lý, thuốc đang dùng..."><?= htmlspecialchars($_SESSION['old_data']['health_notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Trạng thái -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                           <?= (!isset($_SESSION['old_data']['status']) || $_SESSION['old_data']['status'] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status">
                        Kích hoạt HDV
                    </label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= BASE_URL ?>?action=guides" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Tạo HDV</button>
        </div>
    </form>
</div>

<?php unset($_SESSION['old_data']); ?>

