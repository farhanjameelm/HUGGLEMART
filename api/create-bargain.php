<?php
require_once '../config/config.php';
require_once '../classes/Bargain.php';
require_once '../classes/Product.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to negotiate prices']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $productId = (int)$_POST['product_id'];
    $offeredPrice = (float)$_POST['offered_price'];
    $message = $_POST['message'] ?? '';
    $userId = $_SESSION['user_id'];
    
    if (!$productId || !$offeredPrice || $offeredPrice <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or price']);
        exit;
    }
    
    // Get product details
    $product = new Product();
    $productData = $product->getProductById($productId);
    
    if (!$productData) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    if (!$productData['allow_bargaining']) {
        echo json_encode(['success' => false, 'message' => 'Bargaining is not allowed for this product']);
        exit;
    }
    
    // Check if offered price is reasonable (not too low)
    $currentPrice = $productData['sale_price'] ?: $productData['price'];
    $minAllowedPrice = $currentPrice * (1 - ($productData['bargain_min_percentage'] / 100));
    
    if ($offeredPrice < $minAllowedPrice) {
        $minDiscount = $productData['bargain_min_percentage'];
        echo json_encode([
            'success' => false, 
            'message' => "Minimum discount allowed is {$minDiscount}%. Please offer at least $" . number_format($minAllowedPrice, 2)
        ]);
        exit;
    }
    
    // Create bargain
    $bargain = new Bargain();
    $result = $bargain->createBargain($userId, $productId, $currentPrice, $offeredPrice);
    
    if ($result['success']) {
        // Add custom message if provided
        if (!empty($message)) {
            $bargain->addBargainMessage($result['bargain_id'], $userId, 'customer', 'message', $message);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Your bargain offer has been sent! You will be notified when the seller responds.',
            'bargain_id' => $result['bargain_id']
        ]);
    } else {
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred while processing your request']);
}
?>
