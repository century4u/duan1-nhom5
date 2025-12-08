<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-people-fill me-2"></i>Danh sách Đoàn & Lịch khởi
                    hành</h5>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <form method="GET" action="" class="row g-3 mb-4">
                    <input type="hidden" name="action" value="group-lists">

                    <div class="col-md-3">
                        <label class="form-label">Tên Tour / Mã Tour</label>
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..."
                            value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tour</label>
                        <select name="tour_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả Tour --</option>
                            <?php foreach ($tours as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (($filters['tour_id'] ?? '') == $t['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['code'] . ' - ' . $t['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" name="departure_date_from" class="form-control"
                            value="<?= htmlspecialchars($filters['departure_date_from'] ?? '') ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" name="departure_date_to" class="form-control"
                            value="<?= htmlspecialchars($filters['departure_date_to'] ?? '') ?>">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Tìm
                            kiếm</button>
                    </div>
                </form>

                <!-- Departure Schedules List -->
                <?php if (empty($schedules)): ?>
                    <div class="alert alert-info text-center">
                        Không tìm thấy đoàn nào phù hợp với điều kiện tìm kiếm.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Thông tin Đoàn / Tour</th>
                                    <th>Thời gian</th>
                                    <th class="text-center">Số lượng khách</th>
                                    <th class="text-center">Số Booking</th>
                                    <th class="text-center">Dư chỗ</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $index => $s): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($s['tour_name']) ?>
                                            </div>
                                            <div class="small text-muted"><i
                                                    class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($s['tour_code']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><i
                                                    class="bi bi-calendar-event me-1"></i><?= date('d/m/Y', strtotime($s['departure_date'])) ?>
                                            </div>
                                            <div class="small text-muted">
                                                đến <?= date('d/m/Y', strtotime($s['end_date'])) ?>
                                                (<?= formatDuration((strtotime($s['end_date']) - strtotime($s['departure_date'])) / (60 * 60 * 24)) ?>)
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info fs-6"><?= $s['customer_count'] ?? 0 ?> khách</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= $s['booking_count'] ?? 0 ?> đơn</span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            // Assuming seat_limit is available in schedule, if not use default or N/A
                                            $seatLimit = $s['seat_limit'] ?? 20;
                                            $available = max(0, $seatLimit - ($s['customer_count'] ?? 0));
                                            ?>
                                            <?php if ($available > 5): ?>
                                                <span class="text-success fw-bold"><?= $available ?></span>
                                            <?php elseif ($available > 0): ?>
                                                <span class="text-warning fw-bold"><?= $available ?></span>
                                            <?php else: ?>
                                                <span class="text-danger fw-bold">Hết chỗ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>?action=group-lists/show&id=<?= $s['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i> Chi tiết
                                                </a>
                                                <a href="<?= BASE_URL ?>?action=group-lists/print&id=<?= $s['id'] ?>"
                                                    class="btn btn-sm btn-outline-secondary" target="_blank"
                                                    title="In danh sách">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $s['id'] ?>"
                                                    class="btn btn-sm btn-outline-success" title="Check-in">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                            </div>
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
</div>