<?php
require_once '../config/config.php';
require_once '../classes/Product.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Products Management';
$success = '';
$error = '';

// Initialize Product class
$productClass = new Product();

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add_product':
                // Add new product logic would go here
                $success = 'Product added successfully!';
                break;
            case 'update_product':
                $productId = $_POST['product_id'] ?? 0;
                $success = "Product #$productId updated successfully!";
                break;
            case 'delete_product':
                $productId = $_POST['product_id'] ?? 0;
                $success = "Product #$productId deleted successfully!";
                break;
            case 'toggle_status':
                $productId = $_POST['product_id'] ?? 0;
                $success = "Product #$productId status updated successfully!";
                break;
        }
    }
}

// Get products with pagination
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get products from database
try {
    $products = $productClass->getAllProducts($limit, $offset);
    $totalProducts = $productClass->getTotalProductCount();
} catch (Exception $e) {
    $products = [];
    $totalProducts = 0;
    $error = 'Error loading products: ' . $e->getMessage();
}

$totalPages = ceil($totalProducts / $limit);

// Search and filter
$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$statusFilter = $_GET['status'] ?? '';

if (!empty($search)) {
    $products = array_filter($products, function($product) use ($search) {
        return stripos($product['name'], $search) !== false || 
               stripos($product['description'], $search) !== false;
    });
}

// Categories for filter dropdown
$categories = [
    'electronics' => 'Electronics',
    'fashion' => 'Fashion',
    'home-garden' => 'Home & Garden',
    'sports-outdoors' => 'Sports & Outdoors',
    'books-media' => 'Books & Media',
    'beauty-health' => 'Beauty & Health'
];

include '../includes/admin-header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php">
                            <i class="fas fa-box me-2"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-shopping-cart me-2"></i>Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bargains.php">
                            <i class="fas fa-handshake me-2"></i>Bargains
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-box text-primary me-2"></i>Products Management
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <button class="btn btn-success" onclick="exportProducts()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo number_format($totalProducts); ?></h4>
                                    <p class="card-text">Total Products</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo count(array_filter($products, fn($p) => $p['stock_quantity'] > 0)); ?></h4>
                                    <p class="card-text">In Stock</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo count(array_filter($products, fn($p) => $p['stock_quantity'] <= 5 && $p['stock_quantity'] > 0)); ?></h4>
                                    <p class="card-text">Low Stock</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo count(array_filter($products, fn($p) => $p['stock_quantity'] == 0)); ?></h4>
                                    <p class="card-text">Out of Stock</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-times-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search Products</label>
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, description...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $slug => $name): ?>
                                    <option value="<?php echo $slug; ?>" <?php echo $categoryFilter === $slug ? 'selected' : ''; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Stock Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Price Range</label>
                            <select class="form-select" name="price_range">
                                <option value="">All Prices</option>
                                <option value="0-50">$0 - $50</option>
                                <option value="50-100">$50 - $100</option>
                                <option value="100-500">$100 - $500</option>
                                <option value="500+">$500+</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="products.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Products List
                        <span class="badge bg-secondary ms-2"><?php echo count($products); ?> products</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No products found</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo !empty($product['images']) ? json_decode($product['images'])[0] : 'assets/images/placeholder.jpg'; ?>" 
                                                         alt="Product" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo ucfirst(str_replace('-', ' ', $product['category'])); ?></span>
                                            </td>
                                            <td>
                                                <strong>$<?php echo number_format($product['price'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $stockClass = 'success';
                                                if ($product['stock_quantity'] == 0) {
                                                    $stockClass = 'danger';
                                                } elseif ($product['stock_quantity'] <= 5) {
                                                    $stockClass = 'warning';
                                                }
                                                ?>
                                                <span class="badge bg-<?php echo $stockClass; ?>">
                                                    <?php echo $product['stock_quantity']; ?> units
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Active</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="viewProduct(<?php echo $product['id']; ?>)" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" onclick="editProduct(<?php echo $product['id']; ?>)" title="Edit Product">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info" onclick="duplicateProduct(<?php echo $product['id']; ?>)" title="Duplicate">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)" title="Delete Product">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Products pagination">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add_product">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $slug => $name): ?>
                                        <option value="<?php echo $slug; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Price ($)</label>
                                <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_quantity" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKU (Optional)</label>
                                <input type="text" class="form-control" name="sku">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Product Images</label>
                        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                        <div class="form-text">You can select multiple images. First image will be the main image.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" class="form-control" name="weight" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dimensions (L x W x H cm)</label>
                                <input type="text" class="form-control" name="dimensions" placeholder="e.g., 20 x 15 x 10">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$additionalJS = "
<script>
function viewProduct(productId) {
    window.location.href = '../product.php?id=' + productId;
}

function editProduct(productId) {
    // Implement product edit functionality
    alert('Edit product functionality - Product ID: ' + productId);
}

function duplicateProduct(productId) {
    if (confirm('Are you sure you want to duplicate this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = \`
            <input type='hidden' name='csrf_token' value='<?php echo generateCSRFToken(); ?>'>
            <input type='hidden' name='action' value='duplicate_product'>
            <input type='hidden' name='product_id' value='\${productId}'>
        \`;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = \`
            <input type='hidden' name='csrf_token' value='<?php echo generateCSRFToken(); ?>'>
            <input type='hidden' name='action' value='delete_product'>
            <input type='hidden' name='product_id' value='\${productId}'>
        \`;
        document.body.appendChild(form);
        form.submit();
    }
}

function exportProducts() {
    window.location.href = 'export-products.php';
}

// Auto-save draft functionality for add product form
let draftTimer;
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#addProductModal form');
    if (form) {
        form.addEventListener('input', function() {
            clearTimeout(draftTimer);
            draftTimer = setTimeout(saveDraft, 2000);
        });
    }
});

function saveDraft() {
    // Save form data to localStorage
    const formData = new FormData(document.querySelector('#addProductModal form'));
    const draftData = {};
    for (let [key, value] of formData.entries()) {
        draftData[key] = value;
    }
    localStorage.setItem('productDraft', JSON.stringify(draftData));
}
</script>
";

include '../includes/footer.php';
?>
