<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=room-assignments">Quản lý Phân phòng</a></li>
            <li class="breadcrumb-item active">Chi tiết Phân phòng</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết Phân phòng - <?= htmlspecialchars($tour['name']) ?></h2>
        <div>
            <?php if ($schedule): ?>
                <a href="<?= BASE_URL ?>?action=room-assignments/create&departure_schedule_id=<?= $schedule['id'] ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tạo phân phòng
                </a>
            <?php elseif ($tour): ?>
                <a href="<?= BASE_URL ?>?action=room-assignments/create&tour_id=<?= $tour['id'] ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tạo phân phòng
                </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>?action=room-assignments" class="btn btn-secondary">Quay lại</a>
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
                    <?php if ($schedule): ?>
                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?></p>
                        <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Tổng số khách:</strong> <span class="badge bg-info"><?= $stats['total'] ?></span></p>
                    <p><strong>Đã phân phòng:</strong> <span class="badge bg-success"><?= $stats['assigned'] ?></span></p>
                    <p><strong>Chưa phân phòng:</strong> <span class="badge bg-warning"><?= $stats['unassigned'] ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách khách và phân phòng -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Danh sách Phân phòng</h5>
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
                            foreach ($customers as $index => $customer): 
                            ?>
                                <tr class="<?= $customer['room_assignment'] ? '' : 'table-warning' ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($customer['fullname']) ?></strong></td>
                                    <td>
                                        <?php
                                        $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                        echo $genderLabels[$customer['gender']] ?? 'N/A';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['room_assignment']): ?>
                                            <?= htmlspecialchars($customer['room_assignment']['hotel_name']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa phân phòng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['room_assignment']): ?>
                                            <strong><?= htmlspecialchars($customer['room_assignment']['room_number']) ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['room_assignment']): ?>
                                            <?= $roomTypes[$customer['room_assignment']['room_type']] ?? $customer['room_assignment']['room_type'] ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['room_assignment']): ?>
                                            <?= $bedTypes[$customer['room_assignment']['bed_type']] ?? $customer['room_assignment']['bed_type'] ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['room_assignment']): ?>
                                            <?= date('d/m/Y', strtotime($customer['room_assignment']['checkin_date'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['room_assignment'] && $customer['room_assignment']['checkout_date']): ?>
                                            <?= date('d/m/Y', strtotime($customer['room_assignment']['checkout_date'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($customer['room_assignment']): ?>
                                            <a href="<?= BASE_URL ?>?action=room-assignments/edit&id=<?= $customer['room_assignment']['id'] ?>" 
                                               class="btn btn-sm btn-warning">Sửa</a>
                                            <a href="<?= BASE_URL ?>?action=room-assignments/delete&id=<?= $customer['room_assignment']['id'] ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa phân phòng này?')">Xóa</a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>?action=room-assignments/create&booking_detail_id=<?= $customer['id'] ?><?= $schedule ? '&departure_schedule_id=' . $schedule['id'] : '&tour_id=' . $tour['id'] ?>" 
                                               class="btn btn-sm btn-primary">Phân phòng</a>
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
