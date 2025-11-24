<?php

class BookingModel extends BaseModel
{
    protected $table = 'bookings';

    /**
     * Lấy tất cả bookings
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT b.*, t.name as tour_name, t.code as tour_code, 
                       u.full_name as customer_name, u.email as customer_email
                FROM {$this->table} b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND b.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['tour_id'])) {
            $sql .= " AND b.tour_id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND b.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (t.name LIKE :search OR t.code LIKE :search OR u.full_name LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY b.booking_date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy booking theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT b.*, t.name as tour_name, t.code as tour_code, t.duration, t.departure_location, t.destination,
                       t.max_participants, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
                FROM {$this->table} b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo booking mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (user_id, tour_id, booking_date, total_price, status) 
                VALUES 
                (:user_id, :tour_id, :booking_date, :total_price, :status)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'user_id' => $data['user_id'] ?? null,
            'tour_id' => $data['tour_id'],
            'booking_date' => $data['booking_date'] ?? date('Y-m-d H:i:s'),
            'total_price' => $data['total_price'],
            'status' => $data['status'] ?? 'pending'
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }
    //   Cập nhật booking

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                status = :status,
                total_price = :total_price
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $data['status'],
            'total_price' => $data['total_price'] ?? null
        ]);
    }

    /**
     * Cập nhật trạng thái booking (và lưu lịch sử)
     */
    public function updateStatus($id, $newStatus, $changedBy = null, $depositAmount = null, $changeReason = null, $notes = null)
    {
        // Lấy trạng thái hiện tại
        $booking = $this->findById($id);
        if (!$booking) {
            return false;
        }

        $oldStatus = $booking['status'];

        // Nếu trạng thái không thay đổi
        if ($oldStatus === $newStatus) {
            return true;
        }

        // Cập nhật trạng thái
        $sql = "UPDATE {$this->table} SET 
                status = :status";

        $params = [
            'id' => $id,
            'status' => $newStatus
        ];

        // Cập nhật deposit_amount nếu có
        if ($depositAmount !== null) {
            $sql .= ", deposit_amount = :deposit_amount";
            $params['deposit_amount'] = $depositAmount;
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        // Lưu lịch sử thay đổi
        if ($result) {
            $historyModel = new BookingStatusHistoryModel();
            $historyModel->create([
                'booking_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $changedBy,
                'change_reason' => $changeReason,
                'notes' => $notes
            ]);
        }

        return $result;
    }

    /**
     * Lấy danh sách trạng thái hợp lệ
     */
    public static function getStatuses()
    {
        return [
            'pending' => 'Chờ xác nhận',
            'deposit' => 'Đã cọc',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Hủy'
        ];
    }

    /**
     * Kiểm tra có thể chuyển từ trạng thái này sang trạng thái khác không
     */
    public static function canChangeStatus($fromStatus, $toStatus)
    {
        // Quy tắc chuyển trạng thái
        $allowedTransitions = [
            'pending' => ['deposit', 'confirmed', 'cancelled'],
            'deposit' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'completed' => [], // Không thể chuyển từ hoàn tất
            'cancelled' => [] // Không thể chuyển từ hủy
        ];

        return in_array($toStatus, $allowedTransitions[$fromStatus] ?? []);
    }

    /**
     * Xóa booking
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Đếm số lượng booking theo tour_id
     */
    public function countByTourId($tourId, $status = 'confirmed')
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE tour_id = :tour_id AND status = :status";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId, 'status' => $status]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Đếm số người đã đặt tour
     */
    public function countParticipantsByTourId($tourId, $status = 'confirmed')
    {
        $sql = "SELECT COUNT(bd.id) as total 
                FROM {$this->table} b
                INNER JOIN booking_details bd ON b.id = bd.booking_id
                WHERE b.tour_id = :tour_id AND b.status = :status";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId, 'status' => $status]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Kiểm tra chỗ trống còn lại
     */
    public function checkAvailableSlots($tourId)
    {
        // Lấy thông tin tour
        $tourModel = new TourModel();
        $tour = $tourModel->findById($tourId);

        if (!$tour) {
            return false;
        }

        $maxParticipants = $tour['max_participants'] ?? null;

        // Nếu không giới hạn số người
        if ($maxParticipants === null) {
            return true;
        }

        // Đếm số người đã đặt
        $bookedParticipants = $this->countParticipantsByTourId($tourId);

        // Tính số chỗ còn lại
        $availableSlots = $maxParticipants - $bookedParticipants;

        return [
            'available' => $availableSlots > 0,
            'available_slots' => $availableSlots,
            'max_participants' => $maxParticipants,
            'booked_participants' => $bookedParticipants
        ];
    }
}
