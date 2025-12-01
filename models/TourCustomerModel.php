<?php

class TourCustomerModel extends BaseModel
{
    protected $table = 'booking_details';

    /**
     * Lấy danh sách khách theo tour_id
     */
    public function getByTourId($tourId, $filters = [])
    {
        $sql = "SELECT bd.*, 
                       b.id as booking_id, b.booking_date, b.status as booking_status, b.total_price,
                       b.deposit_amount, b.user_id,
                       t.name as tour_name, t.code as tour_code, t.departure_location, t.destination,
                       u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone,
                       ds.id as departure_schedule_id, ds.departure_date, ds.departure_time, ds.meeting_point
                FROM {$this->table} bd
                INNER JOIN bookings b ON bd.booking_id = b.id
                INNER JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN departure_schedules ds ON b.departure_schedule_id = ds.id
                WHERE b.tour_id = :tour_id";

        $params = ['tour_id' => $tourId];

        // Lọc theo trạng thái booking
        if (!empty($filters['booking_status'])) {
            $sql .= " AND b.status = :booking_status";
            $params['booking_status'] = $filters['booking_status'];
        } else {
            // Mặc định chỉ lấy các booking đã xác nhận
            $sql .= " AND b.status IN ('confirmed', 'deposit', 'completed')";
        }

        // Lọc theo lịch khởi hành
        if (!empty($filters['departure_schedule_id'])) {
            $sql .= " AND b.departure_schedule_id = :departure_schedule_id";
            $params['departure_schedule_id'] = $filters['departure_schedule_id'];
        }

        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $sql .= " AND bd.fullname LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY b.booking_date ASC, bd.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách khách theo departure_schedule_id
     */
    public function getByDepartureScheduleId($scheduleId, $filters = [])
    {
        $sql = "SELECT bd.*, 
                       b.id as booking_id, b.booking_date, b.status as booking_status, b.total_price,
                       b.deposit_amount, b.user_id,
                       t.name as tour_name, t.code as tour_code, t.departure_location, t.destination,
                       u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone,
                       ds.id as departure_schedule_id, ds.departure_date, ds.departure_time, ds.meeting_point
                FROM {$this->table} bd
                INNER JOIN bookings b ON bd.booking_id = b.id
                INNER JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON b.user_id = u.id
                INNER JOIN departure_schedules ds ON b.departure_schedule_id = ds.id
                WHERE ds.id = :schedule_id";

        $params = ['schedule_id' => $scheduleId];

        // Lọc theo trạng thái booking
        if (!empty($filters['booking_status'])) {
            $sql .= " AND b.status = :booking_status";
            $params['booking_status'] = $filters['booking_status'];
        } else {
            $sql .= " AND b.status IN ('confirmed', 'deposit', 'completed')";
        }

        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $sql .= " AND bd.fullname LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY b.booking_date ASC, bd.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Đếm số khách theo tour_id
     */
    public function countByTourId($tourId, $filters = [])
    {
        $sql = "SELECT COUNT(bd.id) as total
                FROM {$this->table} bd
                INNER JOIN bookings b ON bd.booking_id = b.id
                WHERE b.tour_id = :tour_id";

        $params = ['tour_id' => $tourId];

        if (!empty($filters['booking_status'])) {
            $sql .= " AND b.status = :booking_status";
            $params['booking_status'] = $filters['booking_status'];
        } else {
            $sql .= " AND b.status IN ('confirmed', 'deposit', 'completed')";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Đếm số khách theo departure_schedule_id
     */
    public function countByDepartureScheduleId($scheduleId, $filters = [])
    {
        $sql = "SELECT COUNT(bd.id) as total
                FROM {$this->table} bd
                INNER JOIN bookings b ON bd.booking_id = b.id
                WHERE b.departure_schedule_id = :schedule_id";

        $params = ['schedule_id' => $scheduleId];

        if (!empty($filters['booking_status'])) {
            $sql .= " AND b.status = :booking_status";
            $params['booking_status'] = $filters['booking_status'];
        } else {
            $sql .= " AND b.status IN ('confirmed', 'deposit', 'completed')";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Lấy thông tin chi tiết một khách hàng
     */
    public function getCustomerDetail($bookingDetailId)
    {
        $sql = "SELECT bd.*, 
                       b.id as booking_id, b.booking_date, b.status as booking_status, b.total_price,
                       b.deposit_amount, b.user_id,
                       t.name as tour_name, t.code as tour_code, t.departure_location, t.destination,
                       u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone,
                       ds.id as departure_schedule_id, ds.departure_date, ds.departure_time, ds.meeting_point
                FROM {$this->table} bd
                INNER JOIN bookings b ON bd.booking_id = b.id
                INNER JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON b.user_id = u.id
                LEFT JOIN departure_schedules ds ON b.departure_schedule_id = ds.id
                WHERE bd.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $bookingDetailId]);
        return $stmt->fetch();
    }
}

