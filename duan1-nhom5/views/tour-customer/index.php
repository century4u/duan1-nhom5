<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Danh sách khách theo tour</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách khách theo tour</h2>
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
            <form method="GET" action="<?= BASE_URL ?>?action=tour-customers">
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
                                    <?= date('d/m/Y H:i', strtotime($s['departure_date'] . ' ' . $s['departure_time'])) ?> -
                                    <?= htmlspecialchars($s['meeting_point']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Lọc</button>
                            <button type="submit" name="export" value="1"
                                formaction="<?= BASE_URL ?>?action=tour-customers/export"
                                class="btn btn-outline-success" title="Xuất Excel/CSV">
                                <i class="bi bi-file-earmark-excel"></i> Xuất
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="<?= BASE_URL ?>?action=tour-customers" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($tour || $schedule): ?>
        <!-- Thông tin tour/lịch khởi hành -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thông tin Tour</h5>
                <a href="<?= BASE_URL ?>?action=tour-customers/show&tour_id=<?= $tour['id'] ?><?= $schedule ? '&departure_schedule_id=' . $schedule['id'] : '' ?>"
                    class="btn btn-sm btn-light">
                    <i class="bi bi-eye"></i> Xem bản in
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tên tour:</strong> <?= htmlspecialchars($tour['name'] ?? 'N/A') ?></p>
                        <p><strong>Mã tour:</strong> <?= htmlspecialchars($tour['code'] ?? 'N/A') ?></p>
                        <p><strong>Điểm khởi hành:</strong> <?= htmlspecialchars($tour['departure_location'] ?? 'N/A') ?>
                        </p>
                        <p><strong>Điểm đến:</strong> <?= htmlspecialchars($tour['destination'] ?? 'N/A') ?></p>
                    </div>
                    <div class="col-md-6">
                        <?php if ($schedule): ?>
                            <p><strong>Ngày khởi hành:</strong>
                                <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?>
                            </p>
                            <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
                            <p><strong>Ngày kết thúc:</strong>
                                <?= date('d/m/Y H:i', strtotime($schedule['end_date'] . ' ' . $schedule['end_time'])) ?></p>
                        <?php endif; ?>
                        <p><strong>Tổng số khách:</strong> <span class="badge bg-info"><?= $stats['total'] ?? 0 ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

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
                        <h5 class="card-title">Nam</h5>
                        <h3 class="text-info"><?= $stats['male'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Nữ</h5>
                        <h3 class="text-danger"><?= $stats['female'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Đã xác nhận</h5>
                        <h3 class="text-success"><?= $stats['confirmed'] ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách khách -->
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách khách hàng</h5>
                <?php if ($schedule): ?>
                    <a href="<?= BASE_URL ?>?action=group-lists/print&id=<?= $schedule['id'] ?>" class="btn btn-light btn-sm"
                        target="_blank">
                        <i class="bi bi-printer"></i> In danh sách
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($customers)): ?>
                    <p class="text-center text-muted">Chưa có khách hàng nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Giới tính</th>
                                    <th>Ngày sinh</th>
                                    <th>Tuổi</th>
                                    <th>Liên hệ</th>
                                    <th>Trạng thái booking</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $index => $customer): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><strong><?= htmlspecialchars($customer['fullname']) ?></strong></td>
                                        <td>
                                            <?php
                                            $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                            echo $genderLabels[$customer['gender']] ?? 'N/A';
                                            ?>
                                        </td>
                                        <td><?= $customer['birthdate'] ? date('d/m/Y', strtotime($customer['birthdate'])) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($customer['birthdate']) {
                                                $birth = new DateTime($customer['birthdate']);
                                                $today = new DateTime();
                                                $age = $today->diff($birth)->y;
                                                echo $age . ' tuổi';
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($customer['customer_phone']): ?>
                                                <small><?= htmlspecialchars($customer['customer_phone']) ?></small><br>
                                            <?php endif; ?>
                                            <?php if ($customer['customer_email']): ?>
                                                <small class="text-muted"><?= htmlspecialchars($customer['customer_email']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusLabels = BookingModel::getStatuses();
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'deposit' => 'info',
                                                'confirmed' => 'success',
                                                'completed' => 'primary',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusLabel = $statusLabels[$customer['booking_status']] ?? $customer['booking_status'];
                                            $statusColor = $statusColors[$customer['booking_status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $statusColor ?>"><?= $statusLabel ?></span>
                                        </td>
                                        <td>
                                            <?php if ($schedule): ?>
                                                <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $schedule['id'] ?>"
                                                    class="btn btn-sm btn-info">Check-in</a>
                                            <?php endif; ?>
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
                <p class="text-muted">Vui lòng chọn tour hoặc lịch khởi hành để xem danh sách khách hàng.</p>
            </div>
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

        fetch(`<?= BASE_URL ?>?action=tour-customers/get-schedules&tour_id=${tourId}`)
            .then(response => response.json())
            .then(data => {
                let html = '<option value="">-- Chọn lịch khởi hành --</option>';
                if (data && data.length > 0) {
                    data.forEach(schedule => {
                        // Format date/time string
                        const date = new Date(schedule.departure_date + ' ' + schedule.departure_time);
                        const dateStr = date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

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