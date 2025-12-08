<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h3>Đặt Tour Mới</h3>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php unset($_SESSION['errors']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>?action=bookings/store" id="bookingForm">
                <!-- Bước 1: Chọn Đợt Đi -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">1. Chọn Đợt Khởi Hành</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="departure_schedule_id" class="form-label">Chọn đợt đi <span
                                    class="text-danger">*</span></label>
                            <?php if (empty($departureSchedules)): ?>
                                <div class="alert alert-warning">
                                    Hiện tại không có đợt khởi hành nào sắp tới. Vui lòng quay lại sau.
                                </div>
                            <?php else: ?>
                                <select class="form-select" id="departure_schedule_id" name="departure_schedule_id"
                                    required>
                                    <option value="">-- Chọn đợt khởi hành --</option>
                                    <?php foreach ($departureSchedules as $ds): ?>
                                        <option value="<?= $ds['id'] ?>"
                                            data-tour-name="<?= htmlspecialchars($ds['tour_name']) ?>"
                                            data-destination="<?= htmlspecialchars($ds['destination']) ?>"
                                            data-duration="<?= $ds['duration'] ?>" data-price="<?= $ds['price'] ?>"
                                            data-start="<?= $ds['departure_date'] ?>" data-end="<?= $ds['end_date'] ?>"
                                            data-guide-count="<?= count($ds['guides']) ?>"
                                            data-available="<?= $ds['available_slots'] ?>">
                                            [<?= $ds['tour_code'] ?>] <?= htmlspecialchars($ds['tour_name']) ?>
                                            - <?= formatDateRange($ds['departure_date'], $ds['end_date']) ?>
                                            (Còn <?= $ds['available_slots'] ?> chỗ)
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div id="schedule-info" class="mt-3" style="display:none;">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">Thông tin đợt đi:</h6>
                                        <p class="mb-1"><strong>Tour:</strong> <span id="info-tour-name"></span></p>
                                        <p class="mb-1"><strong>Điểm đến:</strong> <span id="info-destination"></span></p>
                                        <p class="mb-1"><strong>Thời gian:</strong> <span id="info-duration"></span></p>
                                        <p class="mb-1"><strong>Giá cơ bản:</strong> <span id="info-price"
                                                class="text-danger fw-bold"></span></p>
                                        <p class="mb-0"><strong>Còn trống:</strong> <span id="info-available"
                                                class="badge bg-success"></span> chỗ</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Bước 2: Chọn Hướng dẫn viên -->
                <?php if (!empty($departureSchedules)): ?>
                    <div class="card mb-4" id="guide-selection" style="display:none;">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">2. Chọn Hướng Dẫn Viên (tùy chọn)</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Chọn hướng dẫn viên bạn muốn đi cùng. Nếu không chọn, admin sẽ phân bổ tự
                                động.</p>
                            <div id="guides-container" class="row g-3">
                                <!-- Dynamically loaded -->
                            </div>
                        </div>
                    </div>

                    <!-- Rest of the form continues with booking details... -->
                    <?php require_once __DIR__ . '/_booking_form_participants.php'; ?>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            Xác nhận Đặt Tour
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
    const departureSchedules = <?= json_encode($departureSchedules) ?>;

    // Khi chọn departure schedule
    document.getElementById('departure_schedule_id')?.addEventListener('change', function () {
        const scheduleId = this.value;
        const selected = this.options[this.selectedIndex];

        if (!scheduleId) {
            document.getElementById('schedule-info').style.display = 'none';
            document.getElementById('guide-selection').style.display = 'none';
            document.getElementById('submit-btn').disabled = true;
            return;
        }

        // Hiển thị thông tin đợt đi
        document.getElementById('info-tour-name').textContent = selected.dataset.tourName;
        document.getElementById('info-destination').textContent = selected.dataset.destination;
        document.getElementById('info-duration').textContent = selected.dataset.duration + ' ngày';
        document.getElementById('info-price').textContent = parseFloat(selected.dataset.price).toLocaleString('vi-VN') + ' đ';
        document.getElementById('info-available').textContent = selected.dataset.available;
        document.getElementById('schedule-info').style.display = 'block';

        // Load HDV cho lịch này
        const schedule = departureSchedules.find(ds => ds.id == scheduleId);
        if (schedule && schedule.guides && schedule.guides.length > 0) {
            loadGuides(schedule.guides);
            document.getElementById('guide-selection').style.display = 'block';
        } else {
            document.getElementById('guide-selection').style.display = 'none';
        }

        document.getElementById('submit-btn').disabled = false;
    });

    function loadGuides(guides) {
        const container = document.getElementById('guides-container');
        container.innerHTML = '';

        if (guides.length === 0) {
            container.innerHTML = '<div class="col-12"><p class="text-muted">Chưa có HDV được phân công cho đợt này.</p></div>';
            return;
        }

        guides.forEach(guide => {
            const col = document.createElement('div');
            col.className = 'col-md-4';
            col.innerHTML = `
            <div class="card h-100 guide-card" onclick="selectGuide(${guide.id}, this)">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <input type="radio" name="guide_id" value="${guide.id}" id="guide_${guide.id}" class="form-check-input me-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${guide.full_name}</h6>
                            <small class="text-muted">Mã: ${guide.code || 'N/A'}</small><br>
                            <small class="text-success">${guide.booking_count || 0} khách đã đặt</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
            container.appendChild(col);
        });
    }

    function selectGuide(guideId, card) {
        // Remove selected class from all cards
        document.querySelectorAll('.guide-card').forEach(c => c.classList.remove('border-primary', 'bg-light'));

        // Select this guide
        document.getElementById(`guide_${guideId}`).checked = true;
        card.classList.add('border-primary', 'bg-light');
    }
</script>

<style>
    .guide-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .guide-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>