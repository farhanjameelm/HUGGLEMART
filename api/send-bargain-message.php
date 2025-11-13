<?php
require_once '../config/config.php';
require_once '../classes/Bargain.php';

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
    $message = trim($_POST['message']);
    $userId = $_SESSION['user_id'];
    
    if (!$bargainId || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Invalid bargain ID or message']);
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
    
    // Add message
    $result = $bargain->addBargainMessage($bargainId, $userId, 'customer', 'message', $message);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
