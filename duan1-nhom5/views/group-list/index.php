<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Danh sách đoàn</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách đoàn</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Bộ lọc -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>?action=group-lists">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tour</label>
                        <select class="form-select" name="tour_id" id="tour_id">
                            <option value="">-- Chọn tour --</option>
                            <?php foreach ($tours ?? [] as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($_GET['tour_id']) && $_GET['tour_id'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="draft" <?= (isset($_GET['status']) && $_GET['status'] === 'draft') ? 'selected' : '' ?>>Nháp</option>
                            <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="in_progress" <?= (isset($_GET['status']) && $_GET['status'] === 'in_progress') ? 'selected' : '' ?>>Đang thực hiện</option>
                            <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] === 'completed') ? 'selected' : '' ?>>Hoàn tất</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="<?= BASE_URL ?>?action=group-lists" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đoàn -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($schedules)): ?>
                <p class="text-center text-muted">Không có đoàn nào.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tour</th>
                                <th>Ngày khởi hành</th>
                                <th>Điểm tập trung</th>
                                <th>Số khách</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $index => $schedule): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($schedule['tour_name']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($schedule['tour_code']) ?></small>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?><br>
                                        <small class="text-muted"><?= date('H:i', strtotime($schedule['departure_time'])) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($schedule['meeting_point']) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= $schedule['customer_count'] ?? 0 ?></span> khách
                                    </td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'draft' => 'Nháp',
                                            'confirmed' => 'Đã xác nhận',
                                            'in_progress' => 'Đang thực hiện',
                                            'completed' => 'Hoàn tất',
                                            'cancelled' => 'Hủy'
                                        ];
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'confirmed' => 'success',
                                            'in_progress' => 'info',
                                            'completed' => 'primary',
                                            'cancelled' => 'danger'
                                        ];
                                        $status = $schedule['status'];
                                        $label = $statusLabels[$status] ?? $status;
                                        $color = $statusColors[$status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=group-lists/show&id=<?= $schedule['id'] ?>" 
                                           class="btn btn-sm btn-info">Chi tiết</a>
                                        <a href="<?= BASE_URL ?>?action=group-lists/print&id=<?= $schedule['id'] ?>" 
                                           class="btn btn-sm btn-primary" target="_blank">
                                            <i class="bi bi-printer"></i> In
                                        </a>
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
