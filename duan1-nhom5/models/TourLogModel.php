<?php

class TourLogModel extends BaseModel
{
    protected $table = 'tour_logs';

    /**
     * Get logs by tour ID
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT tl.*, u.full_name as guide_name 
                FROM {$this->table} tl 
                LEFT JOIN users u ON tl.guide_id = u.id 
                WHERE tl.tour_id = :tour_id 
                ORDER BY tl.log_time DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Get log by ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Create new log
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (tour_id, guide_id, log_time, content, image) 
                VALUES (:tour_id, :guide_id, :log_time, :content, :image)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'tour_id' => $data['tour_id'],
            'guide_id' => $data['guide_id'],
            'log_time' => $data['log_time'],
            'content' => $data['content'],
            'image' => $data['image'] ?? null
        ]);
    }

    /**
     * Update log
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                log_time = :log_time, 
                content = :content, 
                image = :image 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'log_time' => $data['log_time'],
            'content' => $data['content'],
            'image' => $data['image']
        ]);
    }

    /**
     * Delete log
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
