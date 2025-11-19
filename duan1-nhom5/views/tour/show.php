<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=tours">Quản lý Tour</a></li>
            <li class="breadcrumb-item active">Chi tiết Tour</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><?= htmlspecialchars($tour['name']) ?></h2>
            <p class="text-muted mb-0">
                <span class="badge bg-info"><?= $categories[$tour['category']] ?? $tour['category'] ?></span>
                <span class="ms-2">Mã tour: <strong><?= htmlspecialchars($tour['code']) ?></strong></span>
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>?action=tours/edit&id=<?= $tour['id'] ?>" class="btn btn-warning">Sửa Tour</a>
            <a href="<?= BASE_URL ?>?action=tours" class="btn btn-secondary">Quay lại</a>
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

    <!-- Thông tin cơ bản -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin cơ bản</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Điểm khởi hành:</strong> <?= htmlspecialchars($tour['departure_location']) ?></p>
                    <p><strong>Điểm đến:</strong> <?= htmlspecialchars($tour['destination']) ?></p>
                    <p><strong>Số ngày:</strong> <?= $tour['duration'] ?> ngày</p>
                    <p><strong>Số người tối đa:</strong> <?= $tour['max_participants'] ?? 'Không giới hạn' ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Giá cơ bản:</strong> <span class="text-danger fw-bold"><?= number_format($tour['price'], 0, ',', '.') ?> đ</span></p>
                    <p><strong>Trạng thái:</strong> 
                        <?php if ($tour['status'] == 1): ?>
                            <span class="badge bg-success">Hoạt động</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Không hoạt động</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Người tạo:</strong> <?= htmlspecialchars($tour['created_by_name'] ?? 'N/A') ?></p>
                </div>
            </div>
            <?php if (!empty($tour['description'])): ?>
                <hr>
                <div>
                    <strong>Mô tả:</strong>
                    <p class="mt-2"><?= nl2br(htmlspecialchars($tour['description'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hình ảnh -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Hình ảnh Tour</h5>
        </div>
        <div class="card-body">
            <?php if (empty($images)): ?>
                <p class="text-muted text-center">Chưa có hình ảnh nào</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($images as $image): ?>
                        <div class="col-md-3">
                            <div class="card">
                                <img src="<?= BASE_ASSETS_UPLOADS . $image['image_path'] ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($image['caption'] ?? '') ?>"
                                     style="height: 200px; object-fit: cover;">
                                <?php if ($image['is_primary']): ?>
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-2">Ảnh chính</span>
                                <?php endif; ?>
                                <?php if (!empty($image['caption'])): ?>
                                    <div class="card-body p-2">
                                        <small class="text-muted"><?= htmlspecialchars($image['caption']) ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lịch trình -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Lịch trình Tour</h5>
        </div>
        <div class="card-body">
            <?php if (empty($schedules)): ?>
                <p class="text-muted text-center">Chưa có lịch trình</p>
            <?php else: ?>
                <div class="timeline">
                    <?php foreach ($schedules as $schedule): ?>
                        <div class="card mb-3 border-start border-4 border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">
                                        <span class="badge bg-primary me-2">Ngày <?= $schedule['day_number'] ?></span>
                                        <?= htmlspecialchars($schedule['title']) ?>
                                    </h6>
                                    <?php if (!empty($schedule['date'])): ?>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($schedule['date'])) ?></small>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($schedule['description'])): ?>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($schedule['description'])) ?></p>
                                <?php endif; ?>

                                <?php if (!empty($schedule['activities'])): ?>
                                    <?php 
                                    $activities = json_decode($schedule['activities'], true);
                                    if (is_array($activities) && !empty($activities)):
                                    ?>
                                        <div class="mb-2">
                                            <strong>Hoạt động:</strong>
                                            <ul class="mb-0">
                                                <?php foreach ($activities as $activity): ?>
                                                    <li><?= htmlspecialchars($activity) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <div class="row mt-2">
                                    <?php if (!empty($schedule['meals'])): ?>
                                        <div class="col-md-4">
                                            <small><strong>Bữa ăn:</strong> <?= htmlspecialchars($schedule['meals']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($schedule['accommodation'])): ?>
                                        <div class="col-md-4">
                                            <small><strong>Nơi nghỉ:</strong> <?= htmlspecialchars($schedule['accommodation']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($schedule['transport'])): ?>
                                        <div class="col-md-4">
                                            <small><strong>Phương tiện:</strong> <?= htmlspecialchars($schedule['transport']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Giá tour -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Bảng giá Tour</h5>
        </div>
        <div class="card-body">
            <?php if (empty($prices)): ?>
                <p class="text-muted text-center">Chưa có bảng giá</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Loại giá</th>
                                <th>Giá</th>
                                <th>Thời gian áp dụng</th>
                                <th>Số lượng</th>
                                <th>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $priceTypeLabels = [
                                'adult' => 'Người lớn',
                                'child' => 'Trẻ em',
                                'infant' => 'Trẻ sơ sinh',
                                'senior' => 'Người cao tuổi',
                                'group' => 'Nhóm'
                            ];
                            foreach ($prices as $price): 
                            ?>
                                <tr>
                                    <td><strong><?= $priceTypeLabels[$price['price_type']] ?? $price['price_type'] ?></strong></td>
                                    <td class="text-danger fw-bold"><?= number_format($price['price'], 0, ',', '.') ?> <?= $price['currency'] ?></td>
                                    <td>
                                        <?php if ($price['start_date'] || $price['end_date']): ?>
                                            <?= $price['start_date'] ? date('d/m/Y', strtotime($price['start_date'])) : 'Từ đầu' ?>
                                            - 
                                            <?= $price['end_date'] ? date('d/m/Y', strtotime($price['end_date'])) : 'Đến cuối' ?>
                                        <?php else: ?>
                                            Áp dụng thường xuyên
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($price['min_quantity'] > 1 || $price['max_quantity']): ?>
                                            <?= $price['min_quantity'] ?>
                                            <?php if ($price['max_quantity']): ?>
                                                - <?= $price['max_quantity'] ?>
                                            <?php else: ?>
                                                trở lên
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($price['description'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chính sách -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Chính sách Tour</h5>
        </div>
        <div class="card-body">
            <?php if (empty($policies)): ?>
                <p class="text-muted text-center">Chưa có chính sách nào</p>
            <?php else: ?>
                <?php 
                $policyTypeLabels = [
                    'booking' => 'Chính sách đặt tour',
                    'cancellation' => 'Chính sách hủy tour',
                    'reschedule' => 'Chính sách đổi lịch',
                    'refund' => 'Chính sách hoàn tiền',
                    'terms' => 'Điều khoản'
                ];
                foreach ($policiesByType as $type => $typePolicies): 
                ?>
                    <div class="mb-4">
                        <h6 class="text-primary"><?= $policyTypeLabels[$type] ?? ucfirst($type) ?></h6>
                        <?php foreach ($typePolicies as $policy): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($policy['title']) ?></h6>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($policy['content'])) ?></p>
                                    <?php if ($policy['days_before']): ?>
                                        <small class="text-muted">Áp dụng: <?= $policy['days_before'] ?> ngày trước khi tour</small>
                                    <?php endif; ?>
                                    <?php if ($policy['refund_percentage']): ?>
                                        <small class="text-muted ms-2">Hoàn tiền: <?= $policy['refund_percentage'] ?>%</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Nhà cung cấp -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Nhà cung cấp dịch vụ</h5>
        </div>
        <div class="card-body">
            <?php if (empty($suppliers)): ?>
                <p class="text-muted text-center">Chưa có nhà cung cấp nào</p>
            <?php else: ?>
                <?php 
                $supplierTypeLabels = [
                    'hotel' => 'Khách sạn',
                    'transport' => 'Vận chuyển',
                    'restaurant' => 'Nhà hàng',
                    'guide' => 'Hướng dẫn viên',
                    'other' => 'Khác'
                ];
                foreach ($suppliersByType as $type => $typeSuppliers): 
                ?>
                    <div class="mb-4">
                        <h6 class="text-secondary"><?= $supplierTypeLabels[$type] ?? ucfirst($type) ?></h6>
                        <div class="row g-3">
                            <?php foreach ($typeSuppliers as $supplier): ?>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title"><?= htmlspecialchars($supplier['supplier_name'] ?? $supplier['name']) ?></h6>
                                            <?php if (!empty($supplier['service_type'])): ?>
                                                <p class="mb-1"><small><strong>Dịch vụ:</strong> <?= htmlspecialchars($supplier['service_type']) ?></small></p>
                                            <?php endif; ?>
                                            <?php if (!empty($supplier['service_date'])): ?>
                                                <p class="mb-1"><small><strong>Ngày:</strong> <?= date('d/m/Y', strtotime($supplier['service_date'])) ?></small></p>
                                            <?php endif; ?>
                                            <?php if (!empty($supplier['phone'])): ?>
                                                <p class="mb-1"><small><strong>Điện thoại:</strong> <?= htmlspecialchars($supplier['phone']) ?></small></p>
                                            <?php endif; ?>
                                            <?php if (!empty($supplier['email'])): ?>
                                                <p class="mb-1"><small><strong>Email:</strong> <?= htmlspecialchars($supplier['email']) ?></small></p>
                                            <?php endif; ?>
                                            <?php if (!empty($supplier['notes'])): ?>
                                                <p class="mb-0"><small class="text-muted"><?= htmlspecialchars($supplier['notes']) ?></small></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.timeline .card {
    position: relative;
}
.timeline .card::before {
    content: '';
    position: absolute;
    left: -2px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #0d6efd;
}
</style>

