<?php
class HvdController {
    public function home() {

        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();
        $scheduleModel = new TourScheduleModel();
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();

        $upcomingTours = $tourModel->getAll(['status' => 'upcoming', 'limit' => 5]);
        require_once PATH_MODEL . 'BookingModel.php';
        $bookingModelForUpcoming = new BookingModel();
        foreach ($upcomingTours as &$ut) {
            $ut['booked_participants'] = $bookingModelForUpcoming->countParticipantsByTourId($ut['id'] ?? $ut['tour_id'] ?? 0);
            $ut['departure_location'] = $ut['departure_location'] ?? ($ut['departure'] ?? null);
        }
        unset($ut);
        $totalTours = $tourModel->count();
        $pendingReports = 0;

        $assignedTours = [];
        $guideId = $_GET['guide_id'] ?? null;
        if ($guideId) {
            $histories = $historyModel->getByGuideId($guideId);
            $today = date('Y-m-d');
            foreach ($histories as $h) {

                if (!empty($h['start_date']) && $h['start_date'] < $today && !empty($h['end_date']) && $h['end_date'] < $today) {
                    continue;
                }
                $tour = $tourModel->findById($h['tour_id']);
                if (!$tour) continue;
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
                    $participants[] = [
                        'booking' => $b,
                        'details' => $details
                    ];
                }

                $assignedTours[] = [
                    'history' => $h,
                    'tour' => $tour,
                    'schedules' => $schedules,
                    'participants' => $participants,
                ];
            }
        }

        require_once PATH_VIEW . 'hdv/home.php';
    }

    public function tours() {
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
                if (!$tour) continue;
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

    public function show() {
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
                if ((int)$h['tour_id'] === (int)$id) {
                    $assignment = $h;
                    break;
                }
            }
        }

        require_once PATH_VIEW . 'hdv/tour_show.php';
    }

    /**
     * Trang check-in chuyên dụng cho HDV
     */
    public function checkinPage() {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'BookingDetailModel.php';
        require_once PATH_MODEL . 'CheckinModel.php';

        $tourModel = new TourModel();
        $bookingModel = new BookingModel();
        $bookingDetailModel = new BookingDetailModel();
        $checkinModel = new CheckinModel();

        $tourId = $_GET['tour_id'] ?? 0;
        $guideId = $_GET['guide_id'] ?? ($_SESSION['user_id'] ?? null);

        $tour = $tourModel->findById($tourId);
        if (!$tour) {
            $_SESSION['error'] = 'Không tìm thấy tour!';
            header('Location: ' . BASE_URL . '?action=hvd/tours');
            exit;
        }

        // Lấy danh sách khách
        $bookings = $bookingModel->getAll(['tour_id' => $tourId]);
        $customers = [];
        foreach ($bookings as $b) {
            if (!in_array($b['status'], ['confirmed', 'deposit', 'completed'])) {
                continue;
            }
            $details = $bookingDetailModel->getByBookingId($b['id']);
            foreach ($details as $d) {
                // Lấy thông tin check-in
                $checkin = $checkinModel->getByBookingDetailId($d['id']);
                $customers[] = [
                    'detail' => $d,
                    'booking' => $b,
                    'checkin' => $checkin
                ];
            }
        }

        // Lấy tổng quan check-in
        $summary = $checkinModel->getCheckInSummary($tourId);

        require_once PATH_VIEW . 'hdv/checkin.php';
    }

    /**
     * Cập nhật yêu cầu đặc biệt
     */
    public function updateSpecialRequirement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        require_once PATH_MODEL . 'BookingDetailModel.php';
        $model = new BookingDetailModel();

        $id = $_POST['id'] ?? 0;
        $specialRequirements = $_POST['special_requirements'] ?? '';

        $result = $model->updateSpecialRequirements($id, $specialRequirements);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Cập nhật thành công!' : 'Cập nhật thất bại!'
        ]);
        exit;
    }

    /**
     * Check-in nhanh (AJAX)
     */
    public function quickCheckin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        require_once PATH_MODEL . 'CheckinModel.php';
        $model = new CheckinModel();

        $bookingDetailId = $_POST['booking_detail_id'] ?? 0;
        $status = $_POST['status'] ?? 'checked_in';
        $checkedBy = $_SESSION['user_id'] ?? null;
        $notes = $_POST['notes'] ?? null;

        $result = $model->quickCheckin($bookingDetailId, $status, $checkedBy, $notes);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $result !== false,
            'message' => $result !== false ? 'Check-in thành công!' : 'Check-in thất bại!'
        ]);
        exit;
    }
}
