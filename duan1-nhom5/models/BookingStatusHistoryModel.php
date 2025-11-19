<?php

class BookingStatusHistoryModel extends BaseModel
{
    protected $table = 'booking_status_history';

    /**
     * Lấy lịch sử thay đổi trạng thái theo booking_id
     */
    public function getByBookingId($bookingId)
    {
        $sql = "SELECT h.*, u.full_name as changed_by_name, u.email as changed_by_email
                FROM {$this->table} h
                LEFT JOIN users u ON h.changed_by = u.id
                WHERE h.booking_id = :booking_id
                ORDER BY h.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy lịch sử thay đổi trạng thái theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT h.*, u.full_name as changed_by_name
                FROM {$this->table} h
                LEFT JOIN users u ON h.changed_by = u.id
                WHERE h.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo bản ghi lịch sử thay đổi trạng thái
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (booking_id, old_status, new_status, changed_by, change_reason, notes) 
                VALUES 
                (:booking_id, :old_status, :new_status, :changed_by, :change_reason, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'booking_id' => $data['booking_id'],
            'old_status' => $data['old_status'] ?? null,
            'new_status' => $data['new_status'],
            'changed_by' => $data['changed_by'] ?? null,
            'change_reason' => $data['change_reason'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Lấy trạng thái hiện tại của booking
     */
    public function getCurrentStatus($bookingId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE booking_id = :booking_id 
                ORDER BY created_at DESC 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return $stmt->fetch();
    }

    /**
     * Đếm số lần thay đổi trạng thái
     */
    public function countByBookingId($bookingId)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE booking_id = :booking_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}

