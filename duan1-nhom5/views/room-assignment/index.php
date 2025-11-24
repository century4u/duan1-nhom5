<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Quản lý Phân phòng Khách sạn</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Phân phòng Khách sạn</h2>
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
            <form method="GET" action="<?= BASE_URL ?>?action=room-assignments">
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
                            <a href="<?= BASE_URL ?>?action=room-assignments" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($tour || $schedule): ?>
        <!-- Danh sách phân phòng -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách Phân phòng</h5>
                <?php if ($schedule): ?>
                    <a href="<?= BASE_URL ?>?action=room-assignments/create&departure_schedule_id=<?= $schedule['id'] ?>" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Tạo phân phòng
                    </a>
                <?php elseif ($tour): ?>
                    <a href="<?= BASE_URL ?>?action=room-assignments/create&tour_id=<?= $tour['id'] ?>" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Tạo phân phòng
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <p class="text-center text-muted">Chưa có phân phòng nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Khách hàng</th>
                                    <th>Khách sạn</th>
                                    <th>Số phòng</th>
                                    <th>Loại phòng</th>
                                    <th>Loại giường</th>
                                    <th>Ngày check-in</th>
                                    <th>Ngày check-out</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $roomTypes = RoomAssignmentModel::getRoomTypes();
                                $bedTypes = RoomAssignmentModel::getBedTypes();
                                foreach ($assignments as $index => $assignment): 
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <?php if ($assignment['fullname']): ?>
                                                <strong><?= htmlspecialchars($assignment['fullname']) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($assignment['hotel_name']) ?></td>
                                        <td><strong><?= htmlspecialchars($assignment['room_number']) ?></strong></td>
                                        <td><?= $roomTypes[$assignment['room_type']] ?? $assignment['room_type'] ?></td>
                                        <td><?= $bedTypes[$assignment['bed_type']] ?? $assignment['bed_type'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($assignment['checkin_date'])) ?></td>
                                        <td><?= $assignment['checkout_date'] ? date('d/m/Y', strtotime($assignment['checkout_date'])) : '-' ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>?action=room-assignments/edit&id=<?= $assignment['id'] ?>" 
                                               class="btn btn-sm btn-warning">Sửa</a>
                                            <a href="<?= BASE_URL ?>?action=room-assignments/delete&id=<?= $assignment['id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa phân phòng này?')">Xóa</a>
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
                <p class="text-muted">Vui lòng chọn tour hoặc lịch khởi hành để xem danh sách phân phòng.</p>
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
