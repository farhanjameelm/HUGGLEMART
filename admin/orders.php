<?php
require_once '../config/config.php';
require_once '../classes/User.php';
require_once '../classes/Product.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Orders Management';
$success = '';
$error = '';

// Mock orders data (in real implementation, this would come from database)
$orders = [
    [
        'id' => 1,
        'user_id' => 2,
        'user_name' => 'John Doe',
        'user_email' => 'john@example.com',
        'total_amount' => 299.99,
        'status' => 'pending',
        'created_at' => '2024-11-13 10:30:00',
        'items_count' => 3
    ],
    [
        'id' => 2,
        'user_id' => 3,
        'user_name' => 'Jane Smith',
        'user_email' => 'jane@example.com',
        'total_amount' => 149.50,
        'status' => 'processing',
        'created_at' => '2024-11-13 09:15:00',
        'items_count' => 2
    ],
    [
        'id' => 3,
        'user_id' => 4,
        'user_name' => 'Mike Johnson',
        'user_email' => 'mike@example.com',
        'total_amount' => 89.99,
        'status' => 'shipped',
        'created_at' => '2024-11-12 14:20:00',
        'items_count' => 1
    ],
    [
        'id' => 4,
        'user_id' => 5,
        'user_name' => 'Sarah Wilson',
        'user_email' => 'sarah@example.com',
        'total_amount' => 459.99,
        'status' => 'delivered',
        'created_at' => '2024-11-11 16:45:00',
        'items_count' => 5
    ]
];

// Handle order actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        $orderId = $_POST['order_id'] ?? 0;
        
        switch ($action) {
            case 'update_status':
                $newStatus = $_POST['status'] ?? '';
                $success = "Order #$orderId status updated to $newStatus successfully!";
                break;
            case 'delete_order':
                $success = "Order #$orderId deleted successfully!";
                break;
        }
    }
}

// Filter and search
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

if (!empty($statusFilter)) {
    $orders = array_filter($orders, fn($order) => $order['status'] === $statusFilter);
}

if (!empty($search)) {
    $orders = array_filter($orders, function($order) use ($search) {
        return stripos($order['user_name'], $search) !== false || 
               stripos($order['user_email'], $search) !== false ||
               strpos($order['id'], $search) !== false;
    });
}

// Calculate statistics
$totalOrders = count($orders);
$pendingOrders = count(array_filter($orders, fn($o) => $o['status'] === 'pending'));
$processingOrders = count(array_filter($orders, fn($o) => $o['status'] === 'processing'));
$completedOrders = count(array_filter($orders, fn($o) => in_array($o['status'], ['shipped', 'delivered'])));
$totalRevenue = array_sum(array_column($orders, 'total_amount'));

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
                        <a class="nav-link active" href="orders.php">
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
                    <i class="fas fa-shopping-cart text-primary me-2"></i>Orders Management
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <button class="btn btn-success" onclick="exportOrders()">
                            <i class="fas fa-download me-1"></i>Export
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
                                    <h4 class="card-title"><?php echo $totalOrders; ?></h4>
                                    <p class="card-text">Total Orders</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
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
                                    <h4 class="card-title"><?php echo $pendingOrders; ?></h4>
                                    <p class="card-text">Pending Orders</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
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
                                    <h4 class="card-title"><?php echo $processingOrders; ?></h4>
                                    <p class="card-text">Processing</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-cog fa-2x"></i>
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
                                    <h4 class="card-title">$<?php echo number_format($totalRevenue, 2); ?></h4>
                                    <p class="card-text">Total Revenue</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
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
                        <div class="col-md-4">
                            <label class="form-label">Search Orders</label>
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by order ID, customer name, email...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $statusFilter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" name="date_range">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="orders.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Orders List
                        <span class="badge bg-secondary ms-2"><?php echo count($orders); ?> orders</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No orders found</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($order['user_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($order['user_email']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $order['items_count']; ?> items</span>
                                            </td>
                                            <td>
                                                <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$order['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $statusColor; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="viewOrder(<?php echo $order['id']; ?>)" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success" onclick="updateOrderStatus(<?php echo $order['id']; ?>)" title="Update Status">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info" onclick="printOrder(<?php echo $order['id']; ?>)" title="Print Invoice">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Update Order Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" id="updateOrderId">
                    
                    <div class="mb-3">
                        <label class="form-label">Order ID</label>
                        <input type="text" class="form-control" id="displayOrderId" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select class="form-select" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes about this status update..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$additionalJS = "
<script>
function viewOrder(orderId) {
    // Redirect to order details page
    window.location.href = 'order-detail.php?id=' + orderId;
}

function updateOrderStatus(orderId) {
    document.getElementById('updateOrderId').value = orderId;
    document.getElementById('displayOrderId').value = '#' + String(orderId).padStart(6, '0');
    
    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    modal.show();
}

function printOrder(orderId) {
    // Open print view in new window
    window.open('print-order.php?id=' + orderId, '_blank', 'width=800,height=600');
}

function exportOrders() {
    // Export orders to CSV
    window.location.href = 'export-orders.php';
}

// Auto-refresh orders every 30 seconds
setInterval(function() {
    // Only refresh if no modals are open
    if (!document.querySelector('.modal.show')) {
        location.reload();
    }
}, 30000);
</script>
";

include '../includes/footer.php';
?>
