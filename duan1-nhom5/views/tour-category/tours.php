<div class="col-12">
    <div class="d-flex justify-content-end mb-3">
        <a href="<?= BASE_URL ?>?action=tour-categories" class="btn btn-outline-secondary">Quay lại</a>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>?action=tour-categories/view-tours&category=<?= htmlspecialchars($category ?? '') ?>">
                <input type="hidden" name="action" value="tour-categories/view-tours">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" 
                               value="<?= $_GET['search'] ?? '' ?>" placeholder="Tên, mã, điểm đến...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Không hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="<?= BASE_URL ?>?action=tour-categories/view-tours&category=<?= htmlspecialchars($category ?? '') ?>" 
                               class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($tours)): ?>
                <div class="text-center py-5">
                    <p class="text-muted mb-3">Không có tour nào trong danh mục này.</p>
                    <a href="<?= BASE_URL ?>?action=tours/create" class="btn btn-primary">Tạo tour mới</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã Tour</th>
                                <th>Tên Tour</th>
                                <th>Điểm khởi hành</th>
                                <th>Điểm đến</th>
                                <th>Số ngày</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $categories = TourModel::getCategories();
                            foreach ($tours as $tour): 
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($tour['code']) ?></strong></td>
                                    <td><?= htmlspecialchars($tour['name']) ?></td>
                                    <td><?= htmlspecialchars($tour['departure_location']) ?></td>
                                    <td><?= htmlspecialchars($tour['destination']) ?></td>
                                    <td><?= $tour['duration'] ?> ngày</td>
                                    <td><strong><?= number_format($tour['price'], 0, ',', '.') ?> đ</strong></td>
                                    <td>
                                        <?php if ($tour['status'] == 1): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=tours/edit&id=<?= $tour['id'] ?>" 
                                           class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="<?= BASE_URL ?>?action=tours/delete&id=<?= $tour['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa tour này?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

