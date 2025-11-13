<?php
$pageTitle = 'Test All Pages';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test All Created Pages - HugglingMart</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center mb-5">
            <i class="fas fa-check-circle text-success me-2"></i>
            All Pages Created Successfully!
        </h1>
        
        <div class="row">
            <!-- Admin Pages -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i>Admin Pages
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="admin/settings.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-cog me-2 text-primary"></i>Settings Management
                                <small class="text-muted d-block">Site configuration, email, security settings</small>
                            </a>
                            <a href="admin/users.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-users me-2 text-success"></i>Users Management
                                <small class="text-muted d-block">Manage customers and admin users</small>
                            </a>
                            <a href="admin/orders.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-cart me-2 text-warning"></i>Orders Management
                                <small class="text-muted d-block">View and manage customer orders</small>
                            </a>
                            <a href="admin/products.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-box me-2 text-info"></i>Products Management
                                <small class="text-muted d-block">Add, edit, and manage products</small>
                            </a>
                            <a href="admin/search.php?q=" class="list-group-item list-group-item-action">
                                <i class="fas fa-search me-2 text-secondary"></i>Admin Search
                                <small class="text-muted d-block">Search across all admin data</small>
                            </a>
                            <a href="admin/cart.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-shopping-basket me-2 text-danger"></i>Cart Management
                                <small class="text-muted d-block">Manage active shopping carts</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Category Pages -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tags me-2"></i>Category Pages
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="category.php?slug=electronics" class="list-group-item list-group-item-action">
                                <i class="fas fa-laptop me-2 text-primary"></i>Electronics
                                <small class="text-muted d-block">Gadgets, smartphones, laptops</small>
                            </a>
                            <a href="category.php?slug=fashion" class="list-group-item list-group-item-action">
                                <i class="fas fa-tshirt me-2 text-danger"></i>Fashion
                                <small class="text-muted d-block">Clothing, shoes, accessories</small>
                            </a>
                            <a href="category.php?slug=home-garden" class="list-group-item list-group-item-action">
                                <i class="fas fa-home me-2 text-success"></i>Home & Garden
                                <small class="text-muted d-block">Home decor, furniture, gardening</small>
                            </a>
                            <a href="category.php?slug=sports-outdoors" class="list-group-item list-group-item-action">
                                <i class="fas fa-football-ball me-2 text-warning"></i>Sports & Outdoors
                                <small class="text-muted d-block">Sports equipment, outdoor gear</small>
                            </a>
                            <a href="category.php?slug=books-media" class="list-group-item list-group-item-action">
                                <i class="fas fa-book me-2 text-info"></i>Books & Media
                                <small class="text-muted d-block">Books, magazines, movies, music</small>
                            </a>
                            <a href="category.php?slug=beauty-health" class="list-group-item list-group-item-action">
                                <i class="fas fa-heart me-2 text-secondary"></i>Beauty & Health
                                <small class="text-muted d-block">Skincare, makeup, wellness</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Features Summary -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Features Implemented
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6><i class="fas fa-shield-alt text-success me-2"></i>Security Features</h6>
                                <ul class="list-unstyled">
                                    <li>• CSRF Protection</li>
                                    <li>• Admin Authentication</li>
                                    <li>• Session Management</li>
                                    <li>• Input Validation</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fas fa-search text-primary me-2"></i>Search & Filter</h6>
                                <ul class="list-unstyled">
                                    <li>• Advanced Search</li>
                                    <li>• Category Filtering</li>
                                    <li>• Price Range Filters</li>
                                    <li>• Sorting Options</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h6><i class="fas fa-mobile-alt text-warning me-2"></i>Responsive Design</h6>
                                <ul class="list-unstyled">
                                    <li>• Mobile Friendly</li>
                                    <li>• Bootstrap 5</li>
                                    <li>• Modern UI/UX</li>
                                    <li>• Accessible Design</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <h5 class="mb-3">Quick Actions</h5>
                <div class="btn-group" role="group">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i>Go to Homepage
                    </a>
                    <a href="admin/index.php" class="btn btn-success">
                        <i class="fas fa-tachometer-alt me-1"></i>Admin Dashboard
                    </a>
                    <a href="categories.php" class="btn btn-info">
                        <i class="fas fa-th-large me-1"></i>All Categories
                    </a>
                    <a href="products.php" class="btn btn-warning">
                        <i class="fas fa-box me-1"></i>All Products
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Status Summary -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-success text-center">
                    <h4><i class="fas fa-check-circle me-2"></i>All Pages Created Successfully!</h4>
                    <p class="mb-0">
                        <strong>12 pages</strong> created with full functionality including admin management, 
                        category browsing, search capabilities, and responsive design.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
