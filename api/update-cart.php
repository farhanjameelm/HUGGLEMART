<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

try {
    switch ($action) {
        case 'update':
            if ($productId && $quantity > 0) {
                if (isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId]['quantity'] = $quantity;
                    echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid product ID or quantity']);
            }
            break;
            
        case 'remove':
            if ($productId && isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
            }
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true, 'message' => 'Cart cleared successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating cart']);
}
?>
