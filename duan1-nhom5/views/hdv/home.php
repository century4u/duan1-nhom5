<style>
  /* Reset & Base */
  :root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    --success-color: #198754;
    --info-color: #0dcaf0;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-bg: #f8f9fa;
    --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  }

  /* Header Styles */
  .bg-header {
    background-color: var(--primary-color) !important;
  }

  /* Card Stats */
  .card-stat {
    border: none;
    border-radius: 4px;
    color: white;
    transition: transform 0.2s;
  }

  .card-stat:hover {
    transform: translateY(-2px);
  }

  .bg-stat-blue {
    background-color: #6c8bef;
  }

  .bg-stat-green {
    background-color: #4ab779;
  }

  .bg-stat-orange {
    background-color: #f1ad57;
  }

  .stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
  }

  .stat-value {
    font-size: 2rem;
    font-weight: bold;
  }

  .stat-label {
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Empty State */
  .empty-state-icon {
    width: 60px;
    height: 60px;
    background-color: #f8f9fa;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
  }

  .empty-state-icon i {
    font-size: 2rem;
    color: #adb5bd;
  }
</style>

<div class="container-fluid p-0">
  <!-- Header -->
  <header class="bg-header text-white py-2 mb-4 rounded">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <i class="bi bi-phone fs-4 me-2"></i>
        <h1 class="h6 mb-0">Dashboard HDV |
          <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User') ?>
        </h1>
      </div>
      <nav>
        <a href="<?= BASE_URL ?>?action=checkins" class="btn btn-light text-dark btn-sm me-2 fw-bold">
          <i class="bi bi-qr-code-scan me-1"></i> Check-in
        </a>
        <a href="<?= BASE_URL ?>?action=logout" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-right"></i> Đăng xuất
        </a>
      </nav>
    </div>
  </header>

  <!-- Stats Cards -->
  <div class="row g-3 mb-4">
    <!-- Total Tours -->
    <div class="col-md-4">
      <div class="card card-stat bg-stat-blue h-100">
        <div class="card-body d-flex align-items-center justify-content-between px-4">
          <div>
            <div class="stat-value"><?= $stats['total_assigned'] ?? 0 ?></div>
            <div class="stat-label">Tổng tour</div>
          </div>
          <i class="bi bi-geo-alt stat-icon"></i>
        </div>
      </div>
    </div>

    <!-- Upcoming -->
    <div class="col-md-4">
      <div class="card card-stat bg-stat-green h-100">
        <div class="card-body d-flex align-items-center justify-content-between px-4">
          <div>
            <div class="stat-value"><?= $stats['upcoming_count'] ?? 0 ?></div>
            <div class="stat-label">Sắp tới</div>
          </div>
          <i class="bi bi-calendar-event stat-icon"></i>
        </div>
      </div>
    </div>

    <!-- Completed -->
    <div class="col-md-4">
      <div class="card card-stat bg-stat-orange h-100">
        <div class="card-body d-flex align-items-center justify-content-between px-4">
          <div>
            <div class="stat-value"><?= $stats['completed_count'] ?? 0 ?></div>
            <div class="stat-label">Hoàn thành</div>
          </div>
          <i class="bi bi-check2-circle stat-icon"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming Work Section -->
  <div class="row">
    <div class="col-12">
      <h5 class="mb-3 text-secondary text-uppercase fs-6 fw-bold">Công việc sắp tới của bạn</h5>

      <?php if (empty($upcomingTours)): ?>
        <div class="bg-white rounded p-5 text-center shadow-sm">
          <div class="empty-state-icon">
            <i class="bi bi-x-lg"></i>
          </div>
          <p class="text-secondary mb-0">Hiện tại bạn chưa có lịch trình tour nào sắp tới.</p>
        </div>
      <?php else: ?>
        <div class="list-group shadow-sm">
          <?php foreach ($upcomingTours as $tour): ?>
            <div class="list-group-item list-group-item-action p-3 border-0 border-bottom">
              <div class="d-flex w-100 justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1 fw-bold text-primary"><?= htmlspecialchars($tour['tour_name'] ?? 'Tour') ?></h6>
                  <small class="text-muted">
                    <i class="bi bi-clock me-1"></i> <?= date('d/m/Y', strtotime($tour['start_date'])) ?>
                    - <?= date('d/m/Y', strtotime($tour['end_date'])) ?>
                  </small>
                </div>
                <span class="badge bg-<?= $tour['status_class'] ?? 'primary' ?> rounded-pill">
                  <?= htmlspecialchars($tour['status_text'] ?? 'Sắp tới') ?>
                </span>
              </div>
              <?php if (isset($tour['participant_count'])): ?>
                <small class="text-muted mt-2 d-block">
                  <i class="bi bi-people me-1"></i> <?= $tour['participant_count'] ?> khách
                </small>
                </small>
              <?php endif; ?>

              <div class="mt-2 d-flex justify-content-end">
                <a href="<?= BASE_URL ?>?action=hvd/checkin&id=<?= $tour['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-qr-code-scan me-1"></i> Check-in
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>