<?php
require_once '../config/config.php';
require_once '../classes/Bargain.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $bargainId = (int)$_POST['bargain_id'];
    $action = $_POST['action'];
    $counterPrice = isset($_POST['counter_price']) ? (float)$_POST['counter_price'] : null;
    $message = $_POST['message'] ?? '';
    $adminId = $_SESSION['user_id'];
    
    if (!$bargainId || !in_array($action, ['accepted', 'rejected', 'countered'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid bargain ID or action']);
        exit;
    }
    
    if ($action === 'countered' && !$counterPrice) {
        echo json_encode(['success' => false, 'message' => 'Counter price is required']);
        exit;
    }
    
    $bargain = new Bargain();
    $result = $bargain->respondToBargain($bargainId, $adminId, $action, $counterPrice, $message);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true, 
            'message' => 'Bargain response sent successfully'
        ]);
    } else {
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred while processing the response']);
}
?>
