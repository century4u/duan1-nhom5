<?php

class TourPolicyModel extends BaseModel
{
    protected $table = 'tour_policies';

    /**
     * Lấy chính sách theo tour_id
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id AND is_active = 1 ORDER BY policy_type, sort_order";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chính sách theo loại
     */
    public function getByType($tourId, $policyType)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE tour_id = :tour_id AND policy_type = :policy_type AND is_active = 1 
                ORDER BY sort_order";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId, 'policy_type' => $policyType]);
        return $stmt->fetchAll();
    }

    /**
     * Tạo chính sách mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, policy_type, title, content, days_before, refund_percentage, sort_order, is_active) 
                VALUES 
                (:tour_id, :policy_type, :title, :content, :days_before, :refund_percentage, :sort_order, :is_active)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'policy_type' => $data['policy_type'],
            'title' => $data['title'],
            'content' => $data['content'],
            'days_before' => $data['days_before'] ?? null,
            'refund_percentage' => $data['refund_percentage'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật chính sách
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                policy_type = :policy_type,
                title = :title,
                content = :content,
                days_before = :days_before,
                refund_percentage = :refund_percentage,
                sort_order = :sort_order,
                is_active = :is_active
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'policy_type' => $data['policy_type'],
            'title' => $data['title'],
            'content' => $data['content'],
            'days_before' => $data['days_before'] ?? null,
            'refund_percentage' => $data['refund_percentage'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? 1
        ]);
    }

    /**
     * Xóa chính sách
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}

