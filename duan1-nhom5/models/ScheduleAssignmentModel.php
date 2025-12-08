<?php

class ScheduleAssignmentModel extends BaseModel
{
    protected $table = 'schedule_assignments';

    /**
     * Lấy tất cả phân bổ theo schedule_id
     */
    public function getByScheduleId($scheduleId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE schedule_id = :schedule_id 
                ORDER BY assignment_type, start_date, start_time";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $scheduleId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy phân bổ theo loại
     */
    public function getByScheduleIdAndType($scheduleId, $assignmentType)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE schedule_id = :schedule_id 
                  AND assignment_type = :assignment_type
                ORDER BY start_date, start_time";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'schedule_id' => $scheduleId,
            'assignment_type' => $assignmentType
        ]);
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
                (schedule_id, assignment_type, resource_id, resource_name, 
                 resource_type, quantity, start_date, end_date, 
                 start_time, end_time, location, status, notes) 
                VALUES 
                (:schedule_id, :assignment_type, :resource_id, :resource_name,
                 :resource_type, :quantity, :start_date, :end_date,
                 :start_time, :end_time, :location, :status, :notes)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'schedule_id' => $data['schedule_id'],
            'assignment_type' => $data['assignment_type'],
            'resource_id' => $data['resource_id'],
            'resource_name' => $data['resource_name'],
            'resource_type' => $data['resource_type'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'location' => $data['location'] ?? null,
            'status' => $data['status'] ?? 'pending',
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
                resource_id = :resource_id,
                resource_name = :resource_name,
                resource_type = :resource_type,
                quantity = :quantity,
                start_date = :start_date,
                end_date = :end_date,
                start_time = :start_time,
                end_time = :end_time,
                location = :location,
                status = :status,
                notes = :notes
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'resource_id' => $data['resource_id'],
            'resource_name' => $data['resource_name'],
            'resource_type' => $data['resource_type'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'location' => $data['location'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Cập nhật trạng thái xác nhận
     */
    public function updateStatus($id, $status, $confirmationDate = null)
    {
        $sql = "UPDATE {$this->table} SET 
                status = :status,
                confirmation_date = :confirmation_date
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
            'confirmation_date' => $confirmationDate ?? date('Y-m-d H:i:s')
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
        $sql = "DELETE FROM {$this->table} WHERE schedule_id = :schedule_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['schedule_id' => $scheduleId]);
    }

    /**
     * Lấy thống kê phân bổ theo loại
     */
    public function getStatisticsByScheduleId($scheduleId)
    {
        $sql = "SELECT assignment_type, 
                       COUNT(*) as total,
                       SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                       SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                       SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                FROM {$this->table}
                WHERE schedule_id = :schedule_id
                GROUP BY assignment_type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $scheduleId]);
        return $stmt->fetchAll();
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

    /**
     * Lấy lịch được phân công cho HDV
     */
    public function getGuideSchedules($guideId, $filters = [])
    {
        $sql = "SELECT sa.*, ds.*, t.name as tour_name, t.code as tour_code, t.duration,
                       t.departure_location, t.destination
                FROM {$this->table} sa
                INNER JOIN departure_schedules ds ON sa.schedule_id = ds.id
                INNER JOIN tours t ON ds.tour_id = t.id
                WHERE sa.assignment_type = 'guide'
                  AND sa.resource_id = :guide_id";

        $params = ['guide_id' => $guideId];

        // Filter theo tháng
        if (!empty($filters['month'])) {
            $sql .= " AND DATE_FORMAT(ds.departure_date, '%Y-%m') = :month";
            $params['month'] = $filters['month'];
        }

        // Filter theo trạng thái
        if (!empty($filters['status'])) {
            $sql .= " AND sa.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY ds.departure_date ASC, ds.departure_time ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
