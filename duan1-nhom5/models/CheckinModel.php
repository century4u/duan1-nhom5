<?php

class CheckinModel extends BaseModel
{
    protected $table = 'checkins';

    /**
     * Tạo bản ghi check-in
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (booking_detail_id, departure_schedule_id, status, checkin_time, notes, checked_by) 
                VALUES 
                (:booking_detail_id, :departure_schedule_id, :status, :checkin_time, :notes, :checked_by)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'booking_detail_id' => $data['booking_detail_id'],
            'departure_schedule_id' => $data['departure_schedule_id'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'checkin_time' => $data['checkin_time'] ?? date('Y-m-d H:i:s'),
            'notes' => $data['notes'] ?? null,
            'checked_by' => $data['checked_by'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Lấy thông tin check-in theo booking_detail_id
     */
    public function getByBookingDetailId($bookingDetailId)
    {
        $sql = "SELECT c.*, 
                       bd.fullname, bd.gender, bd.birthdate,
                       b.id as booking_id, b.tour_id,
                       u.full_name as checked_by_name
                FROM {$this->table} c
                INNER JOIN booking_details bd ON c.booking_detail_id = bd.id
                INNER JOIN bookings b ON bd.booking_id = b.id
                LEFT JOIN users u ON c.checked_by = u.id
                WHERE c.booking_detail_id = :booking_detail_id
                ORDER BY c.checkin_time DESC
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_detail_id' => $bookingDetailId]);
        return $stmt->fetch();
    }

    /**
     * Lấy danh sách check-in theo tour_id
     */
    public function getByTourId($tourId, $filters = [])
    {
        $sql = "SELECT c.*, 
                       bd.id as booking_detail_id, bd.fullname, bd.gender, bd.birthdate,
                       b.id as booking_id, b.tour_id, b.status as booking_status,
                       t.name as tour_name, t.code as tour_code,
                       u.full_name as checked_by_name
                FROM {$this->table} c
                INNER JOIN booking_details bd ON c.booking_detail_id = bd.id
                INNER JOIN bookings b ON bd.booking_id = b.id
                INNER JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON c.checked_by = u.id
                WHERE b.tour_id = :tour_id";

        $params = ['tour_id' => $tourId];

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['departure_schedule_id'])) {
            $sql .= " AND c.departure_schedule_id = :departure_schedule_id";
            $params['departure_schedule_id'] = $filters['departure_schedule_id'];
        }

        $sql .= " ORDER BY c.checkin_time DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách check-in theo departure_schedule_id
     */
    public function getByDepartureScheduleId($scheduleId, $filters = [])
    {
        $sql = "SELECT c.*, 
                       bd.id as booking_detail_id, bd.fullname, bd.gender, bd.birthdate,
                       b.id as booking_id, b.tour_id, b.status as booking_status,
                       t.name as tour_name, t.code as tour_code,
                       u.full_name as checked_by_name
                FROM {$this->table} c
                INNER JOIN booking_details bd ON c.booking_detail_id = bd.id
                INNER JOIN bookings b ON bd.booking_id = b.id
                INNER JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON c.checked_by = u.id
                WHERE c.departure_schedule_id = :schedule_id";

        $params = ['schedule_id' => $scheduleId];

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY c.checkin_time DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cập nhật trạng thái check-in
     */
    public function updateStatus($id, $status, $notes = null, $checkedBy = null)
    {
        $sql = "UPDATE {$this->table} SET 
                status = :status,
                checkin_time = :checkin_time,
                notes = :notes,
                checked_by = :checked_by
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
            'checkin_time' => date('Y-m-d H:i:s'),
            'notes' => $notes,
            'checked_by' => $checkedBy
        ]);
    }

    /**
     * Lấy danh sách trạng thái check-in
     */
    public static function getStatuses()
    {
        return [
            'pending' => 'Chưa đến',
            'checked_in' => 'Đã đến',
            'absent' => 'Vắng mặt',
            'late' => 'Đến muộn',
            'cancelled' => 'Hủy'
        ];
    }

    /**
     * Kiểm tra đã check-in chưa
     */
    public function hasCheckedIn($bookingDetailId, $departureScheduleId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE booking_detail_id = :booking_detail_id 
                AND status IN ('checked_in', 'late')";
        
        $params = ['booking_detail_id' => $bookingDetailId];

        if ($departureScheduleId) {
            $sql .= " AND departure_schedule_id = :departure_schedule_id";
            $params['departure_schedule_id'] = $departureScheduleId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return ($result['total'] ?? 0) > 0;
    }
}

