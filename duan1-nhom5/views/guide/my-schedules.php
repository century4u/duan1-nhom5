<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Lịch Làm Việc Của Tôi</h2>
            <p class="text-muted mb-0">Xem các tour bạn được phân công dẫn</p>
        </div>
        <?php if (!empty($notifications)): ?>
            <a href="<?= BASE_URL ?>?action=notifications" class="btn btn-primary position-relative">
                <i class="bi bi-bell"></i> Thông báo
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= count($notifications) ?>
                </span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>">
                <input type="hidden" name="action" value="guides/my-schedules">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Tháng</label>
                        <input type="month" name="month" class="form-control"
                            value="<?= $_GET['month'] ?? date('Y-m') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Chờ xác
                                nhận</option>
                            <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Đã
                                xác nhận</option>
                            <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Từ
                                chối</option>
                            <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn
                                thành</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedule Timeline -->
    <?php if (empty($schedules)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Bạn chưa được phân công tour nào trong tháng này.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($schedules as $schedule): ?>
                <div class="col-md-6 mb-4">
                    <div
                        class="card h-100 <?= $schedule['status'] === 'pending' ? 'border-warning' : ($schedule['status'] === 'confirmed' ? 'border-success' : '') ?>">
                        <div
                            class="card-header <?= $schedule['status'] === 'pending' ? 'bg-warning bg-opacity-10' : ($schedule['status'] === 'confirmed' ? 'bg-success bg-opacity-10' : 'bg-light') ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1"><?= htmlspecialchars($schedule['tour_name']) ?></h5>
                                    <small class="text-muted"><?= htmlspecialchars($schedule['tour_code']) ?></small>
                                </div>
                                <span
                                    class="badge <?= $schedule['status'] === 'pending' ? 'bg-warning' : ($schedule['status'] === 'confirmed' ? 'bg-success' : 'bg-secondary') ?>">
                                    <?php
                                    $statusLabels = [
                                        'pending' => 'Chờ xác nhận',
                                        'confirmed' => 'Đã xác nhận',
                                        'rejected' => 'Từ chối',
                                        'completed' => 'Hoàn thành'
                                    ];
                                    echo $statusLabels[$schedule['status']] ?? $schedule['status'];
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <p class="mb-2">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                    <strong>Khởi hành:</strong>
                                    <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?>
                                </p>
                                <p class="mb-2">
                                    <i class="bi bi-calendar-x text-danger"></i>
                                    <strong>Kết thúc:</strong>
                                    <?= date('d/m/Y H:i', strtotime($schedule['end_date'] . ' ' . $schedule['end_time'])) ?>
                                </p>
                                <p class="mb-2">
                                    <i class="bi bi-clock text-info"></i>
                                    <strong>Thời gian:</strong>
                                    <?= formatDateRange($schedule['departure_date'], $schedule['end_date']) ?>
                                    (<?= formatDuration($schedule['duration']) ?>)
                                </p>
                                <p class="mb-2">
                                    <i class="bi bi-geo-alt text-success"></i>
                                    <strong>Tuyến:</strong>
                                    <?= htmlspecialchars($schedule['departure_location']) ?>
                                    → <?= htmlspecialchars($schedule['destination']) ?>
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-pin-map"></i>
                                    <strong>Điểm tập trung:</strong>
                                    <?= htmlspecialchars($schedule['meeting_point']) ?>
                                </p>
                            </div>

                            <?php if (!empty($schedule['notes'])): ?>
                                <div class="alert alert-light mb-0">
                                    <small><strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($schedule['notes'])) ?></small>
                                </div>
                            <?php endif; ?>

                            <?php if ($schedule['status'] === 'pending'): ?>
                                <div class="mt-3">
                                    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/update-assignment-status"
                                        class="d-inline">
                                        <input type="hidden" name="assignment_id" value="<?= $schedule['id'] ?>">
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-circle"></i> Xác nhận
                                        </button>
                                    </form>
                                    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/update-assignment-status"
                                        class="d-inline">
                                        <input type="hidden" name="assignment_id" value="<?= $schedule['id'] ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-circle"></i> Từ chối
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .card-header {
        border-bottom: 2px solid rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5em 0.75em;
    }
</style>