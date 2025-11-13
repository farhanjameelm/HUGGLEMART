<?php
require_once '../config/config.php';
require_once '../classes/Bargain.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    redirect('../login.php');
}

$pageTitle = 'Manage Bargains';
$bargain = new Bargain();

// Get filter parameters
$statusFilter = $_GET['status'] ?? null;
$page = (int)($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Get bargains with pagination
$allBargains = $bargain->getAllBargains($limit, $offset, $statusFilter);
$bargainStats = $bargain->getBargainStats();

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
                    <a href="index.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="products.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-box me-2"></i>Products
                    </a>
                    <a href="bargains.php" class="list-group-item list-group-item-action active">
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
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-handshake text-primary me-2"></i>Manage Bargains
                    </h2>
                    <p class="text-muted mb-0">Review and respond to customer price negotiations</p>
                </div>
                <div>
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-1"><?php echo $bargainStats['pending_bargains'] ?? 0; ?></h4>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-1"><?php echo $bargainStats['accepted_bargains'] ?? 0; ?></h4>
                            <p class="mb-0">Accepted</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-1"><?php echo $bargainStats['rejected_bargains'] ?? 0; ?></h4>
                            <p class="mb-0">Rejected</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-1"><?php echo number_format($bargainStats['avg_discount_percentage'] ?? 0, 1); ?>%</h4>
                            <p class="mb-0">Avg Discount</p>
                        </div>
                    </div>
                </div>
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
                        Pending <span class="badge bg-warning ms-1"><?php echo $bargainStats['pending_bargains'] ?? 0; ?></span>
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
            
            <!-- Bargains Table -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        <?php echo $statusFilter ? ucfirst($statusFilter) . ' ' : ''; ?>Bargains
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($allBargains)): ?>
                        <div class="text-center p-5 text-muted">
                            <i class="fas fa-handshake fa-4x mb-3"></i>
                            <h5 class="text-muted">No Bargains Found</h5>
                            <p class="mb-0">
                                <?php if ($statusFilter): ?>
                                    No bargains with status "<?php echo ucfirst($statusFilter); ?>" found.
                                <?php else: ?>
                                    No bargains have been created yet.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Original</th>
                                        <th>Offer</th>
                                        <th>Current</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allBargains as $bargainItem): ?>
                                        <tr>
                                            <td><strong>#<?php echo $bargainItem['id']; ?></strong></td>
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
                                                        <strong><?php echo htmlspecialchars(substr($bargainItem['product_name'], 0, 30)); ?><?php echo strlen($bargainItem['product_name']) > 30 ? '...' : ''; ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo formatPrice($bargainItem['original_price']); ?></td>
                                            <td><?php echo formatPrice($bargainItem['offered_price']); ?></td>
                                            <td><strong><?php echo formatPrice($bargainItem['current_price']); ?></strong></td>
                                            <td>
                                                <?php 
                                                $discount = (($bargainItem['original_price'] - $bargainItem['current_price']) / $bargainItem['original_price']) * 100;
                                                ?>
                                                <span class="text-success">
                                                    <i class="fas fa-arrow-down me-1"></i><?php echo number_format($discount, 1); ?>%
                                                </span>
                                            </td>
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
                                                <small><?php echo date('M j, Y', strtotime($bargainItem['created_at'])); ?></small>
                                                <br>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($bargainItem['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="bargain-detail.php?id=<?php echo $bargainItem['id']; ?>" class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($bargainItem['status'] === 'pending' || $bargainItem['status'] === 'countered'): ?>
                                                        <button class="btn btn-outline-success" onclick="quickRespond(<?php echo $bargainItem['id']; ?>, 'accepted')" title="Accept">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning" onclick="showCounterModal(<?php echo $bargainItem['id']; ?>, <?php echo $bargainItem['current_price']; ?>)" title="Counter Offer">
                                                            <i class="fas fa-handshake"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="quickRespond(<?php echo $bargainItem['id']; ?>, 'rejected')" title="Reject">
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
                        
                        <!-- Pagination -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Showing <?php echo count($allBargains); ?> bargains
                                </small>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <li class="page-item active">
                                            <span class="page-link"><?php echo $page; ?></span>
                                        </li>
                                        
                                        <?php if (count($allBargains) == $limit): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?>">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Counter Offer Modal -->
<div class="modal fade" id="counterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-handshake text-warning me-2"></i>Make Counter Offer
                </h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="counterForm">
                    <input type="hidden" id="counterBargainId" name="bargain_id">
                    <input type="hidden" name="action" value="countered">
                    
                    <div class="mb-3">
                        <label class="form-label">Counter Offer Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="counterPrice" name="counter_price" step="0.01" min="1" required>
                        </div>
                        <div class="form-text">Current offer: <span id="currentOffer"></span></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Message (Optional)</label>
                        <textarea class="form-control" name="message" rows="3" 
                                  placeholder="Explain your counter offer..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitCounter()">
                    <i class="fas fa-handshake me-2"></i>Send Counter Offer
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$additionalJS = "
<script>
function quickRespond(bargainId, action) {
    const actionText = action === 'accepted' ? 'accept' : 'reject';
    
    if (!confirm(\`Are you sure you want to \${actionText} this bargain?\`)) {
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
            showToast(\`Bargain \${action} successfully!\`, 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || \`Failed to \${actionText} bargain\`, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

function showCounterModal(bargainId, currentPrice) {
    document.getElementById('counterBargainId').value = bargainId;
    document.getElementById('currentOffer').textContent = '$' + currentPrice.toFixed(2);
    document.getElementById('counterPrice').value = (currentPrice * 1.1).toFixed(2); // Suggest 10% higher
    
    const modal = new mdb.Modal(document.getElementById('counterModal'));
    modal.show();
}

function submitCounter() {
    const form = document.getElementById('counterForm');
    const formData = new FormData(form);
    
    fetch('../api/admin-respond-bargain.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Counter offer sent successfully!', 'success');
            const modal = mdb.Modal.getInstance(document.getElementById('counterModal'));
            modal.hide();
            
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to send counter offer', 'danger');
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
