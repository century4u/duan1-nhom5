<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Xếp khách vào lịch khởi hành</h3>
            <p class="text-muted mb-0">Tour:
                <?= htmlspecialchars($schedule['tour_code'] . ' - ' . $schedule['tour_name']) ?></p>
        </div>
        <a href="<?= BASE_URL ?>?action=departure-schedules/show&id=<?= $schedule['id'] ?>"
            class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Thông tin Lịch Khởi Hành</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></p>
                    <p><strong>Giờ:</strong> <?= $schedule['departure_time'] ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Số chỗ:</strong> <?= $schedule['current_participants'] ?> /
                        <?= $schedule['max_participants'] ?? 'Không giới hạn' ?></p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/process-assign-customers">
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Booking chưa xếp lịch</h5>
            </div>
            <div class="card-body">
                <?php if (empty($availableBookings)): ?>
                    <p class="text-center text-muted">Không có booking nào đang chờ xếp lịch cho tour này.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="checkAll">
                                    </th>
                                    <th>Mã Booking</th>
                                    <th>Khách hàng</th>
                                    <th>SĐT</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($availableBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="booking_ids[]" value="<?= $booking['id'] ?>"
                                                class="booking-check">
                                        </td>
                                        <td>#<?= $booking['id'] ?></td>
                                        <td><?= htmlspecialchars($booking['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($booking['customer_phone']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($booking['booking_date'])) ?></td>
                                        <td>
                                            <?php if ($booking['status'] == 'deposit'): ?>
                                                <span class="badge bg-warning">Đã cọc</span>
                                            <?php elseif ($booking['status'] == 'confirmed'): ?>
                                                <span class="badge bg-success">Đã xác nhận</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Thêm vào lịch này
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('checkAll')?.addEventListener('change', function () {
        document.querySelectorAll('.booking-check').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>