<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<div class="mb-6">
    <a href="<?= BASE_URL ?>?action=hvd/customers"
        class="text-gray-500 hover:text-blue-600 inline-flex items-center gap-2">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Danh sách khách hàng</h1>
            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($tour['name']) ?></p>
        </div>
        <div class="text-right">
            <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                <?= count($participants) ?> booking(s)
            </span>
        </div>
    </div>

    <div class="p-6">
        <?php if (!empty($participants)): ?>
            <div class="space-y-6">
                <?php foreach ($participants as $pb):
                    $b = $pb['booking'];
                    $details = $pb['details'];
                    $isCancelled = ($b['status'] ?? '') === 'cancelled';
                    ?>
                    <div
                        class="bg-white border rounded-lg overflow-hidden transition-shadow hover:shadow-md <?= $isCancelled ? 'opacity-60 grayscale bg-gray-50' : '' ?>">
                        <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <span
                                    class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                    <?= substr($b['id'], -3) ?>
                                </span>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">Booking #<?= $b['id'] ?></div>
                                    <div class="text-xs text-gray-500">Liên hệ: <?= $b['contact_phone'] ?></div>
                                </div>
                            </div>
                            <?php if ($isCancelled): ?>
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded">Đã hủy</span>
                            <?php endif; ?>

                            <?php if (!$isCancelled): ?>
                                <!-- Helper for identifying group special needs -->
                                <?php
                                $hasSpecialNeeds = false;
                                foreach ($details as $d) {
                                    if (!empty($d['dietary_restrictions']) || !empty($d['special_requirements']) || !empty($d['hobby'])) {
                                        $hasSpecialNeeds = true;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($hasSpecialNeeds): ?>
                                    <span class="bg-amber-100 text-amber-700 text-xs px-2 py-1 rounded border border-amber-200">
                                        <i class="bi bi-star-fill text-[10px] mr-1"></i> Có lưu ý
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="divide-y divide-gray-100">
                            <?php foreach ($details as $guest):
                                $guestHasNote = !empty($guest['dietary_restrictions']) || !empty($guest['special_requirements']) || !empty($guest['hobby']);
                                ?>
                                <div class="p-4 flex items-start justify-between group hover:bg-gray-50/50 transition-colors">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="font-medium text-gray-800"><?= htmlspecialchars($guest['fullname']) ?></span>
                                            <span class="text-xs text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">
                                                <?= $guest['gender'] === 'male' ? 'Nam' : ($guest['gender'] === 'female' ? 'Nữ' : $guest['gender']) ?>
                                                / <?= $guest['birthdate'] ? date('Y', strtotime($guest['birthdate'])) : '?' ?>
                                            </span>
                                        </div>
                                        <?php if (!empty($guest['phone'])): ?>
                                            <div class="text-xs text-blue-600 mb-1 flex items-center gap-1">
                                                <i class="bi bi-telephone"></i> <?= htmlspecialchars($guest['phone']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Notes Display -->
                                        <div class="flex flex-wrap gap-2 text-sm mt-2">
                                            <?php if ($guest['dietary_restrictions']): ?>
                                                <div
                                                    class="flex items-center gap-1.5 text-orange-600 bg-orange-50 px-2 py-1 rounded border border-orange-100">
                                                    <i class="bi bi-egg-fried"></i>
                                                    <span><?= htmlspecialchars($guest['dietary_restrictions']) ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($guest['special_requirements']): ?>
                                                <div
                                                    class="flex items-center gap-1.5 text-purple-600 bg-purple-50 px-2 py-1 rounded border border-purple-100">
                                                    <i class="bi bi-exclamation-circle"></i>
                                                    <span><?= htmlspecialchars($guest['special_requirements']) ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($guest['hobby']): ?>
                                                <div
                                                    class="flex items-center gap-1.5 text-teal-600 bg-teal-50 px-2 py-1 rounded border border-teal-100">
                                                    <i class="bi bi-heart"></i>
                                                    <span><?= htmlspecialchars($guest['hobby']) ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!$guestHasNote && !$isCancelled): ?>
                                                <span
                                                    class="text-gray-400 text-xs italic opacity-0 group-hover:opacity-100 transition-opacity">Chưa
                                                    có ghi chú</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if (!$isCancelled): ?>
                                        <div class="flex-shrink-0 ml-4">
                                            <a href="<?= BASE_URL ?>?action=hvd/customer/edit&id=<?= $guest['id'] ?>&tour_id=<?= $id ?>&guide_id=<?= $guideId ?>"
                                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all"
                                                title="Cập nhật thông tin">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 text-gray-500">
                <i class="bi bi-clipboard-x text-4xl mb-3 block opacity-50"></i>
                <p>Chưa có thông tin khách hàng.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>