<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=room-assignments">Quản lý Phân phòng</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa Phân phòng</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chỉnh sửa Phân phòng</h2>
        <a href="<?= BASE_URL ?>?action=room-assignments/show<?= $assignment['departure_schedule_id'] ? '&departure_schedule_id=' . $assignment['departure_schedule_id'] : '&tour_id=' . $assignment['tour_id'] ?>" class="btn btn-secondary">Quay lại</a>
    </div>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['errors']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Thông tin phân phòng hiện tại -->
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Thông tin Phân phòng</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($assignment['fullname'] ?? 'N/A') ?></p>
                    <p><strong>Tour:</strong> <?= htmlspecialchars($assignment['tour_name']) ?> (<?= htmlspecialchars($assignment['tour_code']) ?>)</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Khách sạn:</strong> <?= htmlspecialchars($assignment['hotel_name']) ?></p>
                    <p><strong>Số phòng:</strong> <?= htmlspecialchars($assignment['room_number']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form chỉnh sửa -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Chỉnh sửa thông tin</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>?action=room-assignments/update">
                <input type="hidden" name="id" value="<?= $assignment['id'] ?>">
                <input type="hidden" name="booking_detail_id" value="<?= $assignment['booking_detail_id'] ?? '' ?>">
                <input type="hidden" name="departure_schedule_id" value="<?= $assignment['departure_schedule_id'] ?? '' ?>">
                <input type="hidden" name="tour_id" value="<?= $assignment['tour_id'] ?? '' ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Khách sạn <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="hotel_name" 
                               value="<?= htmlspecialchars($assignment['hotel_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số phòng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="room_number" 
                               value="<?= htmlspecialchars($assignment['room_number']) ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Loại phòng</label>
                        <select class="form-select" name="room_type">
                            <?php 
                            $roomTypes = RoomAssignmentModel::getRoomTypes();
                            foreach ($roomTypes as $key => $label): 
                            ?>
                                <option value="<?= $key ?>" <?= ($assignment['room_type'] === $key) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Loại giường</label>
                        <select class="form-select" name="bed_type">
                            <?php 
                            $bedTypes = RoomAssignmentModel::getBedTypes();
                            foreach ($bedTypes as $key => $label): 
                            ?>
                                <option value="<?= $key ?>" <?= ($assignment['bed_type'] === $key) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ngày check-in <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="checkin_date" 
                               value="<?= date('Y-m-d', strtotime($assignment['checkin_date'])) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày check-out</label>
                        <input type="date" class="form-control" name="checkout_date" 
                               value="<?= $assignment['checkout_date'] ? date('Y-m-d', strtotime($assignment['checkout_date'])) : '' ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea class="form-control" name="notes" rows="3"><?= htmlspecialchars($assignment['notes'] ?? '') ?></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="<?= BASE_URL ?>?action=room-assignments/show<?= $assignment['departure_schedule_id'] ? '&departure_schedule_id=' . $assignment['departure_schedule_id'] : '&tour_id=' . $assignment['tour_id'] ?>" 
                       class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
