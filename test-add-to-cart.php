<?php
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = 'Test Add to Cart';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=test-add-to-cart.php');
    exit();
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get a sample product for testing
try {
    $productQuery = "SELECT * FROM products WHERE status = 'active' LIMIT 1";
    $productStmt = $pdo->prepare($productQuery);
    $productStmt->execute();
    $testProduct = $productStmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $testProduct = null;
}

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-5">
        <i class="fas fa-shopping-cart text-primary me-2"></i>
        Test Add to Cart Functionality
    </h1>
    
    <!-- User Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-user me-2"></i>User Status</h5>
                <p><strong>Logged in as:</strong> User ID <?php echo $_SESSION['user_id']; ?></p>
                <p><strong>Session Active:</strong> ✅ Yes</p>
            </div>
        </div>
    </div>
    
    <!-- API Test -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-code me-2"></i>API Endpoint Test
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>API URL:</strong> <code>api/add-to-cart.php</code></p>
                    <p><strong>Method:</strong> POST</p>
                    <p><strong>Required Parameters:</strong></p>
                    <ul>
                        <li><code>product_id</code> (integer)</li>
                        <li><code>quantity</code> (integer, default: 1)</li>
                    </ul>
                    
                    <button class="btn btn-info" onclick="testAPI()">
                        <i class="fas fa-flask me-2"></i>Test API Directly
                    </button>
                    <div id="apiTestResult" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-mouse-pointer me-2"></i>Button Click Test
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>JavaScript Function:</strong> <code>addToCart(productId, quantity)</code></p>
                    <p><strong>Expected Behavior:</strong></p>
                    <ul>
                        <li>Show success toast notification</li>
                        <li>Update cart count in header</li>
                        <li>Add item to database</li>
                    </ul>
                    
                    <button class="btn btn-primary" onclick="testAddToCartFunction()">
                        <i class="fas fa-cart-plus me-2"></i>Test addToCart() Function
                    </button>
                    <div id="functionTestResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sample Product Test -->
    <?php if ($testProduct): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Sample Product Test
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <?php if (!empty($testProduct['images'])): ?>
                                <?php $images = json_decode($testProduct['images'], true); ?>
                                <img src="<?php echo htmlspecialchars($images[0] ?? 'assets/images/no-image.jpg'); ?>" 
                                     class="img-fluid rounded" alt="Product Image">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6><?php echo htmlspecialchars($testProduct['name']); ?></h6>
                            <p class="text-muted"><?php echo htmlspecialchars(substr($testProduct['description'], 0, 100)) . '...'; ?></p>
                            <p><strong>Price:</strong> $<?php echo number_format($testProduct['price'], 2); ?></p>
                            <p><strong>Stock:</strong> <?php echo $testProduct['stock_quantity']; ?> available</p>
                            <p><strong>Product ID:</strong> <?php echo $testProduct['id']; ?></p>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="testQuantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="testQuantity" value="1" min="1" max="<?php echo $testProduct['stock_quantity']; ?>">
                            </div>
                            
                            <button class="btn btn-primary w-100 mb-2" 
                                    onclick="addToCart(<?php echo $testProduct['id']; ?>, document.getElementById('testQuantity').value)">
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                            
                            <button class="btn btn-outline-secondary w-100" 
                                    onclick="testRealButton(<?php echo $testProduct['id']; ?>)">
                                <i class="fas fa-test-tube me-2"></i>Test This Button
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>No Test Product Available</h5>
                <p>No active products found in the database. Please add some products first.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Debug Information -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bug me-2"></i>Debug Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>JavaScript Console</h6>
                            <p>Open browser developer tools (F12) and check the Console tab for any JavaScript errors.</p>
                            
                            <h6>Network Tab</h6>
                            <p>Check the Network tab to see if API requests are being sent and what responses are received.</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Common Issues</h6>
                            <ul>
                                <li><strong>404 Error:</strong> API endpoint not found</li>
                                <li><strong>500 Error:</strong> Server/database error</li>
                                <li><strong>No Response:</strong> JavaScript function not called</li>
                                <li><strong>Login Required:</strong> User session expired</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-secondary" onclick="showDebugInfo()">
                            <i class="fas fa-info-circle me-2"></i>Show Debug Info
                        </button>
                        <div id="debugInfo" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Test API directly
function testAPI() {
    const resultDiv = document.getElementById('apiTestResult');
    resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Testing API...';
    
    const formData = new FormData();
    formData.append('product_id', <?php echo $testProduct['id'] ?? 1; ?>);
    formData.append('quantity', 1);
    
    fetch('api/add-to-cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('API Response Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('API Response Data:', data);
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success">✅ API is working! ' + data.message + '</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger">❌ API Error: ' + (data.message || 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        console.error('API Test Error:', error);
        resultDiv.innerHTML = '<div class="alert alert-danger">❌ Network Error: ' + error.message + '</div>';
    });
}

// Test addToCart function
function testAddToCartFunction() {
    const resultDiv = document.getElementById('functionTestResult');
    resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Testing function...';
    
    // Check if function exists
    if (typeof addToCart === 'function') {
        resultDiv.innerHTML = '<div class="alert alert-success">✅ addToCart function exists and is callable</div>';
        
        // Test the function
        try {
            addToCart(<?php echo $testProduct['id'] ?? 1; ?>, 1);
            setTimeout(() => {
                resultDiv.innerHTML += '<div class="alert alert-info">Function called successfully. Check for toast notification above.</div>';
            }, 1000);
        } catch (error) {
            resultDiv.innerHTML = '<div class="alert alert-danger">❌ Function Error: ' + error.message + '</div>';
        }
    } else {
        resultDiv.innerHTML = '<div class="alert alert-danger">❌ addToCart function not found! Check if main.js is loaded.</div>';
    }
}

// Test real button
function testRealButton(productId) {
    console.log('Testing real button click for product ID:', productId);
    
    // This simulates exactly what happens when a real add to cart button is clicked
    const quantity = document.getElementById('testQuantity').value;
    addToCart(productId, quantity);
}

// Show debug information
function showDebugInfo() {
    const debugDiv = document.getElementById('debugInfo');
    
    const debugInfo = {
        'User Agent': navigator.userAgent,
        'Current URL': window.location.href,
        'Session Storage': Object.keys(sessionStorage).length + ' items',
        'Local Storage': Object.keys(localStorage).length + ' items',
        'JavaScript Enabled': true,
        'Fetch API Available': typeof fetch !== 'undefined',
        'addToCart Function': typeof addToCart !== 'undefined' ? 'Available' : 'Missing',
        'showToast Function': typeof showToast !== 'undefined' ? 'Available' : 'Missing',
        'updateCounts Function': typeof updateCounts !== 'undefined' ? 'Available' : 'Missing'
    };
    
    let debugHtml = '<div class="alert alert-light"><h6>Debug Information:</h6><ul>';
    for (const [key, value] of Object.entries(debugInfo)) {
        debugHtml += `<li><strong>${key}:</strong> ${value}</li>`;
    }
    debugHtml += '</ul></div>';
    
    debugDiv.innerHTML = debugHtml;
    debugDiv.style.display = 'block';
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Add to Cart test page loaded');
    console.log('addToCart function available:', typeof addToCart !== 'undefined');
    console.log('showToast function available:', typeof showToast !== 'undefined');
});
</script>

<?php include 'includes/footer.php'; ?>
