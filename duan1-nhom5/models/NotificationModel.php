<?php

class NotificationModel extends BaseModel
{
    protected $table = 'schedule_notifications';

    /**
     * Tạo thông báo mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (schedule_id, assignment_id, notification_type, recipient_type,
                 recipient_id, recipient_name, recipient_email, recipient_phone,
                 subject, message, status) 
                VALUES 
                (:schedule_id, :assignment_id, :notification_type, :recipient_type,
                 :recipient_id, :recipient_name, :recipient_email, :recipient_phone,
                 :subject, :message, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'schedule_id' => $data['schedule_id'],
            'assignment_id' => $data['assignment_id'] ?? null,
            'notification_type' => $data['notification_type'],
            'recipient_type' => $data['recipient_type'],
            'recipient_id' => $data['recipient_id'],
            'recipient_name' => $data['recipient_name'],
            'recipient_email' => $data['recipient_email'] ?? null,
            'recipient_phone' => $data['recipient_phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => $data['status'] ?? 'pending'
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Đánh dấu đã gửi
     */
    public function markAsSent($id)
    {
        $sql = "UPDATE {$this->table} SET 
                status = 'sent',
                sent_at = NOW()
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Đánh dấu đã đọc
     */
    public function markAsRead($id)
    {
        $sql = "UPDATE {$this->table} SET 
                status = 'read',
                read_at = NOW()
                WHERE id = :id AND status = 'sent'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy thông báo chưa gửi
     */
    public function getPendingNotifications()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'pending'
                ORDER BY created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Gửi email thông báo (placeholder - cần implement email service)
     */
    public function sendEmail($notification)
    {
        // TODO: Implement email sending
        // Có thể dùng PHPMailer, SwiftMailer, hoặc service khác
        
        // Tạm thời chỉ đánh dấu là đã gửi
        $this->markAsSent($notification['id']);
        
        return true;
    }
}

