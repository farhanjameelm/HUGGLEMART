<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit();
}

$action = $_POST['action'] ?? '';
$productId = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;
$userId = $_SESSION['user_id'];

try {
    // Check if we have a database connection
    if (!isset($pdo)) {
        $database = new Database();
        $pdo = $database->getConnection();
    }
    
    switch ($action) {
        case 'add':
            // Add item to cart - try both possible table names
            $query = "INSERT INTO cart (user_id, product_id, quantity, created_at) 
                      VALUES (:user_id, :product_id, :quantity, NOW())
                      ON DUPLICATE KEY UPDATE 
                      quantity = quantity + :quantity, updated_at = NOW()";
            
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId,
                ':quantity' => $quantity
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Item added to cart']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add item to cart']);
            }
            break;
            
        case 'update':
            // Update cart item quantity
            $query = "UPDATE cart 
                      SET quantity = :quantity, updated_at = NOW() 
                      WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([
                ':quantity' => $quantity,
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cart updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
            }
            break;
            
        case 'remove':
            // Remove item from cart
            $query = "DELETE FROM cart 
                      WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $pdo->prepare($query);
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log('Cart API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
