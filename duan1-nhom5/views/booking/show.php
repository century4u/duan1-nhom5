<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=bookings">Quản lý Đặt Tour</a></li>
            <li class="breadcrumb-item active">Chi tiết Booking</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Chi tiết Đặt Tour #<?= $booking['id'] ?></h2>
            <p class="text-muted mb-0">
                <span class="badge bg-<?= $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'pending' ? 'warning' : 'danger') ?>">
                    <?php
                    $statusLabels = [
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'cancelled' => 'Đã hủy',
                        'completed' => 'Hoàn thành'
                    ];
                    echo $statusLabels[$booking['status']] ?? $booking['status'];
                    ?>
                </span>
                <span class="ms-2">Ngày đặt: <?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></span>
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>?action=bookings" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Thông tin Tour -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin Tour</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tên tour:</strong> <?= htmlspecialchars($booking['tour_name'] ?? 'N/A') ?></p>
                    <p><strong>Mã tour:</strong> <?= htmlspecialchars($booking['tour_code'] ?? 'N/A') ?></p>
                    <p><strong>Điểm khởi hành:</strong> <?= htmlspecialchars($booking['departure_location'] ?? 'N/A') ?></p>
                    <p><strong>Điểm đến:</strong> <?= htmlspecialchars($booking['destination'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Thời gian:</strong> <?= $booking['duration'] ?? 'N/A' ?> ngày</p>
                    <p><strong>Số người tối đa:</strong> <?= $booking['max_participants'] ?? 'Không giới hạn' ?></p>
                    <p><strong>Loại booking:</strong> 
                        <span class="badge bg-<?= $booking['booking_type'] === 'group' ? 'info' : 'secondary' ?>">
                            <?= $booking['booking_type'] === 'group' ? 'Đoàn' : 'Khách lẻ' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin khách hàng -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Thông tin liên hệ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Tên:</strong> <?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Email:</strong> <?= htmlspecialchars($booking['customer_email'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Điện thoại:</strong> <?= htmlspecialchars($booking['customer_phone'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách khách hàng -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Danh sách khách hàng (<?= $booking['participants_count'] ?> người)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($booking['participants'])): ?>
                <p class="text-muted text-center">Chưa có thông tin khách hàng</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Họ và tên</th>
                                <th>Giới tính</th>
                                <th>Ngày sinh</th>
                                <th>Tuổi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking['participants'] as $index => $participant): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($participant['fullname']) ?></strong></td>
                                    <td>
                                        <?php
                                        $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                        echo $genderLabels[$participant['gender']] ?? 'N/A';
                                        ?>
                                    </td>
                                    <td><?= $participant['birthdate'] ? date('d/m/Y', strtotime($participant['birthdate'])) : 'N/A' ?></td>
                                    <td>
                                        <?php
                                        if ($participant['birthdate']) {
                                            $birth = new DateTime($participant['birthdate']);
                                            $today = new DateTime();
                                            $age = $today->diff($birth)->y;
                                            echo $age . ' tuổi';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thông tin thanh toán -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Thông tin thanh toán</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Số lượng khách:</strong> <?= $booking['participants_count'] ?> người</p>
                    <p><strong>Giá mỗi người:</strong> <?= number_format($booking['total_price'] / max($booking['participants_count'], 1), 0, ',', '.') ?> đ</p>
                </div>
                <div class="col-md-6">
                    <p class="text-end">
                        <strong class="fs-4 text-danger">Tổng giá: <?= number_format($booking['total_price'], 0, ',', '.') ?> đ</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

