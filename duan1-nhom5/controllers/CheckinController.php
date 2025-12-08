<?php

class CheckinController
{
    private $checkinModel;
    private $tourCustomerModel;
    private $departureScheduleModel;
    private $tourModel;
    private $bookingDetailModel;

    public function __construct()
    {
        $this->checkinModel = new CheckinModel();
        $this->tourCustomerModel = new TourCustomerModel();
        $this->departureScheduleModel = new DepartureScheduleModel();
        $this->tourModel = new TourModel();
        $this->bookingDetailModel = new BookingDetailModel();
    }

    /**
     * Danh sách check-in
     */
    public function index()
    {
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'departure_schedule_id' => $_GET['departure_schedule_id'] ?? '',
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        $checkins = [];
        $tour = null;
        $schedule = null;

        if (!empty($filters['departure_schedule_id'])) {
            $scheduleId = $filters['departure_schedule_id'];
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $tour = $this->tourModel->findById($schedule['tour_id']);
                $checkins = $this->checkinModel->getByDepartureScheduleId($scheduleId, $filters);
            }
        } elseif (!empty($filters['tour_id'])) {
            $tourId = $filters['tour_id'];
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $checkins = $this->checkinModel->getByTourId($tourId, $filters);
            }
        }

        // Tính toán thống kê
        $stats = [
            'total' => count($checkins),
            'checked_in' => 0,
            'pending' => 0,
            'absent' => 0,
            'late' => 0
        ];

        foreach ($checkins as $checkin) {
            if ($checkin['status'] === 'checked_in')
                $stats['checked_in']++;
            elseif ($checkin['status'] === 'pending')
                $stats['pending']++;
            elseif ($checkin['status'] === 'absent')
                $stats['absent']++;
            elseif ($checkin['status'] === 'late')
                $stats['late']++;
        }

        // Lấy danh sách tour và lịch khởi hành cho filter
        $tours = $this->tourModel->getAll(['status' => 1]);
        $schedules = [];
        if ($tour) {
            $schedules = $this->departureScheduleModel->getAll(['tour_id' => $tour['id']]);
        }

        $title = 'Quản lý Check-in';
        $view = 'checkin/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Chi tiết check-in của một tour/đoàn
     */
    public function show()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        $scheduleId = $_GET['departure_schedule_id'] ?? 0;

        $tour = null;
        $schedule = null;
        $customers = [];
        $checkins = [];

        if ($scheduleId) {
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $tour = $this->tourModel->findById($schedule['tour_id']);
                $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId);
                $checkins = $this->checkinModel->getByDepartureScheduleId($scheduleId);
            }
        } elseif ($tourId) {
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $customers = $this->tourCustomerModel->getByTourId($tourId);
                $checkins = $this->checkinModel->getByTourId($tourId);
            }
        }

        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=checkins');
            exit;
        }

        // Tạo map check-in theo booking_detail_id
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
            'late' => 0
        ];

        foreach ($customers as $customer) {
            if ($customer['checkin']) {
                if ($customer['checkin']['status'] === 'checked_in')
                    $stats['checked_in']++;
                elseif ($customer['checkin']['status'] === 'late')
                    $stats['late']++;
                elseif ($customer['checkin']['status'] === 'absent')
                    $stats['absent']++;
            } else {
                $stats['pending']++;
            }
        }

        $title = 'Chi tiết Check-in - ' . $tour['name'];
        $view = 'checkin/show';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Xử lý check-in khách hàng
     */
    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=checkins');
            exit;
        }

        $bookingDetailId = $_POST['booking_detail_id'] ?? 0;
        $departureScheduleId = $_POST['departure_schedule_id'] ?? null;
        $status = $_POST['status'] ?? 'checked_in';
        $notes = $_POST['notes'] ?? null;

        // Kiểm tra booking detail tồn tại
        $bookingDetail = $this->bookingDetailModel->findById($bookingDetailId);
        if (!$bookingDetail) {
            $_SESSION['error'] = 'Không tìm thấy thông tin khách hàng!';
            header('Location: ' . BASE_URL . '?action=checkins');
            exit;
        }

        // Kiểm tra đã check-in chưa
        $existingCheckin = $this->checkinModel->getByBookingDetailId($bookingDetailId);

        if ($existingCheckin) {
            // Cập nhật check-in hiện có
            $result = $this->checkinModel->updateStatus(
                $existingCheckin['id'],
                $status,
                $notes,
                $_SESSION['user_id'] ?? null
            );
        } else {
            // Tạo check-in mới
            $result = $this->checkinModel->create([
                'booking_detail_id' => $bookingDetailId,
                'departure_schedule_id' => $departureScheduleId,
                'status' => $status,
                'notes' => $notes,
                'checked_by' => $_SESSION['user_id'] ?? null
            ]);
        }

        if ($result) {
            $_SESSION['success'] = 'Check-in thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi check-in!';
        }

        // Return JSON if AJAX request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'Cập nhật thành công!' : 'Có lỗi xảy ra!',
                'data' => [
                    'status' => $status,
                    'checkin_time' => date('H:i d/m/Y'),
                    'checked_by' => $_SESSION['username'] ?? 'Me'
                ]
            ]);
            exit;
        }

        $redirectUrl = BASE_URL . '?action=checkins/show';
        if ($departureScheduleId) {
            $redirectUrl .= '&departure_schedule_id=' . $departureScheduleId;
        } else {
            // Lấy tour_id từ booking
            $bookingModel = new BookingModel();
            $booking = $bookingModel->findById($bookingDetail['booking_id']);
            if ($booking) {
                $redirectUrl .= '&tour_id=' . $booking['tour_id'];
            }
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Cập nhật trạng thái check-in
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=checkins');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $notes = $_POST['notes'] ?? null;

        // Kiểm tra check-in tồn tại
        $checkin = $this->checkinModel->getByBookingDetailId($_POST['booking_detail_id'] ?? 0);
        if (!$checkin) {
            $_SESSION['error'] = 'Không tìm thấy thông tin check-in!';
            header('Location: ' . BASE_URL . '?action=checkins');
            exit;
        }

        // Validate status
        $statuses = CheckinModel::getStatuses();
        if (!isset($statuses[$status])) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ!';
            header('Location: ' . BASE_URL . '?action=checkins/show&departure_schedule_id=' . ($_POST['departure_schedule_id'] ?? ''));
            exit;
        }

        $result = $this->checkinModel->updateStatus(
            $checkin['id'],
            $status,
            $notes,
            $_SESSION['user_id'] ?? null
        );

        if ($result) {
            $_SESSION['success'] = 'Cập nhật trạng thái check-in thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật!';
        }

        $redirectUrl = BASE_URL . '?action=checkins/show';
        if (!empty($_POST['departure_schedule_id'])) {
            $redirectUrl .= '&departure_schedule_id=' . $_POST['departure_schedule_id'];
        } elseif (!empty($_POST['tour_id'])) {
            $redirectUrl .= '&tour_id=' . $_POST['tour_id'];
        }

        header('Location: ' . $redirectUrl);
        exit;
    }
}

