<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<div class="flex justify-between items-center mb-8">
  <div>
    <h1 class="text-3xl font-bold text-gray-800">Quản lý Tour</h1>
    <p class="text-gray-500 mt-1">Danh sách tất cả các tour được phân công</p>
  </div>

  <div class="flex gap-3">
    <button
      class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center gap-2">
      <i class="bi bi-filter"></i> Lọc trạng thái
    </button>
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
      <i class="bi bi-download"></i> Xuất báo cáo
    </button>
  </div>
</div>

<!-- Tour List Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php if (empty($assignedTours)): ?>
    <div class="col-span-3 text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
      <div class="inline-block p-4 rounded-full bg-gray-50 mb-4">
        <i class="bi bi-inbox text-4xl text-gray-400"></i>
      </div>
      <h3 class="text-lg font-medium text-gray-900">Chưa có tour phân công</h3>
      <p class="text-gray-500 mt-1">Hiện tại bạn chưa được phân công tour nào.</p>
    </div>
  <?php else: ?>
    <?php foreach ($assignedTours as $item):
      $tour = $item['tour'];
      $history = $item['history'];
      $today = date('Y-m-d');
      $startDate = $history['start_date'];
      $endDate = $history['end_date'];
      $dbStatus = $history['status'] ?? '';

      if ($dbStatus === 'completed') {
        $displayStatus = 'completed';
      } elseif ($endDate < $today) {
        $displayStatus = 'past';
      } elseif ($startDate <= $today && $endDate >= $today) {
        $displayStatus = 'ongoing';
      } else {
        $displayStatus = 'upcoming';
      }

      $statusColors = [
        'upcoming' => 'bg-blue-100 text-blue-700',
        'ongoing' => 'bg-yellow-100 text-yellow-800',
        'completed' => 'bg-green-100 text-green-700',
        'past' => 'bg-gray-100 text-gray-600',
        'cancelled' => 'bg-red-100 text-red-700',
      ];
      $statusLabels = [
        'upcoming' => 'Chưa diễn ra',
        'ongoing' => 'Đang diễn ra',
        'completed' => 'Đã hoàn thành',
        'past' => 'Đã qua',
        'cancelled' => 'Đã hủy',
      ];
      ?>
      <div class="tour-card bg-white rounded-xl p-6 flex flex-col h-full relative group">
        <div class="flex justify-between items-start mb-4">
          <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusColors[$displayStatus] ?? 'bg-gray-100' ?>">
            <?= $statusLabels[$displayStatus] ?? $displayStatus ?>
          </span>
          <span class="text-sm font-mono text-gray-500">ID: #<?= $tour['id'] ?></span>
        </div>

        <h3
          class="font-bold text-lg mb-2 text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 min-h-[56px]">
          <?= htmlspecialchars($tour['name']) ?>
        </h3>

        <div class="space-y-3 text-sm text-gray-600 mb-6 flex-grow">
          <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
              <i class="bi bi-calendar3"></i>
            </div>
            <div>
              <div class="text-xs text-gray-500">Thời gian</div>
              <div class="font-medium"><?= date('d/m/Y', strtotime($history['start_date'])) ?> -
                <?= date('d/m/Y', strtotime($history['end_date'])) ?>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
              <i class="bi bi-geo-alt"></i>
            </div>
            <div>
              <div class="text-xs text-gray-500">Khởi hành</div>
              <div class="font-medium"><?= htmlspecialchars($tour['departure_location'] ?? 'Hà Nội') ?></div>
            </div>
          </div>

          <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-50 transition-colors">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
              <i class="bi bi-people"></i>
            </div>
            <div>
              <div class="text-xs text-gray-500">Số lượng khách</div>
              <div class="font-medium"><?= count($item['participants']) ?> người</div>
            </div>
          </div>
        </div>

        <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-3">
          <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= $tour['id'] ?>&guide_id=<?= $guideId ?? '' ?>"
            class="col-span-2 px-4 py-2.5 rounded-lg bg-blue-600 text-white text-center hover:bg-blue-700 font-medium transition-colors shadow-sm hover:shadow-md">
            Xem chi tiết
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>