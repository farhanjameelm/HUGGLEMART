<?php
require_once __DIR__ . '/../config/database.php';

class Bargain {
    private $conn;
    private $table = 'bargains';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createBargain($userId, $productId, $originalPrice, $offeredPrice) {
        // Check if there's already an active bargain for this user and product
        $existingQuery = "SELECT id FROM " . $this->table . " 
                         WHERE user_id = :user_id AND product_id = :product_id 
                         AND status IN ('pending', 'countered') 
                         AND expires_at > NOW()";
        
        $stmt = $this->conn->prepare($existingQuery);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'You already have an active bargain for this product'];
        }

        // Create new bargain
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . BARGAIN_TIMEOUT_HOURS . ' hours'));
        
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, product_id, original_price, offered_price, current_price, expires_at) 
                  VALUES (:user_id, :product_id, :original_price, :offered_price, :current_price, :expires_at)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':original_price', $originalPrice);
        $stmt->bindParam(':offered_price', $offeredPrice);
        $stmt->bindParam(':current_price', $offeredPrice);
        $stmt->bindParam(':expires_at', $expiresAt);
        
        if ($stmt->execute()) {
            $bargainId = $this->conn->lastInsertId();
            
            // Add initial message
            $this->addBargainMessage($bargainId, $userId, 'customer', 'offer', 
                                   "I'd like to buy this for " . formatPrice($offeredPrice), $offeredPrice);
            
            return ['success' => true, 'bargain_id' => $bargainId];
        }
        
        return ['success' => false, 'message' => 'Failed to create bargain'];
    }

    public function respondToBargain($bargainId, $adminId, $action, $counterPrice = null, $message = null) {
        $bargain = $this->getBargainById($bargainId);
        if (!$bargain) {
            return ['success' => false, 'message' => 'Bargain not found'];
        }

        if ($bargain['status'] !== 'pending' && $bargain['status'] !== 'countered') {
            return ['success' => false, 'message' => 'Bargain is no longer active'];
        }

        $newStatus = $action;
        $currentPrice = $bargain['current_price'];
        
        if ($action === 'countered' && $counterPrice) {
            $currentPrice = $counterPrice;
        }

        // Update bargain status
        $query = "UPDATE " . $this->table . " 
                  SET status = :status, current_price = :current_price, 
                      admin_response = :admin_response, updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $bargainId);
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':current_price', $currentPrice);
        $stmt->bindParam(':admin_response', $message);
        
        if ($stmt->execute()) {
            // Add message to bargain chat
            $messageType = $action === 'countered' ? 'counter_offer' : $action;
            $chatMessage = $message ?: ($action === 'accepted' ? 'Offer accepted!' : 'Offer rejected');
            
            $this->addBargainMessage($bargainId, $adminId, 'admin', $messageType, $chatMessage, $counterPrice);
            
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to update bargain'];
    }

    public function addBargainMessage($bargainId, $senderId, $senderType, $messageType, $message, $offeredPrice = null) {
        $query = "INSERT INTO bargain_messages 
                  (bargain_id, sender_id, sender_type, message_type, message, offered_price) 
                  VALUES (:bargain_id, :sender_id, :sender_type, :message_type, :message, :offered_price)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bargain_id', $bargainId);
        $stmt->bindParam(':sender_id', $senderId);
        $stmt->bindParam(':sender_type', $senderType);
        $stmt->bindParam(':message_type', $messageType);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':offered_price', $offeredPrice);
        
        return $stmt->execute();
    }

    public function getBargainById($id) {
        $query = "SELECT b.*, p.name as product_name, p.images as product_images,
                         u.first_name, u.last_name, u.email
                  FROM " . $this->table . " b
                  JOIN products p ON b.product_id = p.id
                  JOIN users u ON b.user_id = u.id
                  WHERE b.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $bargain = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($bargain) {
            $bargain['product_images'] = json_decode($bargain['product_images'], true) ?: [];
        }
        
        return $bargain;
    }

    public function getBargainMessages($bargainId) {
        $query = "SELECT bm.*, u.first_name, u.last_name
                  FROM bargain_messages bm
                  JOIN users u ON bm.sender_id = u.id
                  WHERE bm.bargain_id = :bargain_id
                  ORDER BY bm.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':bargain_id', $bargainId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserBargains($userId, $status = null) {
        $query = "SELECT b.*, p.name as product_name, p.images as product_images, p.slug as product_slug
                  FROM " . $this->table . " b
                  JOIN products p ON b.product_id = p.id
                  WHERE b.user_id = :user_id";
        
        if ($status) {
            $query .= " AND b.status = :status";
        }
        
        $query .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        $bargains = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($bargains as &$bargain) {
            $bargain['product_images'] = json_decode($bargain['product_images'], true) ?: [];
        }
        
        return $bargains;
    }

    public function getAllBargains($limit = null, $offset = null, $status = null) {
        $query = "SELECT b.*, p.name as product_name, p.images as product_images,
                         u.first_name, u.last_name, u.email
                  FROM " . $this->table . " b
                  JOIN products p ON b.product_id = p.id
                  JOIN users u ON b.user_id = u.id";
        
        if ($status) {
            $query .= " WHERE b.status = :status";
        }
        
        $query .= " ORDER BY b.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
            if ($offset) {
                $query .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            if ($offset) {
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        $bargains = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($bargains as &$bargain) {
            $bargain['product_images'] = json_decode($bargain['product_images'], true) ?: [];
        }
        
        return $bargains;
    }

    public function getBargainStats() {
        $query = "SELECT 
                    COUNT(*) as total_bargains,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_bargains,
                    COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted_bargains,
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_bargains,
                    AVG(CASE WHEN status = 'accepted' THEN ((original_price - current_price) / original_price) * 100 END) as avg_discount_percentage
                  FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function expireOldBargains() {
        $query = "UPDATE " . $this->table . " 
                  SET status = 'expired' 
                  WHERE status IN ('pending', 'countered') 
                  AND expires_at < NOW()";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>
