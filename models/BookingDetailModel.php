<?php

class BookingDetailModel extends BaseModel
{
    protected $table = 'booking_details';

    /**
     * Lấy chi tiết theo booking_id
     */
    public function getByBookingId($bookingId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE booking_id = :booking_id ORDER BY id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Tạo chi tiết booking
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (booking_id, fullname, gender, birthdate, special_requirements) 
                VALUES 
                (:booking_id, :fullname, :gender, :birthdate, :special_requirements)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'booking_id' => $data['booking_id'],
            'fullname' => $data['fullname'],
            'gender' => $data['gender'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'special_requirements' => $data['special_requirements'] ?? null
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Tạo nhiều chi tiết booking cùng lúc
     */
    public function createMultiple($bookingId, $participants)
    {
        $results = [];
        foreach ($participants as $participant) {
            $results[] = $this->create([
                'booking_id' => $bookingId,
                'fullname' => $participant['fullname'],
                'gender' => $participant['gender'] ?? null,
                'birthdate' => $participant['birthdate'] ?? null
            ]);
        }
        return $results;
    }

    /**
     * Cập nhật chi tiết booking
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                fullname = :fullname,
                gender = :gender,
                birthdate = :birthdate,
                special_requirements = :special_requirements
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'fullname' => $data['fullname'],
            'gender' => $data['gender'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'special_requirements' => $data['special_requirements'] ?? null
        ]);
    }

    /**
     * Xóa chi tiết booking
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tất cả chi tiết của booking
     */
    public function deleteByBookingId($bookingId)
    {
        $sql = "DELETE FROM {$this->table} WHERE booking_id = :booking_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['booking_id' => $bookingId]);
    }

    /**
     * Đếm số người trong booking
     */
    public function countByBookingId($bookingId)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE booking_id = :booking_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    /**
     * Cập nhật nhanh yêu cầu đặc biệt
     */
    public function updateSpecialRequirements($id, $specialRequirements)
    {
        $sql = "UPDATE {$this->table} SET special_requirements = :special_requirements WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'special_requirements' => $specialRequirements
        ]);
    }

    /**
     * Lấy danh sách khách có yêu cầu đặc biệt
     */
    public function getWithSpecialRequirements($bookingId = null)
    {
        $sql = "SELECT bd.*, b.tour_id 
                FROM {$this->table} bd
                INNER JOIN bookings b ON bd.booking_id = b.id
                WHERE bd.special_requirements IS NOT NULL 
                AND bd.special_requirements != ''";
        
        $params = [];
        if ($bookingId) {
            $sql .= " AND bd.booking_id = :booking_id";
            $params['booking_id'] = $bookingId;
        }
        
        $sql .= " ORDER BY bd.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
