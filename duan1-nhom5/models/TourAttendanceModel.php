<?php

class TourAttendanceModel extends BaseModel
{
    protected $table = 'tour_attendances';

    /**
     * Lấy danh sách khách kèm trạng thái điểm danh cho một lịch trình cụ thể
     */
    public function getGuestListWithStatus($departureScheduleId, $tourScheduleId, $tourId = null)
    {
        // Xây dựng câu query
        $sql = "SELECT bd.id as booking_detail_id, bd.fullname, bd.gender, bd.birthdate,
                       b.id as booking_id, b.contact_phone, b.status as booking_status,
                       ta.status, ta.note, ta.created_at, ta.id as attendance_id,
                       u.full_name as updated_by_name
                FROM booking_details bd
                JOIN bookings b ON bd.booking_id = b.id
                LEFT JOIN {$this->table} ta ON ta.booking_detail_id = bd.id 
                    AND ta.tour_schedule_id = :tour_schedule_id";

        // Add condition for attendance join if departure_schedule_id is present
        if ($departureScheduleId) {
            $sql .= " AND ta.departure_schedule_id = :departure_schedule_id";
        }

        $sql .= " LEFT JOIN users u ON ta.updated_by = u.id
                  WHERE 1=1"; // Removed filter b.status != 'cancelled'

        $params = ['tour_schedule_id' => $tourScheduleId];
        if ($departureScheduleId) {
            $params['departure_schedule_id'] = $departureScheduleId;
        }

        // Ưu tiên filter theo departure_schedule_id nếu có
        if ($departureScheduleId) {
            $sql .= " AND (b.departure_schedule_id = :ds_id OR (b.departure_schedule_id IS NULL AND b.tour_id = :tour_id))";
            $params['ds_id'] = $departureScheduleId;
            $params['tour_id'] = $tourId; // Fallback for bookings without ds_id but matching tour
        } elseif ($tourId) {
            $sql .= " AND b.tour_id = :tour_id";
            $params['tour_id'] = $tourId;
        }

        $sql .= " ORDER BY b.id, bd.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Cập nhật hoặc tạo mới trạng thái điểm danh
     */
    public function saveAttendance($data)
    {
        // Kiểm tra xem record đã tồn tại chưa
        $checkSql = "SELECT id FROM {$this->table} 
                     WHERE booking_detail_id = :booking_detail_id 
                     AND departure_schedule_id <=> :departure_schedule_id
                     AND tour_schedule_id = :tour_schedule_id";

        $stmt = $this->pdo->prepare($checkSql);
        $stmt->execute([
            'booking_detail_id' => $data['booking_detail_id'],
            'departure_schedule_id' => $data['departure_schedule_id'],
            'tour_schedule_id' => $data['tour_schedule_id']
        ]);

        $existing = $stmt->fetch();

        if ($existing) {
            // Update
            $sql = "UPDATE {$this->table} SET 
                    status = :status, 
                    note = :note, 
                    updated_by = :updated_by
                    WHERE id = :id";

            $updateStmt = $this->pdo->prepare($sql);
            return $updateStmt->execute([
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'updated_by' => $data['updated_by'],
                'id' => $existing['id']
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO {$this->table} 
                    (booking_detail_id, departure_schedule_id, tour_schedule_id, status, note, updated_by, created_at) 
                    VALUES 
                    (:booking_detail_id, :departure_schedule_id, :tour_schedule_id, :status, :note, :updated_by, NOW())";

            $insertStmt = $this->pdo->prepare($sql);
            return $insertStmt->execute([
                'booking_detail_id' => $data['booking_detail_id'],
                'departure_schedule_id' => $data['departure_schedule_id'],
                'tour_schedule_id' => $data['tour_schedule_id'],
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'updated_by' => $data['updated_by']
            ]);
        }
    }
}
