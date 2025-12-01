<?php

class OperationReportModel extends BaseModel
{
    protected $table = 'operation_reports';

    /**
     * Tính doanh thu theo tour
     * @param int $tourId ID của tour
     * @param string $startDate Ngày bắt đầu (Y-m-d)
     * @param string $endDate Ngày kết thúc (Y-m-d)
     * @return float Doanh thu
     */
    public function calculateRevenue($tourId = null, $startDate = null, $endDate = null)
    {
        $sql = "SELECT COALESCE(SUM(total_price), 0) as revenue
                FROM bookings
                WHERE status IN ('deposit', 'confirmed', 'completed')";
        
        $params = [];

        if ($tourId) {
            $sql .= " AND tour_id = :tour_id";
            $params['tour_id'] = $tourId;
        }

        if ($startDate) {
            $sql .= " AND booking_date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND booking_date <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (float)($result['revenue'] ?? 0);
    }

    /**
     * Tính chi phí theo tour
     * @param int $tourId ID của tour
     * @param string $startDate Ngày bắt đầu
     * @param string $endDate Ngày kết thúc
     * @return float Chi phí
     */
    public function calculateCost($tourId = null, $startDate = null, $endDate = null)
    {
        // Nếu có bảng operation_reports với trường cost, tính từ đó
        // Nếu không, có thể tính từ các nguồn khác hoặc trả về 0
        $sql = "SELECT COALESCE(SUM(cost), 0) as total_cost
                FROM {$this->table}
                WHERE 1=1";
        
        $params = [];

        if ($tourId) {
            $sql .= " AND tour_id = :tour_id";
            $params['tour_id'] = $tourId;
        }

        if ($startDate) {
            $sql .= " AND report_date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND report_date <= :end_date";
            $params['end_date'] = $endDate;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (float)($result['total_cost'] ?? 0);
    }

    /**
     * Tính lợi nhuận (Doanh thu - Chi phí)
     * @param int $tourId ID của tour
     * @param string $startDate Ngày bắt đầu
     * @param string $endDate Ngày kết thúc
     * @return float Lợi nhuận
     */
    public function calculateProfit($tourId = null, $startDate = null, $endDate = null)
    {
        $revenue = $this->calculateRevenue($tourId, $startDate, $endDate);
        $cost = $this->calculateCost($tourId, $startDate, $endDate);
        return $revenue - $cost;
    }

    /**
     * Báo cáo tổng hợp theo tour
     * @param array $filters Các bộ lọc
     * @return array Danh sách báo cáo theo tour
     */
    public function getReportByTour($filters = [])
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $tourId = $filters['tour_id'] ?? null;

        $sql = "SELECT 
                    t.id as tour_id,
                    t.name as tour_name,
                    t.code as tour_code,
                    COUNT(DISTINCT b.id) as total_bookings,
                    COUNT(DISTINCT bd.id) as total_participants,
                    COALESCE(SUM(CASE WHEN b.status IN ('deposit', 'confirmed', 'completed') THEN b.total_price ELSE 0 END), 0) as revenue,
                    COALESCE(SUM(CASE WHEN b.status = 'cancelled' THEN b.total_price ELSE 0 END), 0) as cancelled_revenue
                FROM tours t
                LEFT JOIN bookings b ON t.id = b.tour_id";
        
        // Join với booking_details để đếm số người tham gia
        $sql .= " LEFT JOIN booking_details bd ON b.id = bd.booking_id";
        
        $sql .= " WHERE 1=1";
        $params = [];

        if ($tourId) {
            $sql .= " AND t.id = :tour_id";
            $params['tour_id'] = $tourId;
        }

        if ($startDate) {
            $sql .= " AND (b.booking_date >= :start_date OR b.booking_date IS NULL)";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND (b.booking_date <= :end_date OR b.booking_date IS NULL)";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $sql .= " GROUP BY t.id, t.name, t.code
                  ORDER BY revenue DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $tours = $stmt->fetchAll();

        // Tính chi phí và lợi nhuận cho mỗi tour
        foreach ($tours as &$tour) {
            $tour['cost'] = $this->calculateCost($tour['tour_id'], $startDate, $endDate);
            $tour['profit'] = (float)$tour['revenue'] - $tour['cost'];
            $tour['profit_margin'] = $tour['revenue'] > 0 
                ? round(($tour['profit'] / $tour['revenue']) * 100, 2) 
                : 0;
        }

        return $tours;
    }

    /**
     * Báo cáo theo thời gian (tháng, quý, năm)
     * @param string $periodType Loại kỳ: 'month', 'quarter', 'year'
     * @param string $startDate Ngày bắt đầu
     * @param string $endDate Ngày kết thúc
     * @return array Danh sách báo cáo theo kỳ
     */
    public function getReportByPeriod($periodType = 'month', $startDate = null, $endDate = null)
    {
        $dateFormat = '%Y-%m';
        switch ($periodType) {
            case 'year':
                $dateFormat = '%Y';
                break;
            case 'quarter':
                $dateFormat = '%Y-Q%q';
                break;
            case 'month':
            default:
                $dateFormat = '%Y-%m';
                break;
        }

        $sql = "SELECT 
                    DATE_FORMAT(b.booking_date, :date_format) as period,
                    COUNT(DISTINCT b.id) as total_bookings,
                    COUNT(DISTINCT bd.id) as total_participants,
                    COUNT(DISTINCT b.tour_id) as total_tours,
                    COALESCE(SUM(CASE WHEN b.status IN ('deposit', 'confirmed', 'completed') THEN b.total_price ELSE 0 END), 0) as revenue
                FROM bookings b
                LEFT JOIN booking_details bd ON b.id = bd.booking_id
                WHERE b.status IN ('deposit', 'confirmed', 'completed', 'cancelled')";
        
        $params = ['date_format' => $dateFormat];

        if ($startDate) {
            $sql .= " AND b.booking_date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND b.booking_date <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $sql .= " GROUP BY period
                  ORDER BY period DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $periods = $stmt->fetchAll();

        // Tính chi phí và lợi nhuận cho mỗi kỳ
        foreach ($periods as &$period) {
            // Parse period để lấy start và end date
            $periodDates = $this->parsePeriodDates($period['period'], $periodType);
            $period['cost'] = $this->calculateCost(null, $periodDates['start'], $periodDates['end']);
            $period['profit'] = (float)$period['revenue'] - $period['cost'];
            $period['profit_margin'] = $period['revenue'] > 0 
                ? round(($period['profit'] / $period['revenue']) * 100, 2) 
                : 0;
        }

        return $periods;
    }

    /**
     * Parse period string để lấy start và end date
     */
    private function parsePeriodDates($period, $periodType)
    {
        $start = null;
        $end = null;

        switch ($periodType) {
            case 'year':
                $start = $period . '-01-01';
                $end = $period . '-12-31';
                break;
            case 'quarter':
                [$year, $quarter] = explode('-Q', $period);
                $month = (($quarter - 1) * 3) + 1;
                $start = sprintf('%s-%02d-01', $year, $month);
                $endMonth = $month + 2;
                $end = date('Y-m-t', strtotime(sprintf('%s-%02d-01', $year, $endMonth)));
                break;
            case 'month':
                $start = $period . '-01';
                $end = date('Y-m-t', strtotime($period . '-01'));
                break;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Lấy báo cáo tổng hợp
     * @param array $filters Các bộ lọc
     * @return array Báo cáo tổng hợp
     */
    public function getSummaryReport($filters = [])
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $tourId = $filters['tour_id'] ?? null;

        $revenue = $this->calculateRevenue($tourId, $startDate, $endDate);
        $cost = $this->calculateCost($tourId, $startDate, $endDate);
        $profit = $revenue - $cost;

        // Đếm số booking
        $sql = "SELECT COUNT(*) as total_bookings
                FROM bookings
                WHERE status IN ('deposit', 'confirmed', 'completed')";
        $params = [];

        if ($tourId) {
            $sql .= " AND tour_id = :tour_id";
            $params['tour_id'] = $tourId;
        }

        if ($startDate) {
            $sql .= " AND booking_date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND booking_date <= :end_date";
            $params['end_date'] = $endDate . ' 23:59:59';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        $totalBookings = (int)($result['total_bookings'] ?? 0);

        // Đếm số tour
        $sql = "SELECT COUNT(DISTINCT tour_id) as total_tours
                FROM bookings
                WHERE status IN ('deposit', 'confirmed', 'completed')";
        
        if ($tourId) {
            $sql .= " AND tour_id = :tour_id";
        } else {
            $params = [];
        }

        if ($startDate) {
            $sql .= " AND booking_date >= :start_date";
            if (!isset($params['start_date'])) {
                $params['start_date'] = $startDate;
            }
        }

        if ($endDate) {
            $sql .= " AND booking_date <= :end_date";
            if (!isset($params['end_date'])) {
                $params['end_date'] = $endDate . ' 23:59:59';
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        $totalTours = (int)($result['total_tours'] ?? 0);

        return [
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
            'profit_margin' => $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0,
            'total_bookings' => $totalBookings,
            'total_tours' => $totalTours
        ];
    }

    /**
     * Lấy tất cả báo cáo vận hành
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['tour_id'])) {
            $sql .= " AND tour_id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND report_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND report_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY report_date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy báo cáo theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo báo cáo mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, report_date, revenue, cost, profit, notes) 
                VALUES 
                (:tour_id, :report_date, :revenue, :cost, :profit, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'] ?? null,
            'report_date' => $data['report_date'] ?? date('Y-m-d'),
            'revenue' => $data['revenue'] ?? 0,
            'cost' => $data['cost'] ?? 0,
            'profit' => ($data['revenue'] ?? 0) - ($data['cost'] ?? 0),
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật báo cáo
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                tour_id = :tour_id,
                report_date = :report_date,
                revenue = :revenue,
                cost = :cost,
                profit = :profit,
                notes = :notes
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'tour_id' => $data['tour_id'] ?? null,
            'report_date' => $data['report_date'] ?? date('Y-m-d'),
            'revenue' => $data['revenue'] ?? 0,
            'cost' => $data['cost'] ?? 0,
            'profit' => ($data['revenue'] ?? 0) - ($data['cost'] ?? 0),
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Xóa báo cáo
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy báo cáo theo tour
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id ORDER BY report_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy báo cáo theo lịch khởi hành
     */
    public function getByScheduleId($scheduleId)
    {
        // TODO: Implement if schedule_id exists in operation_reports table
        return [];
    }

    /**
     * Lấy báo cáo theo khoảng thời gian
     */
    public function getByDateRange($startDate, $endDate)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE report_date >= :start_date AND report_date <= :end_date
                ORDER BY report_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        return $stmt->fetchAll();
    }
}
