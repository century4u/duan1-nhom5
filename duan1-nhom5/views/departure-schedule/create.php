<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Tạo Lịch Khởi Hành Mới</h3>
        <a href="<?= BASE_URL ?>?action=departure-schedules" class="btn btn-secondary">Quay lại</a>
    </div>

    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/store" id="scheduleForm">
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
                                            <?= (isset($_SESSION['old_data']['tour_id']) && $_SESSION['old_data']['tour_id'] == $tour['id']) ? 'selected' : '' ?>>
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
                                           value="<?= $_SESSION['old_data']['departure_date'] ?? '' ?>" 
                                           min="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departure_time" class="form-label">Giờ khởi hành <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="departure_time" name="departure_time" 
                                           value="<?= $_SESSION['old_data']['departure_time'] ?? '08:00' ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="meeting_point" class="form-label">Điểm tập trung <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="meeting_point" name="meeting_point" 
                                   value="<?= htmlspecialchars($_SESSION['old_data']['meeting_point'] ?? '') ?>" 
                                   placeholder="Ví dụ: Sân bay Nội Bài, Ga Hà Nội..." required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= $_SESSION['old_data']['end_date'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" 
                                           value="<?= $_SESSION['old_data']['end_time'] ?? '18:00' ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_participants" class="form-label">Số lượng tối đa</label>
                                    <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                           value="<?= $_SESSION['old_data']['max_participants'] ?? '' ?>" 
                                           min="1" placeholder="Để trống nếu không giới hạn">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="draft" <?= (isset($_SESSION['old_data']['status']) && $_SESSION['old_data']['status'] == 'draft') ? 'selected' : 'selected' ?>>Nháp</option>
                                        <option value="confirmed" <?= (isset($_SESSION['old_data']['status']) && $_SESSION['old_data']['status'] == 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Ghi chú về lịch khởi hành..."><?= htmlspecialchars($_SESSION['old_data']['notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Hướng dẫn</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small">
                            <li>Chọn tour từ danh sách</li>
                            <li>Nhập ngày và giờ khởi hành</li>
                            <li>Xác định điểm tập trung rõ ràng</li>
                            <li>Ngày kết thúc phải sau ngày khởi hành</li>
                            <li>Sau khi tạo, bạn có thể phân bổ nhân sự và dịch vụ</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>?action=departure-schedules" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Tạo Lịch Khởi Hành</button>
        </div>
    </form>
</div>

<script>
// Tự động tính ngày kết thúc dựa trên duration của tour
document.getElementById('tour_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const duration = parseInt(selectedOption.getAttribute('data-duration')) || 0;
    const departureDate = document.getElementById('departure_date').value;
    
    if (departureDate && duration > 0) {
        const startDate = new Date(departureDate);
        startDate.setDate(startDate.getDate() + duration - 1);
        document.getElementById('end_date').value = startDate.toISOString().split('T')[0];
    }
});

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

<?php unset($_SESSION['old_data']); ?>

