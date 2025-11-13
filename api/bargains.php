<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$productId = $_POST['product_id'] ?? 0;
$offeredPrice = $_POST['offered_price'] ?? 0;
$message = $_POST['message'] ?? '';
$userId = $_SESSION['user_id'];

if (!$productId || !$offeredPrice) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    // Check if product exists
    $query = "SELECT id, name, price FROM products WHERE id = :product_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':product_id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }
    
    // Check if user already has an active bargain for this product
    $query = "SELECT id FROM bargains 
              WHERE user_id = :user_id AND product_id = :product_id 
              AND status IN ('pending', 'counter_offered')";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $userId,
        ':product_id' => $productId
    ]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You already have an active bargain for this product']);
        exit();
    }
    
    // Create new bargain
    $query = "INSERT INTO bargains (user_id, product_id, original_price, offered_price, message, status, created_at) 
              VALUES (:user_id, :product_id, :original_price, :offered_price, :message, 'pending', NOW())";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $userId,
        ':product_id' => $productId,
        ':original_price' => $product['price'],
        ':offered_price' => $offeredPrice,
        ':message' => $message
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Bargain request submitted successfully! We will review and respond soon.'
    ]);
    
} catch (Exception $e) {
    error_log('Bargains API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while submitting your bargain']);
}
?>
