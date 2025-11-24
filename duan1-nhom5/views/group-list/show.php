<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=group-lists">Danh sách đoàn</a></li>
            <li class="breadcrumb-item active">Chi tiết đoàn</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết đoàn - <?= htmlspecialchars($schedule['tour_name']) ?></h2>
        <div>
            <a href="<?= BASE_URL ?>?action=group-lists/print&id=<?= $schedule['id'] ?>" class="btn btn-primary" target="_blank">
                <i class="bi bi-printer"></i> In danh sách
            </a>
            <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $schedule['id'] ?>" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Check-in
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Thông tin lịch khởi hành -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin Lịch Khởi Hành</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tour:</strong> <?= htmlspecialchars($schedule['tour_name']) ?> (<?= htmlspecialchars($schedule['tour_code']) ?>)</p>
                    <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?></p>
                    <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Ngày kết thúc:</strong> <?= date('d/m/Y H:i', strtotime($schedule['end_date'] . ' ' . $schedule['end_time'])) ?></p>
                    <p><strong>Tổng số khách:</strong> <span class="badge bg-info"><?= $stats['total'] ?></span></p>
                    <p><strong>Đã check-in:</strong> <span class="badge bg-success"><?= $stats['checked_in'] ?></span></p>
                    <p><strong>Chưa check-in:</strong> <span class="badge bg-warning"><?= $stats['pending'] ?></span></p>
                    <?php if ($stats['absent'] > 0): ?>
                        <p><strong>Vắng mặt:</strong> <span class="badge bg-danger"><?= $stats['absent'] ?></span></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách khách trong đoàn -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Danh sách khách trong đoàn</h5>
        </div>
        <div class="card-body">
            <?php if (empty($customers)): ?>
                <p class="text-center text-muted">Chưa có khách hàng nào trong đoàn này.</p>
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
                                <th>Trạng thái Check-in</th>
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
                                        <?php if ($customer['checkin']): ?>
                                            <?php
                                            $status = $customer['checkin']['status'];
                                            $label = $statusLabels[$status] ?? $status;
                                            $color = $statusColors[$status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                            <?php if ($customer['checkin']['checkin_time']): ?>
                                                <br><small class="text-muted"><?= date('H:i', strtotime($customer['checkin']['checkin_time'])) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Chưa check-in</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $schedule['id'] ?>" 
                                           class="btn btn-sm btn-info">Check-in</a>
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
