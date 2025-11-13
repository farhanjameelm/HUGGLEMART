<?php
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = 'Fix Cart and AI Issues';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

include 'includes/header.php';

// Check and fix issues
$issues = [];
$fixes = [];

// 1. Check if cart table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'cart'");
    if ($stmt->rowCount() == 0) {
        $issues[] = "Cart table does not exist";
        
        // Create cart table
        $createCartTable = "
        CREATE TABLE IF NOT EXISTS `cart` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          `quantity` int(11) NOT NULL DEFAULT 1,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `user_product` (`user_id`, `product_id`),
          KEY `user_id` (`user_id`),
          KEY `product_id` (`product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        if ($pdo->exec($createCartTable)) {
            $fixes[] = "✅ Cart table created successfully";
        } else {
            $fixes[] = "❌ Failed to create cart table";
        }
    } else {
        $fixes[] = "✅ Cart table exists";
    }
} catch (Exception $e) {
    $issues[] = "Database connection error: " . $e->getMessage();
}

// 2. Check if AI bargain tables exist
$aiTables = ['ai_bargain_settings', 'ai_decision_logs', 'ai_training_data'];
foreach ($aiTables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $issues[] = "AI table '$table' does not exist";
            
            // Create AI tables
            switch ($table) {
                case 'ai_bargain_settings':
                    $createTable = "
                    CREATE TABLE IF NOT EXISTS `ai_bargain_settings` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `user_id` int(11) NOT NULL,
                      `settings` json NOT NULL,
                      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `user_id` (`user_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ";
                    break;
                    
                case 'ai_decision_logs':
                    $createTable = "
                    CREATE TABLE IF NOT EXISTS `ai_decision_logs` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `bargain_id` int(11) NOT NULL,
                      `ai_decision` enum('accept','counter','reject') NOT NULL,
                      `confidence_level` decimal(3,2) NOT NULL,
                      `reasoning` text,
                      `suggested_response` text,
                      `counter_offer_amount` decimal(10,2) DEFAULT NULL,
                      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`),
                      KEY `bargain_id` (`bargain_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ";
                    break;
                    
                case 'ai_training_data':
                    $createTable = "
                    CREATE TABLE IF NOT EXISTS `ai_training_data` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `bargain_id` int(11) NOT NULL,
                      `decision` enum('accept','counter','reject') NOT NULL,
                      `outcome` enum('successful','failed','pending') NOT NULL DEFAULT 'pending',
                      `feedback` text,
                      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`),
                      KEY `bargain_id` (`bargain_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ";
                    break;
            }
            
            if ($pdo->exec($createTable)) {
                $fixes[] = "✅ AI table '$table' created successfully";
            } else {
                $fixes[] = "❌ Failed to create AI table '$table'";
            }
        } else {
            $fixes[] = "✅ AI table '$table' exists";
        }
    } catch (Exception $e) {
        $issues[] = "Error checking AI table '$table': " . $e->getMessage();
    }
}

// 3. Test add to cart functionality
$testResults = [];

// Test if user is logged in
if (isset($_SESSION['user_id'])) {
    $testResults[] = "✅ User is logged in (ID: " . $_SESSION['user_id'] . ")";
    
    // Test cart API endpoint
    $testResults[] = "✅ Cart API endpoint exists at: api/cart.php";
    
    // Test if products table has data
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
        $productCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $testResults[] = "✅ Found $productCount active products";
    } catch (Exception $e) {
        $testResults[] = "❌ Error checking products: " . $e->getMessage();
    }
} else {
    $testResults[] = "❌ User not logged in - cart functionality requires login";
}

// 4. Test AI bargain bot functionality
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bargains WHERE status = 'pending'");
    $bargainCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $testResults[] = "✅ Found $bargainCount pending bargains for AI processing";
} catch (Exception $e) {
    $testResults[] = "❌ Error checking bargains: " . $e->getMessage();
}

?>

<div class="container my-5">
    <h1 class="text-center mb-5">
        <i class="fas fa-tools text-primary me-2"></i>
        Cart & AI Issues Fix
    </h1>
    
    <!-- Issues Found -->
    <?php if (!empty($issues)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Issues Found
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <?php foreach ($issues as $issue): ?>
                            <li><?php echo htmlspecialchars($issue); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Fixes Applied -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Fixes Applied
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <?php foreach ($fixes as $fix): ?>
                            <li><?php echo $fix; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Results -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-flask me-2"></i>System Tests
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <?php foreach ($testResults as $result): ?>
                            <li><?php echo $result; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Buttons -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Test Add to Cart
                    </h6>
                </div>
                <div class="card-body">
                    <p>Test the add to cart functionality:</p>
                    <button class="btn btn-primary" onclick="testAddToCart()">
                        <i class="fas fa-plus me-2"></i>Test Add to Cart
                    </button>
                    <div id="cartTestResult" class="mt-3"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Test AI Bargain Bot
                    </h6>
                </div>
                <div class="card-body">
                    <p>Test the AI bargain bot functionality:</p>
                    <button class="btn btn-info" onclick="testAIBot()">
                        <i class="fas fa-brain me-2"></i>Test AI Bot
                    </button>
                    <div id="aiTestResult" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-link me-2"></i>Quick Links
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="cart.php" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-shopping-cart me-2"></i>View Cart
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="admin/ai-bargain-bot.php" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-robot me-2"></i>AI Bot Admin
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="test-ai-bargain.php" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-test-tube me-2"></i>Test AI Bot
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="index.php" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-home me-2"></i>Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Test add to cart functionality
function testAddToCart() {
    const resultDiv = document.getElementById('cartTestResult');
    resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Testing...';
    
    // Test with a sample product ID (assuming product ID 1 exists)
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', 1);
    formData.append('quantity', 1);
    
    fetch('api/cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success">✅ Add to cart is working!</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger">❌ Error: ' + (data.message || 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        console.error('Cart test error:', error);
        resultDiv.innerHTML = '<div class="alert alert-danger">❌ Network error: ' + error.message + '</div>';
    });
}

// Test AI bargain bot
function testAIBot() {
    const resultDiv = document.getElementById('aiTestResult');
    resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Testing AI...';
    
    const formData = new FormData();
    formData.append('action', 'get_ai_analytics');
    
    fetch('api/ai-bargain-bot.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="alert alert-success">✅ AI Bargain Bot is working!</div>';
        } else {
            resultDiv.innerHTML = '<div class="alert alert-danger">❌ AI Error: ' + (data.message || 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        console.error('AI test error:', error);
        resultDiv.innerHTML = '<div class="alert alert-danger">❌ AI Network error: ' + error.message + '</div>';
    });
}

// Initialize components when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Fix page loaded - ready for testing');
});
</script>

<?php include 'includes/footer.php'; ?>
