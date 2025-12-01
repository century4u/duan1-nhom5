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
}
