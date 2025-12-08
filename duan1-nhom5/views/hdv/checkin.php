<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'HDV Check-in' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .customer-card {
            transition: all 0.2s;
            border-left: 5px solid #ccc;
        }

        .customer-card.checked-in {
            border-left-color: #198754;
            background-color: #f0fff4;
        }

        .customer-card.absent {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }

        .customer-card.late {
            border-left-color: #ffc107;
        }

        .btn-check-toggle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="bg-primary text-white sticky-top shadow-sm p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <a href="<?= BASE_URL ?>?action=hvd/home" class="text-white text-decoration-none fs-5">
                <i class="bi bi-chevron-left"></i>
            </a>
            <h5 class="mb-0 text-truncate" style="max-width: 70%;"><?= htmlspecialchars($tour['name']) ?></h5>
            <div class="fw-bold"><span id="checked-count"><?= $stats['checked_in'] ?></span>/<?= $stats['total'] ?>
            </div>
        </div>
    </div>

    <div class="container pb-5">

        <!-- Search -->
        <div class="mb-3">
            <input type="text" class="form-control" id="searchInput" placeholder="Tìm tên khách..."
                onkeyup="filterCustomers()">
        </div>

        <!-- List -->
        <div id="customerList">
            <?php foreach ($customers as $customer):
                $checkin = $customer['checkin'] ?? null;
                $status = $checkin['status'] ?? 'pending';
                ?>
                <div class="card mb-2 shadow-sm border-0 customer-card <?= $status == 'checked_in' ? 'checked-in' : '' ?>"
                    id="card-<?= $customer['id'] ?>">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center overflow-hidden">
                            <div class="me-3 text-center" style="min-width: 40px;">
                                <div class="fw-bold text-muted small">#<?= $customer['id'] ?></div>
                                <i class="bi bi-person-circle fs-3 text-secondary"></i>
                            </div>
                            <div class="text-truncate">
                                <h6 class="mb-0 fw-bold customer-name"><?= htmlspecialchars($customer['fullname']) ?></h6>
                                <small class="text-muted"><?= $customer['gender'] ?> -
                                    <?= date('Y', strtotime($customer['birthdate'])) ?></small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <!-- Check-in Button -->
                            <button
                                class="btn btn-outline-success btn-check-toggle <?= $status == 'checked_in' ? 'active' : '' ?>"
                                onclick="toggleCheckin(<?= $customer['id'] ?>, <?= $schedule['id'] ?>, this)">
                                <i class="bi bi-check-lg fs-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($customers)): ?>
            <div class="text-center text-muted mt-5">
                <i class="bi bi-people fs-1 d-block mb-3"></i>
                <p>Chưa có khách hàng nào trong lịch trình này.</p>
            </div>
        <?php endif; ?>

    </div>

    <!-- Toast -->
    <div class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3">
        <div id="statusToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Cập nhật thành công!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toastEl = document.getElementById('statusToast');
        const toast = new bootstrap.Toast(toastEl);
        const toastBody = toastEl.querySelector('.toast-body');

        function toggleCheckin(bookingDetailId, scheduleId, btn) {
            // Determine new status
            const isChecked = btn.classList.contains('active');
            const newStatus = isChecked ? 'pending' : 'checked_in'; // Toggle Logic

            // Optimistic UI Update
            const card = document.getElementById('card-' + bookingDetailId);
            if (!isChecked) {
                btn.classList.add('active', 'btn-success');
                btn.classList.remove('btn-outline-success');
                card.classList.add('checked-in');
            } else {
                btn.classList.remove('active', 'btn-success');
                btn.classList.add('btn-outline-success');
                card.classList.remove('checked-in');
            }

            // AJAX Call
            const formData = new FormData();
            formData.append('booking_detail_id', bookingDetailId);
            formData.append('departure_schedule_id', scheduleId);
            formData.append('status', newStatus);

            fetch('<?= BASE_URL ?>?action=hvd/checkin/process', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update header count
                        updateCount();
                        showToast(newStatus === 'checked_in' ? 'Đã check-in' : 'Đã hủy check-in');
                    } else {
                        // Revert UI on error
                        alert('Lỗi: ' + data.message);
                        if (!isChecked) {
                            btn.classList.remove('active', 'btn-success');
                            btn.classList.add('btn-outline-success');
                            card.classList.remove('checked-in');
                        } else {
                            btn.classList.add('active', 'btn-success');
                            btn.classList.remove('btn-outline-success');
                            card.classList.add('checked-in');
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi kết nối!');
                });
        }

        function updateCount() {
            const count = document.querySelectorAll('.customer-card.checked-in').length;
            document.getElementById('checked-count').innerText = count;
        }

        function filterCustomers() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const cards = document.getElementsByClassName('customer-card');

            for (let i = 0; i < cards.length; i++) {
                const name = cards[i].querySelector('.customer-name').innerText;
                if (name.toLowerCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

        function showToast(msg) {
            toastBody.innerText = msg;
            toast.show();
        }
    </script>
</body>

</html>