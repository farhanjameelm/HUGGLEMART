<?php
require_once '../config/config.php';
require_once '../classes/User.php';
require_once '../classes/Product.php';
require_once '../classes/Bargain.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    redirect('../login.php');
}

$pageTitle = 'Admin Dashboard';

try {
    // Get statistics
    $product = new Product();
    $bargain = new Bargain();
    $user = new User();

    $productStats = $product->getProductStats();
    $bargainStats = $bargain->getBargainStats();

    // Get recent bargains
    $recentBargains = $bargain->getAllBargains(10, 0);
    
    // Get additional statistics
    $totalUsers = count($user->getAllUsers());
    
    // Calculate today's statistics
    $database = new Database();
    $conn = $database->getConnection();
    
    $todayQuery = "SELECT 
        COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_bargains,
        COUNT(CASE WHEN DATE(created_at) = CURDATE() AND status = 'accepted' THEN 1 END) as today_accepted
        FROM bargains";
    $todayStmt = $conn->prepare($todayQuery);
    $todayStmt->execute();
    $todayStats = $todayStmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = "Error loading dashboard data: " . $e->getMessage();
    $productStats = ['total_products' => 0, 'active_products' => 0];
    $bargainStats = ['total_bargains' => 0, 'pending_bargains' => 0, 'accepted_bargains' => 0, 'avg_discount_percentage' => 0];
    $recentBargains = [];
    $totalUsers = 0;
    $todayStats = ['today_bargains' => 0, 'today_accepted' => 0];
}

include '../includes/header.php';
?>

<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Admin Panel
                    </h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="products.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-box me-2"></i>Products
                    </a>
                    <a href="bargains.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-handshake me-2"></i>Bargains
                    </a>
                    <a href="orders.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-cart me-2"></i>Orders
                    </a>
                    <a href="users.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i>Users
                    </a>
                    <a href="settings.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="../index.php" class="list-group-item list-group-item-action text-primary">
                        <i class="fas fa-store me-2"></i>View Store
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>Admin Dashboard
                    </h2>
                    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</p>
                </div>
                <div class="text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                        <a href="../index.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-store me-1"></i>View Store
                        </a>
                    </div>
                    <div class="mt-1">
                        <small class="text-muted">Last updated: <?php echo date('M j, Y g:i A'); ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><?php echo $productStats['total_products'] ?? 0; ?></h4>
                                    <p class="mb-0">Total Products</p>
                                    <small class="text-white-50">
                                        <?php echo $productStats['active_products'] ?? 0; ?> active
                                    </small>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-box fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="products.php" class="text-white text-decoration-none">
                                <small>Manage Products <i class="fas fa-arrow-right ms-1"></i></small>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><?php echo $bargainStats['pending_bargains'] ?? 0; ?></h4>
                                    <p class="mb-0">Pending Bargains</p>
                                    <small class="text-white-50">
                                        <?php echo $todayStats['today_bargains'] ?? 0; ?> today
                                    </small>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-handshake fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="bargains.php?status=pending" class="text-white text-decoration-none">
                                <small>Review Bargains <i class="fas fa-arrow-right ms-1"></i></small>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><?php echo $bargainStats['accepted_bargains'] ?? 0; ?></h4>
                                    <p class="mb-0">Successful Deals</p>
                                    <small class="text-white-50">
                                        <?php echo $todayStats['today_accepted'] ?? 0; ?> today
                                    </small>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="bargains.php?status=accepted" class="text-white text-decoration-none">
                                <small>View Deals <i class="fas fa-arrow-right ms-1"></i></small>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><?php echo $totalUsers ?? 0; ?></h4>
                                    <p class="mb-0">Total Users</p>
                                    <small class="text-white-50">
                                        <?php echo number_format($bargainStats['avg_discount_percentage'] ?? 0, 1); ?>% avg discount
                                    </small>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="users.php" class="text-white text-decoration-none">
                                <small>Manage Users <i class="fas fa-arrow-right ms-1"></i></small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Stats Row -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-chart-line me-2"></i>Quick Statistics
                            </h6>
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <div class="border-end">
                                        <h5 class="text-primary mb-1"><?php echo $bargainStats['total_bargains'] ?? 0; ?></h5>
                                        <small class="text-muted">Total Bargains</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="border-end">
                                        <?php 
                                        $successRate = ($bargainStats['total_bargains'] ?? 0) > 0 ? 
                                            (($bargainStats['accepted_bargains'] ?? 0) / $bargainStats['total_bargains']) * 100 : 0;
                                        ?>
                                        <h5 class="text-success mb-1"><?php echo number_format($successRate, 1); ?>%</h5>
                                        <small class="text-muted">Success Rate</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="border-end">
                                        <h5 class="text-warning mb-1"><?php echo $bargainStats['rejected_bargains'] ?? 0; ?></h5>
                                        <small class="text-muted">Rejected</small>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <h5 class="text-info mb-1"><?php echo $productStats['out_of_stock'] ?? 0; ?></h5>
                                    <small class="text-muted">Out of Stock</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Bargains -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-handshake me-2"></i>Recent Bargains
                    </h5>
                    <a href="bargains.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentBargains)): ?>
                        <div class="text-center p-4 text-muted">
                            <i class="fas fa-handshake fa-3x mb-3"></i>
                            <p>No bargains found</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Original Price</th>
                                        <th>Offered Price</th>
                                        <th>Current Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentBargains as $bargainItem): ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($bargainItem['first_name'] . ' ' . $bargainItem['last_name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($bargainItem['email']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                    $productImage = !empty($bargainItem['product_images']) ? $bargainItem['product_images'][0] : 'https://via.placeholder.com/40x40?text=No+Image';
                                                    ?>
                                                    <img src="<?php echo $productImage; ?>" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Product">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($bargainItem['product_name']); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo formatPrice($bargainItem['original_price']); ?></td>
                                            <td><?php echo formatPrice($bargainItem['offered_price']); ?></td>
                                            <td><strong><?php echo formatPrice($bargainItem['current_price']); ?></strong></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $bargainItem['status'] === 'pending' ? 'warning' : 
                                                        ($bargainItem['status'] === 'accepted' ? 'success' : 
                                                        ($bargainItem['status'] === 'rejected' ? 'danger' : 'info')); 
                                                ?>">
                                                    <?php echo ucfirst($bargainItem['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo timeAgo($bargainItem['created_at']); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="bargain-detail.php?id=<?php echo $bargainItem['id']; ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($bargainItem['status'] === 'pending' || $bargainItem['status'] === 'countered'): ?>
                                                        <button class="btn btn-outline-success" onclick="respondToBargain(<?php echo $bargainItem['id']; ?>, 'accepted')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="respondToBargain(<?php echo $bargainItem['id']; ?>, 'rejected')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
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
</div>

<?php
$additionalJS = "
<script>
function respondToBargain(bargainId, action) {
    if (!confirm('Are you sure you want to ' + action + ' this bargain?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('bargain_id', bargainId);
    formData.append('action', action);
    
    fetch('../api/admin-respond-bargain.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Bargain ' + action + ' successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to respond to bargain', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}
</script>
";

include '../includes/footer.php';
?>
