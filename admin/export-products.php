<?php
require_once '../config/config.php';
require_once '../classes/Product.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

try {
    $productClass = new Product();
    $products = $productClass->getAllProducts(1000, 0); // Get all products
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'ID',
        'Name',
        'Description',
        'Category',
        'Price',
        'Stock Quantity',
        'SKU',
        'Weight',
        'Dimensions',
        'Created At'
    ]);
    
    // Add product data
    foreach ($products as $product) {
        fputcsv($output, [
            $product['id'] ?? '',
            $product['name'] ?? '',
            $product['description'] ?? '',
            $product['category'] ?? '',
            $product['price'] ?? 0,
            $product['stock_quantity'] ?? 0,
            $product['sku'] ?? '',
            $product['weight'] ?? '',
            $product['dimensions'] ?? '',
            $product['created_at'] ?? ''
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    error_log('Export Products Error: ' . $e->getMessage());
    header('Location: products.php?error=export_failed');
    exit();
}
?>
