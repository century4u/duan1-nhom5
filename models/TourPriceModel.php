<?php

class TourPriceModel extends BaseModel
{
    protected $table = 'tour_prices';

    /**
     * Lấy giá theo tour_id
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id ORDER BY price_type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy giá theo loại
     */
    public function getByType($tourId, $priceType)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE tour_id = :tour_id AND price_type = :price_type 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId, 'price_type' => $priceType]);
        return $stmt->fetch();
    }

    /**
     * Tạo giá mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, price_type, price) 
                VALUES 
                (:tour_id, :price_type, :price)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'price_type' => $data['price_type'],
            'price' => $data['price']
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật giá
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                price_type = :price_type,
                price = :price
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'price_type' => $data['price_type'],
            'price' => $data['price']
        ]);
    }

    /**
     * Xóa giá
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
