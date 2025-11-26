<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Chỉnh sửa Báo cáo Vận hành Tour</h2>
        <a href="<?= BASE_URL ?>?action=operation-reports/show&id=<?= $report['id'] ?? '' ?>" class="btn btn-secondary">Quay lại</a>
    </div>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['errors']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- TODO: Add edit form -->
    
</div>

