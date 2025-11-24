<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=tour-customers">Danh sách khách theo tour</a></li>
            <li class="breadcrumb-item active">Chi tiết danh sách khách</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết danh sách khách - <?= htmlspecialchars($tour['name']) ?></h2>
        <div>
            <?php if ($schedule): ?>
                <a href="<?= BASE_URL ?>?action=group-lists/show&id=<?= $schedule['id'] ?>" class="btn btn-info">Xem danh sách đoàn</a>
                <a href="<?= BASE_URL ?>?action=group-lists/print&id=<?= $schedule['id'] ?>" class="btn btn-primary" target="_blank">
                    <i class="bi bi-printer"></i> In danh sách
                </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>?action=tour-customers" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
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
                    <p><strong>Điểm khởi hành:</strong> <?= htmlspecialchars($tour['departure_location']) ?></p>
                    <p><strong>Điểm đến:</strong> <?= htmlspecialchars($tour['destination']) ?></p>
                </div>
                <div class="col-md-6">
                    <?php if ($schedule): ?>
                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?></p>
                        <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
                        <p><strong>Ngày kết thúc:</strong> <?= date('d/m/Y H:i', strtotime($schedule['end_date'] . ' ' . $schedule['end_time'])) ?></p>
                    <?php endif; ?>
                    <p><strong>Tổng số khách:</strong> <span class="badge bg-info"><?= $stats['total'] ?></span></p>
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
                    <h3 class="text-primary"><?= $stats['total'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Nam</h5>
                    <h3 class="text-info"><?= $stats['male'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Nữ</h5>
                    <h3 class="text-danger"><?= $stats['female'] ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Đã xác nhận</h5>
                    <h3 class="text-success"><?= $stats['confirmed'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách khách -->
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách khách hàng</h5>
            <div>
                <?php if ($schedule): ?>
                    <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $schedule['id'] ?>" class="btn btn-light btn-sm">
                        <i class="bi bi-check-circle"></i> Check-in
                    </a>
                    <a href="<?= BASE_URL ?>?action=room-assignments/show&departure_schedule_id=<?= $schedule['id'] ?>" class="btn btn-light btn-sm">
                        <i class="bi bi-door-open"></i> Phân phòng
                    </a>
                <?php endif; ?>
            </div>
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
                                    <td><?= $customer['birthdate'] ? date('d/m/Y', strtotime($customer['birthdate'])) : 'N/A' ?></td>
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
</div>
