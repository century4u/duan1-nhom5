<?php

class TourModel extends BaseModel
{
    protected $table = 'tours';

    /**
     * Lấy danh sách tất cả tours
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT t.*, u.full_name as created_by_name 
                FROM {$this->table} t 
                LEFT JOIN users u ON t.created_by = u.id 
                WHERE 1=1";

        $params = [];

        // Lọc theo category
        if (!empty($filters['category'])) {
            $sql .= " AND t.category = :category";
            $params['category'] = $filters['category'];
        }

        // Lọc theo status
        if (isset($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        // Tìm kiếm
        if (!empty($filters['search'])) {
            $sql .= " AND (t.name LIKE :search OR t.code LIKE :search OR t.destination LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY t.created_at DESC";

        // Phân trang
        if (isset($filters['limit'])) {
            $sql .= " LIMIT :limit";
            if (isset($filters['offset'])) {
                $sql .= " OFFSET :offset";
            }
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        if (isset($filters['limit'])) {
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
            if (isset($filters['offset'])) {
                $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy tour theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT t.*, u.full_name as created_by_name 
                FROM {$this->table} t 
                LEFT JOIN users u ON t.created_by = u.id 
                WHERE t.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Lấy tour theo code
     */
    public function findByCode($code)
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $code]);
        return $stmt->fetch();
    }

    /**
     * Tạo tour mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (name, code, category, description, duration, price, max_participants, 
                 departure_location, destination, image, status, created_by) 
                VALUES 
                (:name, :code, :category, :description, :duration, :price, :max_participants, 
                 :departure_location, :destination, :image, :status, :created_by)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'name' => $data['name'],
            'code' => $data['code'],
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'],
            'price' => $data['price'],
            'max_participants' => $data['max_participants'] ?? null,
            'departure_location' => $data['departure_location'],
            'destination' => $data['destination'],
            'image' => $data['image'] ?? null,
            'status' => $data['status'] ?? 1,
            'created_by' => $data['created_by']
        ]);

        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật tour
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                name = :name,
                code = :code,
                category = :category,
                description = :description,
                duration = :duration,
                price = :price,
                max_participants = :max_participants,
                departure_location = :departure_location,
                destination = :destination,
                image = :image,
                status = :status
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'code' => $data['code'],
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'],
            'price' => $data['price'],
            'max_participants' => $data['max_participants'] ?? null,
            'departure_location' => $data['departure_location'],
            'destination' => $data['destination'],
            'image' => $data['image'] ?? null,
            'status' => $data['status'] ?? 1
        ]);
    }

    /**
     * Xóa tour (soft delete)
     */
    public function delete($id)
    {
        $sql = "UPDATE {$this->table} SET status = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tour vĩnh viễn
     */
    public function forceDelete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Đếm tổng số tours
     */
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
            $params['category'] = $filters['category'];
        }

        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR code LIKE :search OR destination LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Lấy danh sách categories
     */
    public static function getCategories()
    {
        return [
            'domestic' => 'Tour trong nước',
            'international' => 'Tour quốc tế',
            'customized' => 'Tour theo yêu cầu'
        ];
    }
}

