<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Quản lý Dịch vụ Bổ sung</h3>
            <a href="<?= BASE_URL ?>?action=additional-services/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm mới
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên dịch vụ</th>
                                <th>Mô tả</th>
                                <th>Đơn giá</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có dịch vụ nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?= $service['id'] ?></td>
                                        <td><?= htmlspecialchars($service['name']) ?></td>
                                        <td><?= htmlspecialchars($service['description']) ?></td>
                                        <td><?= number_format($service['price'], 0, ',', '.') ?> VNĐ</td>
                                        <td>
                                            <?php if ($service['status'] == 1): ?>
                                                <span class="badge bg-success">Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Ẩn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>?action=additional-services/edit&id=<?= $service['id'] ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>?action=additional-services/delete&id=<?= $service['id'] ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
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