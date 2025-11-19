<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Quản lý Đặt Tour</h2>
        <a href="<?= BASE_URL ?>?action=bookings/create" class="btn btn-primary">Đặt Tour Mới</a>
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
            <form method="GET" action="<?= BASE_URL ?>?action=bookings">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" 
                               value="<?= $_GET['search'] ?? '' ?>" placeholder="Tên tour, mã tour, tên khách...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : '' ?>>Chờ xác nhận</option>
                            <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status'] === 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                            <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] === 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="<?= BASE_URL ?>?action=bookings" class="btn btn-secondary w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($bookings)): ?>
                <p class="text-center text-muted">Không có booking nào.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Mã Booking</th>
                                <th>Tour</th>
                                <th>Khách hàng</th>
                                <th>Số người</th>
                                <th>Ngày đặt</th>
                                <th>Tổng giá</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><strong>#<?= $booking['id'] ?></strong></td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($booking['tour_name'] ?? 'N/A') ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($booking['tour_code'] ?? '') ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($booking['customer_name'] ?? 'Khách lẻ') ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($booking['customer_email'] ?? '') ?></small>
                                    </td>
                                    <td><?= $booking['participants_count'] ?? 0 ?> người</td>
                                    <td><?= date('d/m/Y H:i', strtotime($booking['booking_date'])) ?></td>
                                    <td><strong><?= number_format($booking['total_price'], 0, ',', '.') ?> đ</strong></td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'pending' => ['label' => 'Chờ xác nhận', 'class' => 'warning'],
                                            'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'success'],
                                            'cancelled' => ['label' => 'Đã hủy', 'class' => 'danger'],
                                            'completed' => ['label' => 'Hoàn thành', 'class' => 'info']
                                        ];
                                        $status = $statusLabels[$booking['status']] ?? ['label' => $booking['status'], 'class' => 'secondary'];
                                        ?>
                                        <span class="badge bg-<?= $status['class'] ?>"><?= $status['label'] ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=bookings/show&id=<?= $booking['id'] ?>" 
                                           class="btn btn-sm btn-info">Chi tiết</a>
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

