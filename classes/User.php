<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (username, email, password, first_name, last_name, phone, address, city, state, zip_code) 
                  VALUES (:username, :email, :password, :first_name, :last_name, :phone, :address, :city, :state, :zip_code)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':zip_code', $data['zip_code']);
        
        return $stmt->execute();
    }

    public function login($email, $password) {
        $query = "SELECT id, username, email, password, first_name, last_name, is_admin 
                  FROM " . $this->table . " 
                  WHERE email = :email AND is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET first_name = :first_name, last_name = :last_name, 
                      phone = :phone, address = :address, city = :city, 
                      state = :state, zip_code = :zip_code, updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':zip_code', $data['zip_code']);
        
        return $stmt->execute();
    }

    public function getAllUsers($limit = null, $offset = null) {
        $query = "SELECT id, username, email, first_name, last_name, phone, 
                         is_admin, is_active, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
            if ($offset) {
                $query .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStats($userId) {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM orders WHERE user_id = :user_id) as total_orders,
                    (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = :user_id AND payment_status = 'paid') as total_spent,
                    (SELECT COUNT(*) FROM bargains WHERE user_id = :user_id) as total_bargains,
                    (SELECT COUNT(*) FROM bargains WHERE user_id = :user_id AND status = 'accepted') as accepted_bargains";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalUserCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>
