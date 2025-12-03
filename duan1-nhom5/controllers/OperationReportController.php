<?php

class OperationReportController
{
    private $operationReportModel;
    private $tourModel;
    private $departureScheduleModel;
    private $bookingModel;

    public function __construct()
    {
        $this->operationReportModel = new OperationReportModel();
        $this->tourModel = new TourModel();
        $this->departureScheduleModel = new DepartureScheduleModel();
        $this->bookingModel = new BookingModel();
    }

    /**
     * Danh sách báo cáo vận hành tour
     */
    public function index()
    {
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'report_type' => $_GET['report_type'] ?? 'by_tour', // by_tour, by_period
            'period_type' => $_GET['period_type'] ?? 'month' // month, quarter, year
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        // Lấy báo cáo tổng hợp
        $summary = $this->operationReportModel->getSummaryReport($filters);

        // Lấy danh sách báo cáo
        $reports = [];
        if ($filters['report_type'] === 'by_period') {
            $reports = $this->operationReportModel->getReportByPeriod(
                $filters['period_type'] ?? 'month',
                $filters['start_date'] ?? null,
                $filters['end_date'] ?? null
            );
        } else {
            $reports = $this->operationReportModel->getReportByTour($filters);
        }

        // Lấy danh sách tour cho filter
        $tours = $this->tourModel->getAll(['status' => 1]);

        $title = 'Báo cáo Vận hành Tour';
        $view = 'operation-report/index';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xem chi tiết báo cáo theo tour
     */
    public function show()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        if (!$tourId) {
            $_SESSION['error'] = 'Vui lòng chọn tour!';
            header('Location: ' . BASE_URL . '?action=operation-reports');
            exit;
        }

        $tour = $this->tourModel->findById($tourId);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=operation-reports');
            exit;
        }

        // Lấy báo cáo chi tiết
        $filters = [
            'tour_id' => $tourId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $summary = $this->operationReportModel->getSummaryReport($filters);
        $tourReport = $this->operationReportModel->getReportByTour($filters);
        $tourData = !empty($tourReport) ? $tourReport[0] : null;

        // Lấy báo cáo theo kỳ cho tour này
        $periodReports = $this->operationReportModel->getReportByPeriod('month', $startDate, $endDate);
        // Filter chỉ lấy data của tour này (cần join với bookings)
        $tourPeriodReports = [];
        foreach ($periodReports as $period) {
            // Tính lại cho tour cụ thể
            $periodDates = $this->parsePeriodDates($period['period'], 'month');
            $periodRevenue = $this->operationReportModel->calculateRevenue($tourId, $periodDates['start'], $periodDates['end']);
            $periodCost = $this->operationReportModel->calculateCost($tourId, $periodDates['start'], $periodDates['end']);
            
            if ($periodRevenue > 0 || $periodCost > 0) {
                $tourPeriodReports[] = [
                    'period' => $period['period'],
                    'revenue' => $periodRevenue,
                    'cost' => $periodCost,
                    'profit' => $periodRevenue - $periodCost,
                    'profit_margin' => $periodRevenue > 0 ? round((($periodRevenue - $periodCost) / $periodRevenue) * 100, 2) : 0
                ];
            }
        }

        $title = 'Chi tiết Báo cáo - ' . $tour['name'];
        $view = 'operation-report/show';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Parse period string để lấy start và end date
     */
    private function parsePeriodDates($period, $periodType)
    {
        $start = null;
        $end = null;

        switch ($periodType) {
            case 'year':
                $start = $period . '-01-01';
                $end = $period . '-12-31';
                break;
            case 'quarter':
                [$year, $quarter] = explode('-Q', $period);
                $month = (($quarter - 1) * 3) + 1;
                $start = sprintf('%s-%02d-01', $year, $month);
                $endMonth = $month + 2;
                $end = date('Y-m-t', strtotime(sprintf('%s-%02d-01', $year, $endMonth)));
                break;
            case 'month':
                $start = $period . '-01';
                $end = date('Y-m-t', strtotime($period . '-01'));
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * So sánh hiệu quả các tour
     */
    public function compare()
    {
        $filters = [
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? ''
        ];

        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        // Lấy báo cáo tất cả các tour
        $reports = $this->operationReportModel->getReportByTour($filters);
        
        // Sắp xếp theo lợi nhuận
        usort($reports, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        $title = 'So sánh Hiệu quả Tour';
        $view = 'operation-report/compare';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Hiển thị form tạo báo cáo mới
     */
    public function create()
    {
        $tours = $this->tourModel->getAll(['status' => 1]);

        $title = 'Tạo Báo cáo Vận hành Tour';
        $view = 'operation-report/create';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xử lý tạo báo cáo mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=operation-reports/create');
            exit;
        }

        $data = [
            'tour_id' => !empty($_POST['tour_id']) ? (int)$_POST['tour_id'] : null,
            'report_date' => $_POST['report_date'] ?? date('Y-m-d'),
            'revenue' => !empty($_POST['revenue']) ? (float)$_POST['revenue'] : 0,
            'cost' => !empty($_POST['cost']) ? (float)$_POST['cost'] : 0,
            'notes' => $_POST['notes'] ?? ''
        ];

        $result = $this->operationReportModel->create($data);

        if ($result) {
            $_SESSION['success'] = 'Tạo báo cáo thành công!';
            header('Location: ' . BASE_URL . '?action=operation-reports');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo báo cáo!';
            header('Location: ' . BASE_URL . '?action=operation-reports/create');
        }
        exit;
    }

    /**
     * Hiển thị form chỉnh sửa báo cáo
     */
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $report = $this->operationReportModel->findById($id);

        if (!$report) {
            $_SESSION['error'] = 'Không tìm thấy báo cáo!';
            header('Location: ' . BASE_URL . '?action=operation-reports');
            exit;
        }

        $tours = $this->tourModel->getAll(['status' => 1]);

        $title = 'Chỉnh sửa Báo cáo Vận hành Tour';
        $view = 'operation-report/edit';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xử lý cập nhật báo cáo
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=operation-reports');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $report = $this->operationReportModel->findById($id);

        if (!$report) {
            $_SESSION['error'] = 'Không tìm thấy báo cáo!';
            header('Location: ' . BASE_URL . '?action=operation-reports');
            exit;
        }

        $data = [
            'tour_id' => !empty($_POST['tour_id']) ? (int)$_POST['tour_id'] : null,
            'report_date' => $_POST['report_date'] ?? date('Y-m-d'),
            'revenue' => !empty($_POST['revenue']) ? (float)$_POST['revenue'] : 0,
            'cost' => !empty($_POST['cost']) ? (float)$_POST['cost'] : 0,
            'notes' => $_POST['notes'] ?? ''
        ];

        $result = $this->operationReportModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật báo cáo thành công!';
            header('Location: ' . BASE_URL . '?action=operation-reports/show&tour_id=' . $data['tour_id']);
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật báo cáo!';
            header('Location: ' . BASE_URL . '?action=operation-reports/edit&id=' . $id);
        }
        exit;
    }

    /**
     * Xóa báo cáo
     */
    public function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=operation-reports');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $result = $this->operationReportModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa báo cáo thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa báo cáo!';
        }

        header('Location: ' . BASE_URL . '?action=operation-reports');
        exit;
    }

    /**
     * Xuất báo cáo (PDF/Excel)
     */
    public function export()
    {
        // TODO: Implement export logic
        $_SESSION['error'] = 'Chức năng xuất báo cáo đang được phát triển!';
        header('Location: ' . BASE_URL . '?action=operation-reports');
        exit;
    }
}
