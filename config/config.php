<?php
session_start();

// Site Configuration
define('SITE_NAME', 'HugglingMart');
define('SITE_URL', 'http://localhost/HUGGLINGMART');
define('ADMIN_EMAIL', 'admin@hugglingmart.com');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hugglingmart');
define('DB_USER', 'root');
define('DB_PASS', '');

// Upload Configuration
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Bargaining Configuration
define('DEFAULT_BARGAIN_THRESHOLD', 10); // 10% minimum discount
define('BARGAIN_TIMEOUT_HOURS', 24); // 24 hours for response

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 20);

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');

// Helper Functions
function redirect($url) {
    header("Location: " . SITE_URL . "/" . $url);
    exit();
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function validateCSRFToken($token) {
    return verifyCSRFToken($token);
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}
?>
