<!doctype html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guide Manager</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap CSS (Retained for Modals/Components) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #1E40AF;
            color: white;
            padding: 24px;
            z-index: 1000;
        }

        .menu-item {
            padding: 12px 16px;
            cursor: pointer;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
            color: white;
            margin-bottom: 4px;
        }

        .menu-item:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
            color: white;
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.2);
            opacity: 1;
            font-weight: 500;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 32px;
            background: #F3F4F6;
            min-height: 100vh;
        }

        /* Cards */
        .tour-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            background: white;
        }

        .tour-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #EFF6FF;
            color: #1E40AF;
            font-weight: bold;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Bootstrap Override/Compatibility */
        a {
            text-decoration: none;
        }

        .btn-primary {
            background-color: #1E40AF;
            border-color: #1E40AF;
        }

        .btn-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }

        .text-primary {
            color: #1E40AF !important;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="flex items-center gap-3 mb-8 px-2">
            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-content-center text-xl">
                🚩
            </div>
            <div>
                <h2 class="text-xl font-bold leading-tight">Trang web cho hướng dẫn viên</h2>
            </div>
        </div>

        <?php
        $action = $_GET['action'] ?? '';
        ?>

        <div class="space-y-1">
            <a href="<?= BASE_URL ?>?action=hvd"
                class="menu-item <?= $action == 'hvd' || $action == 'hvd/home' ? 'active' : '' ?>">
                <i class="bi bi-grid-fill"></i> Quản lí tours
            </a>
            <a href="<?= BASE_URL ?>?action=hvd/schedule"
                class="menu-item <?= $action == 'hvd/schedule' ? 'active' : '' ?>">
                <i class="bi bi-calendar-event-fill"></i> Lịch Làm Việc
            </a>
            <a href="<?= BASE_URL ?>?action=hvd/customers"
                class="menu-item <?= strpos($action, 'hvd/customers') === 0 ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Khách Hàng
            </a>
        </div>

        <div class="mt-auto absolute bottom-6 w-[232px]">
            <div class="pt-6 border-t border-white/20">
                <a href="<?= BASE_URL ?>?action=logout"
                    class="menu-item text-red-200 hover:text-red-100 hover:bg-red-500/20">
                    <i class="bi bi-box-arrow-right"></i> Đăng Xuất
                </a>
                <div class="flex items-center gap-3 mt-4 px-3 py-2 bg-black/20 rounded-lg">
                    <div class="user-avatar text-sm">
                        <?= substr($_SESSION['full_name'] ?? 'U', 0, 1) ?>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-sm font-medium truncate"><?= $_SESSION['full_name'] ?? 'User' ?></div>
                        <div class="text-xs text-blue-200">Hướng Dẫn Viên</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">