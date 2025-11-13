<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = 'Order Confirmation';
$userId = $_SESSION['user_id'];
$orderId = $_GET['order_id'] ?? 0;

if (!$orderId) {
    header('Location: index.php');
    exit();
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get order details
try {
    $orderQuery = "SELECT o.*, u.email, u.first_name, u.last_name 
                   FROM orders o 
                   JOIN users u ON o.user_id = u.id 
                   WHERE o.id = :order_id AND o.user_id = :user_id";
    $orderStmt = $pdo->prepare($orderQuery);
    $orderStmt->execute([':order_id' => $orderId, ':user_id' => $userId]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    error_log('Order Confirmation Error: ' . $e->getMessage());
    header('Location: index.php');
    exit();
}

// Get order items
try {
    $itemsQuery = "SELECT oi.*, p.name, p.images 
                   FROM order_items oi 
                   JOIN products p ON oi.product_id = p.id 
                   WHERE oi.order_id = :order_id";
    $itemsStmt = $pdo->prepare($itemsQuery);
    $itemsStmt->execute([':order_id' => $orderId]);
    $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Order Items Error: ' . $e->getMessage());
    $orderItems = [];
}

// Parse addresses
$shippingAddress = json_decode($order['shipping_address'], true);
$billingAddress = json_decode($order['billing_address'], true);

include 'includes/header.php';
?>

<div class="container my-5">
    <!-- Success Header -->
    <div class="text-center mb-5">
        <div class="mb-4">
            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
        </div>
        <h1 class="text-success mb-3">Order Confirmed!</h1>
        <p class="lead text-muted">Thank you for your purchase. Your order has been successfully placed.</p>
        <div class="alert alert-success d-inline-block">
            <strong>Order #<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?></strong>
        </div>
    </div>

    <div class="row">
        <!-- Order Details -->
        <div class="col-lg-8">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag text-primary me-2"></i>
                        Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach ($orderItems as $item): ?>
                        <?php 
                        $images = !empty($item['images']) ? json_decode($item['images'], true) : [];
                        $mainImage = !empty($images) ? $images[0] : 'assets/images/placeholder.jpg';
                        ?>
                        <div class="d-flex align-items-center mb-3 pb-3 <?php echo end($orderItems) !== $item ? 'border-bottom' : ''; ?>">
                            <img src="<?php echo $mainImage; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                            <div class="ms-3 flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <p class="text-muted mb-1">Quantity: <?php echo $item['quantity']; ?></p>
                                <p class="text-muted mb-0">Price: $<?php echo number_format($item['price'], 2); ?> each</p>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0">$<?php echo number_format($item['total'], 2); ?></h6>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Order Totals -->
                    <div class="border-top pt-3 mt-3">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>$<?php echo number_format($order['shipping_cost'], 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span>$<?php echo number_format($order['tax_amount'], 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong class="text-primary">$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shipping-fast text-primary me-2"></i>
                        Shipping Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Shipping Address</h6>
                            <address>
                                <strong><?php echo htmlspecialchars($shippingAddress['name']); ?></strong><br>
                                <?php echo htmlspecialchars($shippingAddress['address']); ?><br>
                                <?php echo htmlspecialchars($shippingAddress['city']); ?>, 
                                <?php echo htmlspecialchars($shippingAddress['state']); ?> 
                                <?php echo htmlspecialchars($shippingAddress['zip']); ?><br>
                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($shippingAddress['phone']); ?>
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h6>Billing Address</h6>
                            <address>
                                <strong><?php echo htmlspecialchars($billingAddress['name']); ?></strong><br>
                                <?php echo htmlspecialchars($billingAddress['address']); ?><br>
                                <?php echo htmlspecialchars($billingAddress['city']); ?>, 
                                <?php echo htmlspecialchars($billingAddress['state']); ?> 
                                <?php echo htmlspecialchars($billingAddress['zip']); ?>
                            </address>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Payment Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <?php
                        $paymentIcons = [
                            'credit_card' => 'fas fa-credit-card',
                            'paypal' => 'fab fa-paypal',
                            'cash_on_delivery' => 'fas fa-money-bill-wave'
                        ];
                        $paymentNames = [
                            'credit_card' => 'Credit/Debit Card',
                            'paypal' => 'PayPal',
                            'cash_on_delivery' => 'Cash on Delivery'
                        ];
                        ?>
                        <i class="<?php echo $paymentIcons[$order['payment_method']] ?? 'fas fa-credit-card'; ?> text-primary me-2"></i>
                        <span><?php echo $paymentNames[$order['payment_method']] ?? 'Credit/Debit Card'; ?></span>
                        
                        <?php if ($order['payment_method'] === 'cash_on_delivery'): ?>
                            <span class="badge bg-warning ms-2">Payment on Delivery</span>
                        <?php else: ?>
                            <span class="badge bg-success ms-2">Paid</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status & Actions -->
        <div class="col-lg-4">
            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Order Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-warning me-2">Pending</div>
                        <span>Order is being processed</span>
                    </div>
                    
                    <div class="timeline">
                        <div class="timeline-item active">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Order Placed</h6>
                                <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Processing</h6>
                                <small class="text-muted">We're preparing your order</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Shipped</h6>
                                <small class="text-muted">Your order is on the way</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6>Delivered</h6>
                                <small class="text-muted">Order delivered to you</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog text-primary me-2"></i>
                        Order Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="order-details.php?id=<?php echo $orderId; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>View Order Details
                        </a>
                        <a href="invoice.php?order_id=<?php echo $orderId; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-file-pdf me-2"></i>Download Invoice
                        </a>
                        <a href="track-order.php?id=<?php echo $orderId; ?>" class="btn btn-outline-info">
                            <i class="fas fa-map-marker-alt me-2"></i>Track Order
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-headset text-primary me-2"></i>
                        Need Help?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">If you have any questions about your order, we're here to help!</p>
                    <div class="d-grid gap-2">
                        <a href="contact.php" class="btn btn-outline-success">
                            <i class="fas fa-envelope me-2"></i>Contact Support
                        </a>
                        <a href="tel:+1-800-123-4567" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i>Call Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Continue Shopping -->
    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-primary btn-lg">
            <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
        </a>
        <a href="profile.php" class="btn btn-outline-primary btn-lg ms-3">
            <i class="fas fa-user me-2"></i>View My Orders
        </a>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e9ecef;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-item.active .timeline-marker {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.timeline-item.active .timeline-content h6 {
    color: #28a745;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show success toast
    showToast('Order placed successfully! You will receive a confirmation email shortly.', 'success');
    
    // Auto-refresh page every 30 seconds to check for status updates
    setTimeout(() => {
        setInterval(() => {
            // You can add AJAX call here to check order status updates
            console.log('Checking for order status updates...');
        }, 30000);
    }, 5000);
});
</script>

<?php include 'includes/footer.php'; ?>
