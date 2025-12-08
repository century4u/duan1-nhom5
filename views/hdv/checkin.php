<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check-in - <?= htmlspecialchars($tour['name'] ?? 'Tour') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f8f9fa;
      padding-bottom: 80px;
    }
    .customer-card {
      border-radius: 12px;
      border: 2px solid #e0e0e0;
      margin-bottom: 12px;
      padding: 16px;
      background: white;
      transition: all 0.3s ease;
    }
    .customer-card.checked-in {
      border-color: #28a745;
      background-color: #f0fff4;
    }
    .customer-card.late {
      border-color: #ffc107;
      background-color: #fffbf0;
    }
    .customer-card.absent {
      border-color: #dc3545;
      background-color: #fff5f5;
    }
    .checkin-checkbox {
      width: 30px;
      height: 30px;
      cursor: pointer;
    }
    .special-req-badge {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffc107;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 0.85rem;
      display: inline-block;
      margin-top: 4px;
    }
    .special-req-icon {
      color: #ffc107;
      font-size: 1.2rem;
    }
    .summary-card {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: white;
      box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
      padding: 16px;
      z-index: 1000;
    }
    .summary-stats {
      display: flex;
      justify-content: space-around;
      gap: 10px;
    }
    .stat-item {
      text-align: center;
      flex: 1;
    }
    .stat-number {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .stat-label {
      font-size: 0.8rem;
      color: #666;
    }
    .search-box {
      position: sticky;
      top: 0;
      background: white;
      z-index: 999;
      padding: 16px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 16px;
    }
    .btn-check-action {
      min-width: 80px;
      font-size: 0.85rem;
      padding: 4px 8px;
    }
  </style>
</head>
<body>

<!-- Header -->
<header class="bg-primary text-white py-3 mb-3">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
      <h1 class="h5 mb-0"><i class="bi bi-clipboard-check"></i> Check-in khách</h1>
      <small><?= htmlspecialchars($tour['name'] ?? '') ?></small>
    </div>
    <a href="<?= BASE_URL ?>?action=hvd/show&id=<?= urlencode($tourId) ?>&guide_id=<?= urlencode($guideId ?? '') ?>" class="btn btn-light btn-sm">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>
</header>

<!-- Search Box -->
<div class="search-box">
  <div class="container">
    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Tìm kiếm theo tên khách...">
  </div>
</div>

<!-- Customer List -->
<main class="container">
  <?php if (!empty($customers)): ?>
    <div id="customerList">
      <?php foreach ($customers as $c): 
        $detail = $c['detail'];
        $booking = $c['booking'];
        $checkin = $c['checkin'];
        $status = $checkin['status'] ?? 'pending';
        $hasSpecialReq = !empty($detail['special_requirements']);
      ?>
        <div class="customer-card <?= htmlspecialchars($status) ?>" data-name="<?= htmlspecialchars($detail['fullname']) ?>" data-id="<?= $detail['id'] ?>">
          <div class="d-flex align-items-start gap-3">
            <!-- Checkbox -->
            <div class="flex-shrink-0">
              <input 
                type="checkbox" 
                class="checkin-checkbox form-check-input" 
                data-id="<?= $detail['id'] ?>"
                <?= in_array($status, ['checked_in', 'late']) ? 'checked' : '' ?>
                onchange="quickCheckin(this, <?= $detail['id'] ?>)">
            </div>
            
            <!-- Info -->
            <div class="flex-grow-1">
              <h6 class="mb-1">
                <?= htmlspecialchars($detail['fullname']) ?>
                <?php if ($hasSpecialReq): ?>
                  <i class="bi bi-exclamation-triangle-fill special-req-icon" title="Có yêu cầu đặc biệt"></i>
                <?php endif; ?>
              </h6>
              <div class="text-muted small">
                <i class="bi bi-person"></i> <?= htmlspecialchars($detail['gender'] ?? '-') ?> | 
                <i class="bi bi-calendar"></i> <?= htmlspecialchars($detail['birthdate'] ?? '-') ?>
              </div>
              
              <?php if ($hasSpecialReq): ?>
                <div class="special-req-badge mt-2">
                  <i class="bi bi-exclamation-circle"></i> 
                  <strong>Yêu cầu đặc biệt:</strong> <?= nl2br(htmlspecialchars($detail['special_requirements'])) ?>
                </div>
              <?php endif; ?>
              
              <?php if ($checkin && !empty($checkin['checkin_time'])): ?>
                <div class="text-success small mt-2">
                  <i class="bi bi-check-circle-fill"></i> 
                  Check-in lúc: <?= date('H:i d/m/Y', strtotime($checkin['checkin_time'])) ?>
                  <?php if (!empty($checkin['checked_by_name'])): ?>
                    bởi <?= htmlspecialchars($checkin['checked_by_name']) ?>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex-shrink-0">
              <button class="btn btn-sm btn-success btn-check-action" onclick="setStatus(<?= $detail['id'] ?>, 'checked_in')">
                <i class="bi bi-check"></i> Đến
              </button>
              <button class="btn btn-sm btn-warning btn-check-action mt-1" onclick="setStatus(<?= $detail['id'] ?>, 'late')">
                <i class="bi bi-clock"></i> Muộn
              </button>
              <button class="btn btn-sm btn-secondary btn-check-action mt-1" onclick="editSpecialReq(<?= $detail['id'] ?>, '<?= htmlspecialchars(str_replace("'", "\\'", $detail['special_requirements'] ?? ''), ENT_QUOTES) ?>')">
                <i class="bi bi-pencil"></i> YC
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i> Chưa có khách đăng ký tour này.
    </div>
  <?php endif; ?>
</main>

<!-- Summary Card (Fixed Bottom) -->
<div class="summary-card">
  <div class="container">
    <div class="summary-stats">
      <div class="stat-item">
        <div class="stat-number text-success" id="stat-checked"><?= (int)($summary['checked_in'] ?? 0) ?></div>
        <div class="stat-label">Đã đến</div>
      </div>
      <div class="stat-item">
        <div class="stat-number text-warning" id="stat-late"><?= (int)($summary['late'] ?? 0) ?></div>
        <div class="stat-label">Đến muộn</div>
      </div>
      <div class="stat-item">
        <div class="stat-number text-muted" id="stat-pending"><?= (int)($summary['pending'] ?? 0) ?></div>
        <div class="stat-label">Chưa đến</div>
      </div>
      <div class="stat-item">
        <div class="stat-number text-primary" id="stat-total"><?= (int)($summary['total_customers'] ?? 0) ?></div>
        <div class="stat-label">Tổng số</div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  const cards = document.querySelectorAll('.customer-card');
  
  cards.forEach(card => {
    const name = card.dataset.name.toLowerCase();
    if (name.includes(searchTerm)) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
});

// Quick check-in
function quickCheckin(checkbox, id) {
  const status = checkbox.checked ? 'checked_in' : 'pending';
  setStatus(id, status, checkbox);
}

// Set status
function setStatus(id, status, sourceCheckbox = null) {
  const formData = new FormData();
  formData.append('booking_detail_id', id);
  formData.append('status', status);
  
  fetch('<?= BASE_URL ?>?action=hvd/quickCheckin', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Update UI
      const card = document.querySelector(`[data-id="${id}"]`).closest('.customer-card');
      card.className = 'customer-card ' + status;
      
      // Update checkbox if not source
      if (!sourceCheckbox) {
        const checkbox = card.querySelector('.checkin-checkbox');
        checkbox.checked = ['checked_in', 'late'].includes(status);
      }
      
      // Show toast
      showToast(data.message, 'success');
      
      // Reload to update summary
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast(data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('Có lỗi xảy ra!', 'error');
  });
}

// Edit special requirements
function editSpecialReq(id, currentValue) {
  const newValue = prompt('Nhập yêu cầu đặc biệt:\n(Ví dụ: Ăn chay, dị ứng hải sản, khó đi lại, v.v.)', currentValue);
  
  if (newValue === null) return; // User cancelled
  
  const formData = new FormData();
  formData.append('id', id);
  formData.append('special_requirements', newValue);
  
  fetch('<?= BASE_URL ?>?action=hvd/updateSpecialRequirement', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast(data.message, 'success');
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast(data.message, 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showToast('Có lỗi xảy ra!', 'error');
  });
}

// Toast notification
function showToast(message, type = 'info') {
  const colors = {
    success: '#28a745',
    error: '#dc3545',
    info: '#17a2b8'
  };
  
  const toast = document.createElement('div');
  toast.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${colors[type] || colors.info};
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 9999;
    font-weight: 500;
  `;
  toast.textContent = message;
  document.body.appendChild(toast);
  
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}
</script>

</body>
</html>
