<?php
require_once 'config/config.php';
require_once 'classes/User.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
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
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['email'] = $userData['email'];
                $_SESSION['first_name'] = $userData['first_name'];
                $_SESSION['last_name'] = $userData['last_name'];
                $_SESSION['is_admin'] = $userData['is_admin'];
                
                // Redirect to intended page or dashboard
                $redirectTo = $_GET['redirect'] ?? 'dashboard.php';
                redirect($redirectTo);
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}

$pageTitle = 'Login';
include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Logo and Title -->
                    <div class="text-center mb-4">
                        <h2 class="text-primary mb-2">
                            <i class="fas fa-store me-2"></i>HugglingMart
                        </h2>
                        <h4 class="mb-3">Welcome Back!</h4>
                        <p class="text-muted">Sign in to your account to continue shopping and negotiating</p>
                    </div>
                    
                    <!-- Error/Success Messages -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Login Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                       placeholder="Enter your email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>
                    </form>
                    
                    <!-- Divider -->
                    <div class="text-center mb-3">
                        <span class="text-muted">or</span>
                    </div>
                    
                    <!-- Social Login (Placeholder) -->
                    <div class="d-grid gap-2 mb-4">
                        <button class="btn btn-outline-danger" type="button">
                            <i class="fab fa-google me-2"></i>Continue with Google
                        </button>
                        <button class="btn btn-outline-primary" type="button">
                            <i class="fab fa-facebook-f me-2"></i>Continue with Facebook
                        </button>
                    </div>
                    
                    <!-- Links -->
                    <div class="text-center">
                        <p class="mb-2">
                            <a href="forgot-password.php" class="text-decoration-none">Forgot your password?</a>
                        </p>
                        <p class="mb-0">
                            Don't have an account? 
                            <a href="register.php" class="text-decoration-none fw-bold">Sign up here</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Demo Credentials -->
            <div class="card mt-3 bg-light">
                <div class="card-body text-center">
                    <h6 class="card-title">Demo Credentials</h6>
                    <p class="card-text small mb-2">
                        <strong>Admin:</strong> admin@hugglingmart.com / password<br>
                        <strong>User:</strong> user@example.com / password
                    </p>
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="fillDemoCredentials('admin')">
                        Use Admin Demo
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="fillDemoCredentials('user')">
                        Use User Demo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additionalJS = "
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function fillDemoCredentials(type) {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    
    if (type === 'admin') {
        emailInput.value = 'admin@hugglingmart.com';
        passwordInput.value = 'password';
    } else {
        emailInput.value = 'user@example.com';
        passwordInput.value = 'password';
    }
}

// Auto-focus on email field
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('email').focus();
});
</script>
";

include 'includes/footer.php';
?>
