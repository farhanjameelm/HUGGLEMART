<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'AI Bargain Bot Management';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Get recent bargains for AI processing
try {
    $bargainsQuery = "SELECT b.*, p.name as product_name, p.price as original_price, p.category, u.username, u.email 
                      FROM bargains b 
                      JOIN products p ON b.product_id = p.id 
                      JOIN users u ON b.user_id = u.id 
                      WHERE b.status = 'pending' 
                      ORDER BY b.created_at DESC 
                      LIMIT 20";
    $bargainsStmt = $pdo->prepare($bargainsQuery);
    $bargainsStmt->execute();
    $pendingBargains = $bargainsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log('AI Bargain Bot Page Error: ' . $e->getMessage());
    $pendingBargains = [];
}

include '../includes/admin-header.php';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-robot text-primary me-2"></i>AI Bargain Bot
                    </h1>
                    <p class="text-muted mb-0">Automated bargaining assistant for seller negotiations</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aiSettingsModal">
                        <i class="fas fa-cog me-2"></i>AI Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Analytics Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>AI Performance Analytics
                    </h5>
                </div>
                <div class="card-body">
                    <div id="aiAnalyticsContainer">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading analytics...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading AI analytics...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Status & Controls -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-power-off me-2"></i>AI Bot Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-success mb-1">
                                <i class="fas fa-circle me-2"></i>Active
                            </h5>
                            <p class="text-muted mb-0">AI bot is processing bargains automatically</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="aiToggle" checked>
                            <label class="form-check-label" for="aiToggle">Enable AI</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-brain me-2"></i>AI Learning Mode
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-info mb-1">
                                <i class="fas fa-graduation-cap me-2"></i>Learning
                            </h5>
                            <p class="text-muted mb-0">AI is learning from successful negotiations</p>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" onclick="trainAIModel()">
                            <i class="fas fa-play me-1"></i>Train Model
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Bargains for AI Processing -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-handshake me-2"></i>Pending Bargains
                        <span class="badge bg-warning ms-2"><?php echo count($pendingBargains); ?></span>
                    </h5>
                    <div>
                        <button class="btn btn-success btn-sm" onclick="processAllWithAI()">
                            <i class="fas fa-robot me-1"></i>Process All with AI
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="refreshBargains()">
                            <i class="fas fa-sync me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingBargains)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5>No Pending Bargains</h5>
                            <p class="text-muted">All bargains have been processed!</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Original Price</th>
                                        <th>Offered Price</th>
                                        <th>Discount %</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingBargains as $bargain): ?>
                                        <?php 
                                        $discountPercent = (($bargain['original_price'] - $bargain['offered_price']) / $bargain['original_price']) * 100;
                                        $discountClass = $discountPercent <= 15 ? 'text-success' : ($discountPercent <= 25 ? 'text-warning' : 'text-danger');
                                        ?>
                                        <tr class="bargain-card" data-bargain-id="<?php echo $bargain['id']; ?>">
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($bargain['username']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($bargain['email']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($bargain['product_name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($bargain['category']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>$<?php echo number_format($bargain['original_price'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <strong class="text-primary">$<?php echo number_format($bargain['offered_price'], 2); ?></strong>
                                            </td>
                                            <td>
                                                <span class="<?php echo $discountClass; ?>">
                                                    <strong><?php echo number_format($discountPercent, 1); ?>%</strong>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="max-width: 200px;">
                                                    <?php if (!empty($bargain['message'])): ?>
                                                        <small><?php echo htmlspecialchars(substr($bargain['message'], 0, 100)); ?><?php echo strlen($bargain['message']) > 100 ? '...' : ''; ?></small>
                                                    <?php else: ?>
                                                        <small class="text-muted">No message</small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small><?php echo date('M j, Y g:i A', strtotime($bargain['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <div class="bargain-actions">
                                                    <button class="btn btn-info btn-sm me-1" onclick="processAIBargain(<?php echo $bargain['id']; ?>)">
                                                        <i class="fas fa-robot me-1"></i>AI Analyze
                                                    </button>
                                                    <div class="btn-group">
                                                        <button class="btn btn-success btn-sm" onclick="quickAccept(<?php echo $bargain['id']; ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-warning btn-sm" onclick="quickCounter(<?php echo $bargain['id']; ?>)">
                                                            <i class="fas fa-handshake"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm" onclick="quickReject(<?php echo $bargain['id']; ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
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

<!-- AI Settings Modal -->
<div class="modal fade" id="aiSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cog text-primary me-2"></i>AI Bargain Bot Settings
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="aiSettingsForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Acceptance Thresholds</h6>
                            <div class="mb-3">
                                <label for="maxDiscount" class="form-label">Maximum Auto-Accept Discount (%)</label>
                                <input type="number" class="form-control" id="maxDiscount" value="15" min="0" max="50">
                            </div>
                            <div class="mb-3">
                                <label for="minDiscount" class="form-label">Minimum Counter-Offer Discount (%)</label>
                                <input type="number" class="form-control" id="minDiscount" value="5" min="0" max="25">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Category Flexibility</h6>
                            <div class="mb-3">
                                <label for="electronicsFlexibility" class="form-label">Electronics</label>
                                <input type="range" class="form-range" id="electronicsFlexibility" min="0" max="100" value="60">
                                <small class="text-muted">60% flexible</small>
                            </div>
                            <div class="mb-3">
                                <label for="fashionFlexibility" class="form-label">Fashion</label>
                                <input type="range" class="form-range" id="fashionFlexibility" min="0" max="100" value="80">
                                <small class="text-muted">80% flexible</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6>AI Behavior Settings</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="autoProcess" checked>
                                <label class="form-check-label" for="autoProcess">
                                    Auto-process new bargains
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="learningMode" checked>
                                <label class="form-check-label" for="learningMode">
                                    Enable learning mode
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="sentimentAnalysis" checked>
                                <label class="form-check-label" for="sentimentAnalysis">
                                    Use sentiment analysis
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAISettings()">
                    <i class="fas fa-save me-2"></i>Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AI bargain bot
    initializeAIBargainBot();
    
    // Load AI analytics
    getAIAnalytics();
    
    // Auto-refresh every 30 seconds
    setInterval(() => {
        getAIAnalytics();
        refreshBargains();
    }, 30000);
});

// Process all bargains with AI
function processAllWithAI() {
    const bargainCards = document.querySelectorAll('.bargain-card');
    let processed = 0;
    
    if (bargainCards.length === 0) {
        showToast('No pending bargains to process', 'info');
        return;
    }
    
    showToast(`Processing ${bargainCards.length} bargains with AI...`, 'info');
    
    bargainCards.forEach((card, index) => {
        const bargainId = card.dataset.bargainId;
        
        setTimeout(() => {
            processAIBargain(bargainId, true)
                .then(() => {
                    processed++;
                    if (processed === bargainCards.length) {
                        showToast('All bargains processed successfully!', 'success');
                        refreshBargains();
                    }
                })
                .catch(() => {
                    processed++;
                });
        }, index * 1000); // Stagger requests by 1 second
    });
}

// Quick actions
function quickAccept(bargainId) {
    if (confirm('Accept this bargain offer?')) {
        // Implement quick accept logic
        showToast('Bargain accepted!', 'success');
        refreshBargains();
    }
}

function quickCounter(bargainId) {
    const counterPrice = prompt('Enter counter offer amount:');
    if (counterPrice && !isNaN(counterPrice)) {
        // Implement quick counter logic
        showToast(`Counter offer of $${counterPrice} sent!`, 'success');
        refreshBargains();
    }
}

function quickReject(bargainId) {
    if (confirm('Reject this bargain offer?')) {
        // Implement quick reject logic
        showToast('Bargain rejected!', 'info');
        refreshBargains();
    }
}

// Refresh bargains list
function refreshBargains() {
    // Reload the page to get fresh data
    // In a real implementation, you'd use AJAX to refresh just the table
    setTimeout(() => {
        window.location.reload();
    }, 2000);
}

// Save AI settings
function saveAISettings() {
    const settings = {
        maxDiscount: document.getElementById('maxDiscount').value,
        minDiscount: document.getElementById('minDiscount').value,
        electronicsFlexibility: document.getElementById('electronicsFlexibility').value,
        fashionFlexibility: document.getElementById('fashionFlexibility').value,
        autoProcess: document.getElementById('autoProcess').checked,
        learningMode: document.getElementById('learningMode').checked,
        sentimentAnalysis: document.getElementById('sentimentAnalysis').checked
    };
    
    const formData = new FormData();
    formData.append('action', 'update_ai_settings');
    formData.append('settings', JSON.stringify(settings));
    
    fetch(getApiUrl('ai-bargain-bot.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('AI settings saved successfully!', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('aiSettingsModal'));
            modal.hide();
        } else {
            showToast(data.message || 'Failed to save settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Settings Error:', error);
        showToast('Error saving AI settings', 'danger');
    });
}

// Train AI model
function trainAIModel() {
    showToast('Training AI model with recent data...', 'info');
    
    const formData = new FormData();
    formData.append('action', 'train_ai_model');
    formData.append('training_data', JSON.stringify([])); // Add actual training data
    
    fetch(getApiUrl('ai-bargain-bot.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('AI model training completed!', 'success');
        } else {
            showToast(data.message || 'Training failed', 'danger');
        }
    })
    .catch(error => {
        console.error('Training Error:', error);
        showToast('Error training AI model', 'danger');
    });
}
</script>

<?php include '../includes/footer.php'; ?>
