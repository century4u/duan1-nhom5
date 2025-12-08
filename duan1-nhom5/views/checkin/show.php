<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="<?= BASE_URL ?>?action=checkins" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Quay lại Dashboard
            </a>

            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold"><?= htmlspecialchars($tour['name']) ?></h4>
                        <small class="opacity-75">
                            <i class="bi bi-calendar-event me-1"></i>
                            <?= formatDateRange($schedule['start_date'] ?? 'N/A', $schedule['end_date'] ?? 'N/A') ?>
                            | Mã: <?= htmlspecialchars($tour['code']) ?>
                        </small>
                    </div>
                    <div class="text-end">
                        <h5 class="mb-0">Tổng khách: <?= $stats['total'] ?></h5>
                        <small>Đã check-in: <?= $stats['checked_in'] ?> | Chưa: <?= $stats['pending'] ?></small>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= $_SESSION['success'] ?>
                            <?php unset($_SESSION['success']); ?>
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

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 20%">Khách hàng</th>
                                    <th style="width: 15%">Thông tin cá nhân</th>
                                    <th style="width: 40%">Trạng thái & Ghi chú</th>
                                    <th style="width: 20%">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $index => $customer):
                                    $checkin = $customer['checkin'] ?? null;
                                    $status = $checkin['status'] ?? 'pending';
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold fs-5"><?= $customer['fullname'] ?></div>
                                            <div class="text-muted small">Booking: #<?= $customer['booking_id'] ?></div>
                                        </td>
                                        <td>
                                            <div><i class="bi bi-gender-ambiguous me-1"></i><?= $customer['gender'] ?></div>
                                            <div><i
                                                    class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($customer['birthdate'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <form method="POST" action="<?= BASE_URL ?>?action=checkins/process"
                                                id="form-<?= $customer['id'] ?>" class="d-flex flex-column gap-2">
                                                <input type="hidden" name="booking_detail_id"
                                                    value="<?= $customer['id'] ?>">
                                                <input type="hidden" name="departure_schedule_id"
                                                    value="<?= $schedule['id'] ?>">
                                                <?php if ($checkin): ?>
                                                    <input type="hidden" name="id" value="<?= $checkin['id'] ?>">
                                                    <input type="hidden" name="action_type" value="update">
                                                <?php endif; ?>

                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="status"
                                                        id="status_checkin_<?= $customer['id'] ?>" value="checked_in"
                                                        <?= $status == 'checked_in' ? 'checked' : '' ?>>
                                                    <label class="btn btn-outline-success"
                                                        for="status_checkin_<?= $customer['id'] ?>">Check-in</label>

                                                    <input type="radio" class="btn-check" name="status"
                                                        id="status_late_<?= $customer['id'] ?>" value="late"
                                                        <?= $status == 'late' ? 'checked' : '' ?>>
                                                    <label class="btn btn-outline-warning"
                                                        for="status_late_<?= $customer['id'] ?>">Muộn</label>

                                                    <input type="radio" class="btn-check" name="status"
                                                        id="status_absent_<?= $customer['id'] ?>" value="absent"
                                                        <?= $status == 'absent' ? 'checked' : '' ?>>
                                                    <label class="btn btn-outline-danger"
                                                        for="status_absent_<?= $customer['id'] ?>">Vắng</label>
                                                </div>

                                                <input type="text" class="form-control form-control-sm mt-2 note-input" name="notes"
                                                    value="<?= $checkin['notes'] ?? '' ?>" placeholder="Ghi chú...">
                                            </form>
                                        </td>
                                        <td>
                                            </form>
                                        </td>
                                        <td>
                                            <div id="checkin-info-<?= $customer['id'] ?>">
                                                <?php if ($checkin): ?>
                                                    <div class="small text-muted mb-1">
                                                        <i
                                                            class="bi bi-clock me-1"></i><?= date('H:i d/m/Y', strtotime($checkin['checkin_time'])) ?>
                                                    </div>
                                                    <div class="small text-muted">
                                                        <i
                                                            class="bi bi-person-check me-1"></i><?= $checkin['checked_by_name'] ?? 'Admin' ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Chưa check-in</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container for Notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-bell me-2"></i>
            <strong class="me-auto">Thông báo</strong>
            <small>Vừa xong</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Cập nhật thành công!
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastEl = document.getElementById('liveToast');
        const toastBody = toastEl.querySelector('.toast-body');
        const toast = new bootstrap.Toast(toastEl);

        // Function to send AJAX request
        function sendUpdate(form) {
            const formData = new FormData(form);
            const headers = new Headers();
            headers.append('X-Requested-With', 'XMLHttpRequest');

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: headers
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastBody.textContent = 'Cập nhật thành công!';
                    toastEl.classList.remove('text-bg-danger');
                    toastEl.classList.add('text-bg-success');
                    toast.show();

                    // Update UI Info Column if needed
                    const infoDiv = document.getElementById('checkin-info-' + formData.get('booking_detail_id'));
                    if (data.data && data.data.checkin_time) {
                        infoDiv.innerHTML = `
                            <div class="small text-muted mb-1">
                                <i class="bi bi-clock me-1"></i>${data.data.checkin_time}
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-person-check me-1"></i>${data.data.checked_by}
                            </div>
                        `;
                    }
                } else {
                    toastBody.textContent = 'Lỗi: ' + data.message;
                    toastEl.classList.remove('text-bg-success');
                    toastEl.classList.add('text-bg-danger');
                    toast.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastBody.textContent = 'Lỗi kết nối server!';
                toastEl.classList.remove('text-bg-success');
                toastEl.classList.add('text-bg-danger');
                toast.show();
            });
        }

        // Handle Radio Changes
        document.querySelectorAll('input[type=radio][name=status]').forEach(radio => {
            radio.addEventListener('change', function () {
                sendUpdate(this.closest('form'));
            });
        });

        // Handle Note Blur (Auto Save)
        document.querySelectorAll('input[name=notes]').forEach(input => {
            // Helper to track if value changed
            input.dataset.original = input.value;
            
            input.addEventListener('blur', function () {
                if (this.value !== this.dataset.original) {
                    this.dataset.original = this.value;
                    sendUpdate(this.closest('form'));
                }
            });

            // Prevent form submission on Enter key for notes
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur(); // Trigger blur to save
                }
            });
        });
    });
</script>