<?php
require_once 'config/config.php';
require_once 'classes/User.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$pageTitle = 'My Profile';
$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

$message = '';
$messageType = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $message = 'Invalid security token. Please try again.';
        $messageType = 'danger';
    } else {
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $zipCode = trim($_POST['zip_code']);
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Validation
        $errors = [];
        
        if (empty($firstName)) $errors[] = 'First name is required';
        if (empty($lastName)) $errors[] = 'Last name is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
        
        // Check if email is already taken by another user
        $existingUser = $user->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
            $errors[] = 'Email is already taken by another user';
        }
        
        // Password validation (only if changing password)
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required to change password';
            } elseif (!password_verify($currentPassword, $userData['password'])) {
                $errors[] = 'Current password is incorrect';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'New password must be at least 6 characters';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'New passwords do not match';
            }
        }
        
        if (empty($errors)) {
            try {
                $updateData = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'zip_code' => $zipCode
                ];
                
                // Add password to update if provided
                if (!empty($newPassword)) {
                    $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                }
                
                if ($user->updateUser($_SESSION['user_id'], $updateData)) {
                    // Update session data
                    $_SESSION['first_name'] = $firstName;
                    $_SESSION['last_name'] = $lastName;
                    $_SESSION['email'] = $email;
                    
                    $message = 'Profile updated successfully!';
                    $messageType = 'success';
                    
                    // Refresh user data
                    $userData = $user->getUserById($_SESSION['user_id']);
                } else {
                    $message = 'Failed to update profile. Please try again.';
                    $messageType = 'danger';
                }
            } catch (Exception $e) {
                $message = 'An error occurred while updating profile.';
                $messageType = 'danger';
            }
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'danger';
        }
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-user text-primary me-2"></i>My Profile
                    </h2>
                    <p class="text-muted mb-0">Manage your account information</p>
                </div>
                <div>
                    <a href="dashboard.php" class="btn btn-outline-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </div>
            </div>
            
            <!-- Alert Messages -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-mdb-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Personal Information</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="first_name" 
                                       value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" name="last_name" 
                                       value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Address Information</h6>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" 
                                       value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" 
                                       value="<?php echo htmlspecialchars($userData['state'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" class="form-control" name="zip_code" 
                                       value="<?php echo htmlspecialchars($userData['zip_code'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Password Change -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Change Password (Optional)</h6>
                                <p class="small text-muted">Leave blank if you don't want to change your password</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" minlength="6">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" minlength="6">
                            </div>
                        </div>
                        
                        <!-- Account Stats -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Account Information</h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="bg-light p-3 rounded">
                                    <small class="text-muted">Member Since</small>
                                    <div><strong><?php echo date('F j, Y', strtotime($userData['created_at'])); ?></strong></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="bg-light p-3 rounded">
                                    <small class="text-muted">Account Status</small>
                                    <div>
                                        <span class="badge bg-<?php echo $userData['status'] === 'active' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($userData['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
