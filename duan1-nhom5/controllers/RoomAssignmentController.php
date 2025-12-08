<?php

class RoomAssignmentController
{
    private $roomAssignmentModel;
    private $tourCustomerModel;
    private $departureScheduleModel;
    private $tourModel;
    private $bookingModel;

    public function __construct()
    {
        $this->roomAssignmentModel = new RoomAssignmentModel();
        $this->tourCustomerModel = new TourCustomerModel();
        $this->departureScheduleModel = new DepartureScheduleModel();
        $this->tourModel = new TourModel();
        $this->bookingModel = new BookingModel();
    }

    /**
     * Danh sách phân phòng
     */
    public function index()
    {
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'departure_schedule_id' => $_GET['departure_schedule_id'] ?? '',
            'hotel_name' => $_GET['hotel_name'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function ($value) {
            return $value !== '' && $value !== null;
        });

        $assignments = [];
        $tour = null;
        $schedule = null;

        if (!empty($filters['departure_schedule_id'])) {
            $scheduleId = $filters['departure_schedule_id'];
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $tour = $this->tourModel->findById($schedule['tour_id']);
                $assignments = $this->roomAssignmentModel->getByDepartureScheduleId($scheduleId, $filters);
            }
        } elseif (!empty($filters['tour_id'])) {
            $tourId = $filters['tour_id'];
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $assignments = $this->roomAssignmentModel->getByTourId($tourId, $filters);
            }
        }

        // Lấy danh sách tour và lịch khởi hành cho filter
        $tours = $this->tourModel->getAll(['status' => 1]);
        $schedules = [];
        if ($tour) {
            $schedules = $this->departureScheduleModel->getAll(['tour_id' => $tour['id']]);
        }

        $title = 'Quản lý Phân phòng Khách sạn';
        $view = 'room-assignment/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Chi tiết phân phòng của một tour/đoàn
     */
    public function show()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        $scheduleId = $_GET['departure_schedule_id'] ?? 0;

        $tour = null;
        $schedule = null;
        $customers = [];
        $assignments = [];

        if ($scheduleId) {
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $tour = $this->tourModel->findById($schedule['tour_id']);
                $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId);
                $assignments = $this->roomAssignmentModel->getByDepartureScheduleId($scheduleId);
            }
        } elseif ($tourId) {
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $customers = $this->tourCustomerModel->getByTourId($tourId);
                $assignments = $this->roomAssignmentModel->getByTourId($tourId);
            }
        }

        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        // Tạo map phân phòng theo booking_detail_id
        $assignmentMap = [];
        foreach ($assignments as $assignment) {
            if ($assignment['booking_detail_id']) {
                $assignmentMap[$assignment['booking_detail_id']] = $assignment;
            }
        }

        // Thêm thông tin phân phòng vào danh sách khách
        foreach ($customers as &$customer) {
            $customer['room_assignment'] = $assignmentMap[$customer['id']] ?? null;
        }

        // Tính toán thống kê
        $stats = [
            'total' => count($customers),
            'assigned' => count($assignments),
            'unassigned' => count($customers) - count($assignments)
        ];

        $title = 'Chi tiết Phân phòng - ' . $tour['name'];
        $view = 'room-assignment/show';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Tạo phân phòng mới
     */
    public function create()
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
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        // Lấy danh sách phân phòng hiện có
        $existingAssignments = [];
        if ($scheduleId) {
            $existingAssignments = $this->roomAssignmentModel->getByDepartureScheduleId($scheduleId);
        } elseif ($tourId) {
            $existingAssignments = $this->roomAssignmentModel->getByTourId($tourId);
        }

        $assignmentMap = [];
        foreach ($existingAssignments as $assignment) {
            if ($assignment['booking_detail_id']) {
                $assignmentMap[$assignment['booking_detail_id']] = $assignment;
            }
        }

        $title = 'Tạo Phân phòng Mới';
        $view = 'room-assignment/create';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Lưu phân phòng
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        $assignments = $_POST['assignments'] ?? [];
        $departureScheduleId = $_POST['departure_schedule_id'] ?? null;
        $tourId = $_POST['tour_id'] ?? null;

        if (empty($assignments)) {
            $_SESSION['error'] = 'Vui lòng chọn ít nhất một khách hàng để phân phòng!';
            header('Location: ' . BASE_URL . '?action=room-assignments/create' .
                ($departureScheduleId ? '&departure_schedule_id=' . $departureScheduleId : '&tour_id=' . $tourId));
            exit;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($assignments as $assignment) {
            if (empty($assignment['booking_detail_id']) || empty($assignment['hotel_name']) || empty($assignment['room_number'])) {
                $errorCount++;
                continue;
            }

            // Lấy booking_id từ booking_detail_id
            $bookingDetail = (new BookingDetailModel())->findById($assignment['booking_detail_id']);
            if (!$bookingDetail) {
                $errorCount++;
                continue;
            }

            // Kiểm tra trùng phòng
            $conflicts = $this->roomAssignmentModel->getAssignedRooms(
                $assignment['hotel_name'],
                $assignment['checkin_date'],
                $assignment['checkout_date'] ?? $assignment['checkin_date'],
                null
            );

            $roomExists = false;
            foreach ($conflicts as $conflict) {
                if ($conflict['room_number'] === $assignment['room_number']) {
                    $roomExists = true;
                    break;
                }
            }

            if ($roomExists && empty($assignment['allow_duplicate'])) {
                $errorCount++;
                continue;
            }

            $result = $this->roomAssignmentModel->create([
                'booking_detail_id' => $assignment['booking_detail_id'],
                'booking_id' => $bookingDetail['booking_id'],
                'departure_schedule_id' => $departureScheduleId,
                'hotel_name' => $assignment['hotel_name'],
                'room_number' => $assignment['room_number'],
                'room_type' => $assignment['room_type'] ?? 'standard',
                'bed_type' => $assignment['bed_type'] ?? 'double',
                'checkin_date' => $assignment['checkin_date'],
                'checkout_date' => $assignment['checkout_date'] ?? null,
                'notes' => $assignment['notes'] ?? null
            ]);

            if ($result) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        if ($successCount > 0) {
            $_SESSION['success'] = "Phân phòng thành công {$successCount} khách hàng!" .
                ($errorCount > 0 ? " ({$errorCount} lỗi)" : '');
        } else {
            $_SESSION['error'] = 'Không thể phân phòng! Vui lòng kiểm tra lại thông tin.';
        }

        $redirectUrl = BASE_URL . '?action=room-assignments/show';
        if ($departureScheduleId) {
            $redirectUrl .= '&departure_schedule_id=' . $departureScheduleId;
        } elseif ($tourId) {
            $redirectUrl .= '&tour_id=' . $tourId;
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Chỉnh sửa phân phòng
     */
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $assignment = $this->roomAssignmentModel->findById($id);

        if (!$assignment) {
            $_SESSION['error'] = 'Không tìm thấy phân phòng!';
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        $tour = $this->tourModel->findById($assignment['tour_id']);

        $title = 'Chỉnh sửa Phân phòng';
        $view = 'room-assignment/edit';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Cập nhật phân phòng
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $assignment = $this->roomAssignmentModel->findById($id);

        if (!$assignment) {
            $_SESSION['error'] = 'Không tìm thấy phân phòng!';
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        $data = [
            'booking_detail_id' => $_POST['booking_detail_id'] ?? null,
            'hotel_name' => $_POST['hotel_name'] ?? '',
            'room_number' => $_POST['room_number'] ?? '',
            'room_type' => $_POST['room_type'] ?? 'standard',
            'bed_type' => $_POST['bed_type'] ?? 'double',
            'checkin_date' => $_POST['checkin_date'] ?? '',
            'checkout_date' => $_POST['checkout_date'] ?? null,
            'notes' => $_POST['notes'] ?? null
        ];

        // Validate
        if (empty($data['hotel_name']) || empty($data['room_number']) || empty($data['checkin_date'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            header('Location: ' . BASE_URL . '?action=room-assignments/edit&id=' . $id);
            exit;
        }

        // Kiểm tra trùng phòng
        $conflicts = $this->roomAssignmentModel->getAssignedRooms(
            $data['hotel_name'],
            $data['checkin_date'],
            $data['checkout_date'] ?? $data['checkin_date'],
            $id
        );

        foreach ($conflicts as $conflict) {
            if ($conflict['room_number'] === $data['room_number']) {
                $_SESSION['error'] = 'Phòng này đã được phân phối cho khách hàng khác trong khoảng thời gian này!';
                header('Location: ' . BASE_URL . '?action=room-assignments/edit&id=' . $id);
                exit;
            }
        }

        $result = $this->roomAssignmentModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật phân phòng thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật!';
        }

        $redirectUrl = BASE_URL . '?action=room-assignments/show';
        if ($assignment['departure_schedule_id']) {
            $redirectUrl .= '&departure_schedule_id=' . $assignment['departure_schedule_id'];
        } elseif ($assignment['tour_id']) {
            $redirectUrl .= '&tour_id=' . $assignment['tour_id'];
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Xóa phân phòng
     */
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $assignment = $this->roomAssignmentModel->findById($id);

        if (!$assignment) {
            $_SESSION['error'] = 'Không tìm thấy phân phòng!';
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        $result = $this->roomAssignmentModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa phân phòng thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa!';
        }

        $redirectUrl = BASE_URL . '?action=room-assignments/show';
        if ($assignment['departure_schedule_id']) {
            $redirectUrl .= '&departure_schedule_id=' . $assignment['departure_schedule_id'];
        } elseif ($assignment['tour_id']) {
            $redirectUrl .= '&tour_id=' . $assignment['tour_id'];
        }

        header('Location: ' . $redirectUrl);
        exit;
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
     * Xuất danh sách phân phòng (Excel/CSV)
     */
    public function export()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        $scheduleId = $_GET['departure_schedule_id'] ?? 0;

        $tour = null;
        $schedule = null;
        $assignments = [];
        $customers = [];

        if ($scheduleId) {
            $schedule = $this->departureScheduleModel->findById($scheduleId);
            if ($schedule) {
                $tour = $this->tourModel->findById($schedule['tour_id']);
                $assignments = $this->roomAssignmentModel->getByDepartureScheduleId($scheduleId);
                $customers = $this->tourCustomerModel->getByDepartureScheduleId($scheduleId);
            }
        } elseif ($tourId) {
            $tour = $this->tourModel->findById($tourId);
            if ($tour) {
                $assignments = $this->roomAssignmentModel->getByTourId($tourId);
                $customers = $this->tourCustomerModel->getByTourId($tourId);
            }
        }

        if (!$tour || empty($assignments)) {
            $_SESSION['error'] = 'Không có dữ liệu để xuất!';
            header('Location: ' . BASE_URL . '?action=room-assignments');
            exit;
        }

        // Tạo map phân phòng
        $assignmentMap = [];
        foreach ($assignments as $assignment) {
            if ($assignment['booking_detail_id']) {
                $assignmentMap[$assignment['booking_detail_id']] = $assignment;
            }
        }

        // Xuất CSV
        $filename = 'danh-sach-phong-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8 Excel support
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, ['STT', 'Họ và tên', 'Giới tính', 'Ngày sinh', 'Khách sạn', 'Số phòng', 'Loại phòng', 'Loại giường', 'Check-in', 'Check-out', 'Ghi chú']);

        $i = 1;
        foreach ($customers as $customer) {
            $assignment = $assignmentMap[$customer['id']] ?? null;
            if (!$assignment)
                continue; // Chỉ xuất những người đã có phòng

            $gender = $customer['gender'] === 'male' ? 'Nam' : ($customer['gender'] === 'female' ? 'Nữ' : 'Khác');

            fputcsv($output, [
                $i++,
                $customer['fullname'],
                $gender,
                $customer['birthdate'],
                $assignment['hotel_name'],
                $assignment['room_number'],
                $assignment['room_type'],
                $assignment['bed_type'],
                $assignment['checkin_date'],
                $assignment['checkout_date'],
                $assignment['notes']
            ]);
        }

        fclose($output);
        exit;
    }
}

