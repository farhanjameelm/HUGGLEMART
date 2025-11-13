<?php
require_once '../config/config.php';
require_once '../classes/User.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit();
}

try {
    $userClass = new User();
    $users = $userClass->getAllUsers();
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d_H-i-s') . '.csv"');
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'ID',
        'Username',
        'Email',
        'First Name',
        'Last Name',
        'Phone',
        'Is Admin',
        'Is Active',
        'Created At'
    ]);
    
    // Add user data
    foreach ($users as $user) {
        fputcsv($output, [
            $user['id'],
            $user['username'],
            $user['email'],
            $user['first_name'] ?? '',
            $user['last_name'] ?? '',
            $user['phone'] ?? '',
            $user['is_admin'] ? 'Yes' : 'No',
            $user['is_active'] ? 'Yes' : 'No',
            $user['created_at']
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    error_log('Export Users Error: ' . $e->getMessage());
    header('Location: users.php?error=export_failed');
    exit();
}
?>
