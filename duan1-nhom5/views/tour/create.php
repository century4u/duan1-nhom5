<div class="col-md-10 offset-md-1">
    <div class="card">
        <div class="card-header">
            <h3>Tạo Tour Mới</h3>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php unset($_SESSION['errors']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>?action=tours/store" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Tên Tour <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= $_SESSION['old_data']['name'] ?? '' ?>" required>
                        <?php unset($_SESSION['old_data']['name']); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Mã Tour <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" 
                               value="<?= $_SESSION['old_data']['code'] ?? '' ?>" required>
                        <?php unset($_SESSION['old_data']['code']); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Loại Tour <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">-- Chọn loại tour --</option>
                            <?php foreach ($categories as $key => $label): ?>
                                <option value="<?= $key ?>" 
                                    <?= (isset($_SESSION['old_data']['category']) && $_SESSION['old_data']['category'] === $key) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php unset($_SESSION['old_data']['category']); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="duration" class="form-label">Số ngày <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="duration" name="duration" 
                               value="<?= $_SESSION['old_data']['duration'] ?? '' ?>" min="1" required>
                        <?php unset($_SESSION['old_data']['duration']); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="departure_location" class="form-label">Điểm khởi hành <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="departure_location" name="departure_location" 
                               value="<?= $_SESSION['old_data']['departure_location'] ?? '' ?>" required>
                        <?php unset($_SESSION['old_data']['departure_location']); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="destination" class="form-label">Điểm đến <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="destination" name="destination" 
                               value="<?= $_SESSION['old_data']['destination'] ?? '' ?>" required>
                        <?php unset($_SESSION['old_data']['destination']); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Giá Tour (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="<?= $_SESSION['old_data']['price'] ?? '' ?>" min="0" step="1000" required>
                        <?php unset($_SESSION['old_data']['price']); ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="max_participants" class="form-label">Số lượng tối đa (tùy chọn)</label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants" 
                               value="<?= $_SESSION['old_data']['max_participants'] ?? '' ?>" min="1">
                        <?php unset($_SESSION['old_data']['max_participants']); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?= $_SESSION['old_data']['description'] ?? '' ?></textarea>
                    <?php unset($_SESSION['old_data']['description']); ?>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Hình ảnh</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                               <?= (!isset($_SESSION['old_data']['status']) || $_SESSION['old_data']['status'] == 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">
                            Kích hoạt tour
                        </label>
                    </div>
                    <?php unset($_SESSION['old_data']); ?>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>?action=tours" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Tạo Tour</button>
                </div>
            </form>
        </div>
    </div>
</div>

