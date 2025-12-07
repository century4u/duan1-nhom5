<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<h1 class="text-3xl font-bold mb-8 text-gray-800">Thông tin tổng quan</h1>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
  <!-- 1. Đang diễn ra -->
  <div onclick="filterTours('ongoing')"
    class="cursor-pointer p-6 rounded-xl text-white shadow-lg transform hover:scale-105 transition-transform duration-200"
    style="background: linear-gradient(135deg, #F59E0B, #FBBF24);">
    <p class="text-yellow-100 mb-1">Đang diễn ra</p>
    <p class="text-3xl font-bold">
      <?php
      $ongoingCount = 0;
      $today = date('Y-m-d');
      foreach ($assignedTours as $t) {
        $startDate = $t['history']['start_date'] ?? '2999-01-01';
        $endDate = $t['history']['end_date'] ?? '1970-01-01';
        $status = $t['history']['status'] ?? '';
        if ($status !== 'completed' && $startDate <= $today && $endDate >= $today) {
          $ongoingCount++;
        }
      }
      echo $ongoingCount;
      ?>
    </p>
    <div class="mt-4 text-sm text-yellow-100 flex justify-between items-center">
      <span>Hiện tại</span>
      <i class="bi bi-play-circle text-xl"></i>
    </div>
  </div>

  <!-- 2. Sắp diễn ra -->
  <div onclick="filterTours('upcoming')"
    class="cursor-pointer p-6 rounded-xl text-white shadow-lg transform hover:scale-105 transition-transform duration-200"
    style="background: linear-gradient(135deg, #1E40AF, #3B82F6 );">
    <p class="text-green-100 mb-1">Sắp diễn ra</p>
    <p class="text-3xl font-bold">
      <?php
      $upcomingCount = 0;
      foreach ($assignedTours as $t) {
        $startDate = $t['history']['start_date'] ?? '1970-01-01';
        if ($startDate > $today) {
          $upcomingCount++;
        }
      }
      echo $upcomingCount;
      ?>
    </p>
    <div class="mt-4 text-sm text-green-200 flex justify-between items-center">
      <span>Trong tương lai</span>
      <i class="bi bi-calendar-check text-xl"></i>
    </div>
  </div>

  <!-- 3. Hoàn thành/Đã qua -->
  <div onclick="filterTours('completed')"
    class="cursor-pointer p-6 rounded-xl text-white shadow-lg transform hover:scale-105 transition-transform duration-200"
    style="background: linear-gradient(135deg, #059669, #10B981);">
    <p class="text-purple-100 mb-1">Hoàn thành / Đã qua</p>
    <p class="text-3xl font-bold">
      <?php
      $completedCount = 0;
      foreach ($assignedTours as $t) {
        $status = $t['history']['status'] ?? '';
        $endDate = $t['history']['end_date'] ?? '9999-12-31';
        if ($status === 'completed' || $endDate < $today) {
          $completedCount++;
        }
      }
      echo $completedCount;
      ?>
    </p>
    <div class="mt-4 text-sm text-purple-200 flex justify-between items-center">
      <span>Đã kết thúc</span>
      <i class="bi bi-check-circle text-xl"></i>
    </div>
  </div>

  <!-- 4. Tổng phân công -->
  <div onclick="filterTours('all')"
    class="cursor-pointer p-6 rounded-xl text-white shadow-lg transform hover:scale-105 transition-transform duration-200"
    style="background: linear-gradient(135deg, #7C3AED, #8B5CF6);">
    <p class="text-blue-100 mb-1">Tours được phân công</p>
    <p class="text-3xl font-bold"><?= count($assignedTours) ?></p>
    <div class="mt-4 text-sm text-blue-200 flex justify-between items-center">
      <span>Tổng số</span>
      <i class="bi bi-briefcase text-xl"></i>
    </div>
  </div>
</div>

<div class="flex justify-between items-center mb-6">
  <h2 class="text-2xl font-bold text-gray-800" id="tourListTitle">Danh sách Tour</h2>
  <a href="<?= BASE_URL ?>?action=hvd/tours"
    class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
    Xem tất cả <i class="bi bi-arrow-right"></i>
  </a>
</div>

<!-- Tour List Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="tourGrid">
  <?php if (empty($assignedTours)): ?>
    <div class="col-span-3 text-center py-10 text-gray-500 bg-white rounded-xl border border-dashed border-gray-300">
      <i class="bi bi-inbox text-4xl mb-3 block"></i>
      Bạn chưa có tour nào được phân công.
    </div>
  <?php else: ?>
    <?php foreach ($assignedTours as $item):
      $tour = $item['tour'];
      $history = $item['history'];
      $today = date('Y-m-d');
      $startDate = $history['start_date'];
      $endDate = $history['end_date'];
      $dbStatus = $history['status'] ?? ''; // 'completed' or something else

      // Derived Status Logic
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
      <div class="tour-card-wrapper" data-status="<?= $displayStatus ?>">
      <div class="tour-card bg-white rounded-xl p-6 flex flex-col h-full">
        <div class="flex justify-between items-start mb-4">
          <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $statusColors[$displayStatus] ?? 'bg-gray-100' ?>">
            <?= $statusLabels[$displayStatus] ?? $displayStatus ?>
          </span>
          <span class="text-xs text-gray-500">ID: #<?= $tour['id'] ?></span>
        </div>

        <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-2 min-h-[56px]">
          <?= htmlspecialchars($tour['name']) ?>
        </h3>

        <div class="space-y-3 text-sm text-gray-600 mb-6 flex-grow">
          <div class="flex items-center gap-2">
            <i class="bi bi-calendar3 text-blue-500"></i>
            <span><?= date('d/m/Y', strtotime($history['start_date'])) ?> -
              <?= date('d/m/Y', strtotime($history['end_date'])) ?></span>
          </div>
          <div class="flex items-center gap-2">
            <i class="bi bi-geo-alt text-red-500"></i>
            <span><?= htmlspecialchars($tour['departure_location'] ?? 'Hà Nội') ?></span>
          </div>
          <div class="flex items-center gap-2">
            <i class="bi bi-people text-purple-500"></i>
            <span><?= count($item['participants']) ?> khách</span>
          </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex gap-3">
          <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= $tour['id'] ?>&guide_id=<?= $guideId ?? '' ?>"
            class="flex-1 px-4 py-2 rounded-lg bg-blue-600 text-white text-center hover:bg-blue-700 font-medium transition-colors">
            Chi tiết
          </a>
        </div>
      </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
function filterTours(status) {
    const cards = document.querySelectorAll('.tour-card-wrapper');
    const title = document.getElementById('tourListTitle');
    
    cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        
        // Show all
        if (status === 'all') {
            card.style.display = 'block';
        } 
        // Filter logic
        else if (status === 'completed') {
            if (cardStatus === 'completed' || cardStatus === 'past') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        }
        else {
            if (cardStatus === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        }
    });

    const titles = {
        'all': 'Danh sách Tour',
        'ongoing': 'Tour đang diễn ra',
        'upcoming': 'Tour sắp diễn ra',
        'completed': 'Tour hoàn thành / đã qua'
    };
    if(titles[status]) title.textContent = titles[status];
}
</script>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>