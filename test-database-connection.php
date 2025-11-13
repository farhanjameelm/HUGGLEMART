<?php
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h1>Database Connection Test</h1>";

try {
    // Test database connection
    $database = new Database();
    $pdo = $database->getConnection();
    
    if ($pdo) {
        echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px;'>";
        echo "✅ <strong>Database connection successful!</strong><br>";
        echo "Host: " . DB_HOST . "<br>";
        echo "Database: " . DB_NAME . "<br>";
        echo "User: " . DB_USER . "<br>";
        echo "</div>";
        
        // Test if required tables exist
        $tables = ['users', 'products', 'cart', 'orders', 'order_items'];
        echo "<h2>Table Check:</h2>";
        
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo "<div style='color: green;'>✅ Table '$table' exists</div>";
                } else {
                    echo "<div style='color: red;'>❌ Table '$table' missing</div>";
                }
            } catch (Exception $e) {
                echo "<div style='color: red;'>❌ Error checking table '$table': " . $e->getMessage() . "</div>";
            }
        }
        
        // Test user session
        echo "<h2>Session Check:</h2>";
        if (isset($_SESSION['user_id'])) {
            echo "<div style='color: green;'>✅ User logged in (ID: " . $_SESSION['user_id'] . ")</div>";
            
            // Test user data
            try {
                $userQuery = "SELECT * FROM users WHERE id = :user_id";
                $userStmt = $pdo->prepare($userQuery);
                $userStmt->execute([':user_id' => $_SESSION['user_id']]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    echo "<div style='color: green;'>✅ User data found: " . htmlspecialchars($user['email']) . "</div>";
                } else {
                    echo "<div style='color: red;'>❌ User data not found</div>";
                }
            } catch (Exception $e) {
                echo "<div style='color: red;'>❌ Error fetching user data: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div style='color: orange;'>⚠️ No user logged in</div>";
            echo "<div><a href='login.php'>Login here</a></div>";
        }
        
        // Test cart data
        if (isset($_SESSION['user_id'])) {
            echo "<h2>Cart Check:</h2>";
            try {
                $cartQuery = "SELECT COUNT(*) as count FROM cart WHERE user_id = :user_id";
                $cartStmt = $pdo->prepare($cartQuery);
                $cartStmt->execute([':user_id' => $_SESSION['user_id']]);
                $cartCount = $cartStmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                echo "<div style='color: green;'>✅ Cart items: $cartCount</div>";
            } catch (Exception $e) {
                echo "<div style='color: red;'>❌ Error checking cart: " . $e->getMessage() . "</div>";
            }
        }
        
    } else {
        echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>";
        echo "❌ <strong>Database connection failed!</strong>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>";
    echo "❌ <strong>Database connection error:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Go to Home Page</a></li>";
echo "<li><a href='login.php'>Login</a></li>";
echo "<li><a href='cart.php'>View Cart</a></li>";
echo "<li><a href='checkout.php'>Test Checkout</a></li>";
echo "</ul>";

echo "<h2>Database Setup:</h2>";
echo "<p>If tables are missing, run this SQL:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
echo "SOURCE database/checkout_tables.sql;";
echo "</pre>";
?>
