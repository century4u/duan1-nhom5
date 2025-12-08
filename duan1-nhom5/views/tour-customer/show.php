<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Danh sách khách hàng' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            font-family: 'Times New Roman', Times, serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .tour-info {
            margin-bottom: 20px;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            .table {
                font-size: 12pt;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> In danh sách
            </button>
            <a href="<?= BASE_URL ?>?action=tour-customers" class="btn btn-secondary">Quay lại</a>
        </div>

        <div class="header">
            <h2>DANH SÁCH KHÁCH HÀNG</h2>
            <h4><?= htmlspecialchars($tour['name']) ?></h4>
        </div>

        <div class="tour-info">
            <div class="row">
                <div class="col-6">
                    <p><strong>Mã tour:</strong> <?= htmlspecialchars($tour['code']) ?></p>
                    <p><strong>Điểm khởi hành:</strong> <?= htmlspecialchars($tour['departure_location']) ?></p>
                </div>
                <div class="col-6 text-end">
                    <?php if ($schedule): ?>
                        <p><strong>Ngày khởi hành:</strong>
                            <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?>
                        </p>
                        <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%" class="text-center">STT</th>
                    <th>Họ và tên</th>
                    <th width="10%">Giới tính</th>
                    <th width="10%">Ngày sinh</th>
                    <th>Điện thoại</th>
                    <th>Email</th>
                    <th>Địa chỉ</th>
                    <th width="15%">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Không có khách hàng nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $index => $customer): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($customer['fullname']) ?></td>
                            <td>
                                <?php
                                $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                echo $genderLabels[$customer['gender']] ?? 'N/A';
                                ?>
                            </td>
                            <td><?= $customer['birthdate'] ? date('d/m/Y', strtotime($customer['birthdate'])) : '' ?></td>
                            <td><?= htmlspecialchars($customer['customer_phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($customer['customer_email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($customer['address'] ?? '') ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-6">
                <!-- Chữ ký bên trái nếu cần -->
            </div>
            <div class="col-6 text-center">
                <p><em>Ngày ..... tháng ..... năm 20.....</em></p>
                <p><strong>Người lập biểu</strong></p>
                <br><br><br>
            </div>
        </div>
    </div>
</body>

</html>