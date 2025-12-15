<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=group-lists"
                        class="text-decoration-none">Danh sách đoàn</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết đoàn</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-1">Chi tiết đoàn: <?= htmlspecialchars($schedule['tour_name']) ?>
                </h4>
                <div class="text-muted"><i class="bi bi-upc-scan me-1"></i>Mã đoàn:
                    <?= htmlspecialchars($schedule['tour_code']) ?>
                </div>
            </div>

            <div class="btn-group">
                <a href="<?= BASE_URL ?>?action=group-lists/print&id=<?= $schedule['id'] ?>"
                    class="btn btn-outline-primary" target="_blank">
                    <i class="bi bi-printer me-2"></i>In danh sách
                </a>
                <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $schedule['id'] ?>"
                    class="btn btn-success">
                    <i class="bi bi-qr-code-scan me-2"></i>Điểm danh
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Info Cards -->
        <div class="row g-4 mb-4">
            <!-- Schedule Info -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-info-circle me-2"></i>Thông tin lịch
                            trình</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="small text-muted text-uppercase mb-1">Ngày khởi hành</label>
                                <div class="fw-bold fs-5 text-primary">
                                    <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?>
                                </div>
                                <div class="small text-muted">
                                    <?= date('H:i', strtotime($schedule['departure_time'])) ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="small text-muted text-uppercase mb-1">Ngày kết thúc</label>
                                <div class="fw-bold fs-5">
                                    <?= date('d/m/Y', strtotime($schedule['end_date'])) ?>
                                </div>
                                <div class="small text-muted">
                                    <?= date('H:i', strtotime($schedule['end_time'])) ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="small text-muted text-uppercase mb-1">Điểm tập trung</label>
                                <div><i
                                        class="bi bi-geo-alt-fill text-danger me-1"></i><?= htmlspecialchars($schedule['meeting_point']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Info -->
            <div class="col-md-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-pie-chart me-2"></i>Thống kê hành khách
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3">
                            <div class="col-3">
                                <div class="p-2 rounded bg-light">
                                    <div class="h4 mb-0 fw-bold text-primary"><?= $stats['total'] ?></div>
                                    <div class="small text-muted">Tổng</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 rounded bg-success-subtle">
                                    <div class="h4 mb-0 fw-bold text-success"><?= $stats['checked_in'] ?></div>
                                    <div class="small text-success">Đã đến</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 rounded bg-warning-subtle">
                                    <div class="h4 mb-0 fw-bold text-warning"><?= $stats['pending'] ?></div>
                                    <div class="small text-warning">Chưa đến</div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 rounded bg-danger-subtle">
                                    <div class="h4 mb-0 fw-bold text-danger"><?= $stats['absent'] ?></div>
                                    <div class="small text-danger">Vắng/Hủy</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-top d-flex justify-content-between text-muted small">
                            <span>Nam: <strong><?= $stats['male'] ?></strong></span>
                            <span>Nữ: <strong><?= $stats['female'] ?></strong></span>
                            <span>Khác:
                                <strong><?= $stats['total'] - $stats['male'] - $stats['female'] ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer List -->
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Danh sách hành khách</h5>
                <div class="input-group input-group-sm w-auto">
                    <input type="text" id="filterInput" class="form-control" placeholder="Tìm tên khách...">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($customers)): ?>
                    <div class="text-center p-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
                        <p>Chưa có hành khách nào trong đoàn này.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0" id="customerTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">STT</th>
                                    <th>Họ và tên</th>
                                    <th class="text-center" style="width: 100px;">Giới tính</th>
                                    <th class="text-center" style="width: 120px;">Năm sinh</th>
                                    <th>Liên lạc</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center" style="width: 120px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $statusLabels = [
                                    'pending' => ['label' => 'Chưa check-in', 'class' => 'bg-secondary'],
                                    'checked_in' => ['label' => 'Đã check-in', 'class' => 'bg-success'],
                                    'late' => ['label' => 'Đến muộn', 'class' => 'bg-warning text-dark'],
                                    'absent' => ['label' => 'Vắng mặt', 'class' => 'bg-danger'],
                                    'cancelled' => ['label' => 'Đã hủy', 'class' => 'bg-dark']
                                ];
                                $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                ?>
                                <?php foreach ($customers as $index => $customer): ?>
                                    <tr>
                                        <td class="text-center fw-bold text-muted"><?= $index + 1 ?></td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($customer['fullname']) ?></div>
                                            <?php if (!empty($customer['checkin']) && !empty($customer['checkin']['notes'])): ?>
                                                <small class="text-danger fst-italic"><i
                                                        class="bi bi-chat-text me-1"></i><?= htmlspecialchars($customer['checkin']['notes']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $genderLabels[$customer['gender']] ?? 'N/A' ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($customer['birthdate']): ?>
                                                <?= date('Y', strtotime($customer['birthdate'])) ?>
                                                <div class="small text-muted">
                                                    (<?= (int) date('Y') - (int) date('Y', strtotime($customer['birthdate'])) ?>
                                                    tuổi)
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($customer['customer_phone'])): ?>
                                                <div><i class="bi bi-telephone-fill text-muted me-1"
                                                        style="font-size: 0.8rem;"></i><?= htmlspecialchars($customer['customer_phone']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($customer['customer_email']): ?>
                                                <div class="small text-muted text-truncate" style="max-width: 150px;"
                                                    title="<?= htmlspecialchars($customer['customer_email']) ?>">
                                                    <?= htmlspecialchars($customer['customer_email']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $status = !empty($customer['checkin']) ? ($customer['checkin']['status'] ?? 'pending') : 'pending';
                                            $stInfo = $statusLabels[$status] ?? ['label' => $status, 'class' => 'bg-secondary'];
                                            ?>
                                            <span class="badge <?= $stInfo['class'] ?>"><?= $stInfo['label'] ?></span>
                                            <?php if (!empty($customer['checkin']) && !empty($customer['checkin']['checkin_time'])): ?>
                                                <div class="small text-muted mt-1">
                                                    <?= date('H:i', strtotime($customer['checkin']['checkin_time'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>?action=checkins/show&departure_schedule_id=<?= $schedule['id'] ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square"></i> Cập nhật
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
</div>

<script>
    document.getElementById('filterInput').addEventListener('keyup', function () {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#customerTable tbody").rows;

        for (let i = 0; i < rows.length; i++) {
            let nameCol = rows[i].cells[1].textContent;
            let phoneCol = rows[i].cells[4].textContent;
            if (nameCol.toUpperCase().indexOf(filter) > -1 || phoneCol.toUpperCase().indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    });
</script>