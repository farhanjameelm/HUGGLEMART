<?php
require_once 'config/config.php';
require_once 'classes/Bargain.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$pageTitle = 'My Bargains';
$bargain = new Bargain();

// Get filter
$statusFilter = $_GET['status'] ?? null;

// Get user's bargains
$userBargains = $bargain->getUserBargains($_SESSION['user_id'], $statusFilter);

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-handshake text-warning me-2"></i>My Bargains
                    </h2>
                    <p class="text-muted mb-0">Track your price negotiations and offers</p>
                </div>
                <a href="dashboard.php" class="btn btn-outline-primary">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </div>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-pills mb-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo !$statusFilter ? 'active' : ''; ?>" href="bargains.php">
                        All Bargains
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $statusFilter === 'pending' ? 'active' : ''; ?>" href="bargains.php?status=pending">
                        Pending
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $statusFilter === 'countered' ? 'active' : ''; ?>" href="bargains.php?status=countered">
                        Countered
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $statusFilter === 'accepted' ? 'active' : ''; ?>" href="bargains.php?status=accepted">
                        Accepted
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $statusFilter === 'rejected' ? 'active' : ''; ?>" href="bargains.php?status=rejected">
                        Rejected
                    </a>
                </li>
            </ul>
            
            <!-- Bargains List -->
            <?php if (empty($userBargains)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-handshake fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Bargains Found</h4>
                    <p class="text-muted mb-4">
                        <?php if ($statusFilter): ?>
                            No bargains with status "<?php echo ucfirst($statusFilter); ?>" found.
                        <?php else: ?>
                            You haven't started any price negotiations yet.
                        <?php endif; ?>
                    </p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($userBargains as $bargainItem): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4">
                                            <?php 
                                            $productImage = !empty($bargainItem['product_images']) ? $bargainItem['product_images'][0] : 'https://via.placeholder.com/150x150?text=No+Image';
                                            ?>
                                            <img src="<?php echo $productImage; ?>" class="img-fluid rounded-3" alt="Product Image" style="height: 100px; object-fit: cover;">
                                        </div>
                                        <div class="col-8">
                                            <h6 class="card-title mb-2">
                                                <a href="product.php?slug=<?php echo $bargainItem['product_slug']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($bargainItem['product_name']); ?>
                                                </a>
                                            </h6>
                                            
                                            <!-- Status Badge -->
                                            <span class="badge bg-<?php 
                                                echo $bargainItem['status'] === 'pending' ? 'warning' : 
                                                    ($bargainItem['status'] === 'accepted' ? 'success' : 
                                                    ($bargainItem['status'] === 'rejected' ? 'danger' : 
                                                    ($bargainItem['status'] === 'countered' ? 'info' : 'secondary'))); 
                                            ?> mb-2">
                                                <?php echo ucfirst($bargainItem['status']); ?>
                                            </span>
                                            
                                            <!-- Price Information -->
                                            <div class="price-info">
                                                <small class="text-muted d-block">Original: <?php echo formatPrice($bargainItem['original_price']); ?></small>
                                                <small class="text-muted d-block">Your Offer: <?php echo formatPrice($bargainItem['offered_price']); ?></small>
                                                <strong class="text-primary">Current: <?php echo formatPrice($bargainItem['current_price']); ?></strong>
                                            </div>
                                            
                                            <!-- Discount Percentage -->
                                            <div class="mt-2">
                                                <?php 
                                                $discount = (($bargainItem['original_price'] - $bargainItem['current_price']) / $bargainItem['original_price']) * 100;
                                                ?>
                                                <small class="text-success">
                                                    <i class="fas fa-arrow-down me-1"></i><?php echo number_format($discount, 1); ?>% discount
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?php echo timeAgo($bargainItem['created_at']); ?>
                                        </small>
                                        <div class="btn-group btn-group-sm">
                                            <?php if (in_array($bargainItem['status'], ['pending', 'countered'])): ?>
                                                <a href="bargain-chat.php?id=<?php echo $bargainItem['id']; ?>" class="btn btn-primary">
                                                    <i class="fas fa-comments me-1"></i>Chat
                                                </a>
                                            <?php elseif ($bargainItem['status'] === 'accepted'): ?>
                                                <a href="bargain-chat.php?id=<?php echo $bargainItem['id']; ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <button class="btn btn-success" onclick="addToCart(<?php echo $bargainItem['product_id']; ?>, 1, <?php echo $bargainItem['current_price']; ?>)">
                                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                </button>
                                            <?php else: ?>
                                                <a href="bargain-chat.php?id=<?php echo $bargainItem['id']; ?>" class="btn btn-outline-secondary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <a href="product.php?slug=<?php echo $bargainItem['product_slug']; ?>" class="btn btn-primary">
                                                    <i class="fas fa-redo me-1"></i>Try Again
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Bargain Statistics -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h4 class="mb-3">Bargaining Statistics</h4>
                        <div class="row">
                            <?php
                            $stats = [
                                'total' => count($userBargains),
                                'pending' => count(array_filter($userBargains, fn($b) => $b['status'] === 'pending')),
                                'accepted' => count(array_filter($userBargains, fn($b) => $b['status'] === 'accepted')),
                                'rejected' => count(array_filter($userBargains, fn($b) => $b['status'] === 'rejected')),
                                'countered' => count(array_filter($userBargains, fn($b) => $b['status'] === 'countered'))
                            ];
                            
                            $successRate = $stats['total'] > 0 ? ($stats['accepted'] / $stats['total']) * 100 : 0;
                            ?>
                            
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="text-primary"><?php echo $stats['total']; ?></h3>
                                        <p class="text-muted mb-0">Total Bargains</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="text-success"><?php echo $stats['accepted']; ?></h3>
                                        <p class="text-muted mb-0">Accepted</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="text-warning"><?php echo $stats['pending'] + $stats['countered']; ?></h3>
                                        <p class="text-muted mb-0">Active</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h3 class="text-info"><?php echo number_format($successRate, 1); ?>%</h3>
                                        <p class="text-muted mb-0">Success Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
