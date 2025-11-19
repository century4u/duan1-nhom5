<?php

class GuideModel extends BaseModel
{
    protected $table = 'guides';

    /**
     * Lấy tất cả HDV
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['specialization'])) {
            $sql .= " AND specialization = :specialization";
            $params['specialization'] = $filters['specialization'];
        }

        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (full_name LIKE :search OR code LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY full_name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy HDV theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Lấy HDV theo code
     */
    public function findByCode($code)
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $code]);
        return $stmt->fetch();
    }

    /**
     * Tạo HDV mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (code, full_name, birthdate, gender, avatar, phone, email, address, id_card, passport,
                 languages, certificates, experience_years, experience_description, specialization,
                 performance_rating, health_status, health_notes, status) 
                VALUES 
                (:code, :full_name, :birthdate, :gender, :avatar, :phone, :email, :address, :id_card, :passport,
                 :languages, :certificates, :experience_years, :experience_description, :specialization,
                 :performance_rating, :health_status, :health_notes, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'code' => $data['code'],
            'full_name' => $data['full_name'],
            'birthdate' => $data['birthdate'] ?? null,
            'gender' => $data['gender'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'id_card' => $data['id_card'] ?? null,
            'passport' => $data['passport'] ?? null,
            'languages' => !empty($data['languages']) ? json_encode($data['languages']) : null,
            'certificates' => $data['certificates'] ?? null,
            'experience_years' => $data['experience_years'] ?? 0,
            'experience_description' => $data['experience_description'] ?? null,
            'specialization' => $data['specialization'] ?? 'mixed',
            'performance_rating' => $data['performance_rating'] ?? null,
            'health_status' => $data['health_status'] ?? 'good',
            'health_notes' => $data['health_notes'] ?? null,
            'status' => $data['status'] ?? 1
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật HDV
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                code = :code,
                full_name = :full_name,
                birthdate = :birthdate,
                gender = :gender,
                avatar = :avatar,
                phone = :phone,
                email = :email,
                address = :address,
                id_card = :id_card,
                passport = :passport,
                languages = :languages,
                certificates = :certificates,
                experience_years = :experience_years,
                experience_description = :experience_description,
                specialization = :specialization,
                performance_rating = :performance_rating,
                health_status = :health_status,
                health_notes = :health_notes,
                status = :status
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'code' => $data['code'],
            'full_name' => $data['full_name'],
            'birthdate' => $data['birthdate'] ?? null,
            'gender' => $data['gender'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'id_card' => $data['id_card'] ?? null,
            'passport' => $data['passport'] ?? null,
            'languages' => !empty($data['languages']) ? json_encode($data['languages']) : null,
            'certificates' => $data['certificates'] ?? null,
            'experience_years' => $data['experience_years'] ?? 0,
            'experience_description' => $data['experience_description'] ?? null,
            'specialization' => $data['specialization'] ?? 'mixed',
            'performance_rating' => $data['performance_rating'] ?? null,
            'health_status' => $data['health_status'] ?? 'good',
            'health_notes' => $data['health_notes'] ?? null,
            'status' => $data['status'] ?? 1
        ]);
    }

    /**
     * Xóa HDV
     */
    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET status = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Đếm số tour đã dẫn
     */
    public function countTours($guideId)
    {
        $sql = "SELECT COUNT(*) as total FROM guide_tour_history WHERE guide_id = :guide_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guide_id' => $guideId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Lấy danh sách chuyên môn
     */
    public static function getSpecializations()
    {
        return [
            'domestic' => 'Nội địa',
            'international' => 'Quốc tế',
            'specialized_route' => 'Chuyên tuyến',
            'group_tour' => 'Chuyên khách đoàn',
            'mixed' => 'Hỗn hợp'
        ];
    }

    /**
     * Lấy danh sách ngôn ngữ phổ biến
     */
    public static function getCommonLanguages()
    {
        return [
            'Vietnamese' => 'Tiếng Việt',
            'English' => 'Tiếng Anh',
            'Chinese' => 'Tiếng Trung',
            'Japanese' => 'Tiếng Nhật',
            'Korean' => 'Tiếng Hàn',
            'French' => 'Tiếng Pháp',
            'German' => 'Tiếng Đức',
            'Spanish' => 'Tiếng Tây Ban Nha',
            'Thai' => 'Tiếng Thái',
            'Russian' => 'Tiếng Nga'
        ];
    }
}
