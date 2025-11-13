<?php
require_once '../config/config.php';
require_once '../classes/User.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Cart Management';
$success = '';
$error = '';

// Mock cart data (in real implementation, this would come from database)
$activeCarts = [
    [
        'user_id' => 2,
        'user_name' => 'John Doe',
        'user_email' => 'john@example.com',
        'items_count' => 3,
        'total_value' => 299.99,
        'last_updated' => '2024-11-13 10:30:00',
        'items' => [
            ['name' => 'Smartphone', 'price' => 199.99, 'quantity' => 1],
            ['name' => 'Phone Case', 'price' => 29.99, 'quantity' => 2],
            ['name' => 'Screen Protector', 'price' => 19.99, 'quantity' => 2]
        ]
    ],
    [
        'user_id' => 3,
        'user_name' => 'Jane Smith',
        'user_email' => 'jane@example.com',
        'items_count' => 2,
        'total_value' => 149.50,
        'last_updated' => '2024-11-13 09:15:00',
        'items' => [
            ['name' => 'Laptop Bag', 'price' => 79.99, 'quantity' => 1],
            ['name' => 'Wireless Mouse', 'price' => 69.51, 'quantity' => 1]
        ]
    ],
    [
        'user_id' => 4,
        'user_name' => 'Mike Johnson',
        'user_email' => 'mike@example.com',
        'items_count' => 1,
        'total_value' => 89.99,
        'last_updated' => '2024-11-12 14:20:00',
        'items' => [
            ['name' => 'Bluetooth Headphones', 'price' => 89.99, 'quantity' => 1]
        ]
    ]
];

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $userId = $_POST['user_id'] ?? 0;
        
        switch ($action) {
            case 'clear_cart':
                $success = "Cart cleared for user ID $userId successfully!";
                break;
            case 'send_reminder':
                $success = "Reminder email sent to user ID $userId successfully!";
                break;
            case 'convert_to_order':
                $success = "Cart converted to order for user ID $userId successfully!";
                break;
        }
    }
}

// Calculate statistics
$totalActiveCarts = count($activeCarts);
$totalCartValue = array_sum(array_column($activeCarts, 'total_value'));
$totalItems = array_sum(array_column($activeCarts, 'items_count'));
$averageCartValue = $totalActiveCarts > 0 ? $totalCartValue / $totalActiveCarts : 0;

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
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-basket me-2"></i>Carts
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
                    <i class="fas fa-shopping-basket text-primary me-2"></i>Cart Management
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <button class="btn btn-success" onclick="exportCarts()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <button class="btn btn-warning" onclick="sendBulkReminders()">
                            <i class="fas fa-envelope me-1"></i>Send Reminders
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
                                    <h4 class="card-title"><?php echo $totalActiveCarts; ?></h4>
                                    <p class="card-text">Active Carts</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-basket fa-2x"></i>
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
                                    <h4 class="card-title">$<?php echo number_format($totalCartValue, 2); ?></h4>
                                    <p class="card-text">Total Cart Value</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo $totalItems; ?></h4>
                                    <p class="card-text">Total Items</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-boxes fa-2x"></i>
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
                                    <h4 class="card-title">$<?php echo number_format($averageCartValue, 2); ?></h4>
                                    <p class="card-text">Average Cart Value</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search Carts</label>
                            <input type="text" class="form-control" name="search" placeholder="Search by customer name, email...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cart Value</label>
                            <select class="form-select" name="value_range">
                                <option value="">All Values</option>
                                <option value="0-50">$0 - $50</option>
                                <option value="50-100">$50 - $100</option>
                                <option value="100-500">$100 - $500</option>
                                <option value="500+">$500+</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Last Updated</label>
                            <select class="form-select" name="last_updated">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Items Count</label>
                            <select class="form-select" name="items_count">
                                <option value="">All Counts</option>
                                <option value="1">1 item</option>
                                <option value="2-5">2-5 items</option>
                                <option value="6+">6+ items</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Carts -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-basket me-2"></i>Active Shopping Carts
                        <span class="badge bg-secondary ms-2"><?php echo count($activeCarts); ?> carts</span>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($activeCarts)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No active carts found</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($activeCarts as $cart): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($cart['user_name']); ?>
                                                </h6>
                                                <span class="badge bg-primary"><?php echo $cart['items_count']; ?> items</span>
                                            </div>
                                            <small class="text-muted"><?php echo htmlspecialchars($cart['user_email']); ?></small>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>Total Value:</span>
                                                    <strong>$<?php echo number_format($cart['total_value'], 2); ?></strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Last Updated:</span>
                                                    <small><?php echo date('M j, g:i A', strtotime($cart['last_updated'])); ?></small>
                                                </div>
                                            </div>
                                            
                                            <h6>Items in Cart:</h6>
                                            <div class="list-group list-group-flush">
                                                <?php foreach ($cart['items'] as $item): ?>
                                                    <div class="list-group-item px-0 py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <div class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></div>
                                                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                                            </div>
                                                            <span class="text-primary">$<?php echo number_format($item['price'], 2); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewCart(<?php echo $cart['user_id']; ?>)" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success btn-sm" onclick="sendReminder(<?php echo $cart['user_id']; ?>)" title="Send Reminder">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                                <button class="btn btn-outline-info btn-sm" onclick="convertToOrder(<?php echo $cart['user_id']; ?>)" title="Convert to Order">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="clearCart(<?php echo $cart['user_id']; ?>)" title="Clear Cart">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cart Analytics -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-chart-pie me-2"></i>Cart Value Distribution</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="cartValueChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-clock me-2"></i>Cart Age Analysis</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Today</span>
                                    <span class="badge bg-success">2 carts</span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Yesterday</span>
                                    <span class="badge bg-warning">1 cart</span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>This Week</span>
                                    <span class="badge bg-info">0 carts</span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span>Older</span>
                                    <span class="badge bg-danger">0 carts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
$additionalJS = "
<script>
function viewCart(userId) {
    // Redirect to user's cart details
    window.location.href = 'user-cart.php?user_id=' + userId;
}

function sendReminder(userId) {
    if (confirm('Send cart abandonment reminder to this customer?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = \`
            <input type='hidden' name='csrf_token' value='<?php echo generateCSRFToken(); ?>'>
            <input type='hidden' name='action' value='send_reminder'>
            <input type='hidden' name='user_id' value='\${userId}'>
        \`;
        document.body.appendChild(form);
        form.submit();
    }
}

function convertToOrder(userId) {
    if (confirm('Convert this cart to an order? This will create an order for the customer.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = \`
            <input type='hidden' name='csrf_token' value='<?php echo generateCSRFToken(); ?>'>
            <input type='hidden' name='action' value='convert_to_order'>
            <input type='hidden' name='user_id' value='\${userId}'>
        \`;
        document.body.appendChild(form);
        form.submit();
    }
}

function clearCart(userId) {
    if (confirm('Are you sure you want to clear this cart? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = \`
            <input type='hidden' name='csrf_token' value='<?php echo generateCSRFToken(); ?>'>
            <input type='hidden' name='action' value='clear_cart'>
            <input type='hidden' name='user_id' value='\${userId}'>
        \`;
        document.body.appendChild(form);
        form.submit();
    }
}

function sendBulkReminders() {
    if (confirm('Send cart abandonment reminders to all customers with active carts?')) {
        alert('Bulk reminder functionality would be implemented here');
    }
}

function exportCarts() {
    window.location.href = 'export-carts.php';
}

// Auto-refresh every 60 seconds
setInterval(function() {
    location.reload();
}, 60000);
</script>
";

include '../includes/footer.php';
?>
