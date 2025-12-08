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
                <span class="badge bg-<?php
                $statusColors = [
                    'pending' => 'warning',
                    'deposit' => 'info',
                    'confirmed' => 'success',
                    'completed' => 'primary',
                    'cancelled' => 'danger'
                ];
                echo $statusColors[$booking['status']] ?? 'secondary';
                ?>">
                    <?php
                    $statusLabels = BookingModel::getStatuses();
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
                    <p><strong>Điểm khởi hành:</strong> <?= htmlspecialchars($booking['departure_location'] ?? 'N/A') ?>
                    </p>
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
                <div class="row">
                    <?php foreach ($booking['participants'] as $index => $participant): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="bi bi-person-fill"></i>
                                        <strong><?= htmlspecialchars($participant['fullname']) ?></strong>
                                        <span class="badge bg-secondary ms-2">Khách #<?= $index + 1 ?></span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Thông tin cơ bản -->
                                    <div class="mb-3">
                                        <p class="mb-1">
                                            <strong>Giới tính:</strong>
                                            <?php
                                            $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                            echo $genderLabels[$participant['gender']] ?? 'N/A';
                                            ?>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Ngày sinh:</strong>
                                            <?= $participant['birthdate'] ? date('d/m/Y', strtotime($participant['birthdate'])) : 'N/A' ?>
                                        </p>
                                        <p class="mb-0">
                                            <strong>Tuổi:</strong>
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
                                        </p>
                                    </div>

                                    <!-- Ghi chú đặc biệt -->
                                    <?php
                                    $hasSpecialNotes = !empty($participant['dietary_restrictions']) ||
                                        !empty($participant['medical_conditions']) ||
                                        !empty($participant['special_requirements']);
                                    ?>

                                    <?php if ($hasSpecialNotes): ?>
                                        <hr>
                                        <h6 class="text-primary mb-2">
                                            <i class="bi bi-exclamation-circle-fill"></i> Ghi chú đặc biệt
                                        </h6>

                                        <?php if (!empty($participant['dietary_restrictions'])): ?>
                                            <div class="alert alert-success py-2 mb-2">
                                                <strong><i class="bi bi-egg-fried"></i> Hạn chế ăn uống:</strong><br>
                                                <span
                                                    class="ms-3"><?= nl2br(htmlspecialchars($participant['dietary_restrictions'])) ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($participant['medical_conditions'])): ?>
                                            <div class="alert alert-danger py-2 mb-2">
                                                <strong><i class="bi bi-heart-pulse"></i> Tình trạng sức khỏe:</strong><br>
                                                <span
                                                    class="ms-3"><?= nl2br(htmlspecialchars($participant['medical_conditions'])) ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($participant['special_requirements'])): ?>
                                            <div class="alert alert-warning py-2 mb-2">
                                                <strong><i class="bi bi-star"></i> Yêu cầu đặc biệt:</strong><br>
                                                <span
                                                    class="ms-3"><?= nl2br(htmlspecialchars($participant['special_requirements'])) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <hr>
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-info-circle"></i> Không có yêu cầu đặc biệt
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
                    <p><strong>Giá mỗi người:</strong>
                        <?= number_format($booking['total_price'] / max($booking['participants_count'], 1), 0, ',', '.') ?>
                        đ</p>
                </div>
                <div class="col-md-6">
                    <p class="text-end">
                        <strong class="fs-4 text-danger">Tổng giá:
                            <?= number_format($booking['total_price'], 0, ',', '.') ?> đ</strong>
                    </p>
                </div>
            </div>
            <?php if (!empty($booking['deposit_amount'])): ?>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Số tiền đã cọc:</strong> <span
                                class="text-info"><?= number_format($booking['deposit_amount'], 0, ',', '.') ?> đ</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Còn lại:</strong> <span
                                class="text-danger"><?= number_format($booking['total_price'] - $booking['deposit_amount'], 0, ',', '.') ?>
                                đ</span></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cập nhật trạng thái -->
    <?php if (!empty($availableStatuses)): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Cập nhật trạng thái</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>?action=bookings/update-status">
                    <input type="hidden" name="id" value="<?= $booking['id'] ?>">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Trạng thái mới <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required onchange="toggleDepositField()">
                                <option value="">-- Chọn trạng thái --</option>
                                <?php foreach ($availableStatuses as $statusKey => $statusLabel): ?>
                                    <option value="<?= $statusKey ?>"><?= $statusLabel ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" id="depositAmountField" style="display: none;">
                            <label for="deposit_amount" class="form-label">Số tiền cọc <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="deposit_amount" name="deposit_amount" min="0"
                                max="<?= $booking['total_price'] ?>" step="1000" placeholder="Nhập số tiền cọc">
                            <small class="text-muted">Tổng giá: <?= number_format($booking['total_price'], 0, ',', '.') ?>
                                đ</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="change_reason" class="form-label">Lý do thay đổi</label>
                        <input type="text" class="form-control" id="change_reason" name="change_reason"
                            placeholder="Ví dụ: Khách hàng đã thanh toán cọc">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                            placeholder="Ghi chú thêm về việc thay đổi trạng thái..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-body">
                <p class="text-muted mb-0">Không thể thay đổi trạng thái từ trạng thái hiện tại.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lịch sử thay đổi trạng thái -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Lịch sử thay đổi trạng thái</h5>
        </div>
        <div class="card-body">
            <?php if (empty($booking['status_history'])): ?>
                <p class="text-muted text-center">Chưa có lịch sử thay đổi trạng thái</p>
            <?php else: ?>
                <div class="timeline">
                    <?php
                    $statusLabels = BookingModel::getStatuses();
                    foreach ($booking['status_history'] as $history):
                        ?>
                        <div class="card mb-3 border-start border-4 border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="card-title mb-1">
                                            <?php if ($history['old_status']): ?>
                                                <span
                                                    class="badge bg-secondary"><?= $statusLabels[$history['old_status']] ?? $history['old_status'] ?></span>
                                                <i class="bi bi-arrow-right"></i>
                                            <?php endif; ?>
                                            <span
                                                class="badge bg-primary"><?= $statusLabels[$history['new_status']] ?? $history['new_status'] ?></span>
                                        </h6>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i:s', strtotime($history['created_at'])) ?>
                                            <?php if ($history['changed_by_name']): ?>
                                                - Bởi: <?= htmlspecialchars($history['changed_by_name']) ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>

                                <?php if (!empty($history['change_reason'])): ?>
                                    <p class="mb-1"><strong>Lý do:</strong> <?= htmlspecialchars($history['change_reason']) ?></p>
                                <?php endif; ?>

                                <?php if (!empty($history['notes'])): ?>
                                    <p class="mb-0 text-muted"><small><?= nl2br(htmlspecialchars($history['notes'])) ?></small></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleDepositField() {
        const statusSelect = document.getElementById('status');
        const depositField = document.getElementById('depositAmountField');
        const depositInput = document.getElementById('deposit_amount');

        if (statusSelect.value === 'deposit') {
            depositField.style.display = 'block';
            depositInput.required = true;
        } else {
            depositField.style.display = 'none';
            depositInput.required = false;
            depositInput.value = '';
        }
    }
</script>

<style>
    .timeline .card {
        position: relative;
    }

    .timeline .card::before {
        content: '';
        position: absolute;
        left: -2px;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #0d6efd;
    }
</style>