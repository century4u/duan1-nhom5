<?php

class GuideAvailabilityModel extends BaseModel
{
    protected $table = 'guide_availability';

    /**
     * Lấy lịch làm việc theo guide_id và khoảng thời gian
     */
    public function getByGuideId($guideId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE guide_id = :guide_id";
        $params = ['guide_id' => $guideId];

        if ($startDate) {
            $sql .= " AND available_from >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND available_to <= :end_date";
            $params['end_date'] = $endDate;
        }

        $sql .= " ORDER BY available_from ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra HDV có sẵn trong khoảng thời gian không
     */
    public function isAvailable($guideId, $startDate, $endDate)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE guide_id = :guide_id 
                AND ((available_from <= :end_date AND available_to >= :start_date))
                AND status != 'available'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'guide_id' => $guideId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        $result = $stmt->fetch();
        return ($result['count'] ?? 0) == 0;
    }

    /**
     * Tạo lịch làm việc
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (guide_id, available_from, available_to, status)
                VALUES (:guide_id, :available_from, :available_to, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'guide_id' => $data['guide_id'],
            'available_from' => $data['available_from'],
            'available_to' => $data['available_to'],
            'status' => $data['status'] ?? 'available'
        ]);
        
        return $result ? $this->pdo->lastInsertId() : false;
    }
}
