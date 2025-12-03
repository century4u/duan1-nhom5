<?php

class BookingController
{
    private $bookingModel;
    private $bookingDetailModel;
    private $tourModel;
    private $tourPriceModel;
    private $statusHistoryModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->bookingDetailModel = new BookingDetailModel();
        $this->tourModel = new TourModel();
        $this->tourPriceModel = new TourPriceModel();
        $this->statusHistoryModel = new BookingStatusHistoryModel();
    }

    /**
     * Danh sách bookings
     */
    public function index()
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'tour_id' => $_GET['tour_id'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Loại bỏ các filter rỗng
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });

        $bookings = $this->bookingModel->getAll($filters);
        
        // Lấy thông tin chi tiết cho mỗi booking
        foreach ($bookings as &$booking) {
            $booking['participants_count'] = $this->bookingDetailModel->countByBookingId($booking['id']);
        }

        $title = 'Quản lý Đặt Tour';
        $view = 'booking/index';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Hiển thị form tạo booking mới
     */
    public function create()
    {
        $tourId = $_GET['tour_id'] ?? 0;
        $tour = null;
        
        if ($tourId) {
            $tour = $this->tourModel->findById($tourId);
            if (!$tour) {
                $_SESSION['error'] = 'Không tìm thấy tour!';
                header('Location: ' . BASE_URL . '?action=tours');
                exit;
            }

            // Kiểm tra chỗ trống
            $availability = $this->bookingModel->checkAvailableSlots($tourId);
            if (!$availability['available']) {
                $_SESSION['error'] = 'Tour đã hết chỗ!';
                header('Location: ' . BASE_URL . '?action=tours/show&id=' . $tourId);
                exit;
            }
        }

        // Lấy danh sách tour đang hoạt động
        $tours = $this->tourModel->getAll(['status' => 1]);

        $title = 'Đặt Tour Mới';
        $view = 'booking/create';
        require_once PATH_VIEW_ADMIN;
    }

    /**
     * Xử lý tạo booking mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=bookings/create');
            exit;
        }

        $data = [
            'tour_id' => (int)($_POST['tour_id'] ?? 0),
            'user_id' => !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null,
            'booking_type' => $_POST['booking_type'] ?? 'individual', // individual hoặc group
            'contact_name' => $_POST['contact_name'] ?? '',
            'contact_email' => $_POST['contact_email'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'special_requests' => $_POST['special_requests'] ?? '',
            'participants' => $_POST['participants'] ?? []
        ];

        // Validate
        $errors = $this->validateBooking($data);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $data;
            header('Location: ' . BASE_URL . '?action=bookings/create&tour_id=' . $data['tour_id']);
            exit;
        }

        // Kiểm tra tour tồn tại
        $tour = $this->tourModel->findById($data['tour_id']);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=bookings/create');
            exit;
        }

        // Kiểm tra chỗ trống
        $availability = $this->bookingModel->checkAvailableSlots($data['tour_id']);
        $participantsCount = count($data['participants']);
        
        if (!$availability['available']) {
            $_SESSION['error'] = 'Tour đã hết chỗ!';
            header('Location: ' . BASE_URL . '?action=bookings/create&tour_id=' . $data['tour_id']);
            exit;
        }

        if ($availability['available_slots'] < $participantsCount) {
            $_SESSION['error'] = 'Số chỗ còn lại không đủ! Chỉ còn ' . $availability['available_slots'] . ' chỗ.';
            header('Location: ' . BASE_URL . '?action=bookings/create&tour_id=' . $data['tour_id']);
            exit;
        }

        // Tính tổng giá
        $totalPrice = $this->calculateTotalPrice($data['tour_id'], $data['participants']);

        // Tạo booking
        $bookingData = [
            'tour_id' => $data['tour_id'],
            'user_id' => $data['user_id'],
            'total_price' => $totalPrice,
            'status' => 'pending' // Tạm thời, chờ xác nhận
        ];

        $bookingId = $this->bookingModel->create($bookingData);

        if (!$bookingId) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo booking!';
            header('Location: ' . BASE_URL . '?action=bookings/create&tour_id=' . $data['tour_id']);
            exit;
        }

        // Tạo chi tiết booking (thông tin từng khách)
        $this->bookingDetailModel->createMultiple($bookingId, $data['participants']);

        $_SESSION['success'] = 'Đặt tour thành công! Mã booking: #' . $bookingId;
        header('Location: ' . BASE_URL . '?action=bookings/show&id=' . $bookingId);
        exit;
    }

    /**
     * Xem chi tiết booking
     */
    public function show()
    {
        $id = $_GET['id'] ?? 0;
        $booking = $this->bookingModel->findById($id);

        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking!';
            header('Location: ' . BASE_URL . '?action=bookings');
            exit;
        }

        // Lấy chi tiết khách hàng
        $participants = $this->bookingDetailModel->getByBookingId($id);
        $booking['participants'] = $participants;
        $booking['participants_count'] = count($participants);

        // Xác định loại booking
        $booking['booking_type'] = $booking['participants_count'] <= 2 ? 'individual' : 'group';

        // Lấy lịch sử thay đổi trạng thái
        $statusHistory = $this->statusHistoryModel->getByBookingId($id);
        $booking['status_history'] = $statusHistory;

        // Lấy danh sách trạng thái có thể chuyển
        $currentStatus = $booking['status'];
        $availableStatuses = [];
        $allStatuses = BookingModel::getStatuses();
        foreach ($allStatuses as $statusKey => $statusLabel) {
            if (BookingModel::canChangeStatus($currentStatus, $statusKey)) {
                $availableStatuses[$statusKey] = $statusLabel;
            }
        }

        $title = 'Chi tiết Đặt Tour #' . $booking['id'];
        $view = 'booking/show';
        require_once PATH_VIEW_ADMIN.'main.php';
    }

    /**
     * Cập nhật trạng thái booking
     */
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=bookings');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        $depositAmount = !empty($_POST['deposit_amount']) ? (float)$_POST['deposit_amount'] : null;
        $changeReason = $_POST['change_reason'] ?? '';
        $notes = $_POST['notes'] ?? '';

        // Kiểm tra booking tồn tại
        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            $_SESSION['error'] = 'Không tìm thấy booking!';
            header('Location: ' . BASE_URL . '?action=bookings');
            exit;
        }

        // Kiểm tra phân quyền (mở rộng - hiện tại cho phép tất cả)
        // TODO: Thêm logic kiểm tra role user
        $currentUserId = $_SESSION['user_id'] ?? null; // Giả sử có session user_id

        // Kiểm tra trạng thái hợp lệ
        $statuses = BookingModel::getStatuses();
        if (!isset($statuses[$newStatus])) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ!';
            header('Location: ' . BASE_URL . '?action=bookings/show&id=' . $id);
            exit;
        }

        // Kiểm tra có thể chuyển trạng thái không
        if (!BookingModel::canChangeStatus($booking['status'], $newStatus)) {
            $_SESSION['error'] = 'Không thể chuyển từ trạng thái "' . $statuses[$booking['status']] . '" sang "' . $statuses[$newStatus] . '"!';
            header('Location: ' . BASE_URL . '?action=bookings/show&id=' . $id);
            exit;
        }

        // Validate deposit_amount nếu chuyển sang trạng thái "Đã cọc"
        if ($newStatus === 'deposit' && $depositAmount === null) {
            $_SESSION['error'] = 'Vui lòng nhập số tiền cọc!';
            header('Location: ' . BASE_URL . '?action=bookings/show&id=' . $id);
            exit;
        }

        if ($newStatus === 'deposit' && $depositAmount > $booking['total_price']) {
            $_SESSION['error'] = 'Số tiền cọc không được lớn hơn tổng giá!';
            header('Location: ' . BASE_URL . '?action=bookings/show&id=' . $id);
            exit;
        }

        // Cập nhật trạng thái
        $result = $this->bookingModel->updateStatus(
            $id,
            $newStatus,
            $currentUserId,
            $depositAmount,
            $changeReason,
            $notes
        );

        if ($result) {
            $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái!';
        }

        header('Location: ' . BASE_URL . '?action=bookings/show&id=' . $id);
        exit;
    }

    /**
     * Tính tổng giá booking
     */
    private function calculateTotalPrice($tourId, $participants)
    {
        $totalPrice = 0;
        $tour = $this->tourModel->findById($tourId);
        $basePrice = $tour['price'] ?? 0;

        // Lấy giá theo loại
        $prices = $this->tourPriceModel->getByTourId($tourId);
        $priceMap = [];
        foreach ($prices as $price) {
            $priceMap[$price['price_type']] = $price['price'];
        }

        foreach ($participants as $participant) {
            $birthdate = $participant['birthdate'] ?? null;
            $age = null;
            
            if ($birthdate) {
                $birth = new DateTime($birthdate);
                $today = new DateTime();
                $age = $today->diff($birth)->y;
            }

            // Xác định loại giá
            $priceType = 'adult'; // Mặc định
            if ($age !== null) {
                if ($age < 2) {
                    $priceType = 'infant';
                } elseif ($age < 12) {
                    $priceType = 'child';
                } elseif ($age >= 60) {
                    $priceType = 'senior';
                }
            }

            // Lấy giá từ bảng tour_prices hoặc dùng giá cơ bản
            if (isset($priceMap[$priceType])) {
                $totalPrice += $priceMap[$priceType];
            } else {
                // Áp dụng hệ số giảm giá theo loại
                $discount = 1;
                if ($priceType === 'child') {
                    $discount = 0.7; // Trẻ em 70%
                } elseif ($priceType === 'infant') {
                    $discount = 0.3; // Trẻ sơ sinh 30%
                } elseif ($priceType === 'senior') {
                    $discount = 0.9; // Người cao tuổi 90%
                }
                $totalPrice += $basePrice * $discount;
            }
        }

        return $totalPrice;
    }

    /**
     * Validate dữ liệu booking
     */
    private function validateBooking($data)
    {
        $errors = [];

        if (empty($data['tour_id']) || $data['tour_id'] <= 0) {
            $errors[] = 'Vui lòng chọn tour!';
        }

        if (empty($data['contact_name'])) {
            $errors[] = 'Vui lòng nhập tên người liên hệ!';
        }

        if (empty($data['contact_phone'])) {
            $errors[] = 'Vui lòng nhập số điện thoại!';
        }

        if (empty($data['participants']) || !is_array($data['participants'])) {
            $errors[] = 'Vui lòng nhập thông tin ít nhất 1 khách hàng!';
        } else {
            foreach ($data['participants'] as $index => $participant) {
                if (empty($participant['fullname'])) {
                    $errors[] = "Vui lòng nhập tên khách hàng thứ " . ($index + 1) . "!";
                }
            }
        }

        return $errors;
    }
}
