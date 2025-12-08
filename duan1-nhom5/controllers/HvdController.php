<?php
class HvdController
{
    public function home()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
        $title = 'Dashboard HDV';

        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';
        require_once PATH_MODEL . 'BookingModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();
        $scheduleModel = new TourScheduleModel();
        $bookingModel = new BookingModel();

        require_once PATH_MODEL . 'GuideModel.php';
        $guideModel = new GuideModel();

        // Resolve Guide ID from User ID
        $guide = $guideModel->findByUserId($_SESSION['user_id']);

        // If guide not found (should be impossible for HDV role if flow is correct, but safe fallback)
        $guideId = $guide ? $guide['id'] : 0;

        // Fetch all assignments for this guide
        // Note: GuideTourHistoryModel::getByGuideId returns arrays with tour_name, tour_code, start_date, end_date
        $assignments = $historyModel->getByGuideId($guideId);

        $upcomingTours = [];
        $pastTours = [];
        $today = date('Y-m-d');

        foreach ($assignments as $assignment) {
            // Get booking count for this specific schedule/tour
            // Note: Since history table has start_date/end_date, it likely maps to a specific schedule or instance.
            // If it maps to a tour mainly, we might need to check schedule. 
            // Attempting to match date or just showing general tour info.

            // For dashboard, we want to show specific upcoming trips.
            $tourId = $assignment['tour_id'];

            // Get participant count
            // Since we don't have schedule_id in history (based on view), we might approximate or fetch total for tour
            // Ideally we should track participants per history record if possible, 
            // but for now let's use the BookingModel to count active bookings for this tour
            // OR if history has 'participants_count' we use that (it does based on model create).

            $assignment['participant_count'] = $assignment['participants_count'] ?? 0;

            // Status determination
            if ($assignment['start_date'] > $today) {
                $assignment['status_text'] = 'Sắp diễn ra';
                $assignment['status_class'] = 'primary';
                $upcomingTours[] = $assignment;
            } elseif ($assignment['end_date'] < $today) {
                $assignment['status_text'] = 'Đã hoàn thành';
                $assignment['status_class'] = 'success';
                $pastTours[] = $assignment;
            } else {
                $assignment['status_text'] = 'Đang diễn ra';
                $assignment['status_class'] = 'warning';
                $upcomingTours[] = $assignment; // Show active in upcoming list for visibility
            }
        }

        // Sort upcoming by start_date ASC
        usort($upcomingTours, function ($a, $b) {
            return strtotime($a['start_date']) - strtotime($b['start_date']);
        });

        // Summary stats
        $stats = [
            'total_assigned' => count($assignments),
            'upcoming_count' => count($upcomingTours),
            'completed_count' => count($pastTours)
        ];

        $view = 'hdv/home';
        require_once PATH_VIEW . 'main.php';
    }

    public function tours()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();
        $scheduleModel = new TourScheduleModel();
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();

        $assignedTours = [];
        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);
        if ($guideId) {
            $histories = $historyModel->getByGuideId($guideId);
            $today = date('Y-m-d');
            foreach ($histories as $h) {
                if (!empty($h['start_date']) && $h['start_date'] < $today && !empty($h['end_date']) && $h['end_date'] < $today) {
                    continue;
                }
                $tour = $tourModel->findById($h['tour_id']);
                if (!$tour)
                    continue;
                $schedules = $scheduleModel->getByTourId($h['tour_id']);
                foreach ($schedules as &$s) {
                    if (!empty($s['activities'])) {
                        $decoded = json_decode($s['activities'], true);
                        $s['activities_array'] = is_array($decoded) ? $decoded : [];
                    } else {
                        $s['activities_array'] = [];
                    }
                }

                $bookings = $bookingModel->getAll(['tour_id' => $h['tour_id']]);
                $participants = [];
                foreach ($bookings as $b) {
                    $details = $bookingDetailModel->getByBookingId($b['id']);
                    $participants[] = ['booking' => $b, 'details' => $details];
                }

                $assignedTours[] = [
                    'history' => $h,
                    'tour' => $tour,
                    'schedules' => $schedules,
                    'participants' => $participants,
                ];
            }
        }

        require_once PATH_VIEW . 'hdv/tours.php';
    }

    public function show()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';

        $tourModel = new TourModel();
        $scheduleModel = new TourScheduleModel();
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();
        $historyModel = new GuideTourHistoryModel();

        $id = $_GET['id'] ?? 0;
        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        $tour = $tourModel->findById($id);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=hvd/tours');
            exit;
        }

        $schedules = $scheduleModel->getByTourId($id);
        foreach ($schedules as &$s) {
            if (!empty($s['activities'])) {
                $decoded = json_decode($s['activities'], true);
                $s['activities_array'] = is_array($decoded) ? $decoded : [];
            } else {
                $s['activities_array'] = [];
            }
        }
        unset($s);

        $bookings = $bookingModel->getAll(['tour_id' => $id]);
        $participants = [];
        foreach ($bookings as $b) {
            $details = $bookingDetailModel->getByBookingId($b['id']);
            $participants[] = ['booking' => $b, 'details' => $details];

        }

        $assignment = null;
        if ($guideId) {
            $histories = $historyModel->getByGuideId($guideId);
            foreach ($histories as $h) {
                if ((int) $h['tour_id'] === (int) $id) {
                    $assignment = $h;
                    break;
                }
            }
        }

        require_once PATH_VIEW . 'hdv/tour_show.php';
    }

    public function customerEdit()
    {
        require_once PATH_MODEL . 'BookingDetailModel.php';

        $bookingDetailModel = new BookingDetailModel();
        $customerId = $_GET['id'] ?? 0;
        $tour_id = $_GET['tour_id'] ?? 0;
        $guide_id = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        $customer = $bookingDetailModel->findById($customerId);
        if (!$customer) {
            $_SESSION['error'] = 'Không tìm thấy thông tin khách hàng!';
            header('Location: ' . BASE_URL . '?action=hvd/tours/show&id=' . $tour_id . '&guide_id=' . $guide_id);
            exit;
        }

        require_once PATH_VIEW . 'hdv/customer_edit.php';
    }

    public function customerUpdate()
    {
        require_once PATH_MODEL . 'BookingDetailModel.php';

        $bookingDetailModel = new BookingDetailModel();
        $customerId = $_POST['id'] ?? 0;
        $tour_id = $_POST['tour_id'] ?? 0;
        $guide_id = $_POST['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        // Lấy thông tin khách hàng hiện tại
        $currentCustomer = $bookingDetailModel->findById($customerId);
        if (!$currentCustomer) {
            $_SESSION['error'] = 'Không tìm thấy thông tin khách hàng!';
            header('Location: ' . BASE_URL . '?action=hvd/tours/show&id=' . $tour_id . '&guide_id=' . $guide_id);
            exit;
        }

        // Dữ liệu cập nhật
        $updateData = [
            'fullname' => $_POST['fullname'] ?? '',
            'gender' => $_POST['gender'] ?? null,
            'birthdate' => $_POST['birthdate'] ?? null,
            'id_card' => $_POST['id_card'] ?? null,
            'passport' => $_POST['passport'] ?? null,
            'hobby' => $_POST['hobby'] ?? null,
            'special_requirements' => $_POST['special_requirements'] ?? null,
            'dietary_restrictions' => $_POST['dietary_restrictions'] ?? null
        ];

        // Thực hiện cập nhật
        $result = $bookingDetailModel->update($customerId, $updateData);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật thông tin khách hàng thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật thông tin khách hàng thất bại!';
        }

        header('Location: ' . BASE_URL . '?action=hvd/tours/show&id=' . $tour_id . '&guide_id=' . $guide_id);
        exit;
    }

    public function checkin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $scheduleId = $_GET['id'] ?? 0;
        if (!$scheduleId) {
            $_SESSION['error'] = 'Không tìm thấy lịch trình!';
            header('Location: ' . BASE_URL . '?action=hvd/home');
            exit;
        }

        require_once PATH_MODEL . 'GuideModel.php';
        require_once PATH_MODEL . 'TourCustomerModel.php';
        require_once PATH_MODEL . 'DepartureScheduleModel.php';
        require_once PATH_MODEL . 'CheckinModel.php';
        require_once PATH_MODEL . 'TourModel.php';

        $guideModel = new GuideModel();
        $scheduleModel = new DepartureScheduleModel();
        $tourCustomerModel = new TourCustomerModel();
        $checkinModel = new CheckinModel();
        $tourModel = new TourModel();

        // 1. Verify this schedule is assigned to this guide
        $guide = $guideModel->findByUserId($_SESSION['user_id']);
        if (!$guide) {
            $_SESSION['error'] = 'Bạn chưa được kích hoạt tài khoản HDV!';
            header('Location: ' . BASE_URL);
            exit;
        }

        // Use getSchedulesByGuideId to verify assignment
        $assignedSchedules = $scheduleModel->getSchedulesByGuideId($guide['id']);
        $isAssigned = false;
        foreach ($assignedSchedules as $as) {
            if ($as['id'] == $scheduleId) {
                $isAssigned = true;
                break;
            }
        }

        if (!$isAssigned) {
            $_SESSION['error'] = 'Bạn không được phân công lịch trình này!';
            header('Location: ' . BASE_URL . '?action=hvd/home');
            exit;
        }

        $schedule = $scheduleModel->findById($scheduleId);
        $tour = $tourModel->findById($schedule['tour_id']);
        $customers = $tourCustomerModel->getByDepartureScheduleId($scheduleId);
        $checkins = $checkinModel->getByDepartureScheduleId($scheduleId);

        // Map checkins
        $checkinMap = [];
        foreach ($checkins as $c) {
            $checkinMap[$c['booking_detail_id']] = $c;
        }

        // Merge checkin info
        foreach ($customers as &$cus) {
            $cus['checkin'] = $checkinMap[$cus['id']] ?? null;
        }

        // Stats
        $stats = [
            'total' => count($customers),
            'checked_in' => 0,
            'pending' => 0
        ];
        foreach ($customers as $cus) {
            if ($cus['checkin'] && $cus['checkin']['status'] == 'checked_in') {
                $stats['checked_in']++;
            } else {
                $stats['pending']++;
            }
        }

        $title = 'Check-in: ' . $tour['name'];
        require_once PATH_VIEW . 'hdv/checkin.php';
    }

    public function processCheckin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid Request']);
            exit;
        }

        require_once PATH_MODEL . 'CheckinModel.php';
        $checkinModel = new CheckinModel();

        $bookingDetailId = $_POST['booking_detail_id'] ?? 0;
        $scheduleId = $_POST['departure_schedule_id'] ?? 0;
        $status = $_POST['status'] ?? 'checked_in';
        $notes = $_POST['notes'] ?? null;

        if (!$bookingDetailId || !$scheduleId) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }

        // Check if existing
        $existing = $checkinModel->getByBookingDetailId($bookingDetailId);

        if ($existing) {
            $result = $checkinModel->updateStatus($existing['id'], $status, $notes, $_SESSION['user_id']);
        } else {
            $result = $checkinModel->create([
                'booking_detail_id' => $bookingDetailId,
                'departure_schedule_id' => $scheduleId,
                'status' => $status,
                'notes' => $notes,
                'checked_by' => $_SESSION['user_id']
            ]);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Saved' : 'Error',
            'data' => [
                'status' => $status,
                'time' => date('H:i')
            ]
        ]);
        exit;
    }
}
