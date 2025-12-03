<?php

class StatisticsController
{
    private $tourModel;
    private $bookingModel;
    private $guideModel;
    private $scheduleModel;

    public function __construct()
    {
        require_once PATH_MODEL . 'TourModel.php';
        require_once PATH_MODEL . 'BookingModel.php';
        require_once PATH_MODEL . 'GuideModel.php';
        require_once PATH_MODEL . 'DepartureScheduleModel.php';
        
        $this->tourModel = new TourModel();
        $this->bookingModel = new BookingModel();
        $this->guideModel = new GuideModel();
        $this->scheduleModel = new DepartureScheduleModel();
    }

    /**
     * Trang thống kê tổng quan
     */
    public function index()
    {
        // Thống kê Tour
        $totalTours = $this->tourModel->count();
        $activeTours = $this->tourModel->count(['status' => 1]);
        
        // Thống kê theo danh mục
        $categories = TourModel::getCategories();
        $categoryStats = [];
        foreach ($categories as $key => $label) {
            $categoryStats[$key] = [
                'name' => $label,
                'count' => $this->tourModel->count(['category' => $key, 'status' => 1])
            ];
        }

        // Thống kê Booking
        $totalBookings = $this->countRecords('bookings');
        
        // Thống kê HDV
        $totalGuides = $this->countRecords('guides');
        
        // Thống kê Lịch khởi hành
        $totalSchedules = $this->countRecords('departure_schedules');

        $title = 'Thống kê';
        $view = 'statistics/index';
        require_once PATH_VIEW_ADMIN . 'main.php';
    }

    /**
     * Đếm số lượng bản ghi trong bảng
     */
    private function countRecords($table)
    {
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME);
            $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, DB_OPTIONS);
            $sql = "SELECT COUNT(*) as total FROM {$table}";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
}

