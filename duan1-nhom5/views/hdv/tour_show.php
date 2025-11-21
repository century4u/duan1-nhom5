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

<main class="container my-4">
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div>
      <h2><?= htmlspecialchars($tour['name'] ?? '') ?></h2>
      <div class="text-muted">Mã: <?= htmlspecialchars($tour['code'] ?? '-') ?> | Điểm đi: <?= htmlspecialchars($tour['departure_location'] ?? '-') ?> | Điểm đến: <?= htmlspecialchars($tour['destination'] ?? '-') ?></div>
    </div>
    <div class="text-end">
      <a href="<?= BASE_URL ?>?action=hvd/tours&guide_id=<?= urlencode($guideId ?? '') ?>" class="btn btn-link">Quay lại</a>
    </div>
  </div>

  <?php if (!empty($assignment)): ?>
    <div class="alert alert-info">
      <strong>Phân công cho bạn:</strong>
      <div>Thời gian: <?= htmlspecialchars($assignment['start_date'] ?? '-') ?> → <?= htmlspecialchars($assignment['end_date'] ?? '-') ?></div>
      <?php if (!empty($assignment['notes'])): ?><div class="mt-1">Ghi chú: <?= nl2br(htmlspecialchars($assignment['notes'])) ?></div><?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-7">
      <div class="card mb-3">
        <div class="card-header">Lịch trình</div>
        <div class="card-body">
          <?php if (!empty($schedules)): ?>
            <?php foreach ($schedules as $sched): ?>
              <div class="mb-3">
                <h6>Ngày <?= htmlspecialchars($sched['day_number'] ?? '-') ?> <?= !empty($sched['date']) ? ' - ' . htmlspecialchars($sched['date']) : '' ?></h6>
                <div class="fw-semibold"><?= htmlspecialchars($sched['title'] ?? '') ?></div>
                <?php if (!empty($sched['description'])): ?><div class="text-muted small mb-1" style="white-space:pre-line"><?= nl2br(htmlspecialchars($sched['description'])) ?></div><?php endif; ?>
                <?php if (!empty($sched['activities_array'])): ?>
                  <div class="text-muted small">Hoạt động:</div>
                  <ul class="small">
                    <?php foreach ($sched['activities_array'] as $act): ?><li><?= htmlspecialchars(is_string($act) ? $act : json_encode($act)) ?></li><?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-muted">Chưa có lịch trình chi tiết.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card mb-3">
        <div class="card-header">Thông tin nhanh</div>
        <div class="card-body">
          <div class="mb-2"><strong>Giá:</strong> <?= isset($tour['price']) ? number_format($tour['price'],0,',','.').' ₫' : '-' ?></div>
          <div class="mb-2"><strong>Số khách tối đa:</strong> <?= htmlspecialchars($tour['max_participants'] ?? $tour['max_guests'] ?? '-') ?></div>
          <div class="mb-2"><strong>Thời lượng:</strong> <?= htmlspecialchars($tour['duration'] ?? '-') ?></div>
        </div>
      </div>

      <div class="card">
    <div class="card-header">Khách tham gia</div>
    <div class="card-body">
        <?php if (!empty($participants)): ?>
            <?php foreach ($participants as $pb): 
                $b = $pb['booking'];       // Dữ liệu từ bảng bookings
                $details = $pb['details']; // Dữ liệu từ bảng booking_details
            ?>
                <div class="mb-3">

                    <!-- Người đặt -->
                    <div>
                        <strong>Người đặt:</strong> 
                        <?= htmlspecialchars($b['contact_name'] ?? '-') ?>
                    </div>

                    <!-- Email - Điện thoại -->
                    <div class="text-muted small">
                        Email: <?= htmlspecialchars($b['contact_email'] ?? '-') ?> 
                        | Điện thoại: <?= htmlspecialchars($b['contact_phone'] ?? '-') ?>
                    </div>

                    <!-- Số khách -->
                    <div class="text-muted small">Số khách: <?= count($details) ?></div>

                    <!-- Danh sách khách -->
                    <?php if (!empty($details)): ?>
                        <ul class="small mt-2">
                            <?php foreach ($details as $d): ?>
                                <li>
                                    <?= htmlspecialchars($d['fullname'] ?? '-') ?> 
                                    - <?= htmlspecialchars($d['gender'] ?? '-') ?> 
                                    - <?= htmlspecialchars($d['birthdate'] ?? '-') ?> 
                                    | CMND/CCCD: <?= htmlspecialchars($d['id_card'] ?? '-') ?>
                                    | Passport: <?= htmlspecialchars($d['passport'] ?? '-') ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                </div>
                <hr />
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-muted">Chưa có khách đăng ký.</div>
        <?php endif; ?>
    </div>
</div>

  </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
