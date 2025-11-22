<div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Chi tiết Lịch Khởi Hành</h3>
            <p class="text-muted mb-0"><?= htmlspecialchars($schedule['tour_code'] . ' - ' . $schedule['tour_name']) ?></p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>?action=departure-schedules/edit&id=<?= $schedule['id'] ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Sửa
            </a>
            <a href="<?= BASE_URL ?>?action=departure-schedules" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin chính -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin Lịch Khởi Hành</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Tour:</strong><br>
                            <?= htmlspecialchars($schedule['tour_code'] . ' - ' . $schedule['tour_name']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Trạng thái:</strong><br>
                            <?php
                            $statusColors = [
                                'draft' => 'secondary',
                                'confirmed' => 'success',
                                'in_progress' => 'primary',
                                'completed' => 'info',
                                'cancelled' => 'danger'
                            ];
                            $statusLabels = [
                                'draft' => 'Nháp',
                                'confirmed' => 'Đã xác nhận',
                                'in_progress' => 'Đang thực hiện',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy'
                            ];
                            $color = $statusColors[$schedule['status']] ?? 'secondary';
                            $label = $statusLabels[$schedule['status']] ?? $schedule['status'];
                            ?>
                            <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Ngày/Giờ khởi hành:</strong><br>
                            <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?> 
                            lúc <?= date('H:i', strtotime($schedule['departure_time'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Ngày/Giờ kết thúc:</strong><br>
                            <?= date('d/m/Y', strtotime($schedule['end_date'])) ?> 
                            lúc <?= date('H:i', strtotime($schedule['end_time'])) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Điểm tập trung:</strong><br>
                            <?= htmlspecialchars($schedule['meeting_point']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Số lượng:</strong><br>
                            <?= $schedule['current_participants'] ?>/<?= $schedule['max_participants'] ?? '∞' ?> người
                        </div>
                    </div>

                    <?php if (!empty($schedule['notes'])): ?>
                        <div class="mb-3">
                            <strong>Ghi chú:</strong><br>
                            <?= nl2br(htmlspecialchars($schedule['notes'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Phân bổ Nhân sự -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Phân bổ Nhân sự</h5>
                    <a href="<?= BASE_URL ?>?action=departure-schedules/assign-guide&schedule_id=<?= $schedule['id'] ?>" 
                       class="btn btn-sm btn-light">
                        <i class="bi bi-plus-circle"></i> Phân bổ HDV
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($assignmentsByType['guide'] ?? [])): ?>
                        <p class="text-muted mb-0">Chưa có phân bổ HDV</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>HDV</th>
                                        <th>Chuyên môn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignmentsByType['guide'] ?? [] as $assignment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($assignment['resource_name']) ?></td>
                                            <td><?= htmlspecialchars($assignment['resource_type'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $statusColors = ['pending' => 'warning', 'confirmed' => 'success', 'rejected' => 'danger', 'completed' => 'info'];
                                                $statusLabels = ['pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'rejected' => 'Từ chối', 'completed' => 'Hoàn thành'];
                                                $color = $statusColors[$assignment['status']] ?? 'secondary';
                                                $label = $statusLabels[$assignment['status']] ?? $assignment['status'];
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                            </td>
                                            <td>
                                                <?php if ($assignment['status'] === 'pending'): ?>
                                                    <form method="POST" action="<?= BASE_URL ?>?action=departure-schedules/update-assignment-status" class="d-inline">
                                                        <input type="hidden" name="assignment_id" value="<?= $assignment['id'] ?>">
                                                        <button type="submit" name="status" value="confirmed" class="btn btn-sm btn-success" title="Xác nhận">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                    </form>
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

            <!-- Phân bổ Dịch vụ -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Phân bổ Dịch vụ</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="<?= BASE_URL ?>?action=departure-schedules/assign-service&schedule_id=<?= $schedule['id'] ?>&type=vehicle" 
                           class="btn btn-light">Xe</a>
                        <a href="<?= BASE_URL ?>?action=departure-schedules/assign-service&schedule_id=<?= $schedule['id'] ?>&type=hotel" 
                           class="btn btn-light">Khách sạn</a>
                        <a href="<?= BASE_URL ?>?action=departure-schedules/assign-service&schedule_id=<?= $schedule['id'] ?>&type=restaurant" 
                           class="btn btn-light">Nhà hàng</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $serviceTypes = ['vehicle' => 'Xe', 'hotel' => 'Khách sạn', 'flight' => 'Vé máy bay', 'restaurant' => 'Nhà hàng', 'attraction' => 'Điểm tham quan'];
                    $hasServices = false;
                    foreach ($serviceTypes as $type => $label):
                        if (!empty($assignmentsByType[$type] ?? [])):
                            $hasServices = true;
                    ?>
                        <h6 class="mt-3"><?= $label ?></h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tên</th>
                                        <th>Số lượng</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignmentsByType[$type] as $assignment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($assignment['resource_name']) ?></td>
                                            <td><?= $assignment['quantity'] ?></td>
                                            <td>
                                                <?php if ($assignment['start_date']): ?>
                                                    <?= date('d/m/Y', strtotime($assignment['start_date'])) ?>
                                                    <?php if ($assignment['start_time']): ?>
                                                        <?= date('H:i', strtotime($assignment['start_time'])) ?>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $color = $statusColors[$assignment['status']] ?? 'secondary';
                                                $label = $statusLabels[$assignment['status']] ?? $assignment['status'];
                                                ?>
                                                <span class="badge bg-<?= $color ?>"><?= $label ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php
                        endif;
                    endforeach;
                    if (!$hasServices):
                    ?>
                        <p class="text-muted mb-0">Chưa có phân bổ dịch vụ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Thống kê</h6>
                </div>
                <div class="card-body">
                    <p><strong>Booking:</strong> <?= $bookingCount ?></p>
                    <?php if (!empty($statistics)): ?>
                        <?php foreach ($statistics as $stat): ?>
                            <p><strong><?= ucfirst($stat['assignment_type'])?>:</strong> 
                               <?= $stat['confirmed'] ?>/<?= $stat['total'] ?> đã xác nhận
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($statusHistory)): ?>
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Lịch sử thay đổi</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($statusHistory as $history): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($history['created_at'])) ?></small><br>
                                    <strong><?= $history['old_status'] ?? 'N/A' ?></strong> → 
                                    <strong><?= $history['new_status'] ?></strong>
                                    <?php if ($history['changed_by_name']): ?>
                                        <br><small>Bởi: <?= htmlspecialchars($history['changed_by_name']) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

