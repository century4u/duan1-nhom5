<div class="col-12">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>?action=guides">Quản lý HDV</a></li>
            <li class="breadcrumb-item active">Chi tiết HDV</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><?= htmlspecialchars($guide['full_name']) ?></h2>
            <p class="text-muted mb-0">
                <span class="badge bg-info"><?= htmlspecialchars($guide['code']) ?></span>
                <span class="ms-2 badge bg-<?= $guide['status'] == 1 ? 'success' : 'secondary' ?>">
                    <?= $guide['status'] == 1 ? 'Hoạt động' : 'Không hoạt động' ?>
                </span>
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>?action=guides/edit&id=<?= $guide['id'] ?>" class="btn btn-warning">Sửa HDV</a>
            <a href="<?= BASE_URL ?>?action=guides" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
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
                <div class="col-md-3 text-center mb-3">
                    <?php if (!empty($guide['avatar'])): ?>
                        <img src="<?= BASE_ASSETS_UPLOADS . $guide['avatar'] ?>" 
                             alt="Avatar" class="img-thumbnail rounded-circle" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                             style="width: 150px; height: 150px;">
                            <span class="text-white fs-1"><?= strtoupper(substr($guide['full_name'], 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Ngày sinh:</strong> <?= $guide['birthdate'] ? date('d/m/Y', strtotime($guide['birthdate'])) : 'N/A' ?></p>
                            <p><strong>Giới tính:</strong> 
                                <?php
                                $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                echo $genderLabels[$guide['gender']] ?? 'N/A';
                                ?>
                            </p>
                            <p><strong>Chuyên môn:</strong> 
                                <span class="badge bg-info"><?= $specializations[$guide['specialization']] ?? $guide['specialization'] ?></span>
                            </p>
                            <p><strong>Kinh nghiệm:</strong> <?= $guide['experience_years'] ?> năm</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Số tour đã dẫn:</strong> <span class="badge bg-primary"><?= $guide['tours_count'] ?></span></p>
                            <p><strong>Đánh giá trung bình:</strong> 
                                <?php if ($guide['average_rating']): ?>
                                    <span class="text-warning">
                                        <?= number_format($guide['average_rating'], 1) ?>/5.0
                                        <small>(<?= $guide['rated_tours_count'] ?> đánh giá)</small>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Chưa có đánh giá</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Đánh giá năng lực:</strong> 
                                <?php if ($guide['performance_rating']): ?>
                                    <span class="text-success"><?= number_format($guide['performance_rating'], 1) ?>/5.0</span>
                                <?php else: ?>
                                    <span class="text-muted">Chưa đánh giá</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Tình trạng sức khỏe:</strong> 
                                <?php
                                $healthLabels = ['good' => 'Tốt', 'fair' => 'Bình thường', 'poor' => 'Kém'];
                                $healthColors = ['good' => 'success', 'fair' => 'warning', 'poor' => 'danger'];
                                $healthStatus = $healthLabels[$guide['health_status']] ?? $guide['health_status'];
                                $healthColor = $healthColors[$guide['health_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $healthColor ?>"><?= $healthStatus ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin liên hệ -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Thông tin liên hệ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Điện thoại:</strong> <?= htmlspecialchars($guide['phone'] ?? 'N/A') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($guide['email'] ?? 'N/A') ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($guide['address'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>CMND/CCCD:</strong> <?= htmlspecialchars($guide['id_card'] ?? 'N/A') ?></p>
                    <p><strong>Hộ chiếu:</strong> <?= htmlspecialchars($guide['passport'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ngôn ngữ và chứng chỉ -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Ngôn ngữ và chứng chỉ</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Ngôn ngữ sử dụng:</h6>
                    <?php if (!empty($guide['languages_array'])): ?>
                        <div class="mb-3">
                            <?php foreach ($guide['languages_array'] as $lang): ?>
                                <span class="badge bg-primary me-1"><?= htmlspecialchars($lang) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có thông tin</p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h6>Chứng chỉ chuyên môn:</h6>
                    <?php if (!empty($guide['certificates'])): ?>
                        <p><?= nl2br(htmlspecialchars($guide['certificates'])) ?></p>
                    <?php else: ?>
                        <p class="text-muted">Chưa có thông tin</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($guide['experience_description'])): ?>
                <hr>
                <div>
                    <h6>Mô tả kinh nghiệm:</h6>
                    <p><?= nl2br(htmlspecialchars($guide['experience_description'])) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lịch sử dẫn tour -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Lịch sử dẫn tour (<?= count($guide['tour_history']) ?> tour)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($guide['tour_history'])): ?>
                <p class="text-muted text-center">Chưa có lịch sử dẫn tour</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Số khách</th>
                                <th>Đánh giá</th>
                                <th>Phản hồi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guide['tour_history'] as $history): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($history['tour_name'] ?? 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($history['tour_code'] ?? '') ?></small>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($history['start_date'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($history['end_date'])) ?></td>
                                    <td><?= $history['participants_count'] ?> người</td>
                                    <td>
                                        <?php if ($history['rating']): ?>
                                            <span class="text-warning">
                                                <?= number_format($history['rating'], 1) ?>/5.0
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($history['feedback'])): ?>
                                            <small><?= htmlspecialchars(substr($history['feedback'], 0, 50)) ?>...</small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
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

    <!-- Lịch làm việc (30 ngày tới) -->
    <?php if (!empty($guide['availability'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Lịch làm việc (30 ngày tới)</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <?php 
                    $statusLabels = [
                        'available' => ['label' => 'Có sẵn', 'class' => 'success'],
                        'busy' => ['label' => 'Bận', 'class' => 'warning'],
                        'off' => ['label' => 'Nghỉ', 'class' => 'secondary'],
                        'sick' => ['label' => 'Ốm', 'class' => 'danger']
                    ];
                    foreach ($guide['availability'] as $avail): 
                    ?>
                        <div class="col-md-2">
                            <div class="card border">
                                <div class="card-body p-2 text-center">
                                    <small class="d-block text-muted"><?= date('d/m', strtotime($avail['date'])) ?></small>
                                    <span class="badge bg-<?= $statusLabels[$avail['status']]['class'] ?? 'secondary' ?>">
                                        <?= $statusLabels[$avail['status']]['label'] ?? $avail['status'] ?>
                                    </span>
                                    <?php if (!empty($avail['reason'])): ?>
                                        <small class="d-block text-muted mt-1" title="<?= htmlspecialchars($avail['reason']) ?>">
                                            <?= htmlspecialchars(substr($avail['reason'], 0, 15)) ?>...
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Ghi chú sức khỏe -->
    <?php if (!empty($guide['health_notes'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Ghi chú về sức khỏe</h5>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($guide['health_notes'])) ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

