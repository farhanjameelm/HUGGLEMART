<?php
require_once '../config/config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// Mock cart data (replace with actual database query)
$carts = [
    [
        'user_id' => 2,
        'user_name' => 'John Doe',
        'user_email' => 'john@example.com',
        'items_count' => 3,
        'total_value' => 299.99,
        'last_updated' => '2024-11-13 10:30:00'
    ],
    [
        'user_id' => 3,
        'user_name' => 'Jane Smith',
        'user_email' => 'jane@example.com',
        'items_count' => 2,
        'total_value' => 149.50,
        'last_updated' => '2024-11-13 09:15:00'
    ]
];

try {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="carts_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'User ID',
        'Customer Name',
        'Customer Email',
        'Items Count',
        'Total Value',
        'Last Updated'
    ]);
    
    // Add cart data
    foreach ($carts as $cart) {
        fputcsv($output, [
            $cart['user_id'],
            $cart['user_name'],
            $cart['user_email'],
            $cart['items_count'],
            $cart['total_value'],
            $cart['last_updated']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    error_log('Export Carts Error: ' . $e->getMessage());
    header('Location: cart.php?error=export_failed');
    exit();
}
?>
