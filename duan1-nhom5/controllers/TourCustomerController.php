<?php

class TourCustomerController
{
    private $tourCustomerModel;
    private $tourModel;
    private $departureScheduleModel;
    private $bookingModel;

    public function __construct()
    {
        $this->tourCustomerModel = new TourCustomerModel();
        $this->tourModel = new TourModel();
        $this->departureScheduleModel = new DepartureScheduleModel();
        $this->bookingModel = new BookingModel();
    }

    /**
     * Danh sách khách theo tour
     */
    public function index()
    {
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'departure_schedule_id' => $_GET['departure_schedule_id'] ?? '',
            'booking_status' => $_GET['booking_status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        $customers = [];
        $tour = null;
        $schedule = null;

        // Lấy danh sách khách theo tour hoặc lịch khởi hành
        if (!empty($filters['departure_schedule_id'])) {
            $scheduleId = $filters['departure_schedule_id'];
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId, $filters);
                $tour = $this->tourModel->findById($schedule['tour_id']);
            }
        } elseif (!empty($filters['tour_id'])) {
            $tourId = $filters['tour_id'];
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $customers = $this->tourCustomerModel->getByTourId($tourId, $filters);
            }
        }

        // Tính toán thống kê
        $stats = [
            'total' => count($customers),
            'male' => 0,
            'female' => 0,
            'confirmed' => 0,
            'deposit' => 0
        ];

        foreach ($customers as $customer) {
            if ($customer['gender'] === 'male')
                $stats['male']++;
            if ($customer['gender'] === 'female')
                $stats['female']++;
            if ($customer['booking_status'] === 'confirmed')
                $stats['confirmed']++;
            if ($customer['booking_status'] === 'deposit')
                $stats['deposit']++;
        }

        // Lấy danh sách tour và lịch khởi hành cho filter
        $tours = $this->tourModel->getAll(['status' => 1]);
        $schedules = [];
        if ($tour) {
            $schedules = $this->departureScheduleModel->getAll(['tour_id' => $tour['id']]);
        }

        $title = 'Danh sách khách theo tour';
        $view = 'tour-customer/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Chi tiết danh sách khách của một tour/lịch khởi hành
     */
    public function show()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        $scheduleId = $_GET['departure_schedule_id'] ?? 0;

        $tour = null;
        $schedule = null;
        $customers = [];

        if ($scheduleId) {
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $tour = $this->tourModel->findById($schedule['tour_id']);
                $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId);
            }
        } elseif ($tourId) {
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $customers = $this->tourCustomerModel->getByTourId($tourId);
            }
        }

        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=tour-customers');
            exit;
        }

        // Tính toán thống kê
        $stats = [
            'total' => count($customers),
            'male' => 0,
            'female' => 0,
            'confirmed' => 0,
            'deposit' => 0
        ];

        foreach ($customers as $customer) {
            if ($customer['gender'] === 'male')
                $stats['male']++;
            if ($customer['gender'] === 'female')
                $stats['female']++;
            if ($customer['booking_status'] === 'confirmed')
                $stats['confirmed']++;
            if ($customer['booking_status'] === 'deposit')
                $stats['deposit']++;
        }

        $title = 'Chi tiết danh sách khách - ' . $tour['name'];
        $view = 'tour-customer/show';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * API: Lấy danh sách lịch khởi hành theo tour (AJAX)
     */
    public function getSchedules()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        if (!$tourId) {
            echo json_encode([]);
            return;
        }

        $schedules = $this->departureScheduleModel->getAll(['tour_id' => $tourId]);

        // Format lại dữ liệu cần thiết
        $data = array_map(function ($schedule) {
            return [
                'id' => $schedule['id'],
                'departure_date' => $schedule['departure_date'],
                'departure_time' => $schedule['departure_time'],
                'meeting_point' => $schedule['meeting_point']
            ];
        }, $schedules);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Xuất danh sách khách (Excel/CCSV)
     */
    public function export()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        $scheduleId = $_GET['departure_schedule_id'] ?? 0;
        $format = $_GET['format'] ?? 'excel'; // excel (csv) hoặc pdf

        $tour = null;
        $schedule = null;
        $customers = [];

        if ($scheduleId) {
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $tour = $this->tourModel->findById($schedule['tour_id']);
                $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId);
            }
        } elseif ($tourId) {
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $customers = $this->tourCustomerModel->getByTourId($tourId);
            }
        }

        if (!$tour || empty($customers)) {
            $_SESSION['error'] = 'Không có dữ liệu để xuất!';
            header('Location: ' . BASE_URL . '?action=tour-customers');
            exit;
        }

        if ($format === 'pdf') {
            // Chưa hỗ trợ PDF, quay về show
            $_SESSION['error'] = 'Chưa hỗ trợ xuất PDF!';
            header('Location: ' . BASE_URL . '?action=tour-customers/show&tour_id=' . $tourId . ($scheduleId ? '&departure_schedule_id=' . $scheduleId : ''));
            exit;
        }

        // Xuất CSV
        $filename = 'danh-sach-khach-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 Excel support
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, ['STT', 'Họ và tên', 'Giới tính', 'Ngày sinh', 'Điện thoại', 'Email', 'Địa chỉ', 'Trạng thái Assign']);

        $i = 1;
        foreach ($customers as $customer) {
            $gender = $customer['gender'] === 'male' ? 'Nam' : ($customer['gender'] === 'female' ? 'Nữ' : 'Khác');
            $status = $customer['booking_status'];

            fputcsv($output, [
                $i++,
                $customer['fullname'],
                $gender,
                $customer['birthdate'],
                $customer['customer_phone'] ?? '',
                $customer['customer_email'] ?? '',
                $customer['address'] ?? '',
                $status
            ]);
        }

        fclose($output);
        exit;
    }
}

