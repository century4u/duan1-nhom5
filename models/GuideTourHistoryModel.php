<?php

class GuideTourHistoryModel extends BaseModel
{
    protected $table = 'guide_tour_history';

    /**
     * Lấy lịch sử dẫn tour theo guide_id
     */
    public function getByGuideId($guideId, $limit = null)
    {
        $sql = "SELECT h.*, t.name as tour_name, t.code as tour_code, t.destination
                FROM {$this->table} h
                LEFT JOIN tours t ON h.tour_id = t.id
                WHERE h.guide_id = :guide_id
                ORDER BY h.start_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $params = ['guide_id' => $guideId];
        if ($limit) {
            $params['limit'] = $limit;
        }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Tạo bản ghi lịch sử dẫn tour
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (guide_id, tour_id, departure_schedule_id, start_date, end_date, status, notes) 
                VALUES 
                (:guide_id, :tour_id, :departure_schedule_id, :start_date, :end_date, :status, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'guide_id' => $data['guide_id'],
            'tour_id' => $data['tour_id'],
            'departure_schedule_id' => $data['departure_schedule_id'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'status' => $data['status'] ?? 'assigned',
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Đếm số tour đã dẫn
     */
    public function countToursByGuide($guideId)
    {
        $sql = "SELECT COUNT(*) as total_tours
                FROM {$this->table} 
                WHERE guide_id = :guide_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guide_id' => $guideId]);
        $result = $stmt->fetch();
        return $result['total_tours'] ?? 0;
    }
}

