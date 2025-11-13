<?php
require_once '../config/config.php';
require_once '../classes/User.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Settings';
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'site_settings':
                // Handle site settings update
                $success = 'Site settings updated successfully!';
                break;
            case 'email_settings':
                // Handle email settings update
                $success = 'Email settings updated successfully!';
                break;
            case 'security_settings':
                // Handle security settings update
                $success = 'Security settings updated successfully!';
                break;
        }
    }
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
                        <a class="nav-link active" href="settings.php">
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
                    <i class="fas fa-cog text-primary me-2"></i>Settings
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button class="btn btn-outline-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                    </button>
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

            <!-- Settings Tabs -->
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="site-tab" data-bs-toggle="tab" data-bs-target="#site" type="button" role="tab">
                                <i class="fas fa-globe me-2"></i>Site Settings
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                                <i class="fas fa-envelope me-2"></i>Email Settings
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Security
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bargaining-tab" data-bs-toggle="tab" data-bs-target="#bargaining" type="button" role="tab">
                                <i class="fas fa-handshake me-2"></i>Bargaining
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="settingsTabContent">
                        <!-- Site Settings Tab -->
                        <div class="tab-pane fade show active" id="site" role="tabpanel">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Site Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="site_settings">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Site Name</label>
                                                    <input type="text" class="form-control" name="site_name" value="<?php echo SITE_NAME; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Site URL</label>
                                                    <input type="url" class="form-control" name="site_url" value="<?php echo SITE_URL; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Admin Email</label>
                                                    <input type="email" class="form-control" name="admin_email" value="<?php echo ADMIN_EMAIL; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Products Per Page</label>
                                                    <input type="number" class="form-control" name="products_per_page" value="<?php echo PRODUCTS_PER_PAGE; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Site Description</label>
                                            <textarea class="form-control" name="site_description" rows="3">Your ultimate shopping destination where you can negotiate prices and get the best deals.</textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Site Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings Tab -->
                        <div class="tab-pane fade" id="email" role="tabpanel">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Email Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="email_settings">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">SMTP Host</label>
                                                    <input type="text" class="form-control" name="smtp_host" placeholder="smtp.gmail.com">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">SMTP Port</label>
                                                    <input type="number" class="form-control" name="smtp_port" value="587">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">SMTP Username</label>
                                                    <input type="email" class="form-control" name="smtp_username">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">SMTP Password</label>
                                                    <input type="password" class="form-control" name="smtp_password">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="smtp_secure" id="smtpSecure" checked>
                                                <label class="form-check-label" for="smtpSecure">
                                                    Use TLS/SSL Encryption
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Email Settings
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary ms-2">
                                            <i class="fas fa-paper-plane me-2"></i>Test Email
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Security Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="security_settings">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Session Timeout (minutes)</label>
                                                    <input type="number" class="form-control" name="session_timeout" value="30">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Max Login Attempts</label>
                                                    <input type="number" class="form-control" name="max_login_attempts" value="5">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="require_email_verification" id="emailVerification">
                                                <label class="form-check-label" for="emailVerification">
                                                    Require Email Verification for New Users
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="enable_two_factor" id="twoFactor">
                                                <label class="form-check-label" for="twoFactor">
                                                    Enable Two-Factor Authentication
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Security Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Bargaining Settings Tab -->
                        <div class="tab-pane fade" id="bargaining" role="tabpanel">
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Bargaining Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="bargaining_settings">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Default Bargain Threshold (%)</label>
                                                    <input type="number" class="form-control" name="bargain_threshold" value="<?php echo DEFAULT_BARGAIN_THRESHOLD; ?>">
                                                    <div class="form-text">Minimum discount percentage allowed</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Bargain Timeout (hours)</label>
                                                    <input type="number" class="form-control" name="bargain_timeout" value="<?php echo BARGAIN_TIMEOUT_HOURS; ?>">
                                                    <div class="form-text">Time limit for bargain responses</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="auto_accept_threshold" id="autoAccept">
                                                <label class="form-check-label" for="autoAccept">
                                                    Auto-accept bargains within threshold
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotifications" checked>
                                                <label class="form-check-label" for="emailNotifications">
                                                    Send email notifications for new bargains
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Bargaining Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
