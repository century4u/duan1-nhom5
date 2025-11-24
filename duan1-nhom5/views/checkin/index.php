<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Quản lý Check-in</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Check-in</h2>
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
            <form method="GET" action="<?= BASE_URL ?>?action=checkins">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tour</label>
                        <select class="form-select" name="tour_id" id="tour_id" onchange="loadSchedules(this.value)">
                            <option value="">-- Chọn tour --</option>
                            <?php foreach ($tours ?? [] as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($_GET['tour_id']) && $_GET['tour_id'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lịch khởi hành</label>
                        <select class="form-select" name="departure_schedule_id" id="departure_schedule_id">
                            <option value="">-- Chọn lịch khởi hành --</option>
                            <?php foreach ($schedules ?? [] as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= (isset($_GET['departure_schedule_id']) && $_GET['departure_schedule_id'] == $s['id']) ? 'selected' : '' ?>>
                                    <?= date('d/m/Y H:i', strtotime($s['departure_date'] . ' ' . $s['departure_time'])) ?> - <?= htmlspecialchars($s['meeting_point']) ?>
                                </option>
                            <?php endforeach; ?>
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
                            <a href="<?= BASE_URL ?>?action=checkins" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($tour || $schedule): ?>
        <!-- Thống kê -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Tổng số</h5>
                        <h3 class="text-primary"><?= $stats['total'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Đã check-in</h5>
                        <h3 class="text-success"><?= $stats['checked_in'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Chưa check-in</h5>
                        <h3 class="text-warning"><?= $stats['pending'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Vắng mặt</h5>
                        <h3 class="text-danger"><?= $stats['absent'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách check-in -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Danh sách Check-in</h5>
            </div>
            <div class="card-body">
                <?php if (empty($checkins)): ?>
                    <p class="text-center text-muted">Chưa có dữ liệu check-in.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Tour</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $statusLabels = CheckinModel::getStatuses();
                                $statusColors = [
                                    'pending' => 'warning',
                                    'checked_in' => 'success',
                                    'late' => 'secondary',
                                    'absent' => 'danger',
                                    'cancelled' => 'dark'
                                ];
                                foreach ($checkins as $index => $checkin): 
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($checkin['fullname']) ?></strong></td>
                                        <td>
                                            <small><?= htmlspecialchars($checkin['tour_name']) ?></small><br>
                                            <small class="text-muted"><?= htmlspecialchars($checkin['tour_code']) ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $checkin['status'];
                                            $label = $statusLabels[$status] ?? $status;
                                            $color = $statusColors[$status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                        </td>
                                        <td>
                                            <?php if ($checkin['checkin_time']): ?>
                                                <?= date('d/m/Y H:i', strtotime($checkin['checkin_time'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($checkin['notes']): ?>
                                                <small><?= htmlspecialchars($checkin['notes']) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $checkin['departure_schedule_id'] ?? '' ?>&tour_id=<?= $checkin['tour_id'] ?? '' ?>" 
                                               class="btn btn-sm btn-info">Chi tiết</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted">Vui lòng chọn tour hoặc lịch khởi hành để xem danh sách check-in.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function loadSchedules(tourId) {
    if (!tourId) {
        document.getElementById('departure_schedule_id').innerHTML = '<option value="">-- Chọn lịch khởi hành --</option>';
        return;
    }
    // TODO: Load schedules via AJAX
}
</script>
