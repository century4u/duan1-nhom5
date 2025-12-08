<?php

class DepartureScheduleModel extends BaseModel
{
    protected $table = 'departure_schedules';

    /**
     * Lấy tất cả lịch khởi hành
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT ds.*, t.name as tour_name, t.code as tour_code, 
                       t.duration, t.departure_location, t.destination
                FROM {$this->table} ds
                LEFT JOIN tours t ON ds.tour_id = t.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['tour_id'])) {
            $sql .= " AND ds.tour_id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND ds.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['departure_date_from'])) {
            $sql .= " AND ds.departure_date >= :departure_date_from";
            $params['departure_date_from'] = $filters['departure_date_from'];
        }

        if (!empty($filters['departure_date_to'])) {
            $sql .= " AND ds.departure_date <= :departure_date_to";
            $params['departure_date_to'] = $filters['departure_date_to'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (t.name LIKE :search OR t.code LIKE :search OR ds.meeting_point LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY ds.departure_date DESC, ds.departure_time DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy lịch khởi hành theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT ds.*, t.name as tour_name, t.code as tour_code, 
                       t.duration, t.departure_location, t.destination,
                       t.max_participants as tour_max_participants
                FROM {$this->table} ds
                LEFT JOIN tours t ON ds.tour_id = t.id
                WHERE ds.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo lịch khởi hành mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, departure_date, departure_time, meeting_point, 
                 end_date, end_time, max_participants, current_participants, 
                 status, notes) 
                VALUES 
                (:tour_id, :departure_date, :departure_time, :meeting_point,
                 :end_date, :end_time, :max_participants, :current_participants,
                 :status, :notes)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'departure_date' => $data['departure_date'],
            'departure_time' => $data['departure_time'],
            'meeting_point' => $data['meeting_point'],
            'end_date' => $data['end_date'],
            'end_time' => $data['end_time'],
            'max_participants' => $data['max_participants'] ?? null,
            'current_participants' => $data['current_participants'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật lịch khởi hành
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                tour_id = :tour_id,
                departure_date = :departure_date,
                departure_time = :departure_time,
                meeting_point = :meeting_point,
                end_date = :end_date,
                end_time = :end_time,
                max_participants = :max_participants,
                current_participants = :current_participants,
                status = :status,
                notes = :notes
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'tour_id' => $data['tour_id'],
            'departure_date' => $data['departure_date'],
            'departure_time' => $data['departure_time'],
            'meeting_point' => $data['meeting_point'],
            'end_date' => $data['end_date'],
            'end_time' => $data['end_time'],
            'max_participants' => $data['max_participants'] ?? null,
            'current_participants' => $data['current_participants'] ?? 0,
            'status' => $data['status'] ?? 'draft',
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Xóa lịch khởi hành
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $newStatus, $oldStatus = null, $changedBy = null, $notes = null)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'id' => $id,
            'status' => $newStatus
        ]);

        if ($result) {
            // Lưu lịch sử thay đổi
            $historySql = "INSERT INTO schedule_status_history 
                          (schedule_id, old_status, new_status, changed_by, notes) 
                          VALUES (:schedule_id, :old_status, :new_status, :changed_by, :notes)";
            $historyStmt = $this->pdo->prepare($historySql);
            $historyStmt->execute([
                'schedule_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $changedBy,
                'notes' => $notes
            ]);
        }

        return $result;
    }

    /**
     * Lấy lịch sử thay đổi trạng thái
     */
    public function getStatusHistory($scheduleId)
    {
        $sql = "SELECT ssh.*, u.full_name as changed_by_name
                FROM schedule_status_history ssh
                LEFT JOIN users u ON ssh.changed_by = u.id
                WHERE ssh.schedule_id = :schedule_id
                ORDER BY ssh.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $scheduleId]);
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra xung đột lịch (HDV, tài xế, xe)
     */
    public function checkConflict($resourceId, $resourceType, $startDate, $endDate, $excludeScheduleId = null)
    {
        $sql = "SELECT ds.*, sa.assignment_type, sa.resource_id
                FROM {$this->table} ds
                INNER JOIN schedule_assignments sa ON ds.id = sa.schedule_id
                WHERE sa.resource_id = :resource_id
                  AND sa.assignment_type = :assignment_type
                  AND ds.status IN ('draft', 'confirmed', 'in_progress')
                  AND (
                      (ds.departure_date <= :start_date AND ds.end_date >= :start_date)
                      OR (ds.departure_date <= :end_date AND ds.end_date >= :end_date)
                      OR (ds.departure_date >= :start_date AND ds.end_date <= :end_date)
                  )";

        $params = [
            'resource_id' => $resourceId,
            'assignment_type' => $resourceType,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        if ($excludeScheduleId) {
            $sql .= " AND ds.id != :exclude_schedule_id";
            $params['exclude_schedule_id'] = $excludeScheduleId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy lịch khởi hành sắp tới (cho booking)
     */
    public function getUpcoming($limit = 500)
    {
        $sql = "SELECT ds.*, t.name as tour_name, t.code as tour_code, 
                       t.price, t.duration, t.max_participants, t.destination
                FROM {$this->table} ds
                INNER JOIN tours t ON ds.tour_id = t.id
                WHERE ds.departure_date >= CURDATE()
                  AND ds.status = 'confirmed'
                ORDER BY ds.departure_date ASC, ds.departure_time ASC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách HDV được phân cho 1 lịch
     */
    public function getAssignedGuides($scheduleId)
    {
        $sql = "SELECT g.*, sa.id as assignment_id, sa.status as assignment_status,
                       sa.notes as assignment_notes
                FROM schedule_assignments sa
                INNER JOIN guides g ON sa.resource_id = g.id
                WHERE sa.schedule_id = :schedule_id
                  AND sa.assignment_type = 'guide'
                  AND sa.status IN ('confirmed', 'pending')
                ORDER BY g.full_name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $scheduleId]);
        return $stmt->fetchAll();
    }

    /**
     * Đếm số booking theo guide cho 1 lịch
     */
    public function countBookingsByGuide($scheduleId, $guideId)
    {
        $sql = "SELECT COUNT(*) as count
                FROM bookings
                WHERE departure_schedule_id = :schedule_id
                  AND guide_id = :guide_id
                  AND status NOT IN ('cancelled')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'schedule_id' => $scheduleId,
            'guide_id' => $guideId
        ]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    /**
     * Lấy số lượng booking cho lịch khởi hành
     */
    public function getBookingCount($scheduleId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM bookings 
                WHERE departure_schedule_id = :schedule_id 
                  AND status NOT IN ('cancelled')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $scheduleId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    /**
     * Lấy danh sách booking chưa được xếp lịch của tour
     */
    public function getAvailableBookings($tourId)
    {
        $sql = "SELECT b.*, u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.tour_id = :tour_id 
                  AND b.departure_schedule_id IS NULL 
                  AND b.status IN ('deposit', 'confirmed')"; // Chỉ lấy booking đã cọc hoặc xác nhận

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Thêm bookings vào lịch khởi hành
     */
    public function addBookings($scheduleId, $bookingIds)
    {
        if (empty($bookingIds)) {
            return false;
        }

        // Lấy thông tin lịch để update current_participants
        $schedule = $this->findById($scheduleId);
        if (!$schedule) {
            return false;
        }

        // Tạo placeholder cho câu lệnh IN
        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));

        $sql = "UPDATE bookings SET departure_schedule_id = ? 
                WHERE id IN ($placeholders)";

        $params = array_merge([$scheduleId], $bookingIds);

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            // Cập nhật số lượng người tham gia hiện tại
            $this->updateCurrentParticipants($scheduleId);
        }

        return $result;
    }

    /**
     * Cập nhật số lượng người tham gia hiện tại của lịch
     */
    public function updateCurrentParticipants($scheduleId)
    {
        // Tính tổng số người từ các booking đã gán
        $sql = "SELECT COUNT(bd.id) as total_participants
                FROM bookings b
                JOIN booking_details bd ON b.id = bd.booking_id
                WHERE b.departure_schedule_id = :schedule_id 
                  AND b.status NOT IN ('cancelled')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $scheduleId]);
        $result = $stmt->fetch();
        $total = $result['total_participants'] ?? 0;

        // Update vào bảng departure_schedules
        $updateSql = "UPDATE {$this->table} SET current_participants = :total WHERE id = :id";
        $updateStmt = $this->pdo->prepare($updateSql);
        return $updateStmt->execute([
            'total' => $total,
            'id' => $scheduleId
        ]);
    }

    /**
     * Lấy danh sách lịch trình theo Guide ID
     */
    public function getSchedulesByGuideId($guideId)
    {
        $sql = "SELECT ds.*, t.name as tour_name, t.code as tour_code, t.destination, sa.status as assignment_status
                FROM {$this->table} ds
                INNER JOIN schedule_assignments sa ON ds.id = sa.schedule_id
                INNER JOIN tours t ON ds.tour_id = t.id
                WHERE sa.resource_id = :guide_id
                  AND sa.assignment_type = 'guide'
                  AND sa.status IN ('confirmed', 'pending')
                  AND ds.status NOT IN ('cancelled')
                ORDER BY ds.departure_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guide_id' => $guideId]);
        return $stmt->fetchAll();
    }
}
