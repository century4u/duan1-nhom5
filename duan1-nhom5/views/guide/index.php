<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- <h2>Quản lý Hướng dẫn viên</h2> -->
        <a href="<?= BASE_URL ?>?action=guides/create" class="btn btn-primary">Thêm HDV Mới</a>
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
            <form method="GET" action="<?= BASE_URL ?>?action=guides">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" 
                               value="<?= $_GET['search'] ?? '' ?>" placeholder="Tên, mã, email, SĐT...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Chuyên môn</label>
                        <select class="form-select" name="specialization">
                            <option value="">Tất cả</option>
                            <?php foreach ($specializations as $key => $label): ?>
                                <option value="<?= $key ?>" <?= (isset($_GET['specialization']) && $_GET['specialization'] === $key) ? 'selected' : '' ?>>
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
                    <div class="col-md-2">
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
            <?php if (empty($guides)): ?>
                <p class="text-center text-muted">Không có HDV nào.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã HDV</th>
                                <th>Họ và tên</th>
                                <th>Chuyên môn</th>
                                <th>Ngôn ngữ</th>
                                <th>Kinh nghiệm</th>
                                <th>Số tour</th>
                                <th>Đánh giá</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guides as $guide): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($guide['code']) ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($guide['avatar'])): ?>
                                                <img src="<?= BASE_ASSETS_UPLOADS . $guide['avatar'] ?>" 
                                                     alt="Avatar" class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-white"><?= strtoupper(substr($guide['full_name'], 0, 1)) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= htmlspecialchars($guide['full_name']) ?></strong>
                                                <?php if ($guide['birthdate']): ?>
                                                    <br><small class="text-muted"><?= date('d/m/Y', strtotime($guide['birthdate'])) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $specializations[$guide['specialization']] ?? $guide['specialization'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($guide['languages_array'])): ?>
                                            <?php 
                                            $langNames = [];
                                            foreach ($guide['languages_array'] as $lang) {
                                                $langNames[] = $lang;
                                            }
                                            echo implode(', ', array_slice($langNames, 0, 2));
                                            if (count($langNames) > 2) echo '...';
                                            ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $guide['experience_years'] ?> năm</td>
                                    <td>
                                        <span class="badge bg-primary"><?= $guide['tours_count'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($guide['average_rating']): ?>
                                            <div>
                                                <strong><?= number_format($guide['average_rating'], 1) ?></strong>
                                                <small class="text-muted">/5.0</small>
                                                <br>
                                                <small class="text-muted">(<?= $guide['total_tours'] ?> đánh giá)</small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($guide['status'] == 1): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=guides/show&id=<?= $guide['id'] ?>" 
                                           class="btn btn-sm btn-info">Chi tiết</a>
                                        <a href="<?= BASE_URL ?>?action=guides/edit&id=<?= $guide['id'] ?>" 
                                           class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="<?= BASE_URL ?>?action=guides/delete&id=<?= $guide['id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa HDV này?')">Xóa</a>
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

