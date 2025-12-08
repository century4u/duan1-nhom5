<div class="row">
    <div class="col-12">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=operation-reports"
                        class="text-decoration-none">Báo cáo vận hành</a></li>
                <li class="breadcrumb-item active" aria-current="page">So sánh hiệu quả</li>
            </ol>
        </nav>

        <!-- Filter Form -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form action="" method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="action" value="operation-reports/compare">

                    <div class="col-md-4">
                        <label class="form-label small text-muted text-uppercase mb-1">Từ ngày</label>
                        <input type="date" name="start_date" class="form-control"
                            value="<?= $filters['start_date'] ?? '' ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small text-muted text-uppercase mb-1">Đến ngày</label>
                        <input type="date" name="end_date" class="form-control"
                            value="<?= $filters['end_date'] ?? '' ?>">
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-2"></i>Lọc dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Comparison Table -->
        <div class="card shadow border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-bar-chart-line me-2"></i>Bảng xếp hạng hiệu quả
                    Tour</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">Hạng</th>
                                <th>Thông tin Tour</th>
                                <th class="text-center">Số booking/khách</th>
                                <th class="text-end">Doanh thu</th>
                                <th class="text-end">Chi phí</th>
                                <th class="text-end">Lợi nhuận</th>
                                <th class="text-center">Tỷ suất</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                        Không có dữ liệu báo cáo trong khoảng thời gian này.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reports as $index => $report): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if ($index === 0): ?>
                                                <i class="bi bi-trophy-fill text-warning fs-4"></i>
                                            <?php elseif ($index === 1): ?>
                                                <i class="bi bi-award-fill text-secondary fs-4"></i>
                                            <?php elseif ($index === 2): ?>
                                                <i class="bi bi-award-fill text-brown fs-4" style="color: #cd7f32;"></i>
                                            <?php else: ?>
                                                <span class="fw-bold text-muted lead"><?= $index + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary"><?= htmlspecialchars($report['tour_name']) ?>
                                            </div>
                                            <div class="small text-muted">Mã: <?= htmlspecialchars($report['tour_code']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold"><?= $report['total_bookings'] ?> bookings</div>
                                            <div class="small text-muted"><?= $report['total_participants'] ?> khách</div>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            <?= number_format($report['revenue'], 0, ',', '.') ?> ₫
                                        </td>
                                        <td class="text-end text-danger">
                                            <?= number_format($report['cost'], 0, ',', '.') ?> ₫
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            <?= number_format($report['profit'], 0, ',', '.') ?> ₫
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $marginClass = 'bg-success';
                                            if ($report['profit_margin'] < 10)
                                                $marginClass = 'bg-danger';
                                            elseif ($report['profit_margin'] < 30)
                                                $marginClass = 'bg-warning text-dark';
                                            ?>
                                            <span class="badge <?= $marginClass ?> rounded-pill">
                                                <?= $report['profit_margin'] ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>