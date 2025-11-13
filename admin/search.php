<?php
require_once '../config/config.php';
require_once '../classes/Product.php';
require_once '../classes/User.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Admin Search';
$query = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'all';

// Initialize classes
$productClass = new Product();
$userClass = new User();

$searchResults = [
    'products' => [],
    'users' => [],
    'orders' => [],
    'bargains' => []
];

$totalResults = 0;

if (!empty($query)) {
    // Search products
    if ($type === 'all' || $type === 'products') {
        try {
            $allProducts = $productClass->getAllProducts(100, 0);
            $searchResults['products'] = array_filter($allProducts, function($product) use ($query) {
                return stripos($product['name'], $query) !== false || 
                       stripos($product['description'], $query) !== false ||
                       stripos($product['category'], $query) !== false;
            });
        } catch (Exception $e) {
            $searchResults['products'] = [];
        }
    }
    
    // Search users
    if ($type === 'all' || $type === 'users') {
        try {
            $allUsers = $userClass->getAllUsers(100, 0);
            $searchResults['users'] = array_filter($allUsers, function($user) use ($query) {
                return stripos($user['username'], $query) !== false || 
                       stripos($user['email'], $query) !== false ||
                       stripos($user['first_name'], $query) !== false ||
                       stripos($user['last_name'], $query) !== false;
            });
        } catch (Exception $e) {
            $searchResults['users'] = [];
        }
    }
    
    // Mock search for orders and bargains (in real implementation, these would come from database)
    if ($type === 'all' || $type === 'orders') {
        $mockOrders = [
            ['id' => 1, 'user_name' => 'John Doe', 'total' => 299.99, 'status' => 'pending'],
            ['id' => 2, 'user_name' => 'Jane Smith', 'total' => 149.50, 'status' => 'processing']
        ];
        
        $searchResults['orders'] = array_filter($mockOrders, function($order) use ($query) {
            return stripos($order['user_name'], $query) !== false || 
                   strpos($order['id'], $query) !== false;
        });
    }
    
    if ($type === 'all' || $type === 'bargains') {
        $mockBargains = [
            ['id' => 1, 'product_name' => 'Smartphone', 'user_name' => 'John Doe', 'status' => 'pending'],
            ['id' => 2, 'product_name' => 'Laptop', 'user_name' => 'Jane Smith', 'status' => 'accepted']
        ];
        
        $searchResults['bargains'] = array_filter($mockBargains, function($bargain) use ($query) {
            return stripos($bargain['product_name'], $query) !== false || 
                   stripos($bargain['user_name'], $query) !== false;
        });
    }
    
    $totalResults = count($searchResults['products']) + count($searchResults['users']) + 
                   count($searchResults['orders']) + count($searchResults['bargains']);
}

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
                        <a class="nav-link" href="products.php">
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
                    <i class="fas fa-search text-primary me-2"></i>Admin Search
                </h1>
            </div>

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Search Query</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($query); ?>" 
                                       placeholder="Search products, users, orders, bargains..." autofocus>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search In</label>
                            <select class="form-select" name="type">
                                <option value="all" <?php echo $type === 'all' ? 'selected' : ''; ?>>All</option>
                                <option value="products" <?php echo $type === 'products' ? 'selected' : ''; ?>>Products</option>
                                <option value="users" <?php echo $type === 'users' ? 'selected' : ''; ?>>Users</option>
                                <option value="orders" <?php echo $type === 'orders' ? 'selected' : ''; ?>>Orders</option>
                                <option value="bargains" <?php echo $type === 'bargains' ? 'selected' : ''; ?>>Bargains</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($query)): ?>
                <!-- Search Results Summary -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Found <strong><?php echo $totalResults; ?></strong> results for "<strong><?php echo htmlspecialchars($query); ?></strong>"
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center">
                                <h4><?php echo count($searchResults['products']); ?></h4>
                                <p class="mb-0">Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h4><?php echo count($searchResults['users']); ?></h4>
                                <p class="mb-0">Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <h4><?php echo count($searchResults['orders']); ?></h4>
                                <p class="mb-0">Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h4><?php echo count($searchResults['bargains']); ?></h4>
                                <p class="mb-0">Bargains</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Results Tabs -->
                <ul class="nav nav-tabs" id="searchTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                            <i class="fas fa-box me-2"></i>Products (<?php echo count($searchResults['products']); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                            <i class="fas fa-users me-2"></i>Users (<?php echo count($searchResults['users']); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                            <i class="fas fa-shopping-cart me-2"></i>Orders (<?php echo count($searchResults['orders']); ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bargains-tab" data-bs-toggle="tab" data-bs-target="#bargains" type="button" role="tab">
                            <i class="fas fa-handshake me-2"></i>Bargains (<?php echo count($searchResults['bargains']); ?>)
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="searchTabContent">
                    <!-- Products Results -->
                    <div class="tab-pane fade show active" id="products" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($searchResults['products'])): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No products found</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>Stock</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($searchResults['products'] as $product): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img src="<?php echo !empty($product['images']) ? json_decode($product['images'])[0] : 'assets/images/placeholder.jpg'; ?>" 
                                                                     alt="Product" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                                <div>
                                                                    <div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div>
                                                                    <small class="text-muted">ID: <?php echo $product['id']; ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo ucfirst(str_replace('-', ' ', $product['category'])); ?></td>
                                                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                                                        <td><?php echo $product['stock_quantity']; ?></td>
                                                        <td>
                                                            <a href="products.php" class="btn btn-sm btn-outline-primary">View</a>
                                                            <a href="../product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-success">Preview</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Users Results -->
                    <div class="tab-pane fade" id="users" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($searchResults['users'])): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No users found</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Email</th>
                                                    <th>Type</th>
                                                    <th>Joined</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($searchResults['users'] as $user): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                    <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                                                    <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                        <td>
                                                            <?php if ($user['is_admin']): ?>
                                                                <span class="badge bg-danger">Admin</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-primary">Customer</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                                        <td>
                                                            <a href="users.php" class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Results -->
                    <div class="tab-pane fade" id="orders" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($searchResults['orders'])): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No orders found</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($searchResults['orders'] as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                                        <td>$<?php echo number_format($order['total'], 2); ?></td>
                                                        <td><span class="badge bg-warning"><?php echo ucfirst($order['status']); ?></span></td>
                                                        <td>
                                                            <a href="orders.php" class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bargains Results -->
                    <div class="tab-pane fade" id="bargains" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($searchResults['bargains'])): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No bargains found</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Bargain ID</th>
                                                    <th>Product</th>
                                                    <th>Customer</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($searchResults['bargains'] as $bargain): ?>
                                                    <tr>
                                                        <td>#<?php echo $bargain['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($bargain['product_name']); ?></td>
                                                        <td><?php echo htmlspecialchars($bargain['user_name']); ?></td>
                                                        <td><span class="badge bg-info"><?php echo ucfirst($bargain['status']); ?></span></td>
                                                        <td>
                                                            <a href="bargains.php" class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Search Tips -->
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                                <h4>Search the Admin Panel</h4>
                                <p class="text-muted mb-4">Use the search above to find products, users, orders, and bargains quickly.</p>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-lightbulb text-warning me-2"></i>Search Tips</h6>
                                        <ul class="list-unstyled text-start">
                                            <li>• Use specific product names or SKUs</li>
                                            <li>• Search by customer email or username</li>
                                            <li>• Use order IDs for quick lookup</li>
                                            <li>• Search by category names</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-keyboard text-info me-2"></i>Quick Actions</h6>
                                        <ul class="list-unstyled text-start">
                                            <li>• Press <kbd>Ctrl</kbd> + <kbd>K</kbd> to focus search</li>
                                            <li>• Use filters to narrow results</li>
                                            <li>• Click on results to view details</li>
                                            <li>• Export search results</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
// Focus search on Ctrl+K
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        document.querySelector('input[name="q"]').focus();
    }
});

// Auto-submit search after typing (debounced)
let searchTimeout;
document.querySelector('input[name="q"]').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (this.value.length >= 3) {
            this.form.submit();
        }
    }, 1000);
});
</script>
</body>
</html>
