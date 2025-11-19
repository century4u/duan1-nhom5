<?php

class TourSupplierModel extends BaseModel
{
    protected $table = 'tour_suppliers';

    /**
     * Lấy nhà cung cấp theo tour_id
     */
    public function getByTourId($tourId)
    {
        $sql = "SELECT ts.*, s.name as supplier_name, s.type as supplier_type, s.phone, s.email 
                FROM {$this->table} ts
                INNER JOIN suppliers s ON ts.supplier_id = s.id
                WHERE ts.tour_id = :tour_id
                ORDER BY ts.service_date, s.type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tourId]);
        return $stmt->fetchAll();
    }

    /**
     * Liên kết tour với nhà cung cấp
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (tour_id, supplier_id, service_type, service_date, notes) 
                VALUES 
                (:tour_id, :supplier_id, :service_type, :service_date, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'tour_id' => $data['tour_id'],
            'supplier_id' => $data['supplier_id'],
            'service_type' => $data['service_type'] ?? null,
            'service_date' => $data['service_date'] ?? null,
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Xóa liên kết
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tất cả liên kết của tour
     */
    public function deleteByTourId($tourId)
    {
        $sql = "DELETE FROM {$this->table} WHERE tour_id = :tour_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['tour_id' => $tourId]);
    }
}

