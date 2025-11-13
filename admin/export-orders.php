<?php
require_once '../config/config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

// Mock orders data (replace with actual database query)
$orders = [
    [
        'id' => 1,
        'user_name' => 'John Doe',
        'user_email' => 'john@example.com',
        'total_amount' => 299.99,
        'status' => 'pending',
        'created_at' => '2024-11-13 10:30:00',
        'items_count' => 3
    ],
    [
        'id' => 2,
        'user_name' => 'Jane Smith',
        'user_email' => 'jane@example.com',
        'total_amount' => 149.50,
        'status' => 'processing',
        'created_at' => '2024-11-13 09:15:00',
        'items_count' => 2
    ]
];

try {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'Order ID',
        'Customer Name',
        'Customer Email',
        'Total Amount',
        'Status',
        'Items Count',
        'Created At'
    ]);
    
    // Add order data
    foreach ($orders as $order) {
        fputcsv($output, [
            $order['id'],
            $order['user_name'],
            $order['user_email'],
            $order['total_amount'],
            $order['status'],
            $order['items_count'],
            $order['created_at']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    error_log('Export Orders Error: ' . $e->getMessage());
    header('Location: orders.php?error=export_failed');
    exit();
}
?>
