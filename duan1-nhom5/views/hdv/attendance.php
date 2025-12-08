<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<!-- Header -->
<div class="flex justify-between items-center mb-8">
    <div>
        <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= $tour_id ?>&guide_id=<?= $guide_id ?>"
            class="text-gray-500 hover:text-blue-600 mb-2 inline-block text-sm">
            <i class="bi bi-arrow-left"></i> Quay lại chi tiết
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Điểm danh khách hàng</h1>
        <p class="text-gray-500 mt-1"><?= htmlspecialchars($tour['name']) ?> (<?= htmlspecialchars($tour['code']) ?>)
        </p>
    </div>
</div>

<!-- Filter by Stage/Day -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <h5 class="text-lg font-bold text-blue-800 mb-4">Chọn chặng/ngày để điểm danh</h5>
    <div class="flex flex-wrap gap-3">
        <?php foreach ($tourSchedules as $sched): ?>
            <a href="<?= BASE_URL ?>?action=hvd/attendance&tour_id=<?= $tour_id ?>&guide_id=<?= $guide_id ?>&schedule_id=<?= $sched['id'] ?>"
                class="px-4 py-2 rounded-lg border font-medium transition-all duration-200 <?= (int) $selected_schedule_id === (int) $sched['id']
                    ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                    : 'bg-white text-gray-700 border-gray-200 hover:border-blue-300 hover:bg-blue-50' ?>">
                <?= htmlspecialchars($sched['title']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div
        class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center relative alert-dismissible">
        <span><?= $_SESSION['success'];
        unset($_SESSION['success']); ?></span>
        <button type="button" class="text-green-700 hover:text-green-900" data-bs-dismiss="alert"><i
                class="bi bi-x-lg"></i></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div
        class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex justify-between items-center relative alert-dismissible">
        <span><?= $_SESSION['error'];
        unset($_SESSION['error']); ?></span>
        <button type="button" class="text-red-700 hover:text-red-900" data-bs-dismiss="alert"><i
                class="bi bi-x-lg"></i></button>
    </div>
<?php endif; ?>

<!-- Attendance Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h6 class="font-bold text-gray-800 m-0">Danh sách khách hàng</h6>
        <?php if (($assignment['status'] ?? '') !== 'completed'): ?>
            <button type="submit" form="attendanceForm"
                class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center gap-2 shadow-sm transition-colors">
                <i class="bi bi-save"></i> Lưu trạng thái
            </button>
        <?php else: ?>
            <span
                class="px-3 py-1.5 bg-gray-100 text-gray-500 text-sm rounded-lg flex items-center gap-2 border border-gray-200 cursor-not-allowed">
                <i class="bi bi-lock-fill"></i> Đã khóa sổ
            </span>
        <?php endif; ?>
    </div>

    <div class="p-0">
        <form id="attendanceForm" method="POST" action="<?= BASE_URL ?>?action=hvd/attendance/store">
            <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
            <input type="hidden" name="guide_id" value="<?= $guide_id ?>">
            <input type="hidden" name="departure_schedule_id" value="<?= $departure_schedule_id ?>">
            <input type="hidden" name="tour_schedule_id" value="<?= $selected_schedule_id ?>">

            <?php if (($assignment['status'] ?? '') === 'completed'): ?>
                <div class="px-6 py-4 bg-yellow-50 text-yellow-800 border-b border-yellow-100 text-center">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> Tour này đã hoàn thành, không thể điểm danh.
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left align-middle" width="100%" cellspacing="0">
                    <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 w-[5%]">#</th>
                            <th class="px-6 py-3 w-[25%]">Họ và tên</th>
                            <th class="px-6 py-3 w-[15%]">Thông tin</th>
                            <th class="px-6 py-3 w-[15%]">Booking ID</th>
                            <th class="px-6 py-3 w-[20%]">Trạng thái</th>
                            <th class="px-6 py-3 w-[20%]">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (!empty($guests)): ?>
                            <?php foreach ($guests as $index => $guest):
                                $status = $guest['status'] ?? 'pending';
                                $bookingStatus = $guest['booking_status'] ?? 'pending';
                                $isCancelled = $bookingStatus === 'cancelled';
                                $isLocked = ($assignment['status'] ?? '') === 'completed' || $isCancelled;
                                ?>
                                <tr
                                    class="transition-colors <?= $isCancelled ? 'bg-gray-100 opacity-60' : 'hover:bg-gray-50/50' ?>">
                                    <td class="px-6 py-4 text-gray-500"><?= $index + 1 ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 flex items-center gap-2">
                                            <?= htmlspecialchars($guest['fullname']) ?>
                                            <?php if ($isCancelled): ?>
                                                <span
                                                    class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded border border-red-200">Đã
                                                    hủy</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($guest['contact_phone']): ?>
                                            <div class="text-xs text-gray-500 mt-0.5"><i class="bi bi-telephone"></i>
                                                <?= htmlspecialchars($guest['contact_phone']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        <?= $guest['birthdate'] ? htmlspecialchars($guest['birthdate']) : '-' ?>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            <?= $guest['gender'] === 'male' ? 'Nam' : ($guest['gender'] === 'female' ? 'Nữ' : $guest['gender']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-500 text-xs">#<?= $guest['booking_id'] ?></td>
                                    <td class="px-6 py-4">
                                        <select name="attendance[<?= $guest['booking_detail_id'] ?>][status]"
                                            class="form-select status-select block w-full rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                            <?= $isLocked ? 'disabled' : '' ?>
                                            style="background-color: <?= $status === 'present' ? '#d1e7dd' : ($status === 'absent' ? '#f8d7da' : '') ?>">
                                            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Chưa điểm danh
                                            </option>
                                            <option value="present" <?= $status === 'present' ? 'selected' : '' ?>>Có mặt</option>
                                            <option value="late" <?= $status === 'late' ? 'selected' : '' ?>>Đi muộn</option>
                                            <option value="absent" <?= $status === 'absent' ? 'selected' : '' ?>>Vắng mặt</option>
                                            <option value="left" <?= $status === 'left' ? 'selected' : '' ?>>Rời đoàn</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="attendance[<?= $guest['booking_detail_id'] ?>][note]"
                                            class="form-control block w-full rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                            <?= $isLocked ? 'readonly' : '' ?>
                                            value="<?= htmlspecialchars($guest['note'] ?? '') ?>" placeholder="Ghi chú...">
                                        <?php if (!empty($guest['updated_at'])): ?>
                                            <div class="text-[10px] text-gray-400 mt-1 italic">
                                                Cập nhật: <?= date('H:i d/m', strtotime($guest['updated_at'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                    Không tìm thấy danh sách khách hàng hoặc chưa có lịch khởi hành.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (($assignment['status'] ?? '') !== 'completed'): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-end">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                        <i class="bi bi-save"></i> Lưu tất cả thay đổi
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
    // Simple script to change background color of select based on value
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function () {
            this.style.backgroundColor = '';
            // Note: In Tailwind/Custom UI, might want to toggle classes instead, but inline style works for quick feedback
            if (this.value === 'present') this.style.backgroundColor = '#d1e7dd';
            if (this.value === 'absent') this.style.backgroundColor = '#f8d7da';
        });
    });
</script>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>