<?php

class RoomAssignmentModel extends BaseModel
{
    protected $table = 'room_assignments';

    /**
     * Tạo phân phòng
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (booking_detail_id, booking_id, departure_schedule_id, hotel_name, room_number, 
                 room_type, bed_type, checkin_date, checkout_date, notes) 
                VALUES 
                (:booking_detail_id, :booking_id, :departure_schedule_id, :hotel_name, :room_number,
                 :room_type, :bed_type, :checkin_date, :checkout_date, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'booking_detail_id' => $data['booking_detail_id'] ?? null,
            'booking_id' => $data['booking_id'] ?? null,
            'departure_schedule_id' => $data['departure_schedule_id'] ?? null,
            'hotel_name' => $data['hotel_name'],
            'room_number' => $data['room_number'],
            'room_type' => $data['room_type'] ?? 'standard',
            'bed_type' => $data['bed_type'] ?? 'double',
            'checkin_date' => $data['checkin_date'],
            'checkout_date' => $data['checkout_date'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Lấy danh sách phân phòng theo tour_id
     */
    public function getByTourId($tourId, $filters = [])
    {
        $sql = "SELECT ra.*, 
                       bd.id as booking_detail_id, bd.fullname, bd.gender, bd.birthdate,
                       b.id as booking_id, b.tour_id,
                       t.name as tour_name, t.code as tour_code
                FROM {$this->table} ra
                INNER JOIN bookings b ON ra.booking_id = b.id
                LEFT JOIN booking_details bd ON ra.booking_detail_id = bd.id
                INNER JOIN tours t ON b.tour_id = t.id
                WHERE b.tour_id = :tour_id";

        $params = ['tour_id' => $tourId];

        if (!empty($filters['departure_schedule_id'])) {
            $sql .= " AND ra.departure_schedule_id = :departure_schedule_id";
            $params['departure_schedule_id'] = $filters['departure_schedule_id'];
        }

        if (!empty($filters['hotel_name'])) {
            $sql .= " AND ra.hotel_name LIKE :hotel_name";
            $params['hotel_name'] = '%' . $filters['hotel_name'] . '%';
        }

        $sql .= " ORDER BY ra.checkin_date ASC, ra.room_number ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách phân phòng theo departure_schedule_id
     */
    public function getByDepartureScheduleId($scheduleId, $filters = [])
    {
        $sql = "SELECT ra.*, 
                       bd.id as booking_detail_id, bd.fullname, bd.gender, bd.birthdate,
                       b.id as booking_id, b.tour_id,
                       t.name as tour_name, t.code as tour_code
                FROM {$this->table} ra
                INNER JOIN bookings b ON ra.booking_id = b.id
                LEFT JOIN booking_details bd ON ra.booking_detail_id = bd.id
                INNER JOIN tours t ON b.tour_id = t.id
                WHERE ra.departure_schedule_id = :schedule_id";

        $params = ['schedule_id' => $scheduleId];

        if (!empty($filters['hotel_name'])) {
            $sql .= " AND ra.hotel_name LIKE :hotel_name";
            $params['hotel_name'] = '%' . $filters['hotel_name'] . '%';
        }

        $sql .= " ORDER BY ra.checkin_date ASC, ra.room_number ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy phân phòng theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT ra.*, 
                       bd.id as booking_detail_id, bd.fullname, bd.gender, bd.birthdate,
                       b.id as booking_id, b.tour_id,
                       t.name as tour_name, t.code as tour_code
                FROM {$this->table} ra
                INNER JOIN bookings b ON ra.booking_id = b.id
                LEFT JOIN booking_details bd ON ra.booking_detail_id = bd.id
                INNER JOIN tours t ON b.tour_id = t.id
                WHERE ra.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Cập nhật phân phòng
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                booking_detail_id = :booking_detail_id,
                hotel_name = :hotel_name,
                room_number = :room_number,
                room_type = :room_type,
                bed_type = :bed_type,
                checkin_date = :checkin_date,
                checkout_date = :checkout_date,
                notes = :notes
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'booking_detail_id' => $data['booking_detail_id'] ?? null,
            'hotel_name' => $data['hotel_name'],
            'room_number' => $data['room_number'],
            'room_type' => $data['room_type'] ?? 'standard',
            'bed_type' => $data['bed_type'] ?? 'double',
            'checkin_date' => $data['checkin_date'],
            'checkout_date' => $data['checkout_date'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Xóa phân phòng
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy danh sách phòng đã được phân phối (để kiểm tra trùng)
     */
    public function getAssignedRooms($hotelName, $checkinDate, $checkoutDate, $excludeId = null)
    {
        $sql = "SELECT room_number, COUNT(*) as count
                FROM {$this->table}
                WHERE hotel_name = :hotel_name
                AND (
                    (checkin_date <= :checkin_date AND (checkout_date IS NULL OR checkout_date >= :checkin_date))
                    OR (checkin_date <= :checkout_date AND (checkout_date IS NULL OR checkout_date >= :checkout_date))
                    OR (checkin_date >= :checkin_date AND (checkout_date IS NULL OR checkout_date <= :checkout_date))
                )";

        $params = [
            'hotel_name' => $hotelName,
            'checkin_date' => $checkinDate,
            'checkout_date' => $checkoutDate ?? $checkinDate
        ];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " GROUP BY room_number";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách loại phòng
     */
    public static function getRoomTypes()
    {
        return [
            'standard' => 'Phòng tiêu chuẩn',
            'superior' => 'Phòng cao cấp',
            'deluxe' => 'Phòng deluxe',
            'suite' => 'Suite',
            'villa' => 'Villa'
        ];
    }

    /**
     * Lấy danh sách loại giường
     */
    public static function getBedTypes()
    {
        return [
            'single' => 'Giường đơn',
            'double' => 'Giường đôi',
            'twin' => '2 giường đơn',
            'triple' => '3 giường',
            'family' => 'Gia đình'
        ];
    }

    /**
     * Lấy phân phòng theo booking_detail_id
     */
    public function getByBookingDetailId($bookingDetailId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE booking_detail_id = :booking_detail_id
                ORDER BY checkin_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_detail_id' => $bookingDetailId]);
        return $stmt->fetchAll();
    }
}

