<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chỉnh sửa HDV</h2>
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

    <form method="POST" action="<?= BASE_URL ?>?action=guides/update" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $guide['id'] ?>">
        
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
                               value="<?= htmlspecialchars($_SESSION['old_data']['code'] ?? $guide['code']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['full_name'] ?? $guide['full_name']) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="birthdate" class="form-label">Ngày sinh</label>
                        <input type="date" class="form-control" id="birthdate" name="birthdate" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['birthdate'] ?? $guide['birthdate'] ?? '') ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">-- Chọn --</option>
                            <option value="male" <?= (($_SESSION['old_data']['gender'] ?? $guide['gender']) === 'male') ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= (($_SESSION['old_data']['gender'] ?? $guide['gender']) === 'female') ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= (($_SESSION['old_data']['gender'] ?? $guide['gender']) === 'other') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="avatar" class="form-label">Ảnh đại diện</label>
                        <?php if (!empty($guide['avatar'])): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_ASSETS_UPLOADS . $guide['avatar'] ?>" alt="Avatar" 
                                     style="max-width: 100px; max-height: 100px;" class="img-thumbnail">
                                <p class="text-muted small mt-1">Ảnh hiện tại</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        <small class="text-muted">Để trống nếu không muốn thay đổi ảnh</small>
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
                               value="<?= htmlspecialchars($_SESSION['old_data']['phone'] ?? $guide['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['email'] ?? $guide['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($_SESSION['old_data']['address'] ?? $guide['address'] ?? '') ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_card" class="form-label">CMND/CCCD</label>
                        <input type="text" class="form-control" id="id_card" name="id_card" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['id_card'] ?? $guide['id_card'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="passport" class="form-label">Hộ chiếu</label>
                        <input type="text" class="form-control" id="passport" name="passport" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['passport'] ?? $guide['passport'] ?? '') ?>">
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
                                <option value="<?= $key ?>" <?= (($_SESSION['old_data']['specialization'] ?? $guide['specialization']) === $key) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="experience_years" class="form-label">Số năm kinh nghiệm</label>
                        <input type="number" class="form-control" id="experience_years" name="experience_years" 
                               min="0" value="<?= $_SESSION['old_data']['experience_years'] ?? $guide['experience_years'] ?? 0 ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngôn ngữ sử dụng</label>
                    <div class="row">
                        <?php 
                        $selectedLanguages = $_SESSION['old_data']['languages'] ?? $guide['languages_array'] ?? [];
                        foreach ($languages as $key => $label): 
                        ?>
                            <div class="col-md-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="languages[]" 
                                           value="<?= $key ?>" id="lang_<?= $key ?>"
                                           <?= in_array($key, $selectedLanguages) ? 'checked' : '' ?>>
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
                    <textarea class="form-control" id="certificates" name="certificates" rows="3"><?= htmlspecialchars($_SESSION['old_data']['certificates'] ?? $guide['certificates'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="experience_description" class="form-label">Mô tả kinh nghiệm</label>
                    <textarea class="form-control" id="experience_description" name="experience_description" rows="4"><?= htmlspecialchars($_SESSION['old_data']['experience_description'] ?? $guide['experience_description'] ?? '') ?></textarea>
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
                               value="<?= $_SESSION['old_data']['performance_rating'] ?? $guide['performance_rating'] ?? '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="health_status" class="form-label">Tình trạng sức khỏe</label>
                        <select class="form-select" id="health_status" name="health_status">
                            <option value="good" <?= (($_SESSION['old_data']['health_status'] ?? $guide['health_status']) === 'good') ? 'selected' : '' ?>>Tốt</option>
                            <option value="fair" <?= (($_SESSION['old_data']['health_status'] ?? $guide['health_status']) === 'fair') ? 'selected' : '' ?>>Bình thường</option>
                            <option value="poor" <?= (($_SESSION['old_data']['health_status'] ?? $guide['health_status']) === 'poor') ? 'selected' : '' ?>>Kém</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="health_notes" class="form-label">Ghi chú về sức khỏe</label>
                    <textarea class="form-control" id="health_notes" name="health_notes" rows="2"><?= htmlspecialchars($_SESSION['old_data']['health_notes'] ?? $guide['health_notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Trạng thái -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                           <?= (($_SESSION['old_data']['status'] ?? $guide['status']) == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status">
                        Kích hoạt HDV
                    </label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= BASE_URL ?>?action=guides/show&id=<?= $guide['id'] ?>" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Cập nhật HDV</button>
        </div>
    </form>
</div>

<?php unset($_SESSION['old_data']); ?>

