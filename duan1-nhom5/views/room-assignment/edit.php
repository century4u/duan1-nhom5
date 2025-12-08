<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=room-assignments">Phân phòng</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa phân phòng</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">Chỉnh sửa Phân phòng</h4>
        </div>
        <div class="card-body">
            <form action="<?= BASE_URL ?>?action=room-assignments/update" method="POST">
                <input type="hidden" name="id" value="<?= $assignment['id'] ?>">
                <input type="hidden" name="booking_detail_id" value="<?= $assignment['booking_detail_id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Khách hàng</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($assignment['fullname']) ?>"
                        disabled>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Tên Khách sạn</label>
                        <input type="text" class="form-control" name="hotel_name" required
                            value="<?= htmlspecialchars($assignment['hotel_name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Số phòng</label>
                        <input type="text" class="form-control" name="room_number" required
                            value="<?= htmlspecialchars($assignment['room_number']) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Loại phòng</label>
                        <select class="form-select" name="room_type">
                            <?php foreach (RoomAssignmentModel::getRoomTypes() as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $assignment['room_type'] == $key ? 'selected' : '' ?>>
                                    <?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Loại giường</label>
                        <select class="form-select" name="bed_type">
                            <?php foreach (RoomAssignmentModel::getBedTypes() as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $assignment['bed_type'] == $key ? 'selected' : '' ?>>
                                    <?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label required">Ngày Check-in</label>
                        <input type="date" class="form-control" name="checkin_date" required
                            value="<?= $assignment['checkin_date'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ngày Check-out</label>
                        <input type="date" class="form-control" name="checkout_date"
                            value="<?= $assignment['checkout_date'] ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea class="form-control" name="notes"
                        rows="3"><?= htmlspecialchars($assignment['notes']) ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>?action=room-assignments" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>