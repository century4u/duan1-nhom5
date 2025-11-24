<?php

class DepartureScheduleController
{
    private $scheduleModel;
    private $assignmentModel;
    private $tourModel;
    private $guideModel;
    private $supplierModel;
    private $notificationModel;

    public function __construct()
    {
        $this->scheduleModel = new DepartureScheduleModel();
        $this->assignmentModel = new ScheduleAssignmentModel();
        $this->tourModel = new TourModel();
        $this->guideModel = new GuideModel();
        $this->supplierModel = new SupplierModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Danh sách lịch khởi hành
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

        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        $schedules = $this->scheduleModel->getAll($filters);
        
        // Thêm thông tin phân bổ và booking count
        foreach ($schedules as &$schedule) {
            $schedule['assignments'] = $this->assignmentModel->getByScheduleId($schedule['id']);
            $schedule['booking_count'] = $this->scheduleModel->getBookingCount($schedule['id']);
        }

        $tours = $this->tourModel->getAll(['status' => 1]);

        $title = 'Quản lý Lịch Khởi Hành';
        $view = 'departure-schedule/index';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Hiển thị form tạo lịch khởi hành mới
     */
    public function create()
    {
        $tours = $this->tourModel->getAll(['status' => 1]);
        
        $title = 'Tạo Lịch Khởi Hành Mới';
        $view = 'departure-schedule/create';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Xử lý tạo lịch khởi hành mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=departure-schedules/create');
            exit;
        }

        $data = [
            'tour_id' => (int)($_POST['tour_id'] ?? 0),
            'departure_date' => $_POST['departure_date'] ?? '',
            'departure_time' => $_POST['departure_time'] ?? '',
            'meeting_point' => trim($_POST['meeting_point'] ?? ''),
            'end_date' => $_POST['end_date'] ?? '',
            'end_time' => $_POST['end_time'] ?? '',
            'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
            'status' => $_POST['status'] ?? 'draft',
            'notes' => trim($_POST['notes'] ?? '')
        ];

        // Validate
        $errors = $this->validateSchedule($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=departure-schedules/create');
            exit;
        }

        $scheduleId = $this->scheduleModel->create($data);

        if ($scheduleId) {
            $_SESSION['success'] = 'Tạo lịch khởi hành thành công!';
            header('Location: ' . BASE_URL . '?action=departure-schedules/show&id=' . $scheduleId);
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo lịch khởi hành!';
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=departure-schedules/create');
            exit;
        }
    }

    /**
     * Xem chi tiết lịch khởi hành
     */
    public function show()
    {
        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        // Lấy phân bổ theo loại
        $assignments = $this->assignmentModel->getByScheduleId($id);
        $assignmentsByType = [];
        foreach ($assignments as $assignment) {
            $assignmentsByType[$assignment['assignment_type']][] = $assignment;
        }

        // Thống kê phân bổ
        $statistics = $this->assignmentModel->getStatisticsByScheduleId($id);

        // Lịch sử thay đổi trạng thái
        $statusHistory = $this->scheduleModel->getStatusHistory($id);

        // Booking count
        $bookingCount = $this->scheduleModel->getBookingCount($id);

        $title = 'Chi tiết Lịch Khởi Hành - ' . $schedule['tour_name'];
        $view = 'departure-schedule/show';
        require_once PATH_VIEW_ADMIN;
    }

    /**
     * Hiển thị form chỉnh sửa lịch khởi hành
     */
    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $tours = $this->tourModel->getAll(['status' => 1]);

        $title = 'Chỉnh sửa Lịch Khởi Hành';
        $view = 'departure-schedule/edit';
        require_once PATH_VIEW_ADMIN;
    }

    /**
     * Xử lý cập nhật lịch khởi hành
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $data = [
            'tour_id' => (int)($_POST['tour_id'] ?? 0),
            'departure_date' => $_POST['departure_date'] ?? '',
            'departure_time' => $_POST['departure_time'] ?? '',
            'meeting_point' => trim($_POST['meeting_point'] ?? ''),
            'end_date' => $_POST['end_date'] ?? '',
            'end_time' => $_POST['end_time'] ?? '',
            'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null,
            'current_participants' => $schedule['current_participants'],
            'status' => $_POST['status'] ?? 'draft',
            'notes' => trim($_POST['notes'] ?? '')
        ];

        // Validate
        $errors = $this->validateSchedule($data, $id);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=departure-schedules/edit&id=' . $id);
            exit;
        }

        // Kiểm tra thay đổi trạng thái
        $oldStatus = $schedule['status'];
        if ($data['status'] !== $oldStatus) {
            $this->scheduleModel->updateStatus($id, $data['status'], $oldStatus, $_SESSION['user_id'] ?? null);
        }

        $result = $this->scheduleModel->update($id, $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật lịch khởi hành thành công!';
            header('Location: ' . BASE_URL . '?action=departure-schedules/show&id=' . $id);
            exit;
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules/edit&id=' . $id);
            exit;
        }
    }

    /**
     * Xóa lịch khởi hành
     */
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        // Kiểm tra có booking không
        $bookingCount = $this->scheduleModel->getBookingCount($id);
        if ($bookingCount > 0) {
            $_SESSION['error'] = 'Không thể xóa lịch khởi hành đã có booking!';
            header('Location: ' . BASE_URL . '?action=departure-schedules/show&id=' . $id);
            exit;
        }

        $result = $this->scheduleModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'Xóa lịch khởi hành thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa lịch khởi hành!';
        }

        header('Location: ' . BASE_URL . '?action=departure-schedules');
        exit;
    }

    /**
     * Phân bổ hướng dẫn viên
     */
    public function assignGuide()
    {
        $scheduleId = $_GET['schedule_id'] ?? 0;
        $schedule = $this->scheduleModel->findById($scheduleId);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        // Lấy danh sách HDV
        $guides = $this->guideModel->getAll(['status' => 1]);

        // Lấy phân bổ HDV hiện tại
        $currentAssignments = $this->assignmentModel->getByScheduleIdAndType($scheduleId, 'guide');

        $title = 'Phân bổ Hướng dẫn viên';
        $view = 'departure-schedule/assign-guide';
        require_once PATH_VIEW_ADMIN;
    }

    /**
     * Xử lý phân bổ hướng dẫn viên
     */
    public function processAssignGuide()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $scheduleId = $_POST['schedule_id'] ?? 0;
        $schedule = $this->scheduleModel->findById($scheduleId);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $guideIds = $_POST['guide_ids'] ?? [];
        $assignments = [];

        foreach ($guideIds as $guideId) {
            $guide = $this->guideModel->findById($guideId);
            if (!$guide) continue;

            // Kiểm tra xung đột lịch
            $conflicts = $this->scheduleModel->checkConflict(
                $guideId,
                'guide',
                $schedule['departure_date'],
                $schedule['end_date'],
                $scheduleId
            );

            if (!empty($conflicts)) {
                $_SESSION['error'] = "HDV {$guide['full_name']} đã có lịch trùng!";
                header('Location: ' . BASE_URL . '?action=departure-schedules/assign-guide&schedule_id=' . $scheduleId);
                exit;
            }

            $assignments[] = [
                'schedule_id' => $scheduleId,
                'assignment_type' => 'guide',
                'resource_id' => $guideId,
                'resource_name' => $guide['full_name'],
                'resource_type' => $guide['specialization'] ?? null,
                'status' => 'pending',
                'start_date' => $schedule['departure_date'],
                'end_date' => $schedule['end_date']
            ];
        }

        if (empty($assignments)) {
            $_SESSION['error'] = 'Vui lòng chọn ít nhất một HDV!';
            header('Location: ' . BASE_URL . '?action=departure-schedules/assign-guide&schedule_id=' . $scheduleId);
            exit;
        }

        // Xóa phân bổ HDV cũ
        $oldGuideAssignments = $this->assignmentModel->getByScheduleIdAndType($scheduleId, 'guide');
        foreach ($oldGuideAssignments as $old) {
            $this->assignmentModel->delete($old['id']);
        }

        // Tạo phân bổ mới
        $results = $this->assignmentModel->createMultiple($assignments);

        // Gửi thông báo
        foreach ($assignments as $assignment) {
            $guide = $this->guideModel->findById($assignment['resource_id']);
            $this->sendNotification($scheduleId, $assignment['resource_id'], 'guide', $guide, $schedule);
        }

        $_SESSION['success'] = 'Phân bổ HDV thành công! Đã gửi thông báo đến các HDV.';
        header('Location: ' . BASE_URL . '?action=departure-schedules/show&id=' . $scheduleId);
        exit;
    }

    /**
     * Phân bổ dịch vụ
     */
    public function assignService()
    {
        $scheduleId = $_GET['schedule_id'] ?? 0;
        $serviceType = $_GET['type'] ?? 'vehicle'; // vehicle, hotel, flight, restaurant, attraction

        $schedule = $this->scheduleModel->findById($scheduleId);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        // Lấy danh sách nhà cung cấp theo loại
        $suppliers = $this->supplierModel->getAll(['type' => $serviceType, 'status' => 1]);

        // Lấy phân bổ hiện tại
        $currentAssignments = $this->assignmentModel->getByScheduleIdAndType($scheduleId, $serviceType);

        $title = 'Phân bổ Dịch vụ - ' . ucfirst($serviceType);
        $view = 'departure-schedule/assign-service';
        require_once PATH_VIEW_ADMIN;
    }

    /**
     * Xử lý phân bổ dịch vụ
     */
    public function processAssignService()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $scheduleId = $_POST['schedule_id'] ?? 0;
        $serviceType = $_POST['service_type'] ?? '';
        $schedule = $this->scheduleModel->findById($scheduleId);

        if (!$schedule) {
            $_SESSION['error'] = 'Không tìm thấy lịch khởi hành!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $services = $_POST['services'] ?? [];
        $assignments = [];

        foreach ($services as $service) {
            if (empty($service['supplier_id'])) continue;

            $supplier = $this->supplierModel->findById($service['supplier_id']);
            if (!$supplier) continue;

            $assignments[] = [
                'schedule_id' => $scheduleId,
                'assignment_type' => $serviceType,
                'resource_id' => $service['supplier_id'],
                'resource_name' => $supplier['name'],
                'quantity' => $service['quantity'] ?? 1,
                'start_date' => $service['start_date'] ?? $schedule['departure_date'],
                'end_date' => $service['end_date'] ?? $schedule['end_date'],
                'start_time' => $service['start_time'] ?? null,
                'end_time' => $service['end_time'] ?? null,
                'location' => $service['location'] ?? null,
                'status' => 'pending',
                'notes' => $service['notes'] ?? null
            ];
        }

        if (empty($assignments)) {
            $_SESSION['error'] = 'Vui lòng chọn ít nhất một dịch vụ!';
            header('Location: ' . BASE_URL . '?action=departure-schedules/assign-service&schedule_id=' . $scheduleId . '&type=' . $serviceType);
            exit;
        }

        // Xóa phân bổ cũ của loại này
        $oldAssignments = $this->assignmentModel->getByScheduleIdAndType($scheduleId, $serviceType);
        foreach ($oldAssignments as $old) {
            $this->assignmentModel->delete($old['id']);
        }

        // Tạo phân bổ mới
        $results = $this->assignmentModel->createMultiple($assignments);

        // Gửi thông báo
        foreach ($assignments as $assignment) {
            $supplier = $this->supplierModel->findById($assignment['resource_id']);
            $this->sendNotification($scheduleId, $assignment['resource_id'], $serviceType, $supplier, $schedule);
        }

        $_SESSION['success'] = 'Phân bổ dịch vụ thành công! Đã gửi thông báo đến nhà cung cấp.';
        header('Location: ' . BASE_URL . '?action=departure-schedules/show&id=' . $scheduleId);
        exit;
    }

    /**
     * Cập nhật trạng thái xác nhận phân bổ
     */
    public function updateAssignmentStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $assignmentId = $_POST['assignment_id'] ?? 0;
        $status = $_POST['status'] ?? '';

        $assignment = $this->assignmentModel->findById($assignmentId);
        if (!$assignment) {
            $_SESSION['error'] = 'Không tìm thấy phân bổ!';
            header('Location: ' . BASE_URL . '?action=departure-schedules');
            exit;
        }

        $result = $this->assignmentModel->updateStatus($assignmentId, $status);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái!';
        }

        header('Location: ' . BASE_URL . '?action=departure-schedules/show&id=' . $assignment['schedule_id']);
        exit;
    }

    /**
     * Gửi thông báo
     */
    private function sendNotification($scheduleId, $resourceId, $resourceType, $resource, $schedule)
    {
        $notificationType = 'assignment';
        $recipientType = $resourceType;
        
        $subject = '';
        $message = '';

        if ($resourceType === 'guide') {
            $subject = "Phân công dẫn tour: {$schedule['tour_name']}";
            $message = "Bạn đã được phân công dẫn tour:\n\n";
            $message .= "Tour: {$schedule['tour_name']}\n";
            $message .= "Ngày khởi hành: {$schedule['departure_date']} {$schedule['departure_time']}\n";
            $message .= "Điểm tập trung: {$schedule['meeting_point']}\n";
            $message .= "Ngày kết thúc: {$schedule['end_date']} {$schedule['end_time']}\n";
        } else {
            $subject = "Yêu cầu dịch vụ: {$schedule['tour_name']}";
            $message = "Yêu cầu cung cấp dịch vụ cho tour:\n\n";
            $message .= "Tour: {$schedule['tour_name']}\n";
            $message .= "Ngày khởi hành: {$schedule['departure_date']}\n";
            $message .= "Ngày kết thúc: {$schedule['end_date']}\n";
            $message .= "Vui lòng xác nhận và chuẩn bị dịch vụ đúng thời gian.\n";
        }

        $this->notificationModel->create([
            'schedule_id' => $scheduleId,
            'notification_type' => $notificationType,
            'recipient_type' => $recipientType,
            'recipient_id' => $resourceId,
            'recipient_name' => $resource['full_name'] ?? $resource['name'] ?? '',
            'recipient_email' => $resource['email'] ?? null,
            'recipient_phone' => $resource['phone'] ?? null,
            'subject' => $subject,
            'message' => $message,
            'status' => 'pending'
        ]);

        // TODO: Gửi email/SMS thực tế
    }

    /**
     * Validate dữ liệu lịch khởi hành
     */
    private function validateSchedule($data, $excludeId = null)
    {
        $errors = [];

        if (empty($data['tour_id'])) {
            $errors[] = 'Vui lòng chọn tour!';
        }

        if (empty($data['departure_date'])) {
            $errors[] = 'Ngày khởi hành không được để trống!';
        } elseif (strtotime($data['departure_date']) < strtotime('today')) {
            $errors[] = 'Ngày khởi hành không được là ngày trong quá khứ!';
        }

        if (empty($data['departure_time'])) {
            $errors[] = 'Giờ khởi hành không được để trống!';
        }

        if (empty($data['meeting_point'])) {
            $errors[] = 'Điểm tập trung không được để trống!';
        }

        if (empty($data['end_date'])) {
            $errors[] = 'Ngày kết thúc không được để trống!';
        }

        if (empty($data['end_time'])) {
            $errors[] = 'Giờ kết thúc không được để trống!';
        }

        if (!empty($data['departure_date']) && !empty($data['end_date'])) {
            if (strtotime($data['end_date']) < strtotime($data['departure_date'])) {
                $errors[] = 'Ngày kết thúc phải sau ngày khởi hành!';
            }
        }

        return $errors;
    }
}
