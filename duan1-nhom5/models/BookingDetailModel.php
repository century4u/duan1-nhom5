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
                (booking_id, fullname, gender, birthdate, phone, id_card, passport, hobby, special_requirements, medical_conditions, dietary_restrictions) 
                VALUES 
                (:booking_id, :fullname, :gender, :birthdate, :phone, :id_card, :passport, :hobby, :special_requirements, :medical_conditions, :dietary_restrictions)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'booking_id' => $data['booking_id'],
            'fullname' => $data['fullname'],
            'gender' => $data['gender'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'phone' => $data['phone'] ?? null,
            'id_card' => $data['id_card'] ?? null,
            'passport' => $data['passport'] ?? null,
            'hobby' => $data['hobby'] ?? null,
            'special_requirements' => $data['special_requirements'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'dietary_restrictions' => $data['dietary_restrictions'] ?? null
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
                'birthdate' => $participant['birthdate'] ?? null,
                'phone' => $participant['phone'] ?? null,
                'id_card' => $participant['id_card'] ?? null,
                'passport' => $participant['passport'] ?? null,
                'special_requirements' => $participant['special_requirements'] ?? null,
                'medical_conditions' => $participant['medical_conditions'] ?? null,
                'dietary_restrictions' => $participant['dietary_restrictions'] ?? null
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
            phone = :phone,
            id_card = :id_card,
            passport = :passport,
            special_requirements = :special_requirements,
            dietary_restrictions = :dietary_restrictions
            WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        'id' => $id,
        'fullname' => $data['fullname'],
        'gender' => $data['gender'] ?? null,
        'birthdate' => $data['birthdate'] ?? null,
        'id_card' => $data['id_card'] ?? null,
        'passport' => $data['passport'] ?? null,
        'hobby' => $data['hobby'] ?? null,
        'special_requirements' => $data['special_requirements'] ?? null,
        'dietary_restrictions' => $data['dietary_restrictions'] ?? null
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


}