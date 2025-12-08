<?php

class TourPolicyModel extends BaseModel
{
    protected $table = 'tour_policies';

    /**
     * Lấy chính sách theo tour_id
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id ORDER BY policy_type";
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
                WHERE tour_id = :tour_id AND policy_type = :policy_type";
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
                (tour_id, policy_type, content) 
                VALUES 
                (:tour_id, :policy_type, :content)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'policy_type' => $data['policy_type'] ?? null,
            'content' => $data['content']
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
                content = :content
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'policy_type' => $data['policy_type'] ?? null,
            'content' => $data['content']
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
