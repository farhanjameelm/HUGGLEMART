<?php
require_once 'config/config.php';
require_once 'classes/User.php';

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && $_SESSION['is_admin']) {
    redirect('admin/index.php');
}

$pageTitle = 'Admin Login';
$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $user = new User();
            $userData = $user->login($email, $password);
            
            if ($userData) {
                // Check if user is admin
                if ($userData['is_admin']) {
                    // Set session variables
                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['email'] = $userData['email'];
                    $_SESSION['first_name'] = $userData['first_name'];
                    $_SESSION['last_name'] = $userData['last_name'];
                    $_SESSION['is_admin'] = true;
                    
                    // Redirect to admin dashboard
                    redirect('admin/index.php');
                } else {
                    $error = 'Access denied. Admin privileges required.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}

// Handle logout message
if (isset($_GET['message']) && $_GET['message'] === 'logged_out') {
    $success = 'You have been successfully logged out.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - HugglingMart Admin</title>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
        }
        
        .admin-login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #1266f1, #b23cfd);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .admin-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .admin-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .admin-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .login-form {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating input {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .form-floating input:focus {
            border-color: #1266f1;
            box-shadow: 0 0 0 0.2rem rgba(18, 102, 241, 0.25);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #1266f1, #b23cfd);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(18, 102, 241, 0.3);
        }
        
        .demo-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border-left: 4px solid #1266f1;
        }
        
        .back-to-store {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .security-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="security-badge">
                <i class="fas fa-shield-alt me-1"></i>Secure
            </div>
            <div class="admin-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1>Admin Portal</h1>
            <p>HugglingMart Administration</p>
        </div>
        
        <!-- Login Form -->
        <div class="login-form">
            <!-- Alert Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="admin-login.php">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <!-- Email Field -->
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="admin@example.com" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <label for="email">
                        <i class="fas fa-envelope me-2"></i>Admin Email
                    </label>
                </div>
                
                <!-- Password Field -->
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Password" required>
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                </div>
                
                <!-- Login Button -->
                <button type="submit" class="btn btn-admin text-white w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Access Admin Panel
                </button>
            </form>
            
            <!-- Demo Credentials -->
            <div class="demo-credentials">
                <h6 class="mb-2">
                    <i class="fas fa-info-circle text-primary me-2"></i>Demo Credentials
                </h6>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Email:</small>
                        <div class="fw-bold">admin@hugglingmart.com</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Password:</small>
                        <div class="fw-bold">password</div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100" onclick="fillDemoCredentials()">
                    <i class="fas fa-magic me-1"></i>Use Demo Credentials
                </button>
            </div>
            
            <!-- Back to Store -->
            <div class="back-to-store">
                <a href="index.php" class="text-decoration-none">
                    <i class="fas fa-store me-2"></i>Back to Store
                </a>
                <div class="mt-2">
                    <small class="text-muted">
                        Customer login? <a href="login.php" class="text-primary">Click here</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Security Features Notice -->
    <div class="position-fixed bottom-0 start-0 p-3">
        <div class="toast show" role="alert">
            <div class="toast-header">
                <i class="fas fa-shield-alt text-success me-2"></i>
                <strong class="me-auto">Security Notice</strong>
            </div>
            <div class="toast-body">
                <small>This is a secure admin portal with CSRF protection and session management.</small>
            </div>
        </div>
    </div>

    <!-- MDB JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.js"></script>
    
    <script>
        // Fill demo credentials
        function fillDemoCredentials() {
            document.getElementById('email').value = 'admin@hugglingmart.com';
            document.getElementById('password').value = 'password';
            
            // Add visual feedback
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check me-1"></i>Credentials Filled';
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }, 2000);
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Add loading state to form submission
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Authenticating...';
            submitBtn.disabled = true;
        });
        
        // Focus on email field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
        
        // Add enter key support for demo credentials
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                fillDemoCredentials();
            }
        });
    </script>
</body>
</html>
