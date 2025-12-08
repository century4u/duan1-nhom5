<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Quản lý Phân phòng</li>
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
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="<?= BASE_URL ?>?action=room-assignments" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($tour || $schedule): ?>
        <!-- Thông tin tour -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Thông tin Tour</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tên tour:</strong> <?= htmlspecialchars($tour['name']) ?></p>
                        <p><strong>Mã tour:</strong> <?= htmlspecialchars($tour['code']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <?php if ($schedule): ?>
                            <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?></p>
                        <?php endif; ?>
                        <p><strong>Tổng số khách:</strong> <span class="badge bg-primary"><?= count($assignments) + ($tour ? ($this->tourCustomerModel->countByTourId($tour['id']) - count($assignments)) : 0) // Approximation ?></span> (Đã tạo phòng: <?= count($assignments) ?>)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                 <a href="<?= BASE_URL ?>?action=room-assignments/create<?= $schedule ? '&departure_schedule_id='.$schedule['id'] : '&tour_id='.$tour['id'] ?>" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Tạo phân phòng mới
                </a>
            </div>
            <div>
                 <form action="<?= BASE_URL ?>?action=room-assignments/export" method="GET" class="d-inline">
                    <input type="hidden" name="action" value="room-assignments/export">
                    <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                    <?php if ($schedule): ?>
                        <input type="hidden" name="departure_schedule_id" value="<?= $schedule['id'] ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </button>
                </form>
                <a href="<?= BASE_URL ?>?action=room-assignments/show<?= $schedule ? '&departure_schedule_id='.$schedule['id'] : '&tour_id='.$tour['id'] ?>" class="btn btn-info text-white">
                   <i class="bi bi-list-check"></i> Xem danh sách chi tiết
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Danh sách các phòng đã phân</h5>
            </div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <p class="text-center text-muted">Chưa có phân phòng nào được tạo.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Phòng</th>
                                    <th>Khách sạn</th>
                                    <th>Loại phòng</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày Check-in</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Group by rooms for display? Or just list items.
                                // Listing items is simpler for now.
                                foreach ($assignments as $ra): 
                                ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($ra['room_number']) ?></strong></td>
                                        <td><?= htmlspecialchars($ra['hotel_name']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($ra['room_type']) ?> <br>
                                            <small class="text-muted"><?= htmlspecialchars($ra['bed_type']) ?></small>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($ra['fullname']) ?>
                                            <br>
                                            <small><?= $ra['gender'] === 'male' ? 'Nam' : 'Nữ' ?> - <?= date('Y', strtotime($ra['birthdate'])) ?></small>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($ra['checkin_date'])) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>?action=room-assignments/edit&id=<?= $ra['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>?action=room-assignments/delete&id=<?= $ra['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa phân phòng này?')">
                                                <i class="bi bi-trash"></i>
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

    <?php else: ?>
        <div class="alert alert-info text-center">
            Vui lòng chọn Tour hoặc Lịch khởi hành để xem danh sách phân phòng.
        </div>
    <?php endif; ?>
</div>

<script>
function loadSchedules(tourId) {
    const scheduleSelect = document.getElementById('departure_schedule_id');
    
    if (!tourId) {
        scheduleSelect.innerHTML = '<option value="">-- Chọn lịch khởi hành --</option>';
        return;
    }
    
    scheduleSelect.disabled = true;
    scheduleSelect.innerHTML = '<option value="">Đang tải...</option>';

    fetch(`<?= BASE_URL ?>?action=room-assignments/get-schedules&tour_id=${tourId}`)
        .then(response => response.json())
        .then(data => {
            let html = '<option value="">-- Chọn lịch khởi hành --</option>';
            if (data && data.length > 0) {
                data.forEach(schedule => {
                    const date = new Date(schedule.departure_date + ' ' + schedule.departure_time);
                    const dateStr = date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
                    
                    html += `<option value="${schedule.id}">${dateStr} - ${schedule.meeting_point}</option>`;
                });
            } else {
                html = '<option value="">Không có lịch khởi hành</option>';
            }
            scheduleSelect.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            scheduleSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
        })
        .finally(() => {
            scheduleSelect.disabled = false;
        });
}
</script>
