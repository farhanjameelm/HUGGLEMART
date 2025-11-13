<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit();
}

$action = $_POST['action'] ?? '';
$productId = $_POST['product_id'] ?? 0;
$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'add':
            // Add item to wishlist
            $query = "INSERT IGNORE INTO wishlist (user_id, product_id, created_at) 
                      VALUES (:user_id, :product_id, NOW())";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Item added to wishlist']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item already in wishlist']);
            }
            break;
            
        case 'remove':
            // Remove item from wishlist
            $query = "DELETE FROM wishlist 
                      WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Item removed from wishlist']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log('Wishlist API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
