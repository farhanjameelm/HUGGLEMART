<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $productId = (int)$_POST['product_id'];
    $quantity = (int)($_POST['quantity'] ?? 1);
    $negotiatedPrice = isset($_POST['negotiated_price']) ? (float)$_POST['negotiated_price'] : null;
    $userId = $_SESSION['user_id'];
    
    if (!$productId || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
        exit;
    }
    
    // Check if product exists and is active
    $productQuery = "SELECT id, name, stock_quantity, status FROM products WHERE id = :id AND status = 'active'";
    $productStmt = $conn->prepare($productQuery);
    $productStmt->bindParam(':id', $productId);
    $productStmt->execute();
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found or unavailable']);
        exit;
    }
    
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock available']);
        exit;
    }
    
    // Check if item already exists in cart
    $cartQuery = "SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id";
    $cartStmt = $conn->prepare($cartQuery);
    $cartStmt->bindParam(':user_id', $userId);
    $cartStmt->bindParam(':product_id', $productId);
    $cartStmt->execute();
    $existingItem = $cartStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Update existing cart item
        $newQuantity = $existingItem['quantity'] + $quantity;
        $updateQuery = "UPDATE cart SET quantity = :quantity, negotiated_price = :negotiated_price 
                       WHERE id = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':quantity', $newQuantity);
        $updateStmt->bindParam(':negotiated_price', $negotiatedPrice);
        $updateStmt->bindParam(':id', $existingItem['id']);
        $success = $updateStmt->execute();
    } else {
        // Add new cart item
        $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, negotiated_price) 
                       VALUES (:user_id, :product_id, :quantity, :negotiated_price)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bindParam(':user_id', $userId);
        $insertStmt->bindParam(':product_id', $productId);
        $insertStmt->bindParam(':quantity', $quantity);
        $insertStmt->bindParam(':negotiated_price', $negotiatedPrice);
        $success = $insertStmt->execute();
    }
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Product added to cart successfully!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred while adding to cart']);
}
?>
