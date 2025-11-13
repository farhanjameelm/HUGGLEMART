<?php
require_once 'config/config.php';
require_once 'classes/User.php';
require_once 'classes/Bargain.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$pageTitle = 'Dashboard';
$user = new User();
$bargain = new Bargain();

// Get user stats
$userStats = $user->getUserStats($_SESSION['user_id']);
$recentBargains = $bargain->getUserBargains($_SESSION['user_id'], null);
$recentBargains = array_slice($recentBargains, 0, 5); // Get only 5 recent bargains

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Welcome Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>! 👋</h2>
                    <p class="text-muted mb-0">Here's what's happening with your account</p>
                </div>
                <div>
                    <a href="profile.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-5">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo $userStats['total_orders'] ?? 0; ?></h3>
                                    <p class="mb-0">Total Orders</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-shopping-bag fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="orders.php" class="text-white text-decoration-none">
                                View Orders <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo formatPrice($userStats['total_spent'] ?? 0); ?></h3>
                                    <p class="mb-0">Total Spent</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="orders.php" class="text-white text-decoration-none">
                                View History <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo $userStats['total_bargains'] ?? 0; ?></h3>
                                    <p class="mb-0">Total Bargains</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-handshake fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="bargains.php" class="text-white text-decoration-none">
                                View Bargains <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1"><?php echo $userStats['accepted_bargains'] ?? 0; ?></h3>
                                    <p class="mb-0">Successful Deals</p>
                                </div>
                                <div class="text-white-50">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="bargains.php?status=accepted" class="text-white text-decoration-none">
                                View Deals <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row mb-5">
                <div class="col-12">
                    <h4 class="mb-3">Quick Actions</h4>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="index.php" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                                    <h6 class="card-title">Continue Shopping</h6>
                                    <p class="card-text text-muted">Browse our latest products</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="cart.php" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                                    <h6 class="card-title">View Cart</h6>
                                    <p class="card-text text-muted">Review items in your cart</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="wishlist.php" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                                    <h6 class="card-title">Wishlist</h6>
                                    <p class="card-text text-muted">View saved items</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="bargains.php?status=pending" class="card text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-handshake fa-3x text-warning mb-3"></i>
                                    <h6 class="card-title">Active Bargains</h6>
                                    <p class="card-text text-muted">Check negotiation status</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Bargains -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-handshake me-2"></i>Recent Bargains
                            </h5>
                            <a href="bargains.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>View All
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($recentBargains)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">No Bargains Yet</h6>
                                    <p class="text-muted mb-3">Start negotiating prices on products you love!</p>
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($recentBargains as $bargainItem): ?>
                                        <div class="col-lg-6 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-3">
                                                            <?php 
                                                            $productImage = !empty($bargainItem['product_images']) ? $bargainItem['product_images'][0] : 'https://via.placeholder.com/80x80?text=No+Image';
                                                            ?>
                                                            <img src="<?php echo $productImage; ?>" class="img-fluid rounded" alt="Product" style="height: 60px; object-fit: cover;">
                                                        </div>
                                                        <div class="col-9">
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($bargainItem['product_name']); ?></h6>
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <small class="text-muted">
                                                                    Offer: <?php echo formatPrice($bargainItem['current_price']); ?>
                                                                </small>
                                                                <span class="badge bg-<?php 
                                                                    echo $bargainItem['status'] === 'pending' ? 'warning' : 
                                                                        ($bargainItem['status'] === 'accepted' ? 'success' : 
                                                                        ($bargainItem['status'] === 'rejected' ? 'danger' : 'info')); 
                                                                ?>">
                                                                    <?php echo ucfirst($bargainItem['status']); ?>
                                                                </span>
                                                            </div>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted"><?php echo timeAgo($bargainItem['created_at']); ?></small>
                                                                <a href="bargain-chat.php?id=<?php echo $bargainItem['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-comments me-1"></i>View
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
