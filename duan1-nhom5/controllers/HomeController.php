<?php

class HomeController
{
    public function index()
    {
        // Check login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'GuideTourHistoryModel.php';
        require_once PATH_MODEL . 'TourScheduleModel.php';
        require_once PATH_MODEL . 'BookingModel.php';

        $tourModel = new TourModel();
        $historyModel = new GuideTourHistoryModel();
        $scheduleModel = new TourScheduleModel();
        $bookingModel = new BookingModel();

        $guideId = $_SESSION['user_id'];
        $assignments = $historyModel->getByGuideId($guideId);

        $upcomingTours = [];
        $pastTours = [];
        $today = date('Y-m-d');

        foreach ($assignments as $assignment) {
            $assignment['participant_count'] = $assignment['participants_count'] ?? 0;

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
                $upcomingTours[] = $assignment;
            }
        }

        usort($upcomingTours, function ($a, $b) {
            return strtotime($a['start_date']) - strtotime($b['start_date']);
        });

        $stats = [
            'total_assigned' => count($assignments),
            'upcoming_count' => count($upcomingTours),
            'completed_count' => count($pastTours)
        ];

        // $title = 'Dashboard HDV';
        $view = 'hdv/home';
        require_once PATH_VIEW . 'main.php';
    }
}