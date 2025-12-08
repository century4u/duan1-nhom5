<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Danh sách phân phòng' ?></title>
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

        .room-group {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            page-break-inside: avoid;
        }

        .room-header {
            background-color: #f8f9fa;
            padding: 10px;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
        }

        .room-body {
            padding: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
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
            <form action="<?= BASE_URL ?>?action=room-assignments/export" method="GET" class="d-inline">
                <input type="hidden" name="action" value="room-assignments/export">
                <input type="hidden" name="tour_id" value="<?= $tour['id'] ?>">
                <?php if ($schedule): ?>
                    <input type="hidden" name="departure_schedule_id" value="<?= $schedule['id'] ?>">
                <?php endif; ?>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                </button>
            </form>
            <a href="<?= BASE_URL ?>?action=room-assignments" class="btn btn-secondary">Quay lại</a>
        </div>

        <div class="header">
            <h2>DANH SÁCH PHÂN PHÒNG (ROOMING LIST)</h2>
            <h4><?= htmlspecialchars($tour['name']) ?></h4>
        </div>

        <div class="tour-info">
            <div class="row">
                <div class="col-6">
                    <p><strong>Mã tour:</strong> <?= htmlspecialchars($tour['code']) ?></p>
                    <p><strong>Khách sạn:</strong> <?= htmlspecialchars($assignments[0]['hotel_name'] ?? 'N/A') ?></p>
                </div>
                <div class="col-6 text-end">
                    <?php if ($schedule): ?>
                        <p><strong>Ngày khởi hành:</strong> <?= date('d/m/Y', strtotime($schedule['departure_date'])) ?></p>
                    <?php endif; ?>
                    <p><strong>Tổng số khách:</strong> <?= count($customers) ?></p>
                </div>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="10%">Phòng</th>
                    <th width="15%">Loại phòng</th>
                    <th>Họ và tên khách</th>
                    <th width="10%">Giới tính</th>
                    <th width="10%">Năm sinh</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Group by Room Number
                $groupedAssignments = [];
                $unassignedCustomers = [];

                foreach ($customers as $customer) {
                    if (isset($customer['room_assignment'])) {
                        $ra = $customer['room_assignment'];
                        $key = $ra['room_number'] . '-' . $ra['hotel_name'];
                        $groupedAssignments[$key]['info'] = $ra;
                        $groupedAssignments[$key]['customers'][] = $customer;
                    } else {
                        $unassignedCustomers[] = $customer;
                    }
                }

                ksort($groupedAssignments);
                ?>

                <?php foreach ($groupedAssignments as $group): ?>
                    <?php
                    $info = $group['info'];
                    $custs = $group['customers'];
                    $rowSpan = count($custs);
                    ?>
                    <?php foreach ($custs as $index => $cust): ?>
                        <tr>
                            <?php if ($index === 0): ?>
                                <td rowspan="<?= $rowSpan ?>" class="align-middle text-center">
                                    <strong><?= htmlspecialchars($info['room_number']) ?></strong>
                                </td>
                                <td rowspan="<?= $rowSpan ?>" class="align-middle">
                                    <?= htmlspecialchars($info['room_type']) ?><br>
                                    <small><?= htmlspecialchars($info['bed_type']) ?></small>
                                </td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($cust['fullname']) ?></td>
                            <td><?= $cust['gender'] === 'male' ? 'Nam' : 'Nữ' ?></td>
                            <td><?= date('Y', strtotime($cust['birthdate'])) ?></td>
                            <?php if ($index === 0): ?>
                                <td rowspan="<?= $rowSpan ?>" class="align-middle">
                                    <?= htmlspecialchars($info['notes']) ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($unassignedCustomers)): ?>
            <h5 class="mt-4 text-danger">Danh sách chưa xếp phòng</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Họ và tên</th>
                        <th>Giới tính</th>
                        <th>Năm sinh</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unassignedCustomers as $cust): ?>
                        <tr>
                            <td><?= htmlspecialchars($cust['fullname']) ?></td>
                            <td><?= $cust['gender'] === 'male' ? 'Nam' : 'Nữ' ?></td>
                            <td><?= date('Y', strtotime($cust['birthdate'])) ?></td>
                            <td>Chưa xếp</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="row mt-4">
            <div class="col-6">
                <p><strong>Ghi chú chung:</strong></p>
                <ul>
                    <li>Giờ Check-in: 14:00</li>
                    <li>Giờ Check-out: 12:00</li>
                </ul>
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