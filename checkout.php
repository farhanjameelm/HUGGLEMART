<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

$pageTitle = 'Checkout';
$userId = $_SESSION['user_id'];

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get user information
try {
    $userQuery = "SELECT * FROM users WHERE id = :user_id";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->execute([':user_id' => $userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: login.php');
        exit();
    }
} catch (Exception $e) {
    error_log('Checkout User Error: ' . $e->getMessage());
    $user = [];
}

// Get cart items
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
    error_log('Checkout Cart Error: ' . $e->getMessage());
    $cartItems = [];
}

// If cart is empty, redirect to cart page
if (empty($cartItems)) {
    header('Location: cart.php?error=empty_cart');
    exit();
}

// Calculate totals
$subtotal = 0;
$totalWeight = 0;
$totalItems = 0;

foreach ($cartItems as $item) {
    $itemTotal = $item['price'] * $item['quantity'];
    $subtotal += $itemTotal;
    $totalWeight += ($item['weight'] ?? 0) * $item['quantity'];
    $totalItems += $item['quantity'];
}

// Calculate shipping
$shippingCost = 0;
if ($subtotal < 100) {
    $shippingCost = 10; // Free shipping over $100
} elseif ($totalWeight > 10) {
    $shippingCost = 15; // Heavy items
} else {
    $shippingCost = 5; // Standard shipping
}

// Calculate tax (8%)
$taxRate = 0.08;
$taxAmount = $subtotal * $taxRate;

// Calculate total
$total = $subtotal + $shippingCost + $taxAmount;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Create order
            $orderQuery = "INSERT INTO orders (user_id, subtotal, shipping_cost, tax_amount, total_amount, 
                          shipping_address, billing_address, payment_method, order_status, created_at) 
                          VALUES (:user_id, :subtotal, :shipping_cost, :tax_amount, :total_amount, 
                          :shipping_address, :billing_address, :payment_method, 'pending', NOW())";
            
            $shippingAddress = json_encode([
                'name' => $_POST['shipping_name'],
                'address' => $_POST['shipping_address'],
                'city' => $_POST['shipping_city'],
                'state' => $_POST['shipping_state'],
                'zip' => $_POST['shipping_zip'],
                'phone' => $_POST['shipping_phone']
            ]);
            
            $billingAddress = json_encode([
                'name' => $_POST['billing_name'],
                'address' => $_POST['billing_address'],
                'city' => $_POST['billing_city'],
                'state' => $_POST['billing_state'],
                'zip' => $_POST['billing_zip']
            ]);
            
            $orderStmt = $pdo->prepare($orderQuery);
            $orderStmt->execute([
                ':user_id' => $userId,
                ':subtotal' => $subtotal,
                ':shipping_cost' => $shippingCost,
                ':tax_amount' => $taxAmount,
                ':total_amount' => $total,
                ':shipping_address' => $shippingAddress,
                ':billing_address' => $billingAddress,
                ':payment_method' => $_POST['payment_method']
            ]);
            
            $orderId = $pdo->lastInsertId();
            
            // Create order items
            foreach ($cartItems as $item) {
                $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price, total) 
                                  VALUES (:order_id, :product_id, :quantity, :price, :total)";
                $orderItemStmt = $pdo->prepare($orderItemQuery);
                $orderItemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price'],
                    ':total' => $item['price'] * $item['quantity']
                ]);
                
                // Update product stock
                $updateStockQuery = "UPDATE products SET stock_quantity = stock_quantity - :quantity 
                                    WHERE id = :product_id";
                $updateStockStmt = $pdo->prepare($updateStockQuery);
                $updateStockStmt->execute([
                    ':quantity' => $item['quantity'],
                    ':product_id' => $item['product_id']
                ]);
            }
            
            // Clear cart
            $clearCartQuery = "DELETE FROM cart WHERE user_id = :user_id";
            $clearCartStmt = $pdo->prepare($clearCartQuery);
            $clearCartStmt->execute([':user_id' => $userId]);
            
            // Commit transaction
            $pdo->commit();
            
            // Redirect to order confirmation
            header('Location: order-confirmation.php?order_id=' . $orderId);
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction
            $pdo->rollback();
            error_log('Checkout Order Error: ' . $e->getMessage());
            $error = 'Failed to process order. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
    <!-- Progress Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="progress" style="height: 3px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">Cart</small>
                <small class="text-muted">Shipping</small>
                <small class="text-primary fw-bold">Checkout</small>
                <small class="text-muted">Confirmation</small>
            </div>
        </div>
    </div>

    <h1 class="text-center mb-5">
        <i class="fas fa-credit-card text-primary me-2"></i>
        Secure Checkout
    </h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" id="checkoutForm">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        
        <div class="row">
            <!-- Left Column - Forms -->
            <div class="col-lg-8">
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
                            <div class="col-md-6 mb-3">
                                <label for="shipping_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="shipping_name" name="shipping_name" 
                                       value="<?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="shipping_phone" name="shipping_phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Street Address *</label>
                            <input type="text" class="form-control" id="shipping_address" name="shipping_address" 
                                   placeholder="123 Main Street, Apt 4B" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shipping_city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="shipping_city" name="shipping_city" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="shipping_state" class="form-label">State *</label>
                                <select class="form-select" id="shipping_state" name="shipping_state" required>
                                    <option value="">Select State</option>
                                    <option value="AL">Alabama</option>
                                    <option value="CA">California</option>
                                    <option value="FL">Florida</option>
                                    <option value="NY">New York</option>
                                    <option value="TX">Texas</option>
                                    <!-- Add more states as needed -->
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="shipping_zip" class="form-label">ZIP Code *</label>
                                <input type="text" class="form-control" id="shipping_zip" name="shipping_zip" 
                                       pattern="[0-9]{5}(-[0-9]{4})?" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice text-primary me-2"></i>
                            Billing Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="same_as_shipping" checked>
                            <label class="form-check-label" for="same_as_shipping">
                                Same as shipping address
                            </label>
                        </div>
                        
                        <div id="billing_fields" style="display: none;">
                            <div class="mb-3">
                                <label for="billing_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="billing_name" name="billing_name">
                            </div>
                            <div class="mb-3">
                                <label for="billing_address" class="form-label">Street Address *</label>
                                <input type="text" class="form-control" id="billing_address" name="billing_address">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="billing_city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="billing_city" name="billing_city">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="billing_state" class="form-label">State *</label>
                                    <select class="form-select" id="billing_state" name="billing_state">
                                        <option value="">Select State</option>
                                        <option value="AL">Alabama</option>
                                        <option value="CA">California</option>
                                        <option value="FL">Florida</option>
                                        <option value="NY">New York</option>
                                        <option value="TX">Texas</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="billing_zip" class="form-label">ZIP Code *</label>
                                    <input type="text" class="form-control" id="billing_zip" name="billing_zip">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card text-primary me-2"></i>
                            Payment Method
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                    <label class="form-check-label" for="credit_card">
                                        <i class="fas fa-credit-card me-2"></i>Credit/Debit Card
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal me-2"></i>PayPal
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cash_on_delivery" value="cash_on_delivery">
                                    <label class="form-check-label" for="cash_on_delivery">
                                        <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="card_details">
                                    <div class="mb-3">
                                        <label for="card_number" class="form-label">Card Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="card_number" name="card_number" 
                                                   placeholder="1234 5678 9012 3456" maxlength="23" autocomplete="cc-number">
                                            <span class="input-group-text">
                                                <i id="card_icon" class="fab fa-cc-visa"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">Please enter a valid card number</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="expiry_date" class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control" id="expiry_date" name="expiry_date" 
                                                   placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
                                            <div class="invalid-feedback">Please enter a valid expiry date</div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="cvv" name="cvv" 
                                                   placeholder="123" maxlength="4" autocomplete="cc-csc">
                                            <div class="invalid-feedback">Please enter a valid CVV</div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="card_name" class="form-label">Name on Card</label>
                                        <input type="text" class="form-control" id="card_name" name="card_name" 
                                               placeholder="John Doe" autocomplete="cc-name">
                                        <div class="invalid-feedback">Please enter the name on the card</div>
                                    </div>
                                    
                                    <!-- Save Card Option -->
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="save_card" name="save_card">
                                        <label class="form-check-label" for="save_card">
                                            <i class="fas fa-shield-alt text-success me-1"></i>
                                            Save this card for future purchases (secure)
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- PayPal Details -->
                                <div id="paypal_details" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fab fa-paypal me-2"></i>
                                        You will be redirected to PayPal to complete your payment securely.
                                    </div>
                                    <div class="text-center">
                                        <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-200px.png" 
                                             alt="PayPal" style="height: 40px;">
                                    </div>
                                </div>
                                
                                <!-- Cash on Delivery Details -->
                                <div id="cod_details" style="display: none;">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        <strong>Cash on Delivery</strong><br>
                                        You will pay when your order is delivered to your address.
                                    </div>
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>Important Notes:</h6>
                                            <ul class="mb-0">
                                                <li>Payment is due upon delivery</li>
                                                <li>Please have exact change ready</li>
                                                <li>Additional COD fee: $2.99</li>
                                                <li>Available for orders under $500</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-bag text-primary me-2"></i>
                            Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($cartItems as $item): ?>
                                <?php 
                                $images = !empty($item['images']) ? json_decode($item['images'], true) : [];
                                $mainImage = !empty($images) ? $images[0] : 'assets/images/placeholder.jpg';
                                $itemTotal = $item['price'] * $item['quantity'];
                                ?>
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <img src="<?php echo $mainImage; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">$<?php echo number_format($itemTotal, 2); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Order Totals -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal (<?php echo $totalItems; ?> items):</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span><?php echo $shippingCost > 0 ? '$' . number_format($shippingCost, 2) : 'FREE'; ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span>$<?php echo number_format($taxAmount, 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit" name="place_order" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-lock me-2"></i>Place Order
                        </button>

                        <!-- Security Info -->
                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Your payment information is secure and encrypted
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize payment form
    initializePaymentForm();
    
    // Handle billing address toggle
    const sameAsShipping = document.getElementById('same_as_shipping');
    const billingFields = document.getElementById('billing_fields');
    
    sameAsShipping.addEventListener('change', function() {
        if (this.checked) {
            billingFields.style.display = 'none';
            copyShippingToBilling();
        } else {
            billingFields.style.display = 'block';
        }
    });
    
    // Copy shipping to billing
    function copyShippingToBilling() {
        document.getElementById('billing_name').value = document.getElementById('shipping_name').value;
        document.getElementById('billing_address').value = document.getElementById('shipping_address').value;
        document.getElementById('billing_city').value = document.getElementById('shipping_city').value;
        document.getElementById('billing_state').value = document.getElementById('shipping_state').value;
        document.getElementById('billing_zip').value = document.getElementById('shipping_zip').value;
    }
    
    // Enhanced form validation
    const checkoutForm = document.getElementById('checkoutForm');
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default to handle with JavaScript
        
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing Order...';
        submitBtn.disabled = true;
        
        // Validate payment method specific fields
        if (paymentMethod === 'credit_card') {
            const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
            const expiryDate = document.getElementById('expiry_date').value;
            const cvv = document.getElementById('cvv').value;
            const cardName = document.getElementById('card_name').value;
            
            if (!cardNumber || !expiryDate || !cvv || !cardName) {
                showToast('Please fill in all credit card details', 'danger');
                resetSubmitButton(submitBtn);
                return;
            }
            
            // Validate card number
            const cardValidation = validateCardInput(cardNumber);
            if (!cardValidation.valid) {
                showToast('Please enter a valid credit card number', 'danger');
                resetSubmitButton(submitBtn);
                return;
            }
        }
        
        // Submit form normally for server-side processing
        // The server will handle order creation and then redirect to payment processing
        this.submit();
    });
    
    // Reset submit button function
    function resetSubmitButton(btn) {
        btn.innerHTML = '<i class="fas fa-lock me-2"></i>Place Order';
        btn.disabled = false;
    }
    
    // Initialize billing address copy
    copyShippingToBilling();
    
    // Load saved payment methods
    loadSavedPaymentMethods().then(methods => {
        if (methods.length > 0) {
            displaySavedPaymentMethods(methods);
        }
    });
    
    // Display saved payment methods
    function displaySavedPaymentMethods(methods) {
        const cardDetails = document.getElementById('card_details');
        if (!cardDetails) return;
        
        const savedMethodsDiv = document.createElement('div');
        savedMethodsDiv.className = 'mb-3';
        savedMethodsDiv.innerHTML = `
            <label class="form-label">Saved Payment Methods</label>
            <div class="saved-methods">
                ${methods.map(method => `
                    <div class="card mb-2 saved-method" data-method-id="${method.id}">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-cc-${method.card_type.toLowerCase()} me-2"></i>
                                    **** **** **** ${method.last_four}
                                    <small class="text-muted">${method.expiry}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary use-saved-method">
                                    Use This Card
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
        
        cardDetails.insertBefore(savedMethodsDiv, cardDetails.firstChild);
        
        // Handle saved method selection
        document.querySelectorAll('.use-saved-method').forEach(btn => {
            btn.addEventListener('click', function() {
                const methodCard = this.closest('.saved-method');
                const methodId = methodCard.dataset.methodId;
                // You can implement logic to use saved payment method
                showToast('Saved payment method selected', 'success');
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
