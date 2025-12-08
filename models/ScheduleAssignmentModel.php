<?php

class ScheduleAssignmentModel extends BaseModel
{
    protected $table = 'schedule_assignments';

    /**
     * Lấy tất cả phân bổ theo departure_schedule_id
     */
    public function getByScheduleId($scheduleId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE departure_schedule_id = :schedule_id 
                ORDER BY created_at";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $scheduleId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy phân bổ theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo phân bổ mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (departure_schedule_id, guide_id, vehicle_id, driver_name, notes) 
                VALUES 
                (:departure_schedule_id, :guide_id, :vehicle_id, :driver_name, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'departure_schedule_id' => $data['departure_schedule_id'],
            'guide_id' => $data['guide_id'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'driver_name' => $data['driver_name'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật phân bổ
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                guide_id = :guide_id,
                vehicle_id = :vehicle_id,
                driver_name = :driver_name,
                notes = :notes
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'guide_id' => $data['guide_id'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'driver_name' => $data['driver_name'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Xóa phân bổ
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tất cả phân bổ của một schedule
     */
    public function deleteByScheduleId($scheduleId)
    {
        $sql = "DELETE FROM {$this->table} WHERE departure_schedule_id = :schedule_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['schedule_id' => $scheduleId]);
    }

    /**
     * Tạo nhiều phân bổ cùng lúc
     */
    public function createMultiple($assignments)
    {
        $results = [];
        foreach ($assignments as $assignment) {
            $results[] = $this->create($assignment);
        }
        return $results;
    }
}
