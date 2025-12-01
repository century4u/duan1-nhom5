<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Phân bổ Hướng dẫn viên</h3>
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
                    <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?> lúc <?= date('H:i', strtotime($schedule['departure_time'])) ?></p>
                    <p><strong>Ngày kết thúc:</strong> <?= date('d/m/Y', strtotime($schedule['end_date'])) ?> lúc <?= date('H:i', strtotime($schedule['end_time'])) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
                    <p><strong>Điểm đến:</strong> <?= htmlspecialchars($schedule['destination']) ?></p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/process-assign-guide">
        <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
        
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Chọn Hướng dẫn viên</h5>
            </div>
            <div class="card-body">
                <?php if (empty($guides)): ?>
                    <p class="text-muted">Không có HDV nào khả dụng.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" title="Chọn tất cả">
                                    </th>
                                    <th>Họ tên</th>
                                    <th>Mã HDV</th>
                                    <th>Chuyên môn</th>
                                    <th>Ngôn ngữ</th>
                                    <th>Liên hệ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $currentGuideIds = array_column($currentAssignments, 'resource_id');
                                foreach ($guides as $guide): 
                                    $isSelected = in_array($guide['id'], $currentGuideIds);
                                ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="guide_ids[]" value="<?= $guide['id'] ?>" 
                                                   class="guide-checkbox" <?= $isSelected ? 'checked' : '' ?>>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($guide['full_name']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($guide['code'] ?? '-') ?></td>
                                        <td>
                                            <?php
                                            $specializations = GuideModel::getSpecializations();
                                            echo htmlspecialchars($specializations[$guide['specialization']] ?? $guide['specialization'] ?? '-');
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!empty($guide['languages'])) {
                                                $languages = json_decode($guide['languages'], true);
                                                if (is_array($languages)) {
                                                    echo implode(', ', $languages);
                                                }
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <small>
                                                <?= htmlspecialchars($guide['email'] ?? '-') ?><br>
                                                <?= htmlspecialchars($guide['phone'] ?? '-') ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?= BASE_URL ?>?action=departure-schedules/show&id=<?= $schedule['id'] ?>" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">Lưu Phân bổ</button>
        </div>
    </form>
</div>

<script>
// Select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.guide-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Update select all when individual checkbox changes
document.querySelectorAll('.guide-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const allChecked = Array.from(document.querySelectorAll('.guide-checkbox')).every(cb => cb.checked);
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = allChecked;
    });
});
</script>

