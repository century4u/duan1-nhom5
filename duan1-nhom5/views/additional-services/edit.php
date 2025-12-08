<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Chỉnh sửa Dịch vụ</h4>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>?action=additional-services/update" method="POST">
                    <input type="hidden" name="id" value="<?= $service['id'] ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?= htmlspecialchars($service['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Đơn giá <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price"
                            value="<?= $service['price'] ?>" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="3"><?= htmlspecialchars($service['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" <?= $service['status'] == 1 ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= $service['status'] == 0 ? 'selected' : '' ?>>Ẩn</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>?action=additional-services" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>