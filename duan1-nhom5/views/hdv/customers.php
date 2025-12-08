<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Quản lý khách hàng</h1>
        <p class="text-gray-500 mt-1">Ghi nhận và theo dõi nhu cầu đặc biệt của khách hàng</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($assignedTours)): ?>
        <?php foreach ($assignedTours as $item):
            $tour = $item['tour'];
            $history = $item['history'];

            // Determine status for badge
            $status = $history['status'] ?? '';
            $statusLabel = 'Sắp diễn ra';
            $statusClass = 'bg-blue-100 text-blue-700';

            $startDate = substr($history['start_date'] ?? '2999-01-01', 0, 10);
            $endDate = substr($history['end_date'] ?? '1970-01-01', 0, 10);
            $today = date('Y-m-d');

            if ($status === 'completed' || $endDate < $today) {
                $statusLabel = 'Hoàn thành';
                $statusClass = 'bg-gray-100 text-gray-600';
            } elseif ($startDate <= $today && $endDate >= $today) {
                $statusLabel = 'Đang diễn ra';
                $statusClass = 'bg-yellow-100 text-yellow-800 animate-pulse';
            }
            ?>
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow flex flex-col h-full">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $statusClass ?>">
                        <?= $statusLabel ?>
                    </span>
                    <span class="text-gray-400 text-xs">Mã: <?= htmlspecialchars($tour['code']) ?></span>
                </div>

                <h3 class="font-bold text-gray-800 text-lg mb-2 line-clamp-2 min-h-[56px]"
                    title="<?= htmlspecialchars($tour['name']) ?>">
                    <?= htmlspecialchars($tour['name']) ?>
                </h3>

                <div class="space-y-3 mb-6 flex-grow">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div>
                            <div class="font-medium">Thời gian</div>
                            <div class="text-xs"><?= date('d/m/Y', strtotime($history['start_date'])) ?> -
                                <?= date('d/m/Y', strtotime($history['end_date'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="font-medium">Số lượng khách</div>
                            <div class="text-xs font-bold text-gray-800"><?= $tour['real_guest_count'] ?> người</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                            <i class="bi bi-star"></i>
                        </div>
                        <div>
                            <div class="font-medium">Nhu cầu đặc biệt</div>
                            <div
                                class="text-xs font-bold <?= $tour['special_needs_count'] > 0 ? 'text-amber-600' : 'text-gray-400' ?>">
                                <?= $tour['special_needs_count'] ?> yêu cầu
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-4 border-t border-gray-100">
                    <a href="<?= BASE_URL ?>?action=hvd/tours/customers&id=<?= $tour['id'] ?>&guide_id=<?= $guideId ?>"
                        class="block w-full py-2.5 bg-blue-600 text-white rounded-lg text-center font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center justify-center gap-2">
                        <span>Xem danh sách khách</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full py-12 text-center text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
            <i class="bi bi-people text-4xl mb-3 block opacity-50"></i>
            <p>Chưa có tour nào được phân công.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>