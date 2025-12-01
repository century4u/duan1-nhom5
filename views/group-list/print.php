<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In danh sách đoàn - <?= htmlspecialchars($schedule['tour_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            .page-break { page-break-after: always; }
        }
        body { font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { border: 1px solid #000; padding: 5px; }
        table th { background-color: #f0f0f0; text-align: center; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">In</button>
            <button onclick="window.close()" class="btn btn-secondary">Đóng</button>
        </div>

        <div class="header">
            <h3>DANH SÁCH ĐOÀN</h3>
            <p><strong>Tour:</strong> <?= htmlspecialchars($schedule['tour_name']) ?> (<?= htmlspecialchars($schedule['tour_code']) ?>)</p>
            <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y H:i', strtotime($schedule['departure_date'] . ' ' . $schedule['departure_time'])) ?></p>
            <p><strong>Điểm tập trung:</strong> <?= htmlspecialchars($schedule['meeting_point']) ?></p>
            <p><strong>Ngày kết thúc:</strong> <?= date('d/m/Y H:i', strtotime($schedule['end_date'] . ' ' . $schedule['end_time'])) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">STT</th>
                    <th style="width: 25%;">Họ và tên</th>
                    <th style="width: 10%;">Giới tính</th>
                    <th style="width: 15%;">Ngày sinh</th>
                    <th style="width: 10%;">Tuổi</th>
                    <th style="width: 15%;">Số điện thoại</th>
                    <th style="width: 20%;">Trạng thái Check-in</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $statusLabels = CheckinModel::getStatuses();
                foreach ($customers as $index => $customer): 
                ?>
                    <tr>
                        <td style="text-align: center;"><?= $index + 1 ?></td>
                        <td><strong><?= htmlspecialchars($customer['fullname']) ?></strong></td>
                        <td style="text-align: center;">
                            <?php
                            $genderLabels = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                            echo $genderLabels[$customer['gender']] ?? 'N/A';
                            ?>
                        </td>
                        <td style="text-align: center;"><?= $customer['birthdate'] ? date('d/m/Y', strtotime($customer['birthdate'])) : 'N/A' ?></td>
                        <td style="text-align: center;">
                            <?php
                            if ($customer['birthdate']) {
                                $birth = new DateTime($customer['birthdate']);
                                $today = new DateTime();
                                $age = $today->diff($birth)->y;
                                echo $age;
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($customer['customer_phone'] ?? '') ?></td>
                        <td>
                            <?php if ($customer['checkin']): ?>
                                <?= $statusLabels[$customer['checkin']['status']] ?? $customer['checkin']['status'] ?>
                                <?php if ($customer['checkin']['checkin_time']): ?>
                                    <br><small>(<?= date('H:i', strtotime($customer['checkin']['checkin_time'])) ?>)</small>
                                <?php endif; ?>
                            <?php else: ?>
                                Chưa check-in
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-4">
            <p><strong>Tổng số khách:</strong> <?= $stats['total'] ?> người</p>
            <p><strong>Đã check-in:</strong> <?= $stats['checked_in'] ?> người</p>
            <p><strong>Chưa check-in:</strong> <?= $stats['pending'] ?> người</p>
            <?php if ($stats['absent'] > 0): ?>
                <p><strong>Vắng mặt:</strong> <?= $stats['absent'] ?> người</p>
            <?php endif; ?>
        </div>

        <div class="mt-4 text-end">
            <p>Ngày in: <?= date('d/m/Y H:i') ?></p>
        </div>
    </div>
</body>
</html>
