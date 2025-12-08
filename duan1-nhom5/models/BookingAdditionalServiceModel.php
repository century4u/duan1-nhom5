<?php

class BookingAdditionalServiceModel extends BaseModel
{
    protected $table = 'booking_additional_services';

    /**
     * Lấy dịch vụ của một booking
     */
    public function getByBookingId($bookingId)
    {
        $sql = "SELECT bas.*, asv.name as service_name 
                FROM {$this->table} bas
                JOIN additional_services asv ON bas.service_id = asv.id
                WHERE bas.booking_id = :booking_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return $stmt->fetchAll();
    }

    /**
     * Thêm dịch vụ vào booking
     */
    public function create($data)
    {
        // Tính tổng tiền nếu chưa có
        if (!isset($data['total_price'])) {
            $data['total_price'] = ($data['quantity'] ?? 1) * ($data['price'] ?? 0);
        }

        $sql = "INSERT INTO {$this->table} 
                (booking_id, service_id, quantity, price, total_price, notes) 
                VALUES 
                (:booking_id, :service_id, :quantity, :price, :total_price, :notes)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'booking_id' => $data['booking_id'],
            'service_id' => $data['service_id'],
            'quantity' => $data['quantity'] ?? 1,
            'price' => $data['price'] ?? 0,
            'total_price' => $data['total_price'],
            'notes' => $data['notes'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Xóa dịch vụ khỏi booking
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Tính tổng tiền dịch vụ thêm của một booking
     */
    public function getTotalPriceByBookingId($bookingId)
    {
        $sql = "SELECT SUM(total_price) as total 
                FROM {$this->table} 
                WHERE booking_id = :booking_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}
