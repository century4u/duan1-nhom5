<?php

class TourImageModel extends BaseModel
{
    protected $table = 'tour_images';

    /**
     * Lấy tất cả hình ảnh theo tour_id
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id ORDER BY is_primary DESC, sort_order ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy ảnh chính của tour
     */
    public function getPrimaryImage($tourId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id AND is_primary = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetch();
    }

    /**
     * Thêm hình ảnh
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, image_url, image_path, caption, is_primary, sort_order) 
                VALUES 
                (:tour_id, :image_url, :image_path, :caption, :is_primary, :sort_order)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'image_url' => $data['image_url'],
            'image_path' => $data['image_path'],
            'caption' => $data['caption'] ?? null,
            'is_primary' => $data['is_primary'] ?? 0,
            'sort_order' => $data['sort_order'] ?? 0
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Xóa hình ảnh
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Đặt ảnh chính
     */
    public function setPrimary($tourId, $imageId)
    {
        // Bỏ đặt ảnh chính cho tất cả ảnh của tour
        $sql = "UPDATE {$this->table} SET is_primary = 0 WHERE tour_id = :tour_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);

        // Đặt ảnh chính cho ảnh được chọn
        $sql = "UPDATE {$this->table} SET is_primary = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $imageId]);
    }
}

