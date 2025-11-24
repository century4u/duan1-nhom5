<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=checkins">Quản lý Check-in</a></li>
            <li class="breadcrumb-item active">Chi tiết Check-in</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết Check-in - <?= htmlspecialchars($tour['name']) ?></h2>
        <?php if ($schedule): ?>
            <a href="<?= BASE_URL ?>?action=group-lists/show&id=<?= $schedule['id'] ?>" class="btn btn-secondary">Xem danh sách đoàn</a>
        <?php endif; ?>
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

    <!-- Thông tin tour/lịch khởi hành -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin Tour</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tên tour:</strong> <?= htmlspecialchars($tour['name']) ?></p>
                    <p><strong>Mã tour:</strong> <?= htmlspecialchars($tour['code']) ?></p>
                    <?php if ($schedule): ?>
                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?></p>
                        <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Tổng số khách:</strong> <span class="badge bg-info"><?= $stats['total'] ?></span></p>
                    <p><strong>Đã check-in:</strong> <span class="badge bg-success"><?= $stats['checked_in'] ?></span></p>
                    <p><strong>Chưa check-in:</strong> <span class="badge bg-warning"><?= $stats['pending'] ?></span></p>
                    <p><strong>Vắng mặt:</strong> <span class="badge bg-danger"><?= $stats['absent'] ?></span></p>
                    <?php if ($stats['late'] > 0): ?>
                        <p><strong>Đến muộn:</strong> <span class="badge bg-secondary"><?= $stats['late'] ?></span></p>
                    <?php endif; ?>
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
                                <th>Trạng thái Check-in</th>
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
                            foreach ($customers as $index => $customer): 
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($customer['fullname']) ?></strong></td>
                                    <td>
                                        <?php
                                        $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                        echo $genderLabels[$customer['gender']] ?? 'N/A';
                                        ?>
                                    </td>
                                    <td><?= $customer['birthdate'] ? date('d/m/Y', strtotime($customer['birthdate'])) : 'N/A' ?></td>
                                    <td>
                                        <?php if ($customer['checkin']): ?>
                                            <?php
                                            $status = $customer['checkin']['status'];
                                            $label = $statusLabels[$status] ?? $status;
                                            $color = $statusColors[$status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Chưa check-in</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['checkin'] && $customer['checkin']['checkin_time']): ?>
                                            <?= date('d/m/Y H:i', strtotime($customer['checkin']['checkin_time'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['checkin'] && $customer['checkin']['notes']): ?>
                                            <small><?= htmlspecialchars($customer['checkin']['notes']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="openCheckinModal(<?= $customer['id'] ?>, '<?= htmlspecialchars($customer['fullname']) ?>', <?= $schedule['id'] ?? 'null' ?>)">
                                            Check-in
                                        </button>
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

<!-- Modal Check-in -->
<div class="modal fade" id="checkinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Check-in khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>?action=checkins/process">
                <div class="modal-body">
                    <input type="hidden" name="booking_detail_id" id="checkin_booking_detail_id">
                    <input type="hidden" name="departure_schedule_id" value="<?= $schedule['id'] ?? '' ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Khách hàng</label>
                        <input type="text" class="form-control" id="checkin_customer_name" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="checkin_status" required>
                            <?php foreach (CheckinModel::getStatuses() as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea class="form-control" name="notes" id="checkin_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCheckinModal(bookingDetailId, customerName, scheduleId) {
    document.getElementById('checkin_booking_detail_id').value = bookingDetailId;
    document.getElementById('checkin_customer_name').value = customerName;
    if (scheduleId) {
        document.querySelector('input[name="departure_schedule_id"]').value = scheduleId;
    }
    const modal = new bootstrap.Modal(document.getElementById('checkinModal'));
    modal.show();
}
</script>
