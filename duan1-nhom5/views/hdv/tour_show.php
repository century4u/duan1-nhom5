<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<!-- Breadcrumb/Header -->
<div class="flex justify-between items-start mb-8">
  <div>
    <a href="<?= BASE_URL ?>?action=hvd" class="text-gray-500 hover:text-blue-600 mb-2 inline-block text-sm">
      <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
    <h1 class="text-3xl font-bold text-gray-800"><?= htmlspecialchars($tour['name'] ?? '') ?></h1>
    <div class="flex items-center gap-4 text-sm text-gray-600 mt-2">
      <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">Mã:
        <?= htmlspecialchars($tour['code'] ?? '-') ?></span>
      <span class="flex items-center gap-1"><i class="bi bi-geo-alt"></i>
        <?= htmlspecialchars($tour['departure_location'] ?? '-') ?></span>
      <i class="bi bi-arrow-right text-gray-400"></i>
      <span class="flex items-center gap-1"><i class="bi bi-flag"></i>
        <?= htmlspecialchars($tour['destination'] ?? '-') ?></span>
    </div>
  </div>

  <div class="flex gap-3">
    <a href="<?= BASE_URL ?>?action=hvd/attendance&tour_id=<?= $id ?>&guide_id=<?= $guideId ?>"
      class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 shadow-sm transition-colors">
      <i class="bi bi-calendar-check"></i> Điểm danh
    </a>
    <a href="<?= BASE_URL ?>?action=hvd/logs&tour_id=<?= $id ?>&guide_id=<?= $guideId ?>"
      class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 shadow-sm transition-colors">
      <i class="bi bi-journal-text"></i> Nhật ký Tour
    </a>
    <?php if (($assignment['status'] ?? '') !== 'completed'): ?>
      <a href="<?= BASE_URL ?>?action=hvd/tours/finish&tour_id=<?= $id ?>&guide_id=<?= $guideId ?>"
        onclick="return confirm('Bạn có chắc chắn muốn xác nhận hoàn thành tour này? Sau khi hoàn thành, bạn sẽ không thể điểm danh được nữa.');"
        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2 shadow-sm transition-colors">
        <i class="bi bi-check-circle"></i> Hoàn thành Tour
      </a>
    <?php else: ?>
      <span
        class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg flex items-center gap-2 border border-gray-200 cursor-not-allowed">
        <i class="bi bi-check-circle-fill"></i> Đã hoàn thành
      </span>
    <?php endif; ?>
  </div>
</div>

<?php if (!empty($assignment)): ?>
  <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start gap-3">
    <div class="text-blue-600 text-xl"><i class="bi bi-info-circle-fill"></i></div>
    <div>
      <h3 class="font-bold text-blue-800">Thông tin phân công</h3>
      <div class="text-blue-700 text-sm mt-1">
        Thời gian: <span class="font-medium"><?= htmlspecialchars($assignment['start_date'] ?? '-') ?></span> đến <span
          class="font-medium"><?= htmlspecialchars($assignment['end_date'] ?? '-') ?></span>
      </div>
      <?php if (!empty($assignment['notes'])): ?>
        <div class="text-blue-600 text-sm mt-2 p-2 bg-blue-100/50 rounded-lg">
          <?= nl2br(htmlspecialchars($assignment['notes'])) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<!-- Itinerary Section - Full Width -->
<div class="space-y-6 mb-6">
  <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
      <i class="bi bi-map text-blue-600"></i> Lịch trình chi tiết
    </h2>

    <div
      class="space-y-8 relative before:absolute before:inset-0 before:ml-4 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
      <?php if (!empty($schedules)): ?>
        <?php foreach ($schedules as $sched): ?>
          <div class="relative pl-12">
            <div
              class="absolute left-0 top-1 w-8 h-8 rounded-full bg-blue-100 border-4 border-white flex items-center justify-center text-blue-600 font-bold text-sm shadow-sm z-10">
              <?= htmlspecialchars($sched['day_number'] ?? '-') ?>
            </div>

            <div class="mb-2">
              <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Ngày
                <?= htmlspecialchars($sched['day_number'] ?? '-') ?></span>
              <?php if (!empty($sched['date'])): ?>
                <span class="text-gray-400 mx-2">•</span>
                <span class="text-sm text-gray-500"><?= htmlspecialchars($sched['date']) ?></span>
              <?php endif; ?>
            </div>

            <h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($sched['title'] ?? '') ?></h3>

            <?php if (!empty($sched['description'])): ?>
              <div class="text-gray-600 text-sm leading-relaxed mb-3 whitespace-pre-line bg-gray-50 p-3 rounded-lg">
                <?= nl2br(htmlspecialchars($sched['description'])) ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($sched['activities_array'])): ?>
              <div class="flex flex-wrap gap-2">
                <?php foreach ($sched['activities_array'] as $act): ?>
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="bi bi-check2"></i>
                    <?= htmlspecialchars(is_string($act) ? $act : json_encode($act)) ?>
                  </span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-gray-500 text-center py-4 pl-8">Chưa có lịch trình chi tiết.</div>
      <?php endif; ?>
    </div>
  </div>
</div>



<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>