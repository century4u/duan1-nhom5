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
                (guide_id, tour_id, start_date, end_date, participants_count, rating, feedback, notes) 
                VALUES 
                (:guide_id, :tour_id, :start_date, :end_date, :participants_count, :rating, :feedback, :notes)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'guide_id' => $data['guide_id'],
            'tour_id' => $data['tour_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'participants_count' => $data['participants_count'] ?? 0,
            'rating' => $data['rating'] ?? null,
            'feedback' => $data['feedback'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Tính đánh giá trung bình của HDV
     */
    public function getAverageRating($guideId)
    {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_tours
                FROM {$this->table} 
                WHERE guide_id = :guide_id AND rating IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guide_id' => $guideId]);
        return $stmt->fetch();
    }

    /**
     * Update tour assignment status
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }
}

