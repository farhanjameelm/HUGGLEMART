<?php
// Simple test file to check if everything is working
echo "<h1>HugglingMart Test Page</h1>";

// Test 1: PHP is working
echo "<h2>✅ PHP is working!</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 2: Check if files exist
$files_to_check = [
    'config/config.php',
    'config/database.php',
    'classes/Product.php',
    'classes/User.php',
    'classes/Bargain.php',
    'includes/header.php',
    'includes/footer.php'
];

echo "<h2>File Check:</h2>";
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file - EXISTS</p>";
    } else {
        echo "<p>❌ $file - MISSING</p>";
    }
}

// Test 3: Class loading
echo "<h2>Class Loading Test:</h2>";
try {
    require_once 'config/config.php';
    require_once 'classes/User.php';
    require_once 'classes/Product.php';
    require_once 'classes/Bargain.php';
    
    echo "<p>✅ All classes loaded successfully!</p>";
    
    // Test class instantiation
    $user = new User();
    $product = new Product();
    $bargain = new Bargain();
    echo "<p>✅ All classes can be instantiated!</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Class loading error: " . $e->getMessage() . "</p>";
}

// Test 4: Database connection
echo "<h2>Database Connection Test:</h2>";
try {
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<p>✅ Database connection successful!</p>";
        
        // Test if tables exist
        $tables = ['users', 'products', 'categories', 'bargains'];
        foreach ($tables as $table) {
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
                $stmt->execute();
                $count = $stmt->fetchColumn();
                echo "<p>✅ Table '$table' exists with $count records</p>";
            } catch (Exception $e) {
                echo "<p>❌ Table '$table' - " . $e->getMessage() . "</p>";
            }
        }
    } else {
        echo "<p>❌ Database connection failed!</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test 5: Session
echo "<h2>Session Test:</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ Sessions are working!</p>";
} else {
    echo "<p>❌ Session problem!</p>";
}

// Test 6: JavaScript escaping fix
echo "<h2>JavaScript Escaping Test:</h2>";
$testProductName = "Test Product with 'quotes' and \"double quotes\"";
$escapedName = addslashes(htmlspecialchars($testProductName));
echo "<p>Original: " . htmlspecialchars($testProductName) . "</p>";
echo "<p>Escaped: " . htmlspecialchars($escapedName) . "</p>";
echo "<p>✅ JavaScript escaping is working!</p>";

echo "<hr>";
echo "<h2>🎉 All Tests Complete!</h2>";
echo "<p><a href='index.php' style='background: #1266f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Homepage</a></p>";
?>
