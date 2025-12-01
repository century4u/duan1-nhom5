<?php

class TourPriceModel extends BaseModel
{
    protected $table = 'tour_prices';

    /**
     * Lấy giá theo tour_id
     */
    public function getByTourId($tourId, $date = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id AND is_active = 1";
        $params = ['tour_id' => $tourId];

        if ($date) {
            $sql .= " AND (start_date IS NULL OR start_date <= :date) AND (end_date IS NULL OR end_date >= :date)";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY price_type, start_date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy giá theo loại
     */
    public function getByType($tourId, $priceType, $date = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE tour_id = :tour_id AND price_type = :price_type AND is_active = 1";
        $params = ['tour_id' => $tourId, 'price_type' => $priceType];

        if ($date) {
            $sql .= " AND (start_date IS NULL OR start_date <= :date) AND (end_date IS NULL OR end_date >= :date)";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY start_date DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Tạo giá mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, price_type, price, currency, start_date, end_date, min_quantity, max_quantity, description, is_active) 
                VALUES 
                (:tour_id, :price_type, :price, :currency, :start_date, :end_date, :min_quantity, :max_quantity, :description, :is_active)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'price_type' => $data['price_type'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'VND',
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'min_quantity' => $data['min_quantity'] ?? 1,
            'max_quantity' => $data['max_quantity'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? 1
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
                price = :price,
                currency = :currency,
                start_date = :start_date,
                end_date = :end_date,
                min_quantity = :min_quantity,
                max_quantity = :max_quantity,
                description = :description,
                is_active = :is_active
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'price_type' => $data['price_type'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'VND',
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'min_quantity' => $data['min_quantity'] ?? 1,
            'max_quantity' => $data['max_quantity'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? 1
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

