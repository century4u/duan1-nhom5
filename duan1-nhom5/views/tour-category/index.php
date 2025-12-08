<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- <h2>Quản lý Danh mục Tour</h2> -->
        <a href="<?= BASE_URL ?>?action=tours" class="btn btn-outline-primary">
            <i class="bi bi-list"></i> Quản lý Tour
        </a>
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
    <!-- <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Giới thiệu</h5>
            <p class="card-text text-muted">
                Hệ thống quản lý tour được phân loại thành <?= count($categoryStats ?? []) ?> danh mục chính để dễ dàng
                quản lý và tìm kiếm các tour phù hợp với nhu cầu của khách hàng.
                Bạn có thể thêm danh mục mới bằng cách chỉnh sửa file <code>configs/tour_categories.php</code>
            </p>
        </div>
    </div> -->

    <!-- Thống kê Tổng quan -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Thống kê Tổng quan</h5>
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

    <!-- Danh sách danh mục với tours -->
    <?php foreach ($categoryStats as $key => $stat): ?>
        <div class="card mb-4 border-<?= $stat['color'] ?> shadow-sm">
            <div class="card-header bg-<?= $stat['color'] ?> text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        <span class="me-2" style="font-size: 1.3rem;"><?= $stat['icon'] ?></span>
                        <?= htmlspecialchars($stat['name']) ?>
                        <span class="badge bg-light text-dark ms-2"><?= $stat['count'] ?> tours</span>
                    </h5>
                    <div>
                        <a href="<?= BASE_URL ?>?action=tour-categories/view-tours&category=<?= $key ?>"
                            class="btn btn-light btn-sm me-2">
                            <i class="bi bi-eye"></i> Xem danh sách đầy đủ
                        </a>
                        <a href="<?= BASE_URL ?>?action=tours/create&category=<?= $key ?>"
                            class="btn btn-outline-light btn-sm">
                            <i class="bi bi-plus-circle"></i> Tạo tour mới
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3"><?= htmlspecialchars($stat['description']) ?></p>

                <!-- Tours nổi bật -->
                <?php if (!empty($stat['tours'])): ?>
                    <div class="row g-3">
                        <?php foreach ($stat['tours'] as $tour): ?>
                            <div class="col-md-3">
                                <div class="card h-100 shadow-sm hover-card">
                                    <?php if (!empty($tour['image'])): ?>
                                        <img src="<?= BASE_URL . 'assets/uploads/' . htmlspecialchars($tour['image']) ?>"
                                            class="card-img-top" alt="<?= htmlspecialchars($tour['name']) ?>"
                                            style="height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-<?= $stat['color'] ?> bg-opacity-25 d-flex align-items-center justify-content-center"
                                            style="height: 150px;">
                                            <span style="font-size: 3rem;"><?= $stat['icon'] ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="<?= BASE_URL ?>?action=tours/show&id=<?= $tour['id'] ?>"
                                                class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($tour['name']) ?>
                                            </a>
                                        </h6>
                                        <p class="card-text small text-muted mb-1">
                                            <i class="bi bi-code-square"></i> <?= htmlspecialchars($tour['code']) ?>
                                        </p>
                                        <p class="card-text small text-muted mb-1">
                                            <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($tour['destination']) ?>
                                        </p>
                                        <p class="card-text small text-muted mb-2">
                                            <i class="bi bi-calendar"></i> <?= $tour['duration'] ?> ngày
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-<?= $stat['color'] ?>">
                                                <?= number_format($tour['price'], 0, ',', '.') ?>đ
                                            </strong>
                                            <?php if ($tour['status'] == 1): ?>
                                                <span class="badge bg-success">Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Không hoạt động</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-2">
                                            <a href="<?= BASE_URL ?>?action=tours/edit&id=<?= $tour['id'] ?>"
                                                class="btn btn-warning btn-sm w-100">
                                                <i class="bi bi-pencil"></i> Sửa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light mb-0">
                        <p class="mb-0 text-muted">
                            <i class="bi bi-info-circle"></i> Chưa có tour nào trong danh mục này.
                            <a href="<?= BASE_URL ?>?action=tours/create&category=<?= $key ?>" class="alert-link">
                                Tạo tour mới
                            </a>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .hover-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
</style>