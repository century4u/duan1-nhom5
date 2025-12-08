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
                    <select class="form-select" id="tour_id" name="tour_id" required onchange="filterSchedules()">
                        <option value="">-- Chọn tour --</option>
                        <?php foreach ($tours as $t): ?>
                            <option value="<?= $t['id'] ?>" data-price="<?= $t['price'] ?>"
                                data-duration="<?= $t['duration'] ?>"
                                data-destination="<?= htmlspecialchars($t['destination']) ?>">
                                <?= htmlspecialchars($t['name']) ?> - <?= htmlspecialchars($t['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="departure_schedule_id" class="form-label">Lịch khởi hành <span
                            class="text-danger">*</span></label>
                    <select class="form-select" id="departure_schedule_id" name="departure_schedule_id" required>
                        <option value="">-- Chọn lịch khởi hành --</option>
                        <?php foreach ($departureSchedules as $schedule): ?>
                            <option value="<?= $schedule['id'] ?>" data-tour-id="<?= $schedule['tour_id'] ?>"
                                data-available="<?= $schedule['available_slots'] ?>" style="display: none;">
                                <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                (Còn <?= $schedule['available_slots'] ?> chỗ)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="scheduleMsg" class="form-text text-danger" style="display: none;">Không có lịch khởi hành
                        cho tour này.</div>
                </div>

                <div class="mb-3">
                    <label for="guide_id" class="form-label">Hướng dẫn viên (Tùy chọn)</label>
                    <select class="form-select" id="guide_id" name="guide_id">
                        <option value="">-- Chọn hướng dẫn viên --</option>
                        <?php foreach ($guides as $g): ?>
                            <option value="<?= $g['id'] ?>">
                                <?= htmlspecialchars($g['full_name']) ?> - <?= htmlspecialchars($g['code']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="tourInfo" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <p class="mb-1"><strong>Điểm đến:</strong> <span id="infoDestination"></span></p>
                        <p class="mb-1"><strong>Thời gian:</strong> <span id="infoDuration"></span> ngày</p>
                        <p class="mb-0"><strong>Giá cơ bản:</strong> <span id="infoPrice"></span> đ</p>
                    </div>
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
                        <label for="contact_name" class="form-label">Tên người liên hệ <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_name" name="contact_name"
                            value="<?= htmlspecialchars($_SESSION['old_data']['contact_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contact_phone" class="form-label">Số điện thoại <span
                                class="text-danger">*</span></label>
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
                        <option value="custom">Tuỳ chỉnh số lượng</option>
                    </select>
                </div>
                <div class="mb-3" id="custom_quantity_container" style="display: none;">
                    <label for="custom_quantity" class="form-label">Số lượng khách <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="custom_quantity" min="1" max="50"
                            placeholder="Nhập số lượng khách muốn đăng ký">
                        <button class="btn btn-primary" type="button" onclick="applyCustomQuantity()">
                            <i class="bi bi-check-lg me-1"></i> Áp dụng
                        </button>
                    </div>
                    <div class="form-text">Nhập số lượng và nhấn "Áp dụng" để tạo nhanh danh sách khách hàng.</div>
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
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeParticipant(this)"
                                    style="display: none;">
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

                            <!-- Ghi chú đặc biệt -->
                            <div class="mt-2">
                                <a class="text-decoration-none" data-bs-toggle="collapse" href="#specialNotes0"
                                    role="button" aria-expanded="false">
                                    <i class="bi bi-plus-circle"></i> Ghi chú đặc biệt (tùy chọn)
                                </a>
                                <div class="collapse mt-2" id="specialNotes0">
                                    <div class="card card-body bg-light">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">
                                                    <i class="bi bi-egg-fried text-success"></i> Hạn chế ăn uống
                                                </label>
                                                <textarea class="form-control form-control-sm"
                                                    name="participants[0][dietary_restrictions]" rows="2"
                                                    placeholder="Ví dụ: Ăn chay, kiêng hải sản, dị ứng đậu phộng..."></textarea>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">
                                                    <i class="bi bi-heart-pulse text-danger"></i> Tình trạng sức khỏe
                                                </label>
                                                <textarea class="form-control form-control-sm"
                                                    name="participants[0][medical_conditions]" rows="2"
                                                    placeholder="Ví dụ: Dị ứng thuốc, tim mạch, tiểu đường..."></textarea>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">
                                                    <i class="bi bi-star text-warning"></i> Yêu cầu đặc biệt
                                                </label>
                                                <textarea class="form-control form-control-sm"
                                                    name="participants[0][special_requirements]" rows="2"
                                                    placeholder="Ví dụ: Cần phòng đơn, cần xe lăn, ghế ngồi đặc biệt..."></textarea>
                                            </div>
                                        </div>
                                    </div>
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
        
        <!-- Ghi chú đặc biệt -->
        <div class="mt-2">
            <a class="text-decoration-none" data-bs-toggle="collapse" href="#specialNotes${participantCount}" role="button" aria-expanded="false">
                <i class="bi bi-plus-circle"></i> Ghi chú đặc biệt (tùy chọn)
            </a>
            <div class="collapse mt-2" id="specialNotes${participantCount}">
                <div class="card card-body bg-light">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">
                                <i class="bi bi-egg-fried text-success"></i> Hạn chế ăn uống
                            </label>
                            <textarea class="form-control form-control-sm" name="participants[${participantCount}][dietary_restrictions]" rows="2" 
                                      placeholder="Ví dụ: Ăn chay, kiêng hải sản, dị ứng đậu phộng..."></textarea>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">
                                <i class="bi bi-heart-pulse text-danger"></i> Tình trạng sức khỏe
                            </label>
                            <textarea class="form-control form-control-sm" name="participants[${participantCount}][medical_conditions]" rows="2" 
                                      placeholder="Ví dụ: Dị ứng thuốc, tim mạch, tiểu đường..."></textarea>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">
                                <i class="bi bi-star text-warning"></i> Yêu cầu đặc biệt
                            </label>
                            <textarea class="form-control form-control-sm" name="participants[${participantCount}][special_requirements]" rows="2" 
                                      placeholder="Ví dụ: Cần phòng đơn, cần xe lăn, ghế ngồi đặc biệt..."></textarea>
                        </div>
                    </div>
                </div>
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

    // Handle Custom Booking Type
    document.getElementById('booking_type').addEventListener('change', function() {
        const customContainer = document.getElementById('custom_quantity_container');
        if (this.value === 'custom') {
            customContainer.style.display = 'block';
            document.getElementById('custom_quantity').focus();
        } else {
            customContainer.style.display = 'none';
        }
    });

    function applyCustomQuantity() {
        const qtyInput = document.getElementById('custom_quantity');
        const qty = parseInt(qtyInput.value);
        
        if (!qty || qty < 1) {
            alert('Vui lòng nhập số lượng khách hợp lệ (lớn hơn 0).');
            qtyInput.focus();
            return;
        }

        if (confirm(`Bạn có chắc chắn muốn tạo danh sách cho ${qty} khách không? Dữ liệu hiện tại trong form khách hàng sẽ bị thay thế.`)) {
            const container = document.getElementById('participantsContainer');
            container.innerHTML = ''; // Clear existing forms
            participantCount = 0; // Reset counter

            // Create loop to add participants
            for (let i = 0; i < qty; i++) {
                addParticipant();
            }

            // Update summary
            updateSummary();
            
            // Show success message (optional)
            // alert(`Đã tạo ${qty} form khách hàng.`);
        }
    }

    // Cập nhật tóm tắt khi thay đổi
    document.getElementById('tour_id').addEventListener('change', updateSummary);
    document.getElementById('departure_schedule_id').addEventListener('change', updateSummary); // Added listener
    document.getElementById('participantsContainer').addEventListener('input', updateSummary);

    function filterSchedules() {
        const tourSelect = document.getElementById('tour_id');
        const scheduleSelect = document.getElementById('departure_schedule_id');
        const selectedTourId = tourSelect.value;
        const tourInfo = document.getElementById('tourInfo');
        const scheduleMsg = document.getElementById('scheduleMsg');

        // Reset schedule selection
        scheduleSelect.value = "";

        let hasSchedule = false;

        // Show/Hide schedules based on selected tour
        Array.from(scheduleSelect.options).forEach(option => {
            if (option.value === "") return; // Skip default option

            const tourId = option.getAttribute('data-tour-id');
            if (tourId === selectedTourId) {
                option.style.display = 'block';
                hasSchedule = true;
            } else {
                option.style.display = 'none';
            }
        });

        // Show warning if no schedules
        if (selectedTourId && !hasSchedule) {
            scheduleMsg.style.display = 'block';
        } else {
            scheduleMsg.style.display = 'none';
        }

        // Show Tour Info
        if (selectedTourId) {
            const selectedOption = tourSelect.options[tourSelect.selectedIndex];

            const destEl = document.getElementById('infoDestination');
            const durEl = document.getElementById('infoDuration');
            const priceEl = document.getElementById('infoPrice');

            if (destEl) destEl.textContent = selectedOption.dataset.destination;
            if (durEl) durEl.textContent = selectedOption.dataset.duration;
            if (priceEl) priceEl.textContent = new Intl.NumberFormat('vi-VN').format(selectedOption.dataset.price);

            if (tourInfo) tourInfo.style.display = 'block';
        } else {
            if (tourInfo) tourInfo.style.display = 'none';
        }
    }

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