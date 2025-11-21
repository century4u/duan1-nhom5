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
      <a href="<?= BASE_URL ?>?action=hvd/tours" class="btn btn-light btn-sm me-2"><i class="bi bi-compass"></i> Tour</a>
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
  </div><main class="container mt-4">
  <h2 class="mb-3">Danh sách tour được phân công</h2>

  <?php if (!empty($assignedTours)): ?>
    <?php foreach ($assignedTours as $item):
      $h = $item['history'];
      $t = $item['tour'];
    ?>
      <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <h5 class="mb-1"><?= htmlspecialchars($t['name'] ?? $t['tour_name'] ?? '') ?></h5>
            <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($t['destination'] ?? '') ?></div>
            <div class="text-muted small">Thời gian: <?= htmlspecialchars($h['start_date'] ?? '-') ?> → <?= htmlspecialchars($h['end_date'] ?? '-') ?></div>
          </div>
          <div class="text-end">
            <a href="<?= BASE_URL ?>?action=tours/show&id=<?= htmlspecialchars($t['id'] ?? $t['tour_id'] ?? '') ?>" class="btn btn-outline-primary btn-sm">Mở chi tiết tour</a>
            <a href="<?= BASE_URL ?>?action=hvd&guide_id=<?= urlencode($_GET['guide_id'] ?? '') ?>" class="btn btn-link btn-sm">Quay lại</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="alert alert-info">Không có tour nào được phân công.</div>
  <?php endif; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>