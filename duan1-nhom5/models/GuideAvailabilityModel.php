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
            $sql .= " AND date >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $sql .= " AND date <= :end_date";
            $params['end_date'] = $endDate;
        }

        $sql .= " ORDER BY date ASC";

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
                AND date BETWEEN :start_date AND :end_date
                AND status IN ('busy', 'off', 'sick')";
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
     * Tạo/cập nhật lịch làm việc
     */
    public function setAvailability($guideId, $date, $status, $reason = null, $notes = null)
    {
        $sql = "INSERT INTO {$this->table} (guide_id, date, status, reason, notes)
                VALUES (:guide_id, :date, :status, :reason, :notes)
                ON DUPLICATE KEY UPDATE 
                status = :status,
                reason = :reason,
                notes = :notes";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'guide_id' => $guideId,
            'date' => $date,
            'status' => $status,
            'reason' => $reason,
            'notes' => $notes
        ]);
    }
}

