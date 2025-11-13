<?php
require_once '../config/config.php';
require_once '../classes/Bargain.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    redirect('../login.php');
}

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$bargainId = (int)$_GET['id'];
$bargain = new Bargain();

$bargainData = $bargain->getBargainById($bargainId);
if (!$bargainData) {
    redirect('index.php');
}

$messages = $bargain->getBargainMessages($bargainId);
$pageTitle = 'Bargain Detail - ' . $bargainData['product_name'];

include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Admin Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="bargains.php">Bargains</a></li>
                    <li class="breadcrumb-item active">Bargain Detail</li>
                </ol>
            </nav>
            
            <!-- Bargain Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-handshake me-2"></i>Bargain #<?php echo $bargainId; ?>
                        </h5>
                        <div>
                            <a href="bargains.php" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i>Back to Bargains
                            </a>
                            <?php if (in_array($bargainData['status'], ['pending', 'countered'])): ?>
                                <div class="btn-group">
                                    <button class="btn btn-success btn-sm" onclick="showResponseModal('accepted')">
                                        <i class="fas fa-check me-1"></i>Accept
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="showResponseModal('countered')">
                                        <i class="fas fa-handshake me-1"></i>Counter
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="showResponseModal('rejected')">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <?php 
                            $productImage = !empty($bargainData['product_images']) ? $bargainData['product_images'][0] : 'https://via.placeholder.com/200x200?text=No+Image';
                            ?>
                            <img src="<?php echo $productImage; ?>" class="img-fluid rounded-3" alt="Product Image">
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3"><?php echo htmlspecialchars($bargainData['product_name']); ?></h6>
                            
                            <!-- Customer Info -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Customer Information</h6>
                                <p class="mb-1"><strong><?php echo htmlspecialchars($bargainData['first_name'] . ' ' . $bargainData['last_name']); ?></strong></p>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($bargainData['email']); ?></p>
                            </div>
                            
                            <!-- Price Information -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Price Details</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Original Price</small>
                                        <p class="mb-1"><strong><?php echo formatPrice($bargainData['original_price']); ?></strong></p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Customer Offer</small>
                                        <p class="mb-1"><strong><?php echo formatPrice($bargainData['offered_price']); ?></strong></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Current Price</small>
                                        <p class="mb-1"><strong class="text-primary"><?php echo formatPrice($bargainData['current_price']); ?></strong></p>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Discount</small>
                                        <?php 
                                        $discount = (($bargainData['original_price'] - $bargainData['current_price']) / $bargainData['original_price']) * 100;
                                        ?>
                                        <p class="mb-1"><strong class="text-success"><?php echo number_format($discount, 1); ?>%</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <!-- Status and Timeline -->
                            <div class="text-center">
                                <span class="badge bg-<?php 
                                    echo $bargainData['status'] === 'pending' ? 'warning' : 
                                        ($bargainData['status'] === 'accepted' ? 'success' : 
                                        ($bargainData['status'] === 'rejected' ? 'danger' : 'info')); 
                                ?> fs-6 mb-3">
                                    <?php echo ucfirst($bargainData['status']); ?>
                                </span>
                                
                                <div class="mb-3">
                                    <small class="text-muted d-block">Created</small>
                                    <strong><?php echo date('M j, Y g:i A', strtotime($bargainData['created_at'])); ?></strong>
                                </div>
                                
                                <?php if ($bargainData['expires_at']): ?>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            <?php echo strtotime($bargainData['expires_at']) > time() ? 'Expires' : 'Expired'; ?>
                                        </small>
                                        <strong class="<?php echo strtotime($bargainData['expires_at']) > time() ? 'text-warning' : 'text-danger'; ?>">
                                            <?php echo date('M j, Y g:i A', strtotime($bargainData['expires_at'])); ?>
                                        </strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Messages -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Negotiation History
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="chat-container" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($messages)): ?>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-comments fa-3x mb-3"></i>
                                <p>No messages yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message-item p-3 border-bottom">
                                    <div class="d-flex <?php echo $message['sender_type'] === 'customer' ? 'justify-content-start' : 'justify-content-end'; ?>">
                                        <div class="message-bubble <?php echo $message['sender_type'] === 'customer' ? 'bg-light' : 'bg-primary text-white'; ?> rounded-3 p-3" style="max-width: 70%;">
                                            <!-- Message Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="<?php echo $message['sender_type'] === 'customer' ? 'text-muted' : 'text-white-50'; ?>">
                                                    <strong>
                                                        <?php echo $message['sender_type'] === 'customer' ? 'Customer' : 'Admin'; ?>
                                                    </strong>
                                                </small>
                                                <small class="<?php echo $message['sender_type'] === 'customer' ? 'text-muted' : 'text-white-50'; ?>">
                                                    <?php echo timeAgo($message['created_at']); ?>
                                                </small>
                                            </div>
                                            
                                            <!-- Message Type Badge -->
                                            <?php if ($message['message_type'] !== 'message'): ?>
                                                <div class="mb-2">
                                                    <span class="badge <?php 
                                                        echo $message['message_type'] === 'offer' || $message['message_type'] === 'counter_offer' ? 'bg-warning' : 
                                                            ($message['message_type'] === 'accept' ? 'bg-success' : 'bg-danger'); 
                                                    ?>">
                                                        <?php 
                                                        switch($message['message_type']) {
                                                            case 'offer': echo 'Initial Offer'; break;
                                                            case 'counter_offer': echo 'Counter Offer'; break;
                                                            case 'accept': echo 'Accepted'; break;
                                                            case 'reject': echo 'Rejected'; break;
                                                            default: echo ucfirst($message['message_type']);
                                                        }
                                                        ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Price Offer -->
                                            <?php if ($message['offered_price']): ?>
                                                <div class="price-offer mb-2">
                                                    <strong class="<?php echo $message['sender_type'] === 'customer' ? 'text-primary' : 'text-warning'; ?>">
                                                        <?php echo formatPrice($message['offered_price']); ?>
                                                    </strong>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Message Text -->
                                            <?php if ($message['message']): ?>
                                                <div class="message-text">
                                                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-handshake text-primary me-2"></i>Respond to Bargain
                </h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="responseForm">
                    <input type="hidden" id="responseAction" name="action">
                    <input type="hidden" name="bargain_id" value="<?php echo $bargainId; ?>">
                    
                    <div id="counterPriceSection" class="mb-3" style="display: none;">
                        <label class="form-label">Counter Offer Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="counter_price" step="0.01" min="1">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Message (Optional)</label>
                        <textarea class="form-control" name="message" rows="3" 
                                  placeholder="Add a message to explain your decision..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitResponse()">
                    <i class="fas fa-paper-plane me-2"></i>Send Response
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$additionalJS = "
<script>
function showResponseModal(action) {
    document.getElementById('responseAction').value = action;
    
    const counterPriceSection = document.getElementById('counterPriceSection');
    const modalTitle = document.querySelector('#responseModal .modal-title');
    
    if (action === 'countered') {
        counterPriceSection.style.display = 'block';
        modalTitle.innerHTML = '<i class=\"fas fa-handshake text-warning me-2\"></i>Make Counter Offer';
    } else {
        counterPriceSection.style.display = 'none';
        const actionText = action === 'accepted' ? 'Accept' : 'Reject';
        const iconClass = action === 'accepted' ? 'fa-check text-success' : 'fa-times text-danger';
        modalTitle.innerHTML = '<i class=\"fas ' + iconClass + ' me-2\"></i>' + actionText + ' Bargain';
    }
    
    const modal = new mdb.Modal(document.getElementById('responseModal'));
    modal.show();
}

function submitResponse() {
    const form = document.getElementById('responseForm');
    const formData = new FormData(form);
    
    fetch('../api/admin-respond-bargain.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Response sent successfully!', 'success');
            const modal = mdb.Modal.getInstance(document.getElementById('responseModal'));
            modal.hide();
            
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to send response', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

// Auto-scroll to bottom of chat
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.querySelector('.chat-container');
    chatContainer.scrollTop = chatContainer.scrollHeight;
});
</script>
";

include '../includes/footer.php';
?>
