<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<!-- Header -->
<div class="flex justify-between items-center mb-8">
    <div>
        <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= $tour['id'] ?>&guide_id=<?= $guide_id ?>"
            class="text-gray-500 hover:text-blue-600 mb-2 inline-block text-sm">
            <i class="bi bi-arrow-left"></i> Quay lại chi tiết
        </a>
        <h1 class="text-3xl font-bold text-gray-800">Nhật ký Tour</h1>
        <p class="text-gray-500 mt-1"><?= htmlspecialchars($tour['name']) ?></p>
    </div>

    <button type="button"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 shadow-sm transition-colors"
        data-bs-toggle="modal" data-bs-target="#addLogModal">
        <i class="bi bi-plus-lg"></i> Viết nhật ký mới
    </button>
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

<!-- Timeline Logs -->
<div class="max-w-4xl mx-auto">
    <?php if (empty($logs)): ?>
        <div class="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
            <div class="inline-block p-4 rounded-full bg-gray-50 mb-4">
                <i class="bi bi-journal-album text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Chưa có nhật ký nào</h3>
            <p class="text-gray-500 mt-1">Hãy bắt đầu ghi lại những khoảnh khắc đáng nhớ của tour.</p>
        </div>
    <?php else: ?>
        <div
            class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-blue-100">
            <?php foreach ($logs as $log): ?>
                <div class="relative pl-12">
                    <!-- Icon -->
                    <div
                        class="absolute left-0 top-0 w-10 h-10 rounded-full bg-white border-4 border-blue-50 flex items-center justify-center text-blue-600 shadow-sm z-10">
                        <i class="bi bi-clock"></i>
                    </div>

                    <!-- Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-sm font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                                <?= date('H:i d/m/Y', strtotime($log['log_time'])) ?>
                            </span>

                            <!-- Dropdown Menu (Bootstrap) -->
                            <div class="dropdown">
                                <button
                                    class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition-colors"
                                    type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-lg overflow-hidden">
                                    <li>
                                        <a class="dropdown-item py-2 px-4 hover:bg-gray-50 flex items-center gap-2 edit-log-btn"
                                            href="#" data-bs-toggle="modal" data-bs-target="#editLogModal"
                                            data-id="<?= $log['id'] ?>" data-content="<?= htmlspecialchars($log['content']) ?>"
                                            data-time="<?= date('Y-m-d\TH:i', strtotime($log['log_time'])) ?>"
                                            data-image="<?= $log['image'] ? BASE_ASSETS_UPLOADS . $log['image'] : '' ?>">
                                            <i class="bi bi-pencil text-blue-500"></i> Sửa
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider my-0">
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2 px-4 hover:bg-red-50 text-red-600 flex items-center gap-2"
                                            href="<?= BASE_URL ?>?action=hvd/logs/delete&id=<?= $log['id'] ?>&tour_id=<?= $tour_id ?>&guide_id=<?= $guide_id ?>"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa nhật ký này?')">
                                            <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="text-gray-700 whitespace-pre-wrap leading-relaxed mb-4">
                            <?= htmlspecialchars($log['content']) ?>
                        </div>

                        <?php if (!empty($log['image'])): ?>
                            <div class="rounded-lg overflow-hidden border border-gray-100">
                                <img src="<?= BASE_ASSETS_UPLOADS . $log['image'] ?>" alt="Log Image"
                                    class="w-full h-auto object-cover max-h-[400px]">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Add (Keep Bootstrap Modal classes) -->
<div class="modal fade" id="addLogModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xl border-0 shadow-xl">
            <div class="modal-header border-b border-gray-100">
                <h5 class="modal-title font-bold text-gray-800">Viết nhật ký mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>?action=hvd/logs/store" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-6">
                    <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
                    <input type="hidden" name="guide_id" value="<?= $guide_id ?>">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian</label>
                        <input type="datetime-local" name="log_time" class="form-control"
                            value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                        <textarea name="content" class="form-control" rows="4" required
                            placeholder="Ghi lại sự kiện, cảm nhận..."></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh (nếu có)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary bg-blue-600 border-blue-600">Lưu nhật ký</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editLogModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xl border-0 shadow-xl">
            <div class="modal-header border-b border-gray-100">
                <h5 class="modal-title font-bold text-gray-800">Chỉnh sửa nhật ký</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= BASE_URL ?>?action=hvd/logs/update" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-6">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
                    <input type="hidden" name="guide_id" value="<?= $guide_id ?>">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian</label>
                        <input type="datetime-local" name="log_time" id="edit_time" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                        <textarea name="content" id="edit_content" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh</label>
                        <div id="current_image_container" class="mb-3 rounded overflow-hidden"></div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="text-xs text-gray-500 mt-1">Chọn ảnh mới để thay thế ảnh cũ</div>
                    </div>
                </div>
                <div class="modal-footer border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary bg-blue-600 border-blue-600">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editBtns = document.querySelectorAll('.edit-log-btn');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const content = this.dataset.content;
                const time = this.dataset.time;
                const imageUrl = this.dataset.image;

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_content').value = content;
                document.getElementById('edit_time').value = time;

                const imgContainer = document.getElementById('current_image_container');
                if (imageUrl) {
                    imgContainer.innerHTML = `<img src="${imageUrl}" class="w-full h-32 object-cover">`;
                } else {
                    imgContainer.innerHTML = '<span class="text-gray-400 italic text-sm border p-2 rounded block text-center">Không có ảnh</span>';
                }
            });
        });
    });
</script>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>