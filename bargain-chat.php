<?php
require_once 'config/config.php';
require_once 'classes/Bargain.php';
require_once 'classes/Product.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

if (!isset($_GET['id'])) {
    redirect('bargains.php');
}

$bargainId = (int)$_GET['id'];
$bargain = new Bargain();
$product = new Product();

$bargainData = $bargain->getBargainById($bargainId);

if (!$bargainData || $bargainData['user_id'] != $_SESSION['user_id']) {
    redirect('bargains.php');
}

$messages = $bargain->getBargainMessages($bargainId);
$pageTitle = 'Bargain Chat - ' . $bargainData['product_name'];

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Bargain Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-handshake me-2"></i>Price Negotiation
                        </h5>
                        <a href="bargains.php" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to Bargains
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <?php 
                            $productImage = !empty($bargainData['product_images']) ? $bargainData['product_images'][0] : 'https://via.placeholder.com/150x150?text=No+Image';
                            ?>
                            <img src="<?php echo $productImage; ?>" class="img-fluid rounded-3" alt="Product Image" style="max-height: 120px;">
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-2"><?php echo htmlspecialchars($bargainData['product_name']); ?></h6>
                            <p class="text-muted mb-1">Original Price: <strong><?php echo formatPrice($bargainData['original_price']); ?></strong></p>
                            <p class="text-muted mb-1">Your Offer: <strong><?php echo formatPrice($bargainData['offered_price']); ?></strong></p>
                            <p class="text-muted mb-0">Current Price: <strong class="text-primary"><?php echo formatPrice($bargainData['current_price']); ?></strong></p>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="badge bg-<?php 
                                echo $bargainData['status'] === 'pending' ? 'warning' : 
                                    ($bargainData['status'] === 'accepted' ? 'success' : 
                                    ($bargainData['status'] === 'rejected' ? 'danger' : 'info')); 
                            ?> fs-6">
                                <?php echo ucfirst($bargainData['status']); ?>
                            </span>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <?php 
                                    $discount = (($bargainData['original_price'] - $bargainData['current_price']) / $bargainData['original_price']) * 100;
                                    echo number_format($discount, 1) . '% discount';
                                    ?>
                                </small>
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
                    <div class="chat-container" style="height: 400px; overflow-y: auto;">
                        <?php if (empty($messages)): ?>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-comments fa-3x mb-3"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message-item p-3 border-bottom">
                                    <div class="d-flex <?php echo $message['sender_type'] === 'customer' ? 'justify-content-end' : 'justify-content-start'; ?>">
                                        <div class="message-bubble <?php echo $message['sender_type'] === 'customer' ? 'bg-primary text-white' : 'bg-light'; ?> rounded-3 p-3" style="max-width: 70%;">
                                            <!-- Message Header -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="<?php echo $message['sender_type'] === 'customer' ? 'text-white-50' : 'text-muted'; ?>">
                                                    <strong>
                                                        <?php echo $message['sender_type'] === 'customer' ? 'You' : 'Seller'; ?>
                                                    </strong>
                                                </small>
                                                <small class="<?php echo $message['sender_type'] === 'customer' ? 'text-white-50' : 'text-muted'; ?>">
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
                                                    <strong class="<?php echo $message['sender_type'] === 'customer' ? 'text-warning' : 'text-primary'; ?>">
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
                
                <!-- Message Input (only if bargain is still active) -->
                <?php if (in_array($bargainData['status'], ['pending', 'countered'])): ?>
                    <div class="card-footer">
                        <form id="messageForm" class="d-flex gap-2">
                            <input type="hidden" name="bargain_id" value="<?php echo $bargainId; ?>">
                            <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        
                        <!-- Counter Offer Section -->
                        <div class="mt-3">
                            <button class="btn btn-warning btn-sm" onclick="showCounterOfferForm()">
                                <i class="fas fa-handshake me-1"></i>Make Counter Offer
                            </button>
                        </div>
                        
                        <!-- Counter Offer Form (Hidden by default) -->
                        <div id="counterOfferForm" class="mt-3" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Make a Counter Offer</h6>
                                    <form id="counterOfferFormData">
                                        <input type="hidden" name="bargain_id" value="<?php echo $bargainId; ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Your New Offer</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" name="counter_price" step="0.01" min="1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Message (Optional)</label>
                                                <input type="text" class="form-control" name="message" placeholder="Explain your offer...">
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-warning btn-sm">
                                                <i class="fas fa-handshake me-1"></i>Send Counter Offer
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="hideCounterOfferForm()">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($bargainData['status'] === 'accepted'): ?>
                    <div class="card-footer bg-success text-white text-center">
                        <h6 class="mb-2">
                            <i class="fas fa-check-circle me-2"></i>Offer Accepted!
                        </h6>
                        <p class="mb-3">Congratulations! Your offer of <?php echo formatPrice($bargainData['current_price']); ?> has been accepted.</p>
                        <a href="product.php?slug=<?php echo $bargainData['product_slug'] ?? '#'; ?>" class="btn btn-light">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart at Negotiated Price
                        </a>
                    </div>
                <?php elseif ($bargainData['status'] === 'rejected'): ?>
                    <div class="card-footer bg-danger text-white text-center">
                        <h6 class="mb-2">
                            <i class="fas fa-times-circle me-2"></i>Offer Rejected
                        </h6>
                        <p class="mb-0">Unfortunately, your offer was not accepted. You can try making a new offer.</p>
                    </div>
                <?php elseif ($bargainData['status'] === 'expired'): ?>
                    <div class="card-footer bg-warning text-dark text-center">
                        <h6 class="mb-2">
                            <i class="fas fa-clock me-2"></i>Bargain Expired
                        </h6>
                        <p class="mb-0">This bargain has expired. You can start a new negotiation if you're still interested.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$additionalJS = "
<script>
// Auto-scroll to bottom of chat
function scrollToBottom() {
    const chatContainer = document.querySelector('.chat-container');
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

// Scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// Handle message form submission
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('api/send-bargain-message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the form
            this.reset();
            
            // Reload the page to show new message
            location.reload();
        } else {
            showToast(data.message || 'Failed to send message', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
});

// Handle counter offer form submission
document.getElementById('counterOfferFormData').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('api/send-counter-offer.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Counter offer sent successfully!', 'success');
            
            // Reload the page to show new offer
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
});

function showCounterOfferForm() {
    document.getElementById('counterOfferForm').style.display = 'block';
}

function hideCounterOfferForm() {
    document.getElementById('counterOfferForm').style.display = 'none';
}

// Auto-refresh messages every 30 seconds
setInterval(function() {
    // Only refresh if bargain is still active
    const status = '" . $bargainData['status'] . "';
    if (status === 'pending' || status === 'countered') {
        location.reload();
    }
}, 30000);
</script>
";

include 'includes/footer.php';
?>
