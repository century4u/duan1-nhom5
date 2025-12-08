<?php

class TourReportModel extends BaseModel
{
    protected $table = 'tour_reports';

    /**
     * Tạo báo cáo mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (departure_schedule_id, guide_id, actual_start_date, actual_end_date,
                 total_participants, participants_attended, participants_absent,
                 issues_encountered, customer_feedback, expenses_incurred, 
                 revenue_collected, overall_rating, guide_notes, status) 
                VALUES 
                (:departure_schedule_id, :guide_id, :actual_start_date, :actual_end_date,
                 :total_participants, :participants_attended, :participants_absent,
                 :issues_encountered, :customer_feedback, :expenses_incurred,
                 :revenue_collected, :overall_rating, :guide_notes, :status)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'departure_schedule_id' => $data['departure_schedule_id'],
            'guide_id' => $data['guide_id'],
            'actual_start_date' => $data['actual_start_date'] ?? null,
            'actual_end_date' => $data['actual_end_date'] ?? null,
            'total_participants' => $data['total_participants'] ?? 0,
            'participants_attended' => $data['participants_attended'] ?? 0,
            'participants_absent' => $data['participants_absent'] ?? 0,
            'issues_encountered' => $data['issues_encountered'] ?? null,
            'customer_feedback' => $data['customer_feedback'] ?? null,
            'expenses_incurred' => $data['expenses_incurred'] ?? 0,
            'revenue_collected' => $data['revenue_collected'] ?? 0,
            'overall_rating' => $data['overall_rating'] ?? null,
            'guide_notes' => $data['guide_notes'] ?? null,
            'status' => $data['status'] ?? 'pending'
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Lấy báo cáo theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT tr.*, 
                       ds.departure_date, ds.end_date, ds.departure_time,
                       t.name as tour_name, t.code as tour_code,
                       g.full_name as guide_name, g.code as guide_code,
                       reviewer.full_name as reviewer_name
                FROM {$this->table} tr
                INNER JOIN departure_schedules ds ON tr.departure_schedule_id = ds.id
                INNER JOIN tours t ON ds.tour_id = t.id
                INNER JOIN guides g ON tr.guide_id = g.id
                LEFT JOIN users reviewer ON tr.reviewed_by = reviewer.id
                WHERE tr.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Lấy tất cả báo cáo với filter
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT tr.*, 
                       ds.departure_date, ds.end_date,
                       t.name as tour_name, t.code as tour_code,
                       g.full_name as guide_name
                FROM {$this->table} tr
                INNER JOIN departure_schedules ds ON tr.departure_schedule_id = ds.id
                INNER JOIN tours t ON ds.tour_id = t.id
                INNER JOIN guides g ON tr.guide_id = g.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND tr.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['guide_id'])) {
            $sql .= " AND tr.guide_id = :guide_id";
            $params['guide_id'] = $filters['guide_id'];
        }

        if (!empty($filters['departure_schedule_id'])) {
            $sql .= " AND tr.departure_schedule_id = :departure_schedule_id";
            $params['departure_schedule_id'] = $filters['departure_schedule_id'];
        }

        $sql .= " ORDER BY tr.report_date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy báo cáo theo departure schedule
     */
    public function getBySchedule($scheduleId)
    {
        return $this->getAll(['departure_schedule_id' => $scheduleId]);
    }

    /**
     * Lấy báo cáo theo HDV
     */
    public function getByGuide($guideId)
    {
        return $this->getAll(['guide_id' => $guideId]);
    }

    /**
     * Lấy báo cáo chưa duyệt
     */
    public function getPendingReports()
    {
        return $this->getAll(['status' => 'pending']);
    }

    /**
     * Cập nhật trạng thái báo cáo
     */
    public function updateStatus($id, $status, $adminReview = null, $reviewedBy = null)
    {
        $sql = "UPDATE {$this->table} SET 
                status = :status,
                admin_review = :admin_review,
                reviewed_by = :reviewed_by,
                reviewed_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status,
            'admin_review' => $adminReview,
            'reviewed_by' => $reviewedBy
        ]);
    }

    /**
     * Cập nhật báo cáo
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                actual_start_date = :actual_start_date,
                actual_end_date = :actual_end_date,
                total_participants = :total_participants,
                participants_attended = :participants_attended,
                participants_absent = :participants_absent,
                issues_encountered = :issues_encountered,
                customer_feedback = :customer_feedback,
                expenses_incurred = :expenses_incurred,
                revenue_collected = :revenue_collected,
                overall_rating = :overall_rating,
                guide_notes = :guide_notes
                WHERE id = :id AND status = 'pending'";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'actual_start_date' => $data['actual_start_date'] ?? null,
            'actual_end_date' => $data['actual_end_date'] ?? null,
            'total_participants' => $data['total_participants'] ?? 0,
            'participants_attended' => $data['participants_attended'] ?? 0,
            'participants_absent' => $data['participants_absent'] ?? 0,
            'issues_encountered' => $data['issues_encountered'] ?? null,
            'customer_feedback' => $data['customer_feedback'] ?? null,
            'expenses_incurred' => $data['expenses_incurred'] ?? 0,
            'revenue_collected' => $data['revenue_collected'] ?? 0,
            'overall_rating' => $data['overall_rating'] ?? null,
            'guide_notes' => $data['guide_notes'] ?? null
        ]);
    }

    /**
     * Kiểm tra HDV đã báo cáo cho schedule chưa
     */
    public function hasReported($scheduleId, $guideId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE departure_schedule_id = :schedule_id 
                AND guide_id = :guide_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'schedule_id' => $scheduleId,
            'guide_id' => $guideId
        ]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Xóa báo cáo (chỉ khi pending)
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE id = :id AND status = 'pending'";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
