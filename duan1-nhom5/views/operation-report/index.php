<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Báo cáo Vận hành Tour</h2>
        <a href="<?= BASE_URL ?>?action=operation-reports/compare" class="btn btn-info">So sánh Tour</a>
    </div>

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

    <!-- Bộ lọc -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="<?= BASE_URL ?>?action=operation-reports">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tour</label>
                        <select class="form-select" name="tour_id">
                            <option value="">Tất cả Tour</option>
                            <?php foreach ($tours ?? [] as $tour): ?>
                                <option value="<?= $tour['id'] ?>" <?= (isset($_GET['tour_id']) && $_GET['tour_id'] == $tour['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tour['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" name="start_date" 
                               value="<?= $_GET['start_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" name="end_date" 
                               value="<?= $_GET['end_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Loại báo cáo</label>
                        <select class="form-select" name="report_type">
                            <option value="by_tour" <?= (isset($_GET['report_type']) && $_GET['report_type'] === 'by_tour') ? 'selected' : 'selected' ?>>Theo Tour</option>
                            <option value="by_period" <?= (isset($_GET['report_type']) && $_GET['report_type'] === 'by_period') ? 'selected' : '' ?>>Theo Kỳ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kỳ báo cáo</label>
                        <select class="form-select" name="period_type">
                            <option value="month" <?= (isset($_GET['period_type']) && $_GET['period_type'] === 'month') ? 'selected' : 'selected' ?>>Tháng</option>
                            <option value="quarter" <?= (isset($_GET['period_type']) && $_GET['period_type'] === 'quarter') ? 'selected' : '' ?>>Quý</option>
                            <option value="year" <?= (isset($_GET['period_type']) && $_GET['period_type'] === 'year') ? 'selected' : '' ?>>Năm</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tổng hợp -->
    <?php if (isset($summary)): ?>
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng Doanh thu</h6>
                    <h3 class="mb-0"><?= number_format($summary['revenue'], 0, ',', '.') ?> đ</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng Chi phí</h6>
                    <h3 class="mb-0"><?= number_format($summary['cost'], 0, ',', '.') ?> đ</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Lợi nhuận</h6>
                    <h3 class="mb-0"><?= number_format($summary['profit'], 0, ',', '.') ?> đ</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Tỷ suất lợi nhuận</h6>
                    <h3 class="mb-0"><?= $summary['profit_margin'] ?>%</h3>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bảng báo cáo -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($reports)): ?>
                <p class="text-center text-muted">Không có dữ liệu báo cáo.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <?php if (($filters['report_type'] ?? 'by_tour') === 'by_tour'): ?>
                                    <th>Tour</th>
                                    <th>Số Booking</th>
                                    <th>Số Người</th>
                                <?php else: ?>
                                    <th>Kỳ</th>
                                    <th>Số Booking</th>
                                    <th>Số Người</th>
                                    <th>Số Tour</th>
                                <?php endif; ?>
                                <th>Doanh thu</th>
                                <th>Chi phí</th>
                                <th>Lợi nhuận</th>
                                <th>Tỷ suất LN</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <?php if (($filters['report_type'] ?? 'by_tour') === 'by_tour'): ?>
                                        <td>
                                            <strong><?= htmlspecialchars($report['tour_name'] ?? 'N/A') ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($report['tour_code'] ?? '') ?></small>
                                        </td>
                                        <td><?= $report['total_bookings'] ?? 0 ?></td>
                                        <td><?= $report['total_participants'] ?? 0 ?></td>
                                    <?php else: ?>
                                        <td><strong><?= htmlspecialchars($report['period'] ?? 'N/A') ?></strong></td>
                                        <td><?= $report['total_bookings'] ?? 0 ?></td>
                                        <td><?= $report['total_participants'] ?? 0 ?></td>
                                        <td><?= $report['total_tours'] ?? 0 ?></td>
                                    <?php endif; ?>
                                    <td><strong class="text-success"><?= number_format($report['revenue'] ?? 0, 0, ',', '.') ?> đ</strong></td>
                                    <td><strong class="text-danger"><?= number_format($report['cost'] ?? 0, 0, ',', '.') ?> đ</strong></td>
                                    <td>
                                        <strong class="<?= ($report['profit'] ?? 0) >= 0 ? 'text-primary' : 'text-danger' ?>">
                                            <?= number_format($report['profit'] ?? 0, 0, ',', '.') ?> đ
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= ($report['profit_margin'] ?? 0) >= 0 ? 'success' : 'danger' ?>">
                                            <?= $report['profit_margin'] ?? 0 ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (($filters['report_type'] ?? 'by_tour') === 'by_tour'): ?>
                                            <a href="<?= BASE_URL ?>?action=operation-reports/show&tour_id=<?= $report['tour_id'] ?>&start_date=<?= $_GET['start_date'] ?? '' ?>&end_date=<?= $_GET['end_date'] ?? '' ?>" 
                                               class="btn btn-sm btn-info">Chi tiết</a>
                                        <?php endif; ?>
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
