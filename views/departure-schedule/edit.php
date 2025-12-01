<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Chỉnh sửa Lịch Khởi Hành</h3>
        <a href="<?= BASE_URL ?>?action=departure-schedules/show&id=<?= $schedule['id'] ?>" class="btn btn-secondary">Quay lại</a>
    </div>

    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/update" id="scheduleForm">
        <input type="hidden" name="id" value="<?= $schedule['id'] ?>">
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin Lịch Khởi Hành</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                            <select class="form-select" id="tour_id" name="tour_id" required>
                                <option value="">-- Chọn Tour --</option>
                                <?php foreach ($tours as $tour): ?>
                                    <option value="<?= $tour['id'] ?>" 
                                            data-duration="<?= $tour['duration'] ?>"
                                            <?= ($schedule['tour_id'] == $tour['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tour['code'] . ' - ' . $tour['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departure_date" class="form-label">Ngày khởi hành <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="departure_date" name="departure_date" 
                                           value="<?= $schedule['departure_date'] ?>" min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departure_time" class="form-label">Giờ khởi hành <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="departure_time" name="departure_time" 
                                           value="<?= date('H:i', strtotime($schedule['departure_time'])) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="meeting_point" class="form-label">Điểm tập trung <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="meeting_point" name="meeting_point" 
                                   value="<?= htmlspecialchars($schedule['meeting_point']) ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= $schedule['end_date'] ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" 
                                           value="<?= date('H:i', strtotime($schedule['end_time'])) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_participants" class="form-label">Số lượng tối đa</label>
                                    <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                           value="<?= $schedule['max_participants'] ?? '' ?>" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="draft" <?= ($schedule['status'] == 'draft') ? 'selected' : '' ?>>Nháp</option>
                                        <option value="confirmed" <?= ($schedule['status'] == 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                                        <option value="in_progress" <?= ($schedule['status'] == 'in_progress') ? 'selected' : '' ?>>Đang thực hiện</option>
                                        <option value="completed" <?= ($schedule['status'] == 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="cancelled" <?= ($schedule['status'] == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($schedule['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>?action=departure-schedules/show&id=<?= $schedule['id'] ?>" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </div>
    </form>
</div>

<script>
// Validate form
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    const departureDate = document.getElementById('departure_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (departureDate && endDate) {
        if (new Date(endDate) < new Date(departureDate)) {
            e.preventDefault();
            alert('Ngày kết thúc phải sau ngày khởi hành!');
            return false;
        }
    }
});
</script>

