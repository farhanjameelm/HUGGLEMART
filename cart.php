<?php
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = 'Shopping Cart';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=cart.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get cart items from database
try {
    $cartQuery = "SELECT c.*, p.name, p.price, p.images, p.stock_quantity, p.weight 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = :user_id 
                  ORDER BY c.created_at DESC";
    $cartStmt = $pdo->prepare($cartQuery);
    $cartStmt->execute([':user_id' => $userId]);
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('Cart Error: ' . $e->getMessage());
    $cartItems = [];
}

// Calculate totals
$cartTotal = 0;
$cartCount = 0;

foreach ($cartItems as $item) {
    $itemTotal = $item['price'] * $item['quantity'];
    $cartTotal += $itemTotal;
    $cartCount += $item['quantity'];
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-shopping-cart text-primary me-2"></i>Shopping Cart
                    </h2>
                    <p class="text-muted mb-0">
                        <?php echo $cartCount; ?> item<?php echo $cartCount !== 1 ? 's' : ''; ?> in your cart
                    </p>
                </div>
                <div>
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
            
            <?php if (empty($cartItems)): ?>
                <!-- Empty Cart -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">Your Cart is Empty</h4>
                        <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                            </a>
                            <a href="bargaining-guide.php" class="btn btn-outline-info">
                                <i class="fas fa-handshake me-2"></i>Learn About Bargaining
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Cart Items
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <?php foreach ($cartItems as $index => $item): ?>
                                    <?php 
                                    $images = !empty($item['images']) ? json_decode($item['images'], true) : [];
                                    $mainImage = !empty($images) ? $images[0] : 'assets/images/placeholder.jpg';
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    ?>
                                    <div class="cart-item border-bottom p-4" data-product-id="<?php echo $item['product_id']; ?>">
                                        <div class="row align-items-center">
                                            <!-- Product Image -->
                                            <div class="col-md-2 col-3 mb-3 mb-md-0">
                                                <img src="<?php echo $mainImage; ?>" class="img-fluid rounded" alt="Product" style="height: 80px; object-fit: cover;">
                                            </div>
                                            
                                            <!-- Product Details -->
                                            <div class="col-md-4 col-9 mb-3 mb-md-0">
                                                <h6 class="mb-1">
                                                    <a href="product.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none">
                                                        <?php echo htmlspecialchars($item['name']); ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted mb-1 small">Price: $<?php echo number_format($item['price'], 2); ?></p>
                                                
                                                <?php if ($item['stock_quantity'] <= 0): ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php elseif ($item['stock_quantity'] <= 5): ?>
                                                    <span class="badge bg-warning">Low Stock</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Quantity Controls -->
                                            <div class="col-md-2 col-6 mb-3 mb-md-0">
                                                <label class="form-label small">Quantity</label>
                                                <div class="input-group input-group-sm">
                                                    <button class="btn btn-outline-secondary quantity-btn" type="button" data-product-id="<?php echo $item['product_id']; ?>">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" data-product-id="<?php echo $item['product_id']; ?>">
                                                    <button class="btn btn-outline-secondary quantity-btn increment" type="button" data-product-id="<?php echo $item['product_id']; ?>">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">Max: <?php echo $item['stock_quantity']; ?></small>
                                            </div>
                                            
                                            <!-- Price -->
                                            <div class="col-md-2 col-3 mb-3 mb-md-0 text-center">
                                                <div class="price-display">
                                                    <strong class="text-primary fs-5">$<?php echo number_format($item['price'], 2); ?></strong>
                                                    <br>
                                                    <small class="text-muted">each</small>
                                                </div>
                                            </div>
                                            
                                            <!-- Subtotal & Actions -->
                                            <div class="col-md-2 col-3 text-end">
                                                <div class="subtotal mb-2">
                                                    <strong class="text-success">$<?php echo number_format($itemTotal, 2); ?></strong>
                                                </div>
                                                <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Cart Actions -->
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-outline-secondary" onclick="clearCart()">
                                        <i class="fas fa-trash me-2"></i>Clear Cart
                                    </button>
                                    <a href="index.php" class="btn btn-outline-primary">
                                        <i class="fas fa-plus me-2"></i>Add More Items
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card sticky-top" style="top: 100px;">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>Order Summary
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span>Subtotal (<?php echo $cartCount; ?> items)</span>
                                    <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
                                </div>
                                
                                <?php 
                                $shipping = $cartTotal > 50 ? 0 : 5.99;
                                $tax = $cartTotal * 0.08; // 8% tax
                                $finalTotal = $cartTotal + $shipping + $tax;
                                ?>
                                
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span>Shipping</span>
                                    <span>
                                        <?php if ($shipping == 0): ?>
                                            <span class="text-success">FREE</span>
                                        <?php else: ?>
                                            $<?php echo number_format($shipping, 2); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <div class="summary-row d-flex justify-content-between mb-3">
                                    <span>Tax</span>
                                    <span>$<?php echo number_format($tax, 2); ?></span>
                                </div>
                                
                                <hr>
                                
                                <div class="summary-row d-flex justify-content-between mb-3">
                                    <strong>Total</strong>
                                    <strong class="text-primary fs-5">$<?php echo number_format($finalTotal, 2); ?></strong>
                                </div>
                                
                                <?php if ($shipping > 0 && $cartTotal < 50): ?>
                                    <div class="alert alert-info py-2 mb-3">
                                        <small>
                                            <i class="fas fa-truck me-1"></i>
                                            Add $<?php echo number_format(50 - $cartTotal, 2); ?> more for FREE shipping!
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Checkout Button -->
                                <a href="checkout.php" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                </a>
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Secure checkout with SSL encryption
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bargaining Tip -->
                        <div class="card mt-4">
                            <div class="card-body text-center">
                                <i class="fas fa-lightbulb text-warning fa-2x mb-3"></i>
                                <h6>💡 Pro Tip</h6>
                                <p class="small text-muted mb-3">
                                    Found items you like but want a better price? Try bargaining before adding to cart!
                                </p>
                                <a href="bargaining-guide.php" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-handshake me-1"></i>Learn How to Bargain
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$additionalJS = "
<script>
function updateQuantity(productId, change) {
    const input = document.querySelector(\`[data-product-id='\${productId}'] input[type='number']\`);
    const currentValue = parseInt(input.value);
    const newValue = Math.max(1, currentValue + change);
    const maxValue = parseInt(input.getAttribute('max'));
    
    if (newValue <= maxValue) {
        input.value = newValue;
        setQuantity(productId, newValue);
    }
}

function setQuantity(productId, quantity) {
    quantity = Math.max(1, parseInt(quantity));
    
    fetch('api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: \`product_id=\${productId}&quantity=\${quantity}&action=update\`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast(data.message || 'Failed to update quantity', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

function removeFromCart(productId) {
    if (!confirm('Remove this item from your cart?')) {
        return;
    }
    
    fetch('api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: \`product_id=\${productId}&action=remove\`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Item removed from cart', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to remove item', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }
    
    fetch('api/update-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Cart cleared', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to clear cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

function proceedToCheckout() {
    // This would typically redirect to a checkout page
    showToast('Checkout functionality coming soon!', 'info');
}
</script>
";

include 'includes/footer.php';
?>
