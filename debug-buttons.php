<?php
require_once 'config/config.php';
$pageTitle = 'Button Error Debug';
include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-5">
        <i class="fas fa-bug text-danger me-2"></i>
        Button Error Debugging
    </h1>
    
    <!-- Error Console -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>JavaScript Error Console
                    </h5>
                </div>
                <div class="card-body">
                    <div id="errorConsole" class="bg-dark text-light p-3 rounded" style="height: 200px; overflow-y: auto; font-family: monospace;">
                        <div class="text-success">Console initialized. Errors will appear here...</div>
                    </div>
                    <button class="btn btn-secondary btn-sm mt-2" onclick="clearErrorConsole()">
                        <i class="fas fa-trash me-1"></i>Clear Console
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Network Monitor -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-network-wired me-2"></i>Network Request Monitor
                    </h5>
                </div>
                <div class="card-body">
                    <div id="networkMonitor" class="bg-dark text-light p-3 rounded" style="height: 150px; overflow-y: auto; font-family: monospace;">
                        <div class="text-info">Network monitor active. API calls will appear here...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Buttons with Detailed Logging -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-mouse-pointer me-2"></i>Test Buttons with Debug Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Cart Functions -->
                        <div class="col-md-4">
                            <h6>Cart Functions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="debugAddToCart(1)">
                                    <i class="fas fa-shopping-cart me-1"></i>Debug Add to Cart
                                </button>
                                <button class="btn btn-outline-warning" onclick="debugUpdateCart(1, 2)">
                                    <i class="fas fa-sync me-1"></i>Debug Update Cart
                                </button>
                                <button class="btn btn-outline-danger" onclick="debugRemoveFromCart(1)">
                                    <i class="fas fa-trash me-1"></i>Debug Remove from Cart
                                </button>
                            </div>
                        </div>
                        
                        <!-- Wishlist Functions -->
                        <div class="col-md-4">
                            <h6>Wishlist Functions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-success" onclick="debugAddToWishlist(1)">
                                    <i class="fas fa-heart me-1"></i>Debug Add to Wishlist
                                </button>
                                <button class="btn btn-outline-info" onclick="debugRemoveFromWishlist(1)">
                                    <i class="fas fa-heart-broken me-1"></i>Debug Remove from Wishlist
                                </button>
                            </div>
                        </div>
                        
                        <!-- Other Functions -->
                        <div class="col-md-4">
                            <h6>Other Functions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-secondary" onclick="debugBuyNow(1)">
                                    <i class="fas fa-bolt me-1"></i>Debug Buy Now
                                </button>
                                <button class="btn btn-outline-dark" onclick="debugUpdateCounts()">
                                    <i class="fas fa-refresh me-1"></i>Debug Update Counts
                                </button>
                                <button class="btn btn-outline-purple" onclick="debugOpenBargainModal(1, 'Test Product', 99.99)">
                                    <i class="fas fa-handshake me-1"></i>Debug Bargain Modal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>System Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>JavaScript Libraries</h6>
                            <ul id="jsLibraries" class="list-unstyled">
                                <!-- Will be populated by JavaScript -->
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>User Status</h6>
                            <ul class="list-unstyled">
                                <li><strong>Logged In:</strong> <span id="loginStatus">Checking...</span></li>
                                <li><strong>User ID:</strong> <span id="userIdStatus">Checking...</span></li>
                                <li><strong>Admin:</strong> <span id="adminStatus">Checking...</span></li>
                                <li><strong>CSRF Token:</strong> <span id="csrfStatus">Checking...</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- API Test Results -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-flask me-2"></i>API Endpoint Tests
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Cart API</h6>
                            <div id="cartApiStatus" class="alert alert-secondary">Not tested</div>
                        </div>
                        <div class="col-md-4">
                            <h6>Wishlist API</h6>
                            <div id="wishlistApiStatus" class="alert alert-secondary">Not tested</div>
                        </div>
                        <div class="col-md-4">
                            <h6>Bargains API</h6>
                            <div id="bargainsApiStatus" class="alert alert-secondary">Not tested</div>
                        </div>
                    </div>
                    <button class="btn btn-primary" onclick="testAllApis()">
                        <i class="fas fa-play me-1"></i>Test All APIs
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bargain Modal -->
<?php include 'includes/bargain-modal.php'; ?>

<script>
// Enhanced error logging
let errorLog = [];
let networkLog = [];

// Override console.error to capture errors
const originalConsoleError = console.error;
console.error = function(...args) {
    originalConsoleError.apply(console, args);
    logError('Console Error: ' + args.join(' '));
};

// Capture JavaScript errors
window.addEventListener('error', function(e) {
    logError(`JavaScript Error: ${e.message} at ${e.filename}:${e.lineno}:${e.colno}`);
});

// Capture unhandled promise rejections
window.addEventListener('unhandledrejection', function(e) {
    logError(`Unhandled Promise Rejection: ${e.reason}`);
});

// Log error function
function logError(message) {
    const timestamp = new Date().toLocaleTimeString();
    const errorMessage = `[${timestamp}] ${message}`;
    errorLog.push(errorMessage);
    
    const console = document.getElementById('errorConsole');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-danger';
    errorDiv.textContent = errorMessage;
    console.appendChild(errorDiv);
    console.scrollTop = console.scrollHeight;
}

// Log network request
function logNetwork(method, url, status, response) {
    const timestamp = new Date().toLocaleTimeString();
    const networkMessage = `[${timestamp}] ${method} ${url} - Status: ${status}`;
    networkLog.push({timestamp, method, url, status, response});
    
    const monitor = document.getElementById('networkMonitor');
    const networkDiv = document.createElement('div');
    networkDiv.className = status >= 200 && status < 300 ? 'text-success' : 'text-danger';
    networkDiv.textContent = networkMessage;
    monitor.appendChild(networkDiv);
    monitor.scrollTop = monitor.scrollHeight;
}

// Clear error console
function clearErrorConsole() {
    document.getElementById('errorConsole').innerHTML = '<div class="text-success">Console cleared.</div>';
    errorLog = [];
}

// Debug versions of functions
function debugAddToCart(productId) {
    logError('DEBUG: Starting addToCart(' + productId + ')');
    try {
        addToCart(productId);
    } catch (e) {
        logError('DEBUG: addToCart failed - ' + e.message);
    }
}

function debugUpdateCart(productId, quantity) {
    logError('DEBUG: Starting updateCartQuantity(' + productId + ', ' + quantity + ')');
    try {
        updateCartQuantity(productId, quantity);
    } catch (e) {
        logError('DEBUG: updateCartQuantity failed - ' + e.message);
    }
}

function debugRemoveFromCart(productId) {
    logError('DEBUG: Starting removeFromCart(' + productId + ')');
    try {
        removeFromCart(productId);
    } catch (e) {
        logError('DEBUG: removeFromCart failed - ' + e.message);
    }
}

function debugAddToWishlist(productId) {
    logError('DEBUG: Starting addToWishlist(' + productId + ')');
    try {
        addToWishlist(productId);
    } catch (e) {
        logError('DEBUG: addToWishlist failed - ' + e.message);
    }
}

function debugRemoveFromWishlist(productId) {
    logError('DEBUG: Starting removeFromWishlist(' + productId + ')');
    try {
        removeFromWishlist(productId);
    } catch (e) {
        logError('DEBUG: removeFromWishlist failed - ' + e.message);
    }
}

function debugBuyNow(productId) {
    logError('DEBUG: Starting buyNow(' + productId + ')');
    try {
        buyNow(productId);
    } catch (e) {
        logError('DEBUG: buyNow failed - ' + e.message);
    }
}

function debugUpdateCounts() {
    logError('DEBUG: Starting updateCounts()');
    try {
        updateCounts();
    } catch (e) {
        logError('DEBUG: updateCounts failed - ' + e.message);
    }
}

function debugOpenBargainModal(productId, productName, price) {
    logError('DEBUG: Starting openBargainModal(' + productId + ', "' + productName + '", ' + price + ')');
    try {
        openBargainModal(productId, productName, price);
    } catch (e) {
        logError('DEBUG: openBargainModal failed - ' + e.message);
    }
}

// Test API endpoints
function testAllApis() {
    testCartApi();
    testWishlistApi();
    testBarganApi();
}

function testCartApi() {
    const formData = new FormData();
    formData.append('product_id', 1);
    formData.append('quantity', 1);
    formData.append('action', 'add');
    
    fetch(getApiUrl('cart.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => {
        logNetwork('POST', 'cart.php', response.status, 'Testing...');
        return response.json();
    })
    .then(data => {
        const status = document.getElementById('cartApiStatus');
        if (data.success) {
            status.className = 'alert alert-success';
            status.textContent = 'Cart API working: ' + data.message;
        } else {
            status.className = 'alert alert-warning';
            status.textContent = 'Cart API response: ' + data.message;
        }
    })
    .catch(error => {
        const status = document.getElementById('cartApiStatus');
        status.className = 'alert alert-danger';
        status.textContent = 'Cart API error: ' + error.message;
        logError('Cart API test failed: ' + error.message);
    });
}

function testWishlistApi() {
    const formData = new FormData();
    formData.append('product_id', 1);
    formData.append('action', 'add');
    
    fetch(getApiUrl('wishlist.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => {
        logNetwork('POST', 'wishlist.php', response.status, 'Testing...');
        return response.json();
    })
    .then(data => {
        const status = document.getElementById('wishlistApiStatus');
        if (data.success) {
            status.className = 'alert alert-success';
            status.textContent = 'Wishlist API working: ' + data.message;
        } else {
            status.className = 'alert alert-warning';
            status.textContent = 'Wishlist API response: ' + data.message;
        }
    })
    .catch(error => {
        const status = document.getElementById('wishlistApiStatus');
        status.className = 'alert alert-danger';
        status.textContent = 'Wishlist API error: ' + error.message;
        logError('Wishlist API test failed: ' + error.message);
    });
}

function testBarganApi() {
    const formData = new FormData();
    formData.append('product_id', 1);
    formData.append('offered_price', 50.00);
    formData.append('message', 'Test bargain');
    
    fetch(getApiUrl('bargains.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => {
        logNetwork('POST', 'bargains.php', response.status, 'Testing...');
        return response.json();
    })
    .then(data => {
        const status = document.getElementById('bargainsApiStatus');
        if (data.success) {
            status.className = 'alert alert-success';
            status.textContent = 'Bargains API working: ' + data.message;
        } else {
            status.className = 'alert alert-warning';
            status.textContent = 'Bargains API response: ' + data.message;
        }
    })
    .catch(error => {
        const status = document.getElementById('bargainsApiStatus');
        status.className = 'alert alert-danger';
        status.textContent = 'Bargains API error: ' + error.message;
        logError('Bargains API test failed: ' + error.message);
    });
}

// Check system status
document.addEventListener('DOMContentLoaded', function() {
    // Check JavaScript libraries
    const jsLibraries = document.getElementById('jsLibraries');
    const libraries = [
        {name: 'Bootstrap', check: () => typeof bootstrap !== 'undefined'},
        {name: 'MDB', check: () => typeof mdb !== 'undefined'},
        {name: 'jQuery', check: () => typeof $ !== 'undefined'},
        {name: 'Main.js Functions', check: () => typeof addToCart === 'function'}
    ];
    
    libraries.forEach(lib => {
        const li = document.createElement('li');
        const isAvailable = lib.check();
        li.innerHTML = `<i class="fas fa-${isAvailable ? 'check text-success' : 'times text-danger'} me-2"></i>${lib.name}: ${isAvailable ? 'Available' : 'Missing'}`;
        jsLibraries.appendChild(li);
    });
    
    // Check user status
    document.getElementById('loginStatus').textContent = window.userLoggedIn ? 'Yes' : 'No';
    document.getElementById('userIdStatus').textContent = window.userId || 'Not set';
    document.getElementById('adminStatus').textContent = window.isAdmin ? 'Yes' : 'No';
    document.getElementById('csrfStatus').textContent = getCSRFToken() ? 'Present' : 'Missing';
    
    logError('DEBUG: Page loaded, system status checked');
});

// Override fetch to monitor network requests
const originalFetch = window.fetch;
window.fetch = function(...args) {
    const url = args[0];
    const options = args[1] || {};
    const method = options.method || 'GET';
    
    return originalFetch.apply(this, args)
        .then(response => {
            logNetwork(method, url, response.status, 'Success');
            return response;
        })
        .catch(error => {
            logNetwork(method, url, 'ERROR', error.message);
            throw error;
        });
};
</script>

<?php include 'includes/footer.php'; ?>
