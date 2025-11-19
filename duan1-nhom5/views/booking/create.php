<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Đặt Tour Mới</h2>
        <a href="<?= BASE_URL ?>?action=bookings" class="btn btn-secondary">Quay lại</a>
    </div>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['errors']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>?action=bookings/store" id="bookingForm">
        <!-- Thông tin tour -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">1. Chọn Tour</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                    <select class="form-select" id="tour_id" name="tour_id" required>
                        <option value="">-- Chọn tour --</option>
                        <?php foreach ($tours as $t): ?>
                            <option value="<?= $t['id'] ?>" 
                                    <?= (isset($tour) && $tour['id'] == $t['id']) ? 'selected' : '' ?>
                                    data-price="<?= $t['price'] ?>"
                                    data-duration="<?= $t['duration'] ?>"
                                    data-destination="<?= htmlspecialchars($t['destination']) ?>">
                                <?= htmlspecialchars($t['name']) ?> - <?= htmlspecialchars($t['code']) ?> 
                                (<?= number_format($t['price'], 0, ',', '.') ?> đ)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($tour)): ?>
                        <div class="mt-2">
                            <p class="mb-1"><strong>Điểm đến:</strong> <?= htmlspecialchars($tour['destination']) ?></p>
                            <p class="mb-1"><strong>Thời gian:</strong> <?= $tour['duration'] ?> ngày</p>
                            <p class="mb-0"><strong>Giá cơ bản:</strong> <?= number_format($tour['price'], 0, ',', '.') ?> đ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">2. Thông tin liên hệ</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="contact_name" class="form-label">Tên người liên hệ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_name" name="contact_name" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['contact_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contact_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                               value="<?= htmlspecialchars($_SESSION['old_data']['contact_phone'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="contact_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="contact_email" name="contact_email" 
                           value="<?= htmlspecialchars($_SESSION['old_data']['contact_email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="booking_type" class="form-label">Loại đặt tour</label>
                    <select class="form-select" id="booking_type" name="booking_type">
                        <option value="individual">Khách lẻ (1-2 người)</option>
                        <option value="group">Đoàn (3+ người)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="special_requests" class="form-label">Yêu cầu đặc biệt</label>
                    <textarea class="form-control" id="special_requests" name="special_requests" rows="3" 
                              placeholder="Ví dụ: Yêu cầu phòng đơn, dị ứng thức ăn, yêu cầu đặc biệt khác..."><?= htmlspecialchars($_SESSION['old_data']['special_requests'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">3. Thông tin khách hàng</h5>
                <button type="button" class="btn btn-light btn-sm" onclick="addParticipant()">
                    <i class="bi bi-plus-circle"></i> Thêm khách
                </button>
            </div>
            <div class="card-body">
                <div id="participantsContainer">
                    <!-- Participant sẽ được thêm bằng JavaScript -->
                    <div class="participant-item border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Khách hàng 1</h6>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeParticipant(this)" style="display: none;">
                                <i class="bi bi-trash"></i> Xóa
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="participants[0][fullname]" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Giới tính</label>
                                <select class="form-select" name="participants[0][gender]">
                                    <option value="">-- Chọn --</option>
                                    <option value="male">Nam</option>
                                    <option value="female">Nữ</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" name="participants[0][birthdate]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tóm tắt và xác nhận -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">4. Tóm tắt</h5>
            </div>
            <div class="card-body">
                <div id="summaryContent">
                    <p class="text-muted">Vui lòng chọn tour và nhập thông tin để xem tóm tắt</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="<?= BASE_URL ?>?action=bookings" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Xác nhận đặt tour</button>
        </div>
    </form>
</div>

<script>
let participantCount = 1;

function addParticipant() {
    const container = document.getElementById('participantsContainer');
    const newItem = document.createElement('div');
    newItem.className = 'participant-item border rounded p-3 mb-3';
    newItem.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Khách hàng ${participantCount + 1}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeParticipant(this)">
                <i class="bi bi-trash"></i> Xóa
            </button>
        </div>
        <div class="row">
            <div class="col-md-6 mb-2">
                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="participants[${participantCount}][fullname]" required>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Giới tính</label>
                <select class="form-select" name="participants[${participantCount}][gender]">
                    <option value="">-- Chọn --</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" name="participants[${participantCount}][birthdate]">
            </div>
        </div>
    `;
    container.appendChild(newItem);
    participantCount++;
    
    // Hiện nút xóa cho item đầu tiên nếu có nhiều hơn 1
    updateRemoveButtons();
}

function removeParticipant(button) {
    const item = button.closest('.participant-item');
    item.remove();
    updateRemoveButtons();
    updateParticipantNumbers();
}

function updateRemoveButtons() {
    const items = document.querySelectorAll('.participant-item');
    items.forEach((item, index) => {
        const removeBtn = item.querySelector('.btn-danger');
        if (items.length > 1) {
            removeBtn.style.display = 'block';
        } else {
            removeBtn.style.display = 'none';
        }
    });
}

function updateParticipantNumbers() {
    const items = document.querySelectorAll('.participant-item');
    items.forEach((item, index) => {
        const title = item.querySelector('h6');
        title.textContent = `Khách hàng ${index + 1}`;
    });
}

// Cập nhật tóm tắt khi thay đổi
document.getElementById('tour_id').addEventListener('change', updateSummary);
document.getElementById('participantsContainer').addEventListener('input', updateSummary);

function updateSummary() {
    const tourSelect = document.getElementById('tour_id');
    const selectedOption = tourSelect.options[tourSelect.selectedIndex];
    const summaryContent = document.getElementById('summaryContent');
    
    if (!selectedOption.value) {
        summaryContent.innerHTML = '<p class="text-muted">Vui lòng chọn tour và nhập thông tin để xem tóm tắt</p>';
        return;
    }
    
    const tourName = selectedOption.text;
    const basePrice = parseFloat(selectedOption.dataset.price) || 0;
    const participantInputs = document.querySelectorAll('input[name*="[fullname]"]');
    const participantCount = Array.from(participantInputs).filter(input => input.value.trim() !== '').length;
    
    let summary = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Tour:</strong> ${tourName}</p>
                <p><strong>Số lượng khách:</strong> ${participantCount} người</p>
            </div>
            <div class="col-md-6">
                <p><strong>Giá ước tính:</strong> <span class="text-danger fw-bold">${(basePrice * participantCount).toLocaleString('vi-VN')} đ</span></p>
                <small class="text-muted">(Giá có thể thay đổi tùy theo độ tuổi của khách hàng)</small>
            </div>
        </div>
    `;
    
    summaryContent.innerHTML = summary;
}

<?php unset($_SESSION['old_data']); ?>
</script>

