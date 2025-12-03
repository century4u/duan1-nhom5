<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=operation-reports">Báo cáo Vận hành</a></li>
            <li class="breadcrumb-item active">Chi tiết Báo cáo</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Chi tiết Báo cáo Vận hành Tour</h2>
            <p class="text-muted mb-0">
                <strong><?= htmlspecialchars($tour['name'] ?? 'N/A') ?></strong> - 
                Mã: <strong><?= htmlspecialchars($tour['code'] ?? '') ?></strong>
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>?action=operation-reports" class="btn btn-secondary">Quay lại</a>
        </div>
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

    <!-- Tổng hợp -->
    <?php if (isset($summary)): ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Tổng Doanh thu</h6>
                    <h3 class="mb-0"><?= number_format($summary['revenue'], 0, ',', '.') ?> đ</h3>
                    <small>Số booking: <?= $summary['total_bookings'] ?? 0 ?></small>
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

    <!-- Chi tiết theo tour -->
    <?php if (isset($tourData)): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin Chi tiết</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Tên Tour:</th>
                            <td><?= htmlspecialchars($tourData['tour_name'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>Mã Tour:</th>
                            <td><?= htmlspecialchars($tourData['tour_code'] ?? '') ?></td>
                        </tr>
                        <tr>
                            <th>Tổng số Booking:</th>
                            <td><strong><?= $tourData['total_bookings'] ?? 0 ?></strong></td>
                        </tr>
                        <tr>
                            <th>Tổng số Người tham gia:</th>
                            <td><strong><?= $tourData['total_participants'] ?? 0 ?></strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Doanh thu:</th>
                            <td class="text-success"><strong><?= number_format($tourData['revenue'] ?? 0, 0, ',', '.') ?> đ</strong></td>
                        </tr>
                        <tr>
                            <th>Chi phí:</th>
                            <td class="text-danger"><strong><?= number_format($tourData['cost'] ?? 0, 0, ',', '.') ?> đ</strong></td>
                        </tr>
                        <tr>
                            <th>Lợi nhuận:</th>
                            <td class="<?= ($tourData['profit'] ?? 0) >= 0 ? 'text-primary' : 'text-danger' ?>">
                                <strong><?= number_format($tourData['profit'] ?? 0, 0, ',', '.') ?> đ</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Tỷ suất lợi nhuận:</th>
                            <td>
                                <span class="badge bg-<?= ($tourData['profit_margin'] ?? 0) >= 0 ? 'success' : 'danger' ?>">
                                    <?= $tourData['profit_margin'] ?? 0 ?>%
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Báo cáo theo tháng -->
    <?php if (!empty($tourPeriodReports)): ?>
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Báo cáo theo Tháng</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th>Doanh thu</th>
                            <th>Chi phí</th>
                            <th>Lợi nhuận</th>
                            <th>Tỷ suất LN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tourPeriodReports as $period): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($period['period']) ?></strong></td>
                                <td class="text-success"><strong><?= number_format($period['revenue'], 0, ',', '.') ?> đ</strong></td>
                                <td class="text-danger"><strong><?= number_format($period['cost'], 0, ',', '.') ?> đ</strong></td>
                                <td class="<?= $period['profit'] >= 0 ? 'text-primary' : 'text-danger' ?>">
                                    <strong><?= number_format($period['profit'], 0, ',', '.') ?> đ</strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $period['profit_margin'] >= 0 ? 'success' : 'danger' ?>">
                                        <?= $period['profit_margin'] ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <p class="text-center text-muted">Không có dữ liệu báo cáo theo tháng.</p>
            </div>
        </div>
    <?php endif; ?>
</div>
