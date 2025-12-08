<?php require_once PATH_VIEW . 'hdv/layouts/header.php'; ?>

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Lịch làm việc</h1>
        <p class="text-gray-500 mt-1">Theo dõi lịch dẫn tour của bạn</p>
    </div>

    <div class="flex items-center gap-4 bg-white p-1 rounded-xl shadow-sm border border-gray-200">
        <a href="<?= BASE_URL ?>?action=hvd/schedule&guide_id=<?= $guideId ?>&month=<?= $prevMonth ?>&year=<?= $prevYear ?>"
            class="p-2 hover:bg-gray-100 rounded-lg text-gray-600 transition-colors">
            <i class="bi bi-chevron-left"></i>
        </a>
        <span class="font-bold text-lg min-w-[150px] text-center">
            Tháng <?= $month ?>, <?= $year ?>
        </span>
        <a href="<?= BASE_URL ?>?action=hvd/schedule&guide_id=<?= $guideId ?>&month=<?= $nextMonth ?>&year=<?= $nextYear ?>"
            class="p-2 hover:bg-gray-100 rounded-lg text-gray-600 transition-colors">
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Calendar Header -->
    <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
        <?php
        $daysOfWeek = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        foreach ($daysOfWeek as $day) {
            echo '<div class="py-3 text-center text-sm font-semibold text-gray-600 uppercase tracking-wider">' . $day . '</div>';
        }
        ?>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 auto-rows-fr">
        <?php
        // Logic to calculate dates
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $numberDays = date('t', $firstDayOfMonth);
        $dateComponents = getdate($firstDayOfMonth);
        $dayOfWeek = $dateComponents['wday']; // 0 (Sun) - 6 (Sat)
        
        // Padding for previous month
        for ($i = 0; $i < $dayOfWeek; $i++) {
            echo '<div class="min-h-[120px] bg-gray-50 border-b border-r border-gray-100"></div>';
        }

        // Days of month
        for ($day = 1; $day <= $numberDays; $day++) {
            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $isToday = ($currentDate === date('Y-m-d'));

            // Find tours for this day
            $daysTours = [];
            $dayStatus = null; // completed, ongoing, upcoming
        
            foreach ($assignedTours as $t) {
                $start = date('Y-m-d', strtotime($t['history']['start_date']));
                $end = date('Y-m-d', strtotime($t['history']['end_date']));

                if ($currentDate >= $start && $currentDate <= $end) {
                    $daysTours[] = $t;

                    $dbStatus = $t['history']['status'] ?? '';
                    // Infer status for this day
                    $status = '';
                    if ($dbStatus === 'completed' || $end < date('Y-m-d')) {
                        $status = 'completed';
                    } elseif ($start <= date('Y-m-d') && $end >= date('Y-m-d')) {
                        $status = 'ongoing';
                    } elseif ($start > date('Y-m-d')) {
                        $status = 'upcoming';
                    }
                    if ($status === 'ongoing')
                        $dayStatus = 'ongoing';
                    elseif ($status === 'upcoming' && $dayStatus !== 'ongoing')
                        $dayStatus = 'upcoming';
                    elseif ($status === 'completed' && !$dayStatus)
                        $dayStatus = 'completed';
                }
            }

            // Map status to colors
            $bgClass = 'bg-white';
            if ($dayStatus === 'ongoing')
                $bgClass = 'bg-yellow-100'; // Đang diễn ra -> Vàng
            elseif ($dayStatus === 'upcoming')
                $bgClass = 'bg-blue-100'; // Sắp diễn ra -> Xanh da trời
            elseif ($dayStatus === 'completed')
                $bgClass = 'bg-green-100'; // Hoàn thành -> Xanh lá
            elseif ($isToday)
                $bgClass = 'bg-blue-50/50';

            ?>
            <div
                class="min-h-[120px] p-2 border-b border-r border-gray-100 transition-colors hover:opacity-90 group relative <?= $bgClass ?>">
                <div class="flex justify-between items-start mb-2">
                    <span
                        class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full <?= $isToday ? 'bg-blue-600 text-white shadow-md' : 'text-gray-700' ?>">
                        <?= $day ?>
                    </span>
                    <?php if (!empty($daysTours)): ?>
                        <span class="xs:hidden md:inline-flex w-2 h-2 rounded-full bg-blue-500"></span>
                    <?php endif; ?>
                </div>

                <div class="space-y-1">
                    <?php foreach ($daysTours as $t):
                        $status = $t['history']['status'] ?? '';
                        // Keep pills white for contrast against colored backgrounds
                        $pillClass = 'bg-white border text-gray-700 shadow-sm';
                        ?>
                        <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= $t['tour']['id'] ?>&guide_id=<?= $guideId ?>"
                            class="block text-[10px] sm:text-xs px-2 py-1 rounded <?= $pillClass ?> truncate hover:scale-[1.02] transition-transform"
                            title="<?= htmlspecialchars($t['tour']['name']) ?>">
                            <?= htmlspecialchars($t['tour']['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php

            // End of week wrap (optional if using grid)
            if (($day + $dayOfWeek) % 7 == 0 && $day != $numberDays) {
                // New row logic handled by CSS grid automatically
            }
        }

        // Padding for next month
        $remainingDays = 7 - (($numberDays + $dayOfWeek) % 7);
        if ($remainingDays < 7) {
            for ($i = 0; $i < $remainingDays; $i++) {
                echo '<div class="min-h-[120px] bg-gray-50 border-b border-r border-gray-100"></div>';
            }
        }
        ?>
    </div>
</div>

<div class="mt-6 flex gap-4 text-sm text-gray-600">
    <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded bg-blue-100 border border-blue-200"></span> Đang diễn ra
    </div>
    <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded bg-green-100 border border-green-200"></span> Hoàn thành
    </div>
    <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-200"></span> Sắp tới
    </div>
</div>

<?php require_once PATH_VIEW . 'hdv/layouts/footer.php'; ?>