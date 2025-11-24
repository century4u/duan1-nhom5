<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=room-assignments">Quản lý Phân phòng</a></li>
            <li class="breadcrumb-item active">Tạo Phân phòng</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tạo Phân phòng Mới</h2>
        <a href="<?= BASE_URL ?>?action=room-assignments<?= $schedule ? '&departure_schedule_id=' . $schedule['id'] : ($tour ? '&tour_id=' . $tour['id'] : '') ?>" class="btn btn-secondary">Quay lại</a>
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

    <!-- Thông tin tour -->
    <?php if ($tour): ?>
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thông tin Tour</h5>
            </div>
            <div class="card-body">
                <p><strong>Tour:</strong> <?= htmlspecialchars($tour['name']) ?> (<?= htmlspecialchars($tour['code']) ?>)</p>
                <?php if ($schedule): ?>
                    <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form tạo phân phòng -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Phân phòng cho khách hàng</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>?action=room-assignments/store" id="roomAssignmentForm">
                <input type="hidden" name="departure_schedule_id" value="<?= $schedule['id'] ?? '' ?>">
                <input type="hidden" name="tour_id" value="<?= $tour['id'] ?? '' ?>">

                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%;">STT</th>
                                <th style="width: 20%;">Khách hàng</th>
                                <th style="width: 15%;">Khách sạn <span class="text-danger">*</span></th>
                                <th style="width: 10%;">Số phòng <span class="text-danger">*</span></th>
                                <th style="width: 12%;">Loại phòng</th>
                                <th style="width: 12%;">Loại giường</th>
                                <th style="width: 13%;">Ngày check-in <span class="text-danger">*</span></th>
                                <th style="width: 13%;">Ngày check-out</th>
                                <th style="width: 10%;">Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentRows">
                            <?php 
                            $roomTypes = RoomAssignmentModel::getRoomTypes();
                            $bedTypes = RoomAssignmentModel::getBedTypes();
                            $selectedCustomerId = $_GET['booking_detail_id'] ?? null;
                            $displayCustomers = [];
                            foreach ($customers as $customer): 
                                // Nếu có booking_detail_id trong URL, chỉ hiển thị khách đó
                                if ($selectedCustomerId && $customer['id'] != $selectedCustomerId) continue;
                                
                                // Bỏ qua nếu đã có phân phòng (trừ khi đang chỉnh sửa)
                                if (!$selectedCustomerId && isset($assignmentMap[$customer['id']])) continue;
                                
                                $displayCustomers[] = $customer;
                            endforeach;
                            
                            foreach ($displayCustomers as $index => $customer): 
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($customer['fullname']) ?></strong>
                                        <input type="hidden" name="assignments[<?= $index ?>][booking_detail_id]" value="<?= $customer['id'] ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="assignments[<?= $index ?>][hotel_name]" 
                                               required placeholder="Tên khách sạn">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                               name="assignments[<?= $index ?>][room_number]" 
                                               required placeholder="Số phòng">
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="assignments[<?= $index ?>][room_type]">
                                            <?php foreach ($roomTypes as $key => $label): ?>
                                                <option value="<?= $key ?>"><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="assignments[<?= $index ?>][bed_type]">
                                            <?php foreach ($bedTypes as $key => $label): ?>
                                                <option value="<?= $key ?>"><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" class="form-control form-control-sm" 
                                               name="assignments[<?= $index ?>][checkin_date]" 
                                               required value="<?= $schedule ? date('Y-m-d', strtotime($schedule['departure_date'])) : '' ?>">
                                    </td>
                                    <td>
                                        <input type="date" class="form-control form-control-sm" 
                                               name="assignments[<?= $index ?>][checkout_date]" 
                                               value="<?= $schedule ? date('Y-m-d', strtotime($schedule['end_date'])) : '' ?>">
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" 
                                                  name="assignments[<?= $index ?>][notes]" 
                                                  rows="2" placeholder="Ghi chú"></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (empty($customers) || ($selectedCustomerId && !in_array($selectedCustomerId, array_column($customers, 'id')))): ?>
                    <div class="alert alert-warning">
                        Không có khách hàng nào để phân phòng.
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu phân phòng
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
