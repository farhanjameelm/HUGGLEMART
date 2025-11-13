<?php
require_once '../config/config.php';
require_once '../classes/Bargain.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $bargainId = (int)$_POST['bargain_id'];
    $counterPrice = (float)$_POST['counter_price'];
    $message = trim($_POST['message'] ?? '');
    $userId = $_SESSION['user_id'];
    
    if (!$bargainId || !$counterPrice || $counterPrice <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid bargain ID or counter price']);
        exit;
    }
    
    $bargain = new Bargain();
    
    // Verify bargain belongs to user and is active
    $bargainData = $bargain->getBargainById($bargainId);
    if (!$bargainData || $bargainData['user_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Bargain not found or access denied']);
        exit;
    }
    
    if (!in_array($bargainData['status'], ['pending', 'countered'])) {
        echo json_encode(['success' => false, 'message' => 'This bargain is no longer active']);
        exit;
    }
    
    // Update bargain with new counter offer
    $database = new Database();
    $conn = $database->getConnection();
    
    $updateQuery = "UPDATE bargains SET current_price = :current_price, status = 'countered', updated_at = NOW() 
                    WHERE id = :id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':current_price', $counterPrice);
    $updateStmt->bindParam(':id', $bargainId);
    
    if ($updateStmt->execute()) {
        // Add counter offer message
        $messageText = $message ?: "I'd like to counter with " . formatPrice($counterPrice);
        $bargain->addBargainMessage($bargainId, $userId, 'customer', 'counter_offer', $messageText, $counterPrice);
        
        echo json_encode(['success' => true, 'message' => 'Counter offer sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send counter offer']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
