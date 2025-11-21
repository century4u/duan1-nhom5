<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Phân bổ Dịch vụ - <?= ucfirst($serviceType) ?></h3>
            <p class="text-muted mb-0">Tour: <?= htmlspecialchars($schedule['tour_code'] . ' - ' . $schedule['tour_name']) ?></p>
        </div>
        <a href="<?= BASE_URL ?>?action=departure-schedules/show&id=<?= $schedule['id'] ?>" class="btn btn-secondary">Quay lại</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Thông tin Lịch Khởi Hành</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></p>
                    <p><strong>Ngày kết thúc:</strong> <?= date('d/m/Y', strtotime($schedule['end_date'])) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Điểm đến:</strong> <?= htmlspecialchars($schedule['destination']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/process-assign-service" id="assignServiceForm">
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
        <input type="hidden" name="service_type" value="<?= $serviceType ?>">
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Chọn Dịch vụ</h5>
            </div>
            <div class="card-body">
                <?php if (empty($suppliers)): ?>
                    <p class="text-muted">Không có nhà cung cấp nào cho loại dịch vụ này.</p>
                <?php else: ?>
                    <div id="servicesContainer">
                        <div class="service-item mb-3 p-3 border rounded">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                                    <select class="form-select supplier-select" name="services[0][supplier_id]" required>
                                        <option value="">-- Chọn nhà cung cấp --</option>
                                        <?php foreach ($suppliers as $supplier): ?>
                                            <option value="<?= $supplier['id'] ?>">
                                                <?= htmlspecialchars($supplier['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Số lượng</label>
                                    <input type="number" class="form-control" name="services[0][quantity]" value="1" min="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Từ ngày</label>
                                    <input type="date" class="form-control" name="services[0][start_date]" 
                                           value="<?= $schedule['departure_date'] ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Đến ngày</label>
                                    <input type="date" class="form-control" name="services[0][end_date]" 
                                           value="<?= $schedule['end_date'] ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger w-100 remove-service" style="display:none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Địa điểm</label>
                                    <input type="text" class="form-control" name="services[0][location]" 
                                           placeholder="Địa điểm cụ thể">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ghi chú</label>
                                    <input type="text" class="form-control" name="services[0][notes]" 
                                           placeholder="Ghi chú">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addService">
                        <i class="bi bi-plus-circle"></i> Thêm dịch vụ
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= BASE_URL ?>?action=departure-schedules/show&id=<?= $schedule['id'] ?>" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Lưu Phân bổ</button>
        </div>
    </form>
</div>

<script>
let serviceIndex = 1;

document.getElementById('addService')?.addEventListener('click', function() {
    const container = document.getElementById('servicesContainer');
    const newItem = container.firstElementChild.cloneNode(true);
    
    // Update indices
    newItem.querySelectorAll('select, input').forEach(el => {
        if (el.name) {
            el.name = el.name.replace(/\[0\]/, `[${serviceIndex}]`);
        }
        if (el.value && el.type === 'date') {
            el.value = '';
        } else if (el.type === 'number') {
            el.value = 1;
        } else if (el.type === 'text') {
            el.value = '';
        }
    });
    
    // Show remove button
    newItem.querySelector('.remove-service').style.display = 'block';
    
    container.appendChild(newItem);
    serviceIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-service')) {
        const item = e.target.closest('.service-item');
        if (document.querySelectorAll('.service-item').length > 1) {
            item.remove();
        } else {
            alert('Phải có ít nhất một dịch vụ!');
        }
    }
});
</script>

