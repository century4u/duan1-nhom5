<?php

class TourReportController
{
    private $reportModel;
    private $scheduleModel;
    private $guideModel;

    public function __construct()
    {
        $this->reportModel = new TourReportModel();
        $this->scheduleModel = new DepartureScheduleModel();
        $this->guideModel = new GuideModel();
    }

    /**
     * HDV xem danh sách báo cáo của mình
     */
    public function myReports()
    {
        requireHvd();

        $guideId = $_SESSION['user_id'] ?? 0;
        $reports = $this->reportModel->getByGuide($guideId);

        $title = 'Báo cáo Vận hành Tour của tôi';
        $view = 'tour-report/my-reports';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * HDV tạo báo cáo mới
     */
    public function create()
    {
        requireHvd();

        $scheduleId = $_GET['schedule_id'] ?? 0;
        $guideId = $_SESSION['user_id'] ?? 0;

        // Kiểm tra schedule
        $schedule = $this->scheduleModel->findById($scheduleId);
        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=guides/my-schedules');
            exit;
        }

        // Kiểm tra đã báo cáo chưa
        if ($this->reportModel->hasReported($scheduleId, $guideId)) {
            $_SESSION['error'] = 'Bạn đã báo cáo cho lịch này rồi!';
            header('Location: ' . BASE_URL . '?action=tour-reports/my-reports');
            exit;
        }

        // Đếm số booking của HDV này
        $bookingCount = $this->scheduleModel->countBookingsByGuide($scheduleId, $guideId);

        $title = 'Tạo Báo cáo Vận hành';
        $view = 'tour-report/create';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Lưu báo cáo mới
     */
    public function store()
    {
        requireHvd();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=guides/my-schedules');
            exit;
        }

        $guideId = $_SESSION['user_id'] ?? 0;

        $data = [
            'departure_schedule_id' => $_POST['departure_schedule_id'] ?? 0,
            'guide_id' => $guideId,
            'actual_start_date' => $_POST['actual_start_date'] ?? null,
            'actual_end_date' => $_POST['actual_end_date'] ?? null,
            'total_participants' => (int) ($_POST['total_participants'] ?? 0),
            'participants_attended' => (int) ($_POST['participants_attended'] ?? 0),
            'participants_absent' => (int) ($_POST['participants_absent'] ?? 0),
            'issues_encountered' => $_POST['issues_encountered'] ?? null,
            'customer_feedback' => $_POST['customer_feedback'] ?? null,
            'expenses_incurred' => (float) ($_POST['expenses_incurred'] ?? 0),
            'revenue_collected' => (float) ($_POST['revenue_collected'] ?? 0),
            'overall_rating' => !empty($_POST['overall_rating']) ? (float) $_POST['overall_rating'] : null,
            'guide_notes' => $_POST['guide_notes'] ?? null,
            'status' => 'pending'
        ];

        // Validate
        $errors = [];
        if (empty($data['departure_schedule_id'])) {
            $errors[] = 'Thiếu thông tin lịch khởi hành!';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=tour-reports/create&schedule_id=' . $data['departure_schedule_id']);
            exit;
        }

        $reportId = $this->reportModel->create($data);

        if ($reportId) {
            $_SESSION['success'] = 'Tạo báo cáo thành công! Chờ admin duyệt.';
            header('Location: ' . BASE_URL . '?action=tour-reports/my-reports');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo báo cáo!';
            header('Location: ' . BASE_URL . '?action=tour-reports/create&schedule_id=' . $data['departure_schedule_id']);
        }
        exit;
    }

    /**
     * Admin xem tất cả báo cáo
     */
    public function index()
    {
        requireAdmin();

        $filters = [
            'status' => $_GET['status'] ?? '',
            'guide_id' => $_GET['guide_id'] ?? ''
        ];

        $filters = array_filter($filters);

        $reports = $this->reportModel->getAll($filters);
        $guides = $this->guideModel->getAll();

        $title = 'Quản lý Báo cáo Vận hành';
        $view = 'tour-report/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Xem chi tiết báo cáo
     */
    public function show()
    {
        requireAdmin();

        $id = $_GET['id'] ?? 0;
        $report = $this->reportModel->findById($id);

        if (!$report) {
            $_SESSION['error'] = 'Không tìm thấy báo cáo!';
            header('Location: ' . BASE_URL . '?action=tour-reports');
            exit;
        }

        // Lấy danh sách booking của HDV này trong lịch này
        $bookingModel = new BookingModel();
        $bookings = $bookingModel->getAll([
            'departure_schedule_id' => $report['departure_schedule_id'],
            'guide_id' => $report['guide_id']
        ]);

        $title = 'Chi tiết Báo cáo - ' . $report['tour_name'];
        $view = 'tour-report/show';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Admin duyệt báo cáo
     */
    public function review()
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=tour-reports');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 'reviewed';
        $adminReview = $_POST['admin_review'] ?? null;
        $reviewedBy = $_SESSION['user_id'] ?? null;

        $result = $this->reportModel->updateStatus($id, $status, $adminReview, $reviewedBy);

        if ($result) {
            $_SESSION['success'] = 'Đã duyệt báo cáo thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi duyệt báo cáo!';
        }

        header('Location: ' . BASE_URL . '?action=tour-reports/show&id=' . $id);
        exit;
    }

    /**
     * HDV xóa báo cáo (chỉ khi pending)
     */
    public function delete()
    {
        requireHvd();

        $id = $_GET['id'] ?? 0;
        $result = $this->reportModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'Đã xóa báo cáo!';
        } else {
            $_SESSION['error'] = 'Không thể xóa báo cáo (chỉ xóa được báo cáo đang chờ duyệt)!';
        }

        header('Location: ' . BASE_URL . '?action=tour-reports/my-reports');
        exit;
    }
}
