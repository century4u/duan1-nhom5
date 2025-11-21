<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - Hướng dẫn viên</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
    }
    .card-hover:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
    }
    .stat-card {
      border-radius: 12px;
      padding: 20px;
      color: #fff;
    }
    .bg-tours { background: #667eea; }
    .bg-calendar { background: #48bb78; }
    .bg-reports { background: #f6ad55; }
    .card-title { font-size: 1.1rem; font-weight: 600; }
    .upcoming-tour { font-size: 0.9rem; padding: 4px 8px; border-radius: 6px; background-color: #e7f1ff; color: #0a58ca; margin-bottom: 4px; cursor: pointer; }
    .upcoming-tour:hover { background-color: #cfe2ff; }
  </style>
</head>
<body>

<!-- Header -->
<header class="bg-primary text-white py-3 mb-4">
  <div class="container d-flex justify-content-between align-items-center">
    <a class="nav-link" href="<?= BASE_URL ?>?action=hvd">
    <h1 class="h5 mb-0"><i class="bi bi-person"></i> Xin chào...</h1></a>
    <nav>
      <a href="<?= BASE_URL ?>?action=hvd/tourss" class="btn btn-light btn-sm me-2"><i class="bi bi-compass"></i>Tổng Tour</a>
      <a href="calendar.html" class="btn btn-light btn-sm me-2"><i class="bi bi-calendar3"></i> Lịch làm việc</a>
      <a href="reports.html" class="btn btn-light btn-sm"><i class="bi bi-file-earmark-text"></i> Báo cáo</a>
    </nav>
  </div>
</header>

<main class="container">

  <!-- Thống kê nhanh -->
  <div class="row g-4 mb-4">
    <a href="<?= BASE_URL ?>?action=hvd/tours&guide_id=<?= urlencode($_GET['guide_id'] ?? ($_SESSION['user_id'] ?? '')) ?>" class="text-decoration-none col-md-4">
      <div class="stat-card bg-tours card-hover text-center">
        <div class="card-title"><i class="bi bi-compass"></i> Tour đang phụ trách</div>
        <div class="display-6 mt-2" id="total-tours"><?= isset($totalTours) ? (int)$totalTours : 0 ?></div>
      </div>
    </a>
    <div class="col-md-4">
      <div class="stat-card bg-calendar card-hover text-center">
        <div class="card-title"><i class="bi bi-calendar3"></i> Lịch sắp tới</div>
        <div class="display-6 mt-2" id="upcoming-days"><?= isset($upcomingTours) ? count($upcomingTours) : 0 ?></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card bg-reports card-hover text-center">
        <div class="card-title"><i class="bi bi-file-earmark-text"></i> Báo cáo chưa nộp</div>
        <div class="display-6 mt-2" id="pending-reports"><?= isset($pendingReports) ? (int)$pendingReports : 0 ?></div>
      </div>
    </div>
  </div>

  <!-- Lịch làm việc sắp tới -->
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
      <h3 class="h5 mb-0"><i class="bi bi-clock-history"></i> Tour sắp tới</h3>
    </div>
    <div class="card-body" id="upcoming-tours">
      <?php if (!empty($upcomingTours)): ?>
        <?php foreach ($upcomingTours as $t): ?>
          <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= htmlspecialchars($t['id'] ?? $t['tour_id'] ?? '') ?>&guide_id=<?= urlencode($_GET['guide_id'] ?? ($_SESSION['user_id'] ?? '')) ?>" class="text-decoration-none text-reset">
          <div class="card mb-3" style="cursor:pointer;">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-1"><?= htmlspecialchars($t['name'] ?? $t['tour_name'] ?? '') ?></h6>
                <p class="text-muted small mb-0"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($t['destination'] ?? ($t['departure_location'] ?? '-')) ?></p>
                <div class="text-muted small">Thời gian: <?= htmlspecialchars($t['duration'] ?? '-') ?></div>
              </div>
              <div class="text-end">
                <span class="badge bg-primary"><?= htmlspecialchars($t['status'] ?? '') ?></span>
                <div class="text-muted small">Giá: <?= isset($t['price']) ? number_format($t['price'], 0, ',', '.') . ' ₫' : '-' ?></div>
                <div class="text-muted small">Số khách đã đăng ký: <?= htmlspecialchars($t['booked_participants'] ?? 0) ?> / <?= htmlspecialchars($t['max_participants'] ?? $t['max_guests'] ?? '-') ?></div>
              </div>
            </div>
          </div>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-muted">Chưa có tour sắp tới</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Danh sách tour gần đây -->
  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <h3 class="h5 mb-0"><i class="bi bi-compass"></i> Tour gần đây</h3>
    </div>
    <div class="card-body" id="recent-tours">
      <div class="text-muted">Chưa có tour nào</div>
    </div>
  </div>

</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
