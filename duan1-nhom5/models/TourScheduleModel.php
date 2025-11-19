<?php

class TourScheduleModel extends BaseModel
{
    protected $table = 'tour_schedules';

    /**
     * Lấy lịch trình theo tour_id
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tour_id = :tour_id ORDER BY day_number ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy lịch trình theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo lịch trình mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, day_number, date, title, description, activities, meals, accommodation, transport) 
                VALUES 
                (:tour_id, :day_number, :date, :title, :description, :activities, :meals, :accommodation, :transport)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'day_number' => $data['day_number'],
            'date' => $data['date'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'activities' => !empty($data['activities']) ? json_encode($data['activities']) : null,
            'meals' => $data['meals'] ?? null,
            'accommodation' => $data['accommodation'] ?? null,
            'transport' => $data['transport'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật lịch trình
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                day_number = :day_number,
                date = :date,
                title = :title,
                description = :description,
                activities = :activities,
                meals = :meals,
                accommodation = :accommodation,
                transport = :transport
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'day_number' => $data['day_number'],
            'date' => $data['date'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'activities' => !empty($data['activities']) ? json_encode($data['activities']) : null,
            'meals' => $data['meals'] ?? null,
            'accommodation' => $data['accommodation'] ?? null,
            'transport' => $data['transport'] ?? null
        ]);
    }

    /**
     * Xóa lịch trình
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tất cả lịch trình của tour
     */
    public function deleteByTourId($tourId)
    {
        $sql = "DELETE FROM {$this->table} WHERE tour_id = :tour_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['tour_id' => $tourId]);
    }
}

