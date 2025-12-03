<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật thông tin khách hàng - Hướng dẫn viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="bg-primary text-white py-3 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="nav-link" href="<?= BASE_URL ?>?action=hvd">
            <h1 class="h5 mb-0"><i class="bi bi-person"></i> Xin chào, <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User') ?></h1>
        </a>
        <nav>
            <a href="<?= BASE_URL ?>?action=hvd/tours" class="btn btn-light btn-sm me-2"><i class="bi bi-compass"></i> Tour</a>
            <a href="calendar.html" class="btn btn-light btn-sm me-2"><i class="bi bi-calendar3"></i> Lịch làm việc</a>
            <a href="reports.html" class="btn btn-light btn-sm"><i class="bi bi-file-earmark-text"></i> Báo cáo</a>
        </nav>
    </div>
</header>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0"><i class="bi bi-person-gear"></i> Cập nhật thông tin khách hàng</h3>
                        <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= $tour_id ?>&guide_id=<?= $guide_id ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>?action=hvd/customer/update">
                        <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                        <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
                        <input type="hidden" name="guide_id" value="<?= $guide_id ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" 
                                           value="<?= htmlspecialchars($customer['fullname'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Giới tính</label>
<select class="form-select" id="gender" name="gender">
    <option value="">Chọn giới tính</option>
    <option value="male" <?= ($customer['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam</option>
    <option value="female" <?= ($customer['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ</option>
    <option value="other" <?= ($customer['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
</select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="birthdate" class="form-label">Ngày sinh</label>
                                    <input type="date" class="form-control" id="birthdate" name="birthdate" 
                                           value="<?= htmlspecialchars($customer['birthdate'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_card" class="form-label">CMND/CCCD</label>
                                    <input type="text" class="form-control" id="id_card" name="id_card" 
                                           value="<?= htmlspecialchars($customer['id_card'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="passport" class="form-label">Passport</label>
                                    <input type="text" class="form-control" id="passport" name="passport" 
                                           value="<?= htmlspecialchars($customer['passport'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="hobby" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="hobby" name="hobby" rows="3" 
                                      placeholder="Nhập sở thích của khách hàng..."><?= htmlspecialchars($customer['hobby'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="special_requirements" class="form-label">Yêu cầu đặc biệt</label>
                            <textarea class="form-control" id="special_requirements" name="special_requirements" rows="3" 
                                      placeholder="Nhập yêu cầu đặc biệt của khách hàng..."><?= htmlspecialchars($customer['special_requirements'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="dietary_restrictions" class="form-label">Hạn chế ăn uống</label>
                            <textarea class="form-control" id="dietary_restrictions" name="dietary_restrictions" rows="3" 
                                      placeholder="Nhập hạn chế ăn uống của khách hàng..."><?= htmlspecialchars($customer['dietary_restrictions'] ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= BASE_URL ?>?action=hvd/tours/show&id=<?= $tour_id ?>&guide_id=<?= $guide_id ?>" class="btn btn-secondary me-md-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>