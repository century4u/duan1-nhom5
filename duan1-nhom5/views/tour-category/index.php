<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Danh mục Tour</h2>
        <a href="<?= BASE_URL ?>?action=tours" class="btn btn-outline-primary">Quản lý Tour</a>
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

    <!-- Mô tả tổng quan -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Giới thiệu</h5>
            <p class="card-text text-muted">
                Hệ thống quản lý tour được phân loại thành 3 danh mục chính để dễ dàng quản lý và tìm kiếm các tour phù hợp với nhu cầu của khách hàng.
            </p>
        </div>
    </div>

    <!-- Danh sách danh mục -->
    <div class="row g-4">
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
        ?>
            <div class="col-md-4">
                <div class="card h-100 border-<?= $color ?> shadow-sm">
                    <div class="card-header bg-<?= $color ?> text-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                <span class="me-2"><?= $icon ?></span>
                                <?= htmlspecialchars($stat['name']) ?>
                            </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-<?= $color ?> rounded-pill me-2" style="font-size: 1.2rem;">
                                    <?= $stat['count'] ?>
                                </span>
                                <span class="text-muted">tour đang hoạt động</span>
                            </div>
                        </div>
                        <p class="card-text text-muted" style="min-height: 80px;">
                            <?= htmlspecialchars($stat['description']) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <a href="<?= BASE_URL ?>?action=tour-categories/view-tours&category=<?= $key ?>" 
                               class="btn btn-<?= $color ?>">
                                <i class="bi bi-eye"></i> Xem danh sách tour
                            </a>
                            <a href="<?= BASE_URL ?>?action=tours?category=<?= $key ?>" 
                               class="btn btn-outline-<?= $color ?> btn-sm">
                                Quản lý tour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Thống kê tổng quan</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="p-3">
                        <h3 class="text-primary"><?= array_sum(array_column($categoryStats, 'count')) ?></h3>
                        <p class="text-muted mb-0">Tổng số tour</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <h3 class="text-success"><?= count($categoryStats) ?></h3>
                        <p class="text-muted mb-0">Danh mục tour</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <h3 class="text-info">
                            <?php
                            $totalTours = array_sum(array_column($categoryStats, 'count'));
                            $avgPerCategory = $totalTours > 0 ? round($totalTours / count($categoryStats), 1) : 0;
                            echo $avgPerCategory;
                            ?>
                        </h3>
                        <p class="text-muted mb-0">Trung bình mỗi danh mục</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

