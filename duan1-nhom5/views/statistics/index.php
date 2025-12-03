<div class="col-12">
    <div class="row g-4">
        <!-- Thống kê Tour -->
        <div class="col-md-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-map text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="text-primary mb-1"><?= $totalTours ?? 0 ?></h2>
                    <p class="text-muted mb-0">Tổng số Tour</p>
                    <small class="text-success"><?= $activeTours ?? 0 ?> đang hoạt động</small>
                </div>
            </div>
        </div>

        <!-- Thống kê Booking -->
        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar-check text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="text-success mb-1"><?= $totalBookings ?? 0 ?></h2>
                    <p class="text-muted mb-0">Tổng số Đặt Tour</p>
                </div>
            </div>
        </div>

        <!-- Thống kê HDV -->
        <div class="col-md-3">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-people text-info" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="text-info mb-1"><?= $totalGuides ?? 0 ?></h2>
                    <p class="text-muted mb-0">Tổng số Hướng dẫn viên</p>
                </div>
            </div>
        </div>

        <!-- Thống kê Lịch khởi hành -->
        <div class="col-md-3">
            <div class="card border-warning shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar-event text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="text-warning mb-1"><?= $totalSchedules ?? 0 ?></h2>
                    <p class="text-muted mb-0">Lịch khởi hành</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê theo danh mục Tour -->
    <?php if (!empty($categoryStats)): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-bar-chart me-2"></i>
                Thống kê Tour theo danh mục
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php 
                $categoryIcons = [
                    'domestic' => '🏞️',
                    'international' => '✈️',
                    'customized' => '🎯'
                ];
                
                $categoryColors = [
                    'domestic' => 'primary',
                    'international' => 'success',
                    'customized' => 'warning'
                ];
                
                foreach ($categoryStats as $key => $stat): 
                    $icon = $categoryIcons[$key] ?? '📋';
                    $color = $categoryColors[$key] ?? 'secondary';
                    $totalCategoryTours = array_sum(array_column($categoryStats, 'count'));
                    $percentage = $totalCategoryTours > 0 ? round(($stat['count'] / $totalCategoryTours) * 100, 1) : 0;
                ?>
                    <div class="col-md-4">
                        <div class="card border-<?= $color ?>">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">
                                        <span class="me-2"><?= $icon ?></span>
                                        <?= htmlspecialchars($stat['name']) ?>
                                    </h6>
                                    <span class="badge bg-<?= $color ?>"><?= $stat['count'] ?></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-<?= $color ?>" 
                                         role="progressbar" 
                                         style="width: <?= $percentage ?>%"
                                         aria-valuenow="<?= $percentage ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted"><?= $percentage ?>% tổng số tour</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Liên kết nhanh -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-link-45deg me-2"></i>
                Liên kết nhanh
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>?action=tours" class="btn btn-outline-primary w-100">
                        <i class="bi bi-map me-2"></i>
                        Quản lý Tour
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>?action=bookings" class="btn btn-outline-success w-100">
                        <i class="bi bi-calendar-check me-2"></i>
                        Quản lý Đặt Tour
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>?action=guides" class="btn btn-outline-info w-100">
                        <i class="bi bi-people me-2"></i>
                        Quản lý HDV
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>?action=departure-schedules" class="btn btn-outline-warning w-100">
                        <i class="bi bi-calendar-event me-2"></i>
                        Lịch Khởi Hành
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

