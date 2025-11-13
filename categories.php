<?php
require_once 'config/config.php';
require_once 'classes/Product.php';

$pageTitle = 'Product Categories';
$product = new Product();

// Get all categories with product counts
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $categoryQuery = "SELECT c.*, COUNT(p.id) as product_count 
                     FROM categories c 
                     LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                     GROUP BY c.id 
                     ORDER BY c.name";
    $categoryStmt = $conn->prepare($categoryQuery);
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-th-large text-primary me-3"></i>Product Categories
                </h1>
                <p class="lead text-muted">Browse our wide selection of products organized by category</p>
            </div>
            
            <?php if (empty($categories)): ?>
                <!-- No Categories -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-th-large fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No Categories Available</h4>
                        <p class="text-muted mb-4">Categories will appear here once they are added to the system.</p>
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Back to Homepage
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Categories Grid -->
                <div class="row">
                    <?php foreach ($categories as $category): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 category-card">
                                <div class="position-relative">
                                    <?php 
                                    // Default category images
                                    $categoryImages = [
                                        'Electronics' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=250&fit=crop',
                                        'Fashion' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=250&fit=crop',
                                        'Home & Garden' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=250&fit=crop',
                                        'Sports & Outdoors' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=250&fit=crop',
                                        'Books & Media' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&h=250&fit=crop',
                                        'Beauty & Health' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=250&fit=crop',
                                        'Toys & Games' => 'https://images.unsplash.com/photo-1558060370-d644479cb6f7?w=400&h=250&fit=crop',
                                        'Automotive' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=400&h=250&fit=crop'
                                    ];
                                    
                                    $categoryImage = $categoryImages[$category['name']] ?? 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=250&fit=crop';
                                    ?>
                                    <img src="<?php echo $categoryImage; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($category['name']); ?>" style="height: 200px; object-fit: cover;">
                                    
                                    <!-- Product Count Badge -->
                                    <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                        <?php echo $category['product_count']; ?> Products
                                    </span>
                                    
                                    <!-- Category Overlay -->
                                    <div class="position-absolute bottom-0 start-0 w-100 bg-gradient-dark text-white p-3">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($category['name']); ?></h5>
                                        <?php if (!empty($category['description'])): ?>
                                            <p class="mb-0 small opacity-75">
                                                <?php echo htmlspecialchars(substr($category['description'], 0, 80)); ?>...
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <?php if (!empty($category['description'])): ?>
                                        <p class="card-text text-muted">
                                            <?php echo htmlspecialchars($category['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-muted">
                                            <i class="fas fa-box me-1"></i>
                                            <?php echo $category['product_count']; ?> product<?php echo $category['product_count'] !== 1 ? 's' : ''; ?>
                                        </div>
                                        <div>
                                            <?php if ($category['product_count'] > 0): ?>
                                                <a href="search.php?category=<?php echo $category['id']; ?>" class="btn btn-primary">
                                                    <i class="fas fa-arrow-right me-1"></i>Browse
                                                </a>
                                            <?php else: ?>
                                                <span class="btn btn-outline-secondary disabled">
                                                    No Products
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Category Stats -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <div class="row">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <h4 class="text-primary mb-1"><?php echo count($categories); ?></h4>
                                        <p class="text-muted mb-0">Total Categories</p>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <?php 
                                        $totalProducts = array_sum(array_column($categories, 'product_count'));
                                        ?>
                                        <h4 class="text-success mb-1"><?php echo $totalProducts; ?></h4>
                                        <p class="text-muted mb-0">Total Products</p>
                                    </div>
                                    <div class="col-md-4">
                                        <?php 
                                        $avgProductsPerCategory = count($categories) > 0 ? round($totalProducts / count($categories), 1) : 0;
                                        ?>
                                        <h4 class="text-info mb-1"><?php echo $avgProductsPerCategory; ?></h4>
                                        <p class="text-muted mb-0">Avg Products per Category</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.bg-gradient-dark {
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
}
</style>

<?php include 'includes/footer.php'; ?>
