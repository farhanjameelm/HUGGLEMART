<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$database = new Database();
$pdo = $database->getConnection();

$action = $_POST['action'] ?? '';
$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'process_payment':
            $orderId = $_POST['order_id'] ?? 0;
            $paymentMethod = $_POST['payment_method'] ?? '';
            $amount = $_POST['amount'] ?? 0;
            
            if (!$orderId || !$paymentMethod || !$amount) {
                echo json_encode(['success' => false, 'message' => 'Missing required payment information']);
                exit();
            }
            
            // Verify order belongs to user
            $orderQuery = "SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id";
            $orderStmt = $pdo->prepare($orderQuery);
            $orderStmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Order not found']);
                exit();
            }
            
            // Process payment based on method
            $paymentResult = processPayment($paymentMethod, $amount, $_POST);
            
            if ($paymentResult['success']) {
                // Update order payment status
                $updateQuery = "UPDATE orders SET payment_status = 'paid', payment_reference = :reference, updated_at = NOW() WHERE id = :order_id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([
                    ':reference' => $paymentResult['reference'],
                    ':order_id' => $orderId
                ]);
                
                // Log payment transaction
                $logQuery = "INSERT INTO payment_logs (order_id, user_id, payment_method, amount, status, reference, response_data, created_at) 
                            VALUES (:order_id, :user_id, :payment_method, :amount, 'success', :reference, :response_data, NOW())";
                $logStmt = $pdo->prepare($logQuery);
                $logStmt->execute([
                    ':order_id' => $orderId,
                    ':user_id' => $userId,
                    ':payment_method' => $paymentMethod,
                    ':amount' => $amount,
                    ':reference' => $paymentResult['reference'],
                    ':response_data' => json_encode($paymentResult['data'])
                ]);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Payment processed successfully',
                    'reference' => $paymentResult['reference'],
                    'redirect' => 'order-confirmation.php?order_id=' . $orderId
                ]);
            } else {
                // Log failed payment
                $logQuery = "INSERT INTO payment_logs (order_id, user_id, payment_method, amount, status, error_message, response_data, created_at) 
                            VALUES (:order_id, :user_id, :payment_method, :amount, 'failed', :error_message, :response_data, NOW())";
                $logStmt = $pdo->prepare($logQuery);
                $logStmt->execute([
                    ':order_id' => $orderId,
                    ':user_id' => $userId,
                    ':payment_method' => $paymentMethod,
                    ':amount' => $amount,
                    ':error_message' => $paymentResult['message'],
                    ':response_data' => json_encode($paymentResult['data'])
                ]);
                
                echo json_encode([
                    'success' => false, 
                    'message' => $paymentResult['message']
                ]);
            }
            break;
            
        case 'validate_card':
            $cardNumber = $_POST['card_number'] ?? '';
            $expiryDate = $_POST['expiry_date'] ?? '';
            $cvv = $_POST['cvv'] ?? '';
            
            $validation = validateCreditCard($cardNumber, $expiryDate, $cvv);
            echo json_encode($validation);
            break;
            
        case 'save_payment_method':
            $paymentMethod = $_POST['payment_method'] ?? '';
            $cardData = $_POST['card_data'] ?? [];
            
            // Save encrypted payment method for future use
            $saveResult = savePaymentMethod($userId, $paymentMethod, $cardData);
            echo json_encode($saveResult);
            break;
            
        case 'get_saved_methods':
            $savedMethods = getSavedPaymentMethods($userId);
            echo json_encode(['success' => true, 'methods' => $savedMethods]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log('Payment API Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Payment processing error occurred']);
}

// Payment processing functions
function processPayment($method, $amount, $data) {
    switch ($method) {
        case 'credit_card':
            return processCreditCard($amount, $data);
        case 'paypal':
            return processPayPal($amount, $data);
        case 'cash_on_delivery':
            return processCashOnDelivery($amount, $data);
        default:
            return ['success' => false, 'message' => 'Unsupported payment method'];
    }
}

function processCreditCard($amount, $data) {
    // Simulate credit card processing
    $cardNumber = $data['card_number'] ?? '';
    $expiryDate = $data['expiry_date'] ?? '';
    $cvv = $data['cvv'] ?? '';
    $cardName = $data['card_name'] ?? '';
    
    // Basic validation
    if (!$cardNumber || !$expiryDate || !$cvv || !$cardName) {
        return ['success' => false, 'message' => 'Missing credit card information'];
    }
    
    // Validate card number (basic Luhn algorithm)
    if (!validateCardNumber($cardNumber)) {
        return ['success' => false, 'message' => 'Invalid credit card number'];
    }
    
    // Validate expiry date
    if (!validateExpiryDate($expiryDate)) {
        return ['success' => false, 'message' => 'Invalid or expired card'];
    }
    
    // Simulate payment gateway response
    $reference = 'CC_' . time() . '_' . rand(1000, 9999);
    
    // In a real implementation, you would integrate with:
    // - Stripe: https://stripe.com/docs/api
    // - PayPal: https://developer.paypal.com/
    // - Square: https://developer.squareup.com/
    // - Razorpay: https://razorpay.com/docs/
    
    return [
        'success' => true,
        'reference' => $reference,
        'message' => 'Credit card payment processed successfully',
        'data' => [
            'last_four' => substr($cardNumber, -4),
            'card_type' => getCardType($cardNumber),
            'amount' => $amount
        ]
    ];
}

function processPayPal($amount, $data) {
    // Simulate PayPal processing
    $reference = 'PP_' . time() . '_' . rand(1000, 9999);
    
    return [
        'success' => true,
        'reference' => $reference,
        'message' => 'PayPal payment processed successfully',
        'data' => [
            'amount' => $amount,
            'currency' => 'USD'
        ]
    ];
}

function processCashOnDelivery($amount, $data) {
    // Cash on delivery doesn't require immediate processing
    $reference = 'COD_' . time() . '_' . rand(1000, 9999);
    
    return [
        'success' => true,
        'reference' => $reference,
        'message' => 'Cash on delivery order confirmed',
        'data' => [
            'amount' => $amount,
            'payment_due' => 'on_delivery'
        ]
    ];
}

function validateCardNumber($cardNumber) {
    // Remove spaces and non-digits
    $cardNumber = preg_replace('/\D/', '', $cardNumber);
    
    // Check length
    if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
        return false;
    }
    
    // Luhn algorithm
    $sum = 0;
    $alternate = false;
    
    for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
        $digit = intval($cardNumber[$i]);
        
        if ($alternate) {
            $digit *= 2;
            if ($digit > 9) {
                $digit = ($digit % 10) + 1;
            }
        }
        
        $sum += $digit;
        $alternate = !$alternate;
    }
    
    return ($sum % 10) == 0;
}

function validateExpiryDate($expiryDate) {
    if (!preg_match('/^(\d{2})\/(\d{2})$/', $expiryDate, $matches)) {
        return false;
    }
    
    $month = intval($matches[1]);
    $year = intval('20' . $matches[2]);
    
    if ($month < 1 || $month > 12) {
        return false;
    }
    
    $currentYear = date('Y');
    $currentMonth = date('n');
    
    if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
        return false;
    }
    
    return true;
}

function getCardType($cardNumber) {
    $cardNumber = preg_replace('/\D/', '', $cardNumber);
    
    if (preg_match('/^4/', $cardNumber)) {
        return 'Visa';
    } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
        return 'MasterCard';
    } elseif (preg_match('/^3[47]/', $cardNumber)) {
        return 'American Express';
    } elseif (preg_match('/^6(?:011|5)/', $cardNumber)) {
        return 'Discover';
    }
    
    return 'Unknown';
}

function validateCreditCard($cardNumber, $expiryDate, $cvv) {
    $errors = [];
    
    if (!validateCardNumber($cardNumber)) {
        $errors[] = 'Invalid credit card number';
    }
    
    if (!validateExpiryDate($expiryDate)) {
        $errors[] = 'Invalid or expired card';
    }
    
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        $errors[] = 'Invalid CVV';
    }
    
    return [
        'success' => empty($errors),
        'errors' => $errors,
        'card_type' => empty($errors) ? getCardType($cardNumber) : null
    ];
}

function savePaymentMethod($userId, $method, $cardData) {
    // In a real implementation, you would encrypt sensitive data
    // This is a simplified version for demonstration
    return [
        'success' => true,
        'message' => 'Payment method saved successfully'
    ];
}

function getSavedPaymentMethods($userId) {
    // Return saved payment methods (encrypted/tokenized)
    return [
        [
            'id' => 1,
            'type' => 'credit_card',
            'last_four' => '4242',
            'card_type' => 'Visa',
            'expiry' => '12/25'
        ]
    ];
}
?>
