<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=room-assignments">Phân phòng</a></li>
            <li class="breadcrumb-item active">Tạo phân phòng</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Tạo Phân phòng Mới</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Hướng dẫn:</strong> Chọn một hoặc nhiều khách hàng bên dưới, sau đó điền thông tin phòng để phân
                họ vào cùng một phòng.
            </div>

            <form action="<?= BASE_URL ?>?action=room-assignments/store" method="POST">
                <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                <?php if ($schedule): ?>
                    <input type="hidden" name="departure_schedule_id" value="<?= $schedule['id'] ?>">
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label required">Tên Khách sạn</label>
                        <input type="text" class="form-control" name="hotel_name" required list="hotels"
                            placeholder="Nhập tên khách sạn...">
                        <datalist id="hotels">
                            <!-- Popular hotels could be seeded here -->
                        </datalist>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">Số phòng</label>
                        <input type="text" class="form-control" name="room_number" required placeholder="VD: 101">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Loại phòng</label>
                        <select class="form-select" name="room_type">
                            <?php foreach (RoomAssignmentModel::getRoomTypes() as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Loại giường</label>
                        <select class="form-select" name="bed_type">
                            <?php foreach (RoomAssignmentModel::getBedTypes() as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label required">Check-in</label>
                        <input type="date" class="form-control" name="checkin_date"
                            value="<?= $schedule ? $schedule['departure_date'] : date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="allow_duplicate" name="allow_duplicate"
                        value="1">
                    <label class="form-check-label text-danger" for="allow_duplicate">Cho phép xếp thêm vào phòng đang
                        có người (Ghép phòng)</label>
                </div>

                <h5>Danh sách Khách hàng chưa xếp phòng</h5>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%"><input type="checkbox" id="selectAll"></th>
                                <th>Họ tên</th>
                                <th>Giới tính</th>
                                <th>Ngày sinh</th>
                                <th>Booking ID</th>
                                <th>Đã xếp phòng?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $unassignedCount = 0;
                            foreach ($customers as $customer):
                                $isAssigned = isset($assignmentMap[$customer['id']]);
                                if ($isAssigned)
                                    continue; // Skip assigned
                                $unassignedCount++;
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="assignments[<?= $customer['id'] ?>][booking_detail_id]"
                                            value="<?= $customer['id'] ?>" class="customer-check">
                                        <!-- Hidden fields to replicate room data for each selected customer -->
                                        <input type="hidden" name="assignments[<?= $customer['id'] ?>][hotel_name]"
                                            class="h-hotel">
                                        <input type="hidden" name="assignments[<?= $customer['id'] ?>][room_number]"
                                            class="h-room">
                                        <input type="hidden" name="assignments[<?= $customer['id'] ?>][room_type]"
                                            class="h-type">
                                        <input type="hidden" name="assignments[<?= $customer['id'] ?>][bed_type]"
                                            class="h-bed">
                                        <input type="hidden" name="assignments[<?= $customer['id'] ?>][checkin_date]"
                                            class="h-in">
                                        <input type="hidden" name="assignments[<?= $customer['id'] ?>][allow_duplicate]"
                                            class="h-dup">
                                    </td>
                                    <td><?= htmlspecialchars($customer['fullname']) ?></td>
                                    <td><?= $customer['gender'] === 'male' ? 'Nam' : 'Nữ' ?></td>
                                    <td><?= date('d/m/Y', strtotime($customer['birthdate'])) ?></td>
                                    <td><?= $customer['booking_code'] ?? 'N/A' ?></td>
                                    <td><span class="badge bg-secondary">Chưa xếp</span></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($unassignedCount === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tất cả khách hàng đã được xếp phòng!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>?action=room-assignments" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary" onclick="return syncData()">
                        <i class="bi bi-save"></i> Lưu Phân phòng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.customer-check');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Sync main form inputs to hidden inputs for each selected user
    function syncData() {
        const hotel = document.querySelector('input[name="hotel_name"]').value;
        const room = document.querySelector('input[name="room_number"]').value;
        const type = document.querySelector('select[name="room_type"]').value;
        const bed = document.querySelector('select[name="bed_type"]').value;
        const checkin = document.querySelector('input[name="checkin_date"]').value;
        const dup = document.getElementById('allow_duplicate').checked ? 1 : 0;

        // Check if at least one customer is selected
        const selected = document.querySelectorAll('.customer-check:checked');
        if (selected.length === 0) {
            alert('Vui lòng chọn ít nhất một khách hàng!');
            return false;
        }

        selected.forEach(cb => {
            const row = cb.closest('tr');
            row.querySelector('.h-hotel').value = hotel;
            row.querySelector('.h-room').value = room;
            row.querySelector('.h-type').value = type;
            row.querySelector('.h-bed').value = bed;
            row.querySelector('.h-in').value = checkin;
            row.querySelector('.h-dup').value = dup;
        });

        return true;
    }
</script>