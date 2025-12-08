<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <!-- <h3 class="mb-0">Danh sách Lịch Khởi Hành</h3> -->
        </div>
        <a href="<?= BASE_URL ?>?action=departure-schedules/create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tạo Lịch Mới
        </a>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>?action=departure-schedules">
                <input type="hidden" name="action" value="departure-schedules">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tour</label>
                        <select class="form-select" name="tour_id">
                            <option value="">Tất cả</option>
                            <?php foreach ($tours as $tour): ?>
                                <option value="<?= $tour['id'] ?>" <?= (isset($_GET['tour_id']) && $_GET['tour_id'] == $tour['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tour['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="draft" <?= (isset($_GET['status']) && $_GET['status'] == 'draft') ? 'selected' : '' ?>>Nháp</option>
                            <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="in_progress" <?= (isset($_GET['status']) && $_GET['status'] == 'in_progress') ? 'selected' : '' ?>>Đang thực hiện</option>
                            <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="departure_date_from" value="<?= $_GET['departure_date_from'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="departure_date_to" value="<?= $_GET['departure_date_to'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Tên tour, điểm tập trung...">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($schedules)): ?>
                <div class="text-center py-5">
                    <p class="text-muted mb-3">Không có lịch khởi hành nào.</p>
                    <a href="<?= BASE_URL ?>?action=departure-schedules/create" class="btn btn-primary">Tạo lịch mới</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Ngày/Giờ khởi hành</th>
                                <th>Điểm tập trung</th>
                                <th>Ngày/Giờ kết thúc</th>
                                <th>Số lượng</th>
                                <th>Phân bổ</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($schedule['tour_code']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($schedule['tour_name']) ?></small>
                                    </td>
                                    <td>
                                        <strong><?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></strong><br>
                                        <small class="text-muted"><?= date('H:i', strtotime($schedule['departure_time'])) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($schedule['meeting_point']) ?></td>
                                    <td>
                                        <?php if (!empty($schedule['return_date'])): ?>
                                            <strong><?= date('d/m/Y', strtotime($schedule['return_date'])) ?></strong><br>
                                            <small class="text-muted">-</small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $schedule['booked_slots'] ?? 0 ?>/<?= $schedule['available_slots'] ?? '∞' ?>
                                    </td>
                                    <td>
                                        <?php
                                        $guideCount = 0;
                                        $serviceCount = 0;
                                        if (!empty($schedule['assignments'])) {
                                            foreach ($schedule['assignments'] as $assignment) {
                                                if (isset($assignment['assignment_type']) && $assignment['assignment_type'] === 'guide') {
                                                    $guideCount++;
                                                } else {
                                                    $serviceCount++;
                                                }
                                            }
                                        }
                                        ?>
                                        <small>HDV: <?= $guideCount ?><br>DV: <?= $serviceCount ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'confirmed' => 'success',
                                            'in_progress' => 'primary',
                                            'completed' => 'info',
                                            'cancelled' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Nháp',
                                            'confirmed' => 'Đã xác nhận',
                                            'in_progress' => 'Đang thực hiện',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        $color = $statusColors[$schedule['status']] ?? 'secondary';
                                        $label = $statusLabels[$schedule['status']] ?? $schedule['status'];
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=departure-schedules/show&id=<?= $schedule['id'] ?>" 
                                           class="btn btn-sm btn-info" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?action=departure-schedules/edit&id=<?= $schedule['id'] ?>" 
                                           class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?action=departure-schedules/delete&id=<?= $schedule['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa lịch khởi hành này?')" 
                                           title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </a>
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

