<?php
require_once 'config/config.php';
$pageTitle = 'Button Test Page';
include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-5">
        <i class="fas fa-mouse-pointer text-primary me-2"></i>
        Button Functionality Test
    </h1>
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>
    
    <!-- Customer Buttons Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Customer Buttons
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Product Card for Testing -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Test Product</h6>
                                    <p class="card-text">A sample product for testing buttons</p>
                                    <p class="text-primary h5">$99.99</p>
                                    
                                    <div class="btn-group w-100 mb-2" role="group">
                                        <button class="btn btn-outline-primary" onclick="addToCart(1)">
                                            <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                        </button>
                                        <button class="btn btn-primary" onclick="buyNow(1)">
                                            <i class="fas fa-bolt me-1"></i>Buy Now
                                        </button>
                                    </div>
                                    
                                    <div class="btn-group w-100 mb-2" role="group">
                                        <button class="btn btn-outline-danger" onclick="addToWishlist(1)">
                                            <i class="fas fa-heart me-1"></i>Add to Wishlist
                                        </button>
                                        <button class="btn btn-success" onclick="openBargainModal(1, 'Test Product', 99.99)">
                                            <i class="fas fa-handshake me-1"></i>Negotiate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cart Controls -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Cart Controls</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Quantity Controls</label>
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary quantity-btn" type="button">-</button>
                                            <input type="number" class="form-control quantity-input text-center" value="1" min="1" data-product-id="1">
                                            <button class="btn btn-outline-secondary quantity-btn increment" type="button">+</button>
                                        </div>
                                    </div>
                                    
                                    <button class="btn btn-warning w-100 mb-2" onclick="updateCartQuantity(1, 2)">
                                        <i class="fas fa-sync me-1"></i>Update Quantity
                                    </button>
                                    
                                    <button class="btn btn-danger w-100" onclick="removeFromCart(1)">
                                        <i class="fas fa-trash me-1"></i>Remove from Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Admin Buttons Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>Admin Buttons
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- User Management -->
                        <div class="col-md-4">
                            <h6>User Management</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="viewUser(1)">
                                    <i class="fas fa-eye me-1"></i>View User
                                </button>
                                <button class="btn btn-outline-warning" onclick="editUser(1)">
                                    <i class="fas fa-edit me-1"></i>Edit User
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteUser(1)">
                                    <i class="fas fa-trash me-1"></i>Delete User
                                </button>
                            </div>
                        </div>
                        
                        <!-- Product Management -->
                        <div class="col-md-4">
                            <h6>Product Management</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="viewProduct(1)">
                                    <i class="fas fa-eye me-1"></i>View Product
                                </button>
                                <button class="btn btn-outline-warning" onclick="editProduct(1)">
                                    <i class="fas fa-edit me-1"></i>Edit Product
                                </button>
                                <button class="btn btn-outline-info" onclick="duplicateProduct(1)">
                                    <i class="fas fa-copy me-1"></i>Duplicate Product
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteProduct(1)">
                                    <i class="fas fa-trash me-1"></i>Delete Product
                                </button>
                            </div>
                        </div>
                        
                        <!-- Export Functions -->
                        <div class="col-md-4">
                            <h6>Export Functions</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-success" onclick="exportUsers()">
                                    <i class="fas fa-download me-1"></i>Export Users
                                </button>
                                <button class="btn btn-outline-success" onclick="exportOrders()">
                                    <i class="fas fa-download me-1"></i>Export Orders
                                </button>
                                <button class="btn btn-outline-success" onclick="exportProducts()">
                                    <i class="fas fa-download me-1"></i>Export Products
                                </button>
                                <button class="btn btn-outline-success" onclick="exportCarts()">
                                    <i class="fas fa-download me-1"></i>Export Carts
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Form Submission Test -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i>Form Submission Test
                    </h5>
                </div>
                <div class="card-body">
                    <form id="testForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Test Field</label>
                                    <input type="text" class="form-control" name="test_field" value="Test Value">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Another Field</label>
                                    <select class="form-select" name="test_select">
                                        <option value="option1">Option 1</option>
                                        <option value="option2">Option 2</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="submitForm('testForm', 'Form submitted successfully!')">
                            <i class="fas fa-paper-plane me-1"></i>Submit Test Form
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status Display -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>Testing Instructions:</h6>
                <ol>
                    <li><strong>Customer Buttons:</strong> Test add to cart, buy now, wishlist, and negotiation buttons</li>
                    <li><strong>Admin Buttons:</strong> Test user/product management and export functions</li>
                    <li><strong>Form Submission:</strong> Test AJAX form submission</li>
                    <li><strong>Check Console:</strong> Open browser developer tools to see any JavaScript errors</li>
                    <li><strong>Check Network:</strong> Monitor network requests to see API calls</li>
                </ol>
                <p class="mb-0"><strong>Note:</strong> Some buttons may show "file not found" errors for missing pages, but the JavaScript should execute without errors.</p>
            </div>
        </div>
    </div>
</div>

<!-- Include Bargain Modal -->
<?php include 'includes/bargain-modal.php'; ?>

<script>
// Initialize quantity controls
document.addEventListener('DOMContentLoaded', function() {
    initQuantityControls();
    
    // Test toast on page load
    setTimeout(() => {
        showToast('Button test page loaded successfully!', 'success');
    }, 1000);
});
</script>

<?php include 'includes/footer.php'; ?>
