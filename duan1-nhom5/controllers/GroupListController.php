<?php

class GroupListController
{
    private $departureScheduleModel;
    private $tourCustomerModel;
    private $tourModel;
    private $checkinModel;

    public function __construct()
    {
        $this->departureScheduleModel = new DepartureScheduleModel();
        $this->tourCustomerModel = new TourCustomerModel();
        $this->tourModel = new TourModel();
        $this->checkinModel = new CheckinModel();
    }

    /**
     * Danh sách các đoàn (lịch khởi hành)
     */
    public function index()
    {
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'departure_date_from' => $_GET['departure_date_from'] ?? '',
            'departure_date_to' => $_GET['departure_date_to'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        $schedules = $this->departureScheduleModel->getAll($filters);
        
        // Thêm thông tin số lượng khách cho mỗi đoàn
        foreach ($schedules as &$schedule) {
            $schedule['customer_count'] = $this->tourCustomerModel->countByDepartureScheduleId($schedule['id']);
            $schedule['booking_count'] = $this->departureScheduleModel->getBookingCount($schedule['id']);
        }

        $tours = $this->tourModel->getAll(['status' => 1]);

        $title = 'Danh sách đoàn';
        $view = 'group-list/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Chi tiết một đoàn
     */
    public function show()
    {
        $scheduleId = $_GET['id'] ?? 0;
        $schedule = $this->departureScheduleModel->findById($scheduleId);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=group-lists');
            exit;
        }

        // Lấy danh sách khách trong đoàn
        $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId);
        
        // Lấy thông tin check-in
        $checkins = $this->checkinModel->getByDepartureScheduleId($scheduleId);
        $checkinMap = [];
        foreach ($checkins as $checkin) {
            $checkinMap[$checkin['booking_detail_id']] = $checkin;
        }

        // Thêm thông tin check-in vào danh sách khách
        foreach ($customers as &$customer) {
            $customer['checkin'] = $checkinMap[$customer['id']] ?? null;
        }

        // Tính toán thống kê
        $stats = [
            'total' => count($customers),
            'checked_in' => 0,
            'pending' => 0,
            'absent' => 0,
            'male' => 0,
            'female' => 0
        ];

        foreach ($customers as $customer) {
            if ($customer['checkin']) {
                if ($customer['checkin']['status'] === 'checked_in' || $customer['checkin']['status'] === 'late') {
                    $stats['checked_in']++;
                } elseif ($customer['checkin']['status'] === 'absent') {
                    $stats['absent']++;
                }
            } else {
                $stats['pending']++;
            }
            if ($customer['gender'] === 'male') $stats['male']++;
            if ($customer['gender'] === 'female') $stats['female']++;
        }

        $title = 'Chi tiết đoàn - ' . $schedule['tour_name'];
        $view = 'group-list/show';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * In danh sách đoàn
     */
    public function print()
    {
        $scheduleId = $_GET['id'] ?? 0;
        $schedule = $this->departureScheduleModel->findById($scheduleId);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=group-lists');
            exit;
        }

        // Lấy danh sách khách trong đoàn
        $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId);
        
        // Lấy thông tin check-in
        $checkins = $this->checkinModel->getByDepartureScheduleId($scheduleId);
        $checkinMap = [];
        foreach ($checkins as $checkin) {
            $checkinMap[$checkin['booking_detail_id']] = $checkin;
        }

        // Thêm thông tin check-in vào danh sách khách
        foreach ($customers as &$customer) {
            $customer['checkin'] = $checkinMap[$customer['id']] ?? null;
        }

        // Tính toán thống kê
        $stats = [
            'total' => count($customers),
            'checked_in' => 0,
            'pending' => 0,
            'absent' => 0
        ];

        foreach ($customers as $customer) {
            if ($customer['checkin']) {
                if ($customer['checkin']['status'] === 'checked_in' || $customer['checkin']['status'] === 'late') {
                    $stats['checked_in']++;
                } elseif ($customer['checkin']['status'] === 'absent') {
                    $stats['absent']++;
                }
            } else {
                $stats['pending']++;
            }
        }

        // Hiển thị trang in (không có header/footer admin)
        $title = 'In danh sách đoàn - ' . $schedule['tour_name'];
        $view = 'group-list/print';
        require_once PATH_VIEW . 'group-list/print.php';
        exit;
    }
}

