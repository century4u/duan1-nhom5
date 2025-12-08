<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- <h2>Danh sách Tour</h2> -->
        <a href="<?= BASE_URL ?>?action=tours/create" class="btn btn-primary">Tạo Tour Mới</a>
    </div>

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

    <!-- Bộ lọc -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>?action=tours">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" 
                               value="<?= $_GET['search'] ?? '' ?>" placeholder="Tên, mã, điểm đến...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Loại tour</label>
                        <select class="form-select" name="category">
                            <option value="">Tất cả</option>
                            <?php foreach ($categories as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (isset($_GET['category']) && $_GET['category'] === $key) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($tours)): ?>
                <p class="text-center text-muted">Không có tour nào.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã Tour</th>
                                <th>Tên Tour</th>
                                <th>Loại</th>
                                <th>Điểm khởi hành</th>
                                <th>Điểm đến</th>
                                <th>Số ngày</th>
                                <th>Giá</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours as $tour): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($tour['code']) ?></strong></td>
                                    <td><?= htmlspecialchars($tour['name']) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $categories[$tour['tour_category_id'] ?? ''] ?? 'Chưa phân loại' ?>
                                        </span>
                                    </td>
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
                                        <a href="<?= BASE_URL ?>?action=tours/show&id=<?= $tour['id'] ?>" 
                                           class="btn btn-sm btn-info">Chi tiết</a>
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

