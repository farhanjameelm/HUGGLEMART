<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $userId = $_SESSION['user_id'];
    
    // Get cart count
    $cartQuery = "SELECT COUNT(*) as cart_count FROM cart WHERE user_id = :user_id";
    $cartStmt = $conn->prepare($cartQuery);
    $cartStmt->bindParam(':user_id', $userId);
    $cartStmt->execute();
    $cartCount = $cartStmt->fetch(PDO::FETCH_ASSOC)['cart_count'];
    
    // Get active bargains count
    $bargainQuery = "SELECT COUNT(*) as bargain_count FROM bargains 
                     WHERE user_id = :user_id AND status IN ('pending', 'countered')";
    $bargainStmt = $conn->prepare($bargainQuery);
    $bargainStmt->bindParam(':user_id', $userId);
    $bargainStmt->execute();
    $bargainCount = $bargainStmt->fetch(PDO::FETCH_ASSOC)['bargain_count'];
    
    echo json_encode([
        'success' => true,
        'cart_count' => (int)$cartCount,
        'active_bargains' => (int)$bargainCount
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
