<?php

class UserModel extends BaseModel
{
    protected $table = 'users';

    /**
     * Lấy user theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Lấy user theo username
     */
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    /**
     * Lấy user theo email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Kiểm tra username hoặc email đã tồn tại chưa
     */
    public function exists($username, $email)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE username = :username OR email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email
        ]);
        $result = $stmt->fetch();
        return ($result['count'] ?? 0) > 0;
    }

    /**
     * Tạo user mới (đăng ký)
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (username, email, password, full_name, role, status) 
                VALUES 
                (:username, :email, :password, :full_name, :role, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'], // Đã được hash trước khi truyền vào
            'full_name' => $data['full_name'],
            'role' => $data['role'] ?? 'USER',
            'status' => $data['status'] ?? 1
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật thông tin user
     */
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                full_name = :full_name,
                email = :email";
        
        $params = [
            'id' => $id,
            'full_name' => $data['full_name'],
            'email' => $data['email']
        ];

        // Cập nhật mật khẩu nếu có
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = $data['password'];
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xác thực đăng nhập
     */
    public function authenticate($username, $password)
    {
        // Tìm user theo username hoặc email
        $user = $this->findByUsername($username);
        if (!$user) {
            $user = $this->findByEmail($username);
        }

        if (!$user) {
            return false;
        }

        // Kiểm tra trạng thái
        if ($user['status'] != 1) {
            return false;
        }

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Không trả về mật khẩu
            unset($user['password']);
            return $user;
        }

        return false;
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $userId,
            'password' => $hashedPassword
        ]);
    }
}
