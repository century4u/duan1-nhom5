<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-qr-code-scan me-2"></i>Quản lý Check-in & Điểm
                        danh</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <form method="GET" action="" class="row g-3 mb-4">
                        <input type="hidden" name="action" value="checkins">

                        <div class="col-md-4">
                            <label class="form-label">Chọn Tour</label>
                            <select name="tour_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Tất cả Tour --</option>
                                <?php foreach ($tours as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= (($filters['tour_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['code'] . ' - ' . $t['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Lịch khởi hành</label>
                            <select name="departure_schedule_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Tất cả Lịch --</option>
                                <?php foreach ($schedules as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= (($filters['departure_schedule_id'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                                        <?= formatDateRange($s['departure_date'], $s['end_date']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Tất cả --</option>
                                <option value="checked_in" <?= (($filters['status'] ?? '') == 'checked_in') ? 'selected' : '' ?>>Đã check-in</option>
                                <option value="pending" <?= (($filters['status'] ?? '') == 'pending') ? 'selected' : '' ?>>
                                    Chưa check-in</option>
                                <option value="late" <?= (($filters['status'] ?? '') == 'late') ? 'selected' : '' ?>>Đến
                                    muộn</option>
                                <option value="absent" <?= (($filters['status'] ?? '') == 'absent') ? 'selected' : '' ?>>
                                    Vắng mặt</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <a href="<?= BASE_URL ?>?action=checkins" class="btn btn-secondary w-100"><i
                                    class="bi bi-arrow-clockwise me-1"></i>Reset</a>
                        </div>
                    </form>

                    <!-- Stats Cards -->
                    <?php if ($tour || $schedule): ?>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body text-center">
                                        <h6 class="text-uppercase opacity-75">Tổng khách</h6>
                                        <h2 class="display-6 fw-bold mb-0"><?= $stats['total'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center">
                                        <h6 class="text-uppercase opacity-75">Đã điểm danh</h6>
                                        <h2 class="display-6 fw-bold mb-0"><?= $stats['checked_in'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-dark h-100">
                                    <div class="card-body text-center">
                                        <h6 class="text-uppercase opacity-75">Chưa điểm danh</h6>
                                        <h2 class="display-6 fw-bold mb-0"><?= $stats['pending'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light border h-100">
                                    <div class="card-body text-center">
                                        <h6 class="text-uppercase text-muted opacity-75">Vắng / Muộn</h6>
                                        <h2 class="display-6 fw-bold mb-0"><?= $stats['absent'] + $stats['late'] ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Check-in List Table -->
                    <?php if (empty($filters['tour_id']) && empty($filters['departure_schedule_id'])): ?>
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                            <div>
                                Vui lòng chọn <strong>Tour</strong> và <strong>Lịch khởi hành</strong> để bắt đầu điểm danh.
                            </div>
                        </div>
                    <?php elseif (empty($checkins) && empty($customers) && $stats['total'] == 0): ?>
                        <div class="alert alert-warning text-center">
                            Chưa có dữ liệu khách hàng cho lịch trình này.
                        </div>
                    <?php else: ?>
                        <div class="d-grid mb-3">
                            <a href="<?= BASE_URL ?>?action=checkins/show&tour_id=<?= $filters['tour_id'] ?? '' ?>&departure_schedule_id=<?= $filters['departure_schedule_id'] ?? '' ?>"
                                class="btn btn-primary btn-lg">
                                <i class="bi bi-list-check me-2"></i>Điểm danh chi tiết / Quét QR
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Khách hàng</th>
                                        <th>Tour / Lịch</th>
                                        <th>Trạng thái</th>
                                        <th>Ghi chú</th>
                                        <th>Người check</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($checkins as $c): ?>
                                        <tr>
                                            <td><?= date('H:i d/m/Y', strtotime($c['checkin_time'])) ?></td>
                                            <td>
                                                <div class="fw-bold"><?= $c['fullname'] ?></div>
                                                <small class="text-muted"><?= date('d/m/Y', strtotime($c['birthdate'])) ?> -
                                                    <?= $c['gender'] ?></small>
                                            </td>
                                            <td>
                                                <div class="text-primary"><?= $c['tour_code'] ?></div>
                                                <small><?= formatDateRange($schedule['departure_date'] ?? $c['departure_date'] ?? 'now', $schedule['end_date'] ?? $c['end_date'] ?? 'now') ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $statusBadge = [
                                                    'checked_in' => '<span class="badge bg-success">Đã check-in</span>',
                                                    'late' => '<span class="badge bg-warning text-dark">Đến muộn</span>',
                                                    'absent' => '<span class="badge bg-danger">Vắng mặt</span>',
                                                    'pending' => '<span class="badge bg-secondary">Chưa check-in</span>',
                                                ];
                                                echo $statusBadge[$c['status']] ?? $c['status'];
                                                ?>
                                            </td>
                                            <td><?= $c['notes'] ?></td>
                                            <td><?= $c['checked_by_name'] ?? 'N/A' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>