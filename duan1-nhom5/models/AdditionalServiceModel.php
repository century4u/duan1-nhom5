<?php

class AdditionalServiceModel extends BaseModel
{
    protected $table = 'additional_services';

    /**
     * Lấy tất cả dịch vụ
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy dịch vụ theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo dịch vụ mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (name, description, price, status) 
                VALUES 
                (:name, :description, :price, :status)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? 0,
            'status' => $data['status'] ?? 1
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật dịch vụ
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                name = :name,
                description = :description,
                price = :price,
                status = :status
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? 0,
            'status' => $data['status'] ?? 1
        ]);
    }

    /**
     * Xóa dịch vụ (Soft delete nếu cần hoặc hard delete)
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
