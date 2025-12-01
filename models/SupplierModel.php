<?php

class SupplierModel extends BaseModel
{
    protected $table = 'suppliers';

    /**
     * Lấy tất cả nhà cung cấp
     */
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filters['type'])) {
            $sql .= " AND type = :type";
            $params['type'] = $filters['type'];
        }

        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy nhà cung cấp theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Lấy nhà cung cấp theo tour_id
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT s.*, ts.service_type, ts.service_date, ts.notes 
                FROM {$this->table} s
                INNER JOIN tour_suppliers ts ON s.id = ts.supplier_id
                WHERE ts.tour_id = :tour_id
                ORDER BY ts.service_date, s.type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Tạo nhà cung cấp mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (name, type, contact_person, phone, email, address, description, rating, status) 
                VALUES 
                (:name, :type, :contact_person, :phone, :email, :address, :description, :rating, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'name' => $data['name'],
            'type' => $data['type'],
            'contact_person' => $data['contact_person'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'description' => $data['description'] ?? null,
            'rating' => $data['rating'] ?? null,
            'status' => $data['status'] ?? 1
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật nhà cung cấp
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                name = :name,
                type = :type,
                contact_person = :contact_person,
                phone = :phone,
                email = :email,
                address = :address,
                description = :description,
                rating = :rating,
                status = :status
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'type' => $data['type'],
            'contact_person' => $data['contact_person'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'description' => $data['description'] ?? null,
            'rating' => $data['rating'] ?? null,
            'status' => $data['status'] ?? 1
        ]);
    }

    /**
     * Xóa nhà cung cấp
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}

