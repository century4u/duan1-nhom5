<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> - Quản lý Tour</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css?v=<?= time() ?>">
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3 class="mb-0">
                    <i class="bi bi-airplane-fill"></i>
                    <span class="sidebar-title">Quản lý Tour</span>
                </h3>
                <button type="button" id="sidebarCollapse" class="btn btn-sm btn-outline-light d-md-none">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="sidebar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="user-details">
                        <div class="user-name">
                            <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User') ?>
                        </div>
                        <div class="user-role">
                            <?php if (isAdmin()): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">User</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="<?= BASE_URL ?>" class="menu-link">
                        <i class="bi bi-house-door"></i>
                        <span>Trang chủ</span>
                    </a>
                </li>

                <li class="menu-divider">
                    <span>Quản lý Tour</span>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=tour-categories"
                        class="menu-link <?= (isset($_GET['action']) && $_GET['action'] === 'tour-categories') ? 'active' : '' ?>">
                        <i class="bi bi-folder"></i>
                        <span>Danh mục Tour</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=tours"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'tours') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-map"></i>
                        <span>Quản lý Tour</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=bookings"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'bookings') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-calendar-check"></i>
                        <span>Đặt Tour</span>
                        <span class="badge bg-primary ms-auto">New</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=guides"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'guides') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>
                        <span>Quản lý HDV</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=departure-schedules"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'departure-schedules') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-calendar-event"></i>
                        <span>Lịch Khởi Hành</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=group-lists"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'group-lists') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-people-fill"></i>
                        <span>Danh sách đoàn</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=tour-customers"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'tour-customers') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-person-lines-fill"></i>
                        <span>Khách theo Tour</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=room-assignments"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'room-assignments') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-houses"></i>
                        <span>Phân phòng</span>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=checkins"
                        class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'checkins') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Quản lý Check-in</span>
                    </a>
                </li>

                <?php if (isAdmin()): ?>
                    <li class="menu-divider">
                        <span>Báo cáo & Thống kê</span>
                    </li>

                    <li class="menu-item">
                        <a href="<?= BASE_URL ?>?action=operation-reports"
                            class="menu-link <?= (isset($_GET['action']) && strpos($_GET['action'] ?? '', 'operation-reports') === 0) ? 'active' : '' ?>">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Báo cáo Vận hành Tour</span>
                        </a>
                    </li>

                    <li class="menu-divider">
                        <span>Hệ thống</span>
                    </li>

                    <li class="menu-item">
                        <a href="<?= BASE_URL ?>?action=statistics"
                            class="menu-link <?= (isset($_GET['action']) && $_GET['action'] === 'statistics') ? 'active' : '' ?>">
                            <i class="bi bi-bar-chart"></i>
                            <span>Thống kê</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="menu-divider"></li>

                <li class="menu-item">
                    <a href="<?= BASE_URL ?>?action=logout" class="menu-link text-danger">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>


        </nav>

        <!-- Page Content -->
        <div id="content" class="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                <div class="container-fluid">
                    <button type="button" id="sidebarToggle" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="navbar-nav ms-auto">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-bell me-2"></i>
                                <span class="badge bg-danger rounded-pill">3</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <h6 class="dropdown-header">Thông báo</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Booking mới #123</a></li>
                                <li><a class="dropdown-item" href="#">Tour sắp hết hạn</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-center" href="#">Xem tất cả</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid px-4">


                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= $_SESSION['success'] ?>
                        <?php unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?= $_SESSION['error'] ?>
                        <?php unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Có lỗi xảy ra:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php unset($_SESSION['errors']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Page Content -->
                <?php
                if (isset($view)) {
                    require_once PATH_VIEW . $view . '.php';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Admin JS -->
    <script>
        // Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarCollapse = document.getElementById('sidebarCollapse');

            // Toggle sidebar
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                    content.classList.toggle('active');
                });
            }

            // Close sidebar on mobile
            if (sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function () {
                    sidebar.classList.remove('active');
                    content.classList.remove('active');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function (event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 768) {
                    sidebar.classList.remove('active');
                    content.classList.remove('active');
                }
            });

            // Auto dismiss alerts after 2 seconds
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                setTimeout(function () {
                    alerts.forEach(function (alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 2000);
            }
        });
    </script>
</body>

</html>