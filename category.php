<?php
require_once 'config/config.php';
require_once 'classes/Product.php';

$pageTitle = 'Category';
$slug = $_GET['slug'] ?? '';

// Define categories
$categories = [
    'electronics' => [
        'name' => 'Electronics',
        'description' => 'Latest gadgets, smartphones, laptops, and electronic accessories',
        'icon' => 'fas fa-laptop',
        'color' => 'primary'
    ],
    'fashion' => [
        'name' => 'Fashion',
        'description' => 'Trendy clothing, shoes, accessories, and fashion items',
        'icon' => 'fas fa-tshirt',
        'color' => 'danger'
    ],
    'home-garden' => [
        'name' => 'Home & Garden',
        'description' => 'Home decor, furniture, gardening tools, and household items',
        'icon' => 'fas fa-home',
        'color' => 'success'
    ],
    'sports-outdoors' => [
        'name' => 'Sports & Outdoors',
        'description' => 'Sports equipment, outdoor gear, fitness accessories',
        'icon' => 'fas fa-football-ball',
        'color' => 'warning'
    ],
    'books-media' => [
        'name' => 'Books & Media',
        'description' => 'Books, magazines, movies, music, and digital media',
        'icon' => 'fas fa-book',
        'color' => 'info'
    ],
    'beauty-health' => [
        'name' => 'Beauty & Health',
        'description' => 'Skincare, makeup, health supplements, and wellness products',
        'icon' => 'fas fa-heart',
        'color' => 'secondary'
    ]
];

// Check if category exists
if (!isset($categories[$slug])) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit();
}

$category = $categories[$slug];
$pageTitle = $category['name'];

// Initialize Product class
$productClass = new Product();

// Get products for this category
$page = $_GET['page'] ?? 1;
$limit = 12;
$offset = ($page - 1) * $limit;
$sortBy = $_GET['sort'] ?? 'name';
$order = $_GET['order'] ?? 'ASC';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$search = $_GET['search'] ?? '';

try {
    // Get all products and filter by category
    $allProducts = $productClass->getAllProducts(1000, 0);
    $products = array_filter($allProducts, function($product) use ($slug) {
        return isset($product['category']) && $product['category'] === $slug;
    });
    
    // Apply search filter
    if (!empty($search)) {
        $products = array_filter($products, function($product) use ($search) {
            return stripos($product['name'], $search) !== false || 
                   stripos($product['description'], $search) !== false;
        });
    }
    
    // Apply price filter
    if (!empty($minPrice)) {
        $products = array_filter($products, function($product) use ($minPrice) {
            return $product['price'] >= floatval($minPrice);
        });
    }
    
    if (!empty($maxPrice)) {
        $products = array_filter($products, function($product) use ($maxPrice) {
            return $product['price'] <= floatval($maxPrice);
        });
    }
    
    // Sort products
    usort($products, function($a, $b) use ($sortBy, $order) {
        $result = 0;
        switch ($sortBy) {
            case 'price':
                $result = $a['price'] <=> $b['price'];
                break;
            case 'name':
            default:
                $result = strcasecmp($a['name'], $b['name']);
                break;
        }
        return $order === 'DESC' ? -$result : $result;
    });
    
    $totalProducts = count($products);
    $products = array_slice($products, $offset, $limit);
    
} catch (Exception $e) {
    $products = [];
    $totalProducts = 0;
}

$totalPages = ceil($totalProducts / $limit);

include 'includes/header.php';
?>

<!-- Category Hero Section -->
<div class="hero-section bg-<?php echo $category['color']; ?> text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <i class="<?php echo $category['icon']; ?> fa-3x me-3"></i>
                    <div>
                        <h1 class="display-4 mb-0"><?php echo $category['name']; ?></h1>
                        <p class="lead mb-0"><?php echo $category['description']; ?></p>
                    </div>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item"><a href="categories.php" class="text-white">Categories</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?php echo $category['name']; ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-end">
                <div class="bg-white bg-opacity-10 rounded p-3">
                    <h4 class="mb-1"><?php echo number_format($totalProducts); ?></h4>
                    <p class="mb-0">Products Available</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="slug" value="<?php echo $slug; ?>">
                        
                        <div class="col-md-4">
                            <label class="form-label">Search in <?php echo $category['name']; ?></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                       placeholder="Search products...">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Min Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="min_price" value="<?php echo htmlspecialchars($minPrice); ?>" 
                                       placeholder="0" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Max Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="max_price" value="<?php echo htmlspecialchars($maxPrice); ?>" 
                                       placeholder="1000" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" name="sort">
                                <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Name</option>
                                <option value="price" <?php echo $sortBy === 'price' ? 'selected' : ''; ?>>Price</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Order</label>
                            <select class="form-select" name="order">
                                <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                                <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-<?php echo $category['color']; ?> me-2">
                                <i class="fas fa-filter me-1"></i>Apply Filters
                            </button>
                            <a href="category.php?slug=<?php echo $slug; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h5>
                <i class="<?php echo $category['icon']; ?> text-<?php echo $category['color']; ?> me-2"></i>
                <?php echo $category['name']; ?> Products
                <span class="badge bg-<?php echo $category['color']; ?> ms-2"><?php echo count($products); ?> of <?php echo $totalProducts; ?></span>
            </h5>
        </div>
        <div class="col-md-6 text-end">
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="view" id="gridView" autocomplete="off" checked>
                <label class="btn btn-outline-<?php echo $category['color']; ?>" for="gridView">
                    <i class="fas fa-th"></i> Grid
                </label>
                
                <input type="radio" class="btn-check" name="view" id="listView" autocomplete="off">
                <label class="btn btn-outline-<?php echo $category['color']; ?>" for="listView">
                    <i class="fas fa-list"></i> List
                </label>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row" id="productsContainer">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="<?php echo $category['icon']; ?> fa-4x text-muted mb-4"></i>
                    <h4>No Products Found</h4>
                    <p class="text-muted mb-4">
                        <?php if (!empty($search) || !empty($minPrice) || !empty($maxPrice)): ?>
                            No products match your current filters. Try adjusting your search criteria.
                        <?php else: ?>
                            We're working on adding products to this category. Check back soon!
                        <?php endif; ?>
                    </p>
                    <a href="category.php?slug=<?php echo $slug; ?>" class="btn btn-<?php echo $category['color']; ?>">
                        <i class="fas fa-refresh me-1"></i>Clear Filters
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <?php 
                            $images = isset($product['images']) && !empty($product['images']) ? json_decode($product['images'], true) : [];
                            $mainImage = !empty($images) ? $images[0] : 'assets/images/placeholder.jpg';
                            ?>
                            <img src="<?php echo $mainImage; ?>" class="card-img-top" alt="<?php echo htmlspecialchars(isset($product['name']) ? $product['name'] : 'Product'); ?>" 
                                 style="height: 200px; object-fit: cover;">
                            
                            <!-- Stock Badge -->
                            <?php if (isset($product['stock_quantity']) && $product['stock_quantity'] <= 0): ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">Out of Stock</span>
                            <?php elseif (isset($product['stock_quantity']) && $product['stock_quantity'] <= 5): ?>
                                <span class="badge bg-warning position-absolute top-0 end-0 m-2">Low Stock</span>
                            <?php endif; ?>
                            
                            <!-- Quick Actions -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <button class="btn btn-sm btn-light rounded-circle" onclick="addToWishlist(<?php echo isset($product['id']) ? $product['id'] : 0; ?>)" title="Add to Wishlist">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">
                                <a href="product.php?id=<?php echo isset($product['id']) ? $product['id'] : '#'; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars(isset($product['name']) ? $product['name'] : 'Unknown Product'); ?>
                                </a>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1">
                                <?php echo htmlspecialchars(substr(isset($product['description']) ? $product['description'] : '', 0, 100)) . '...'; ?>
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 text-<?php echo $category['color']; ?> mb-0">
                                        $<?php echo number_format(isset($product['price']) ? $product['price'] : 0, 2); ?>
                                    </span>
                                    <small class="text-muted">
                                        <i class="fas fa-boxes me-1"></i><?php echo isset($product['stock_quantity']) ? $product['stock_quantity'] : 0; ?> left
                                    </small>
                                </div>
                                
                                <div class="btn-group w-100" role="group">
                                    <?php if (isset($product['stock_quantity']) && $product['stock_quantity'] > 0): ?>
                                        <button class="btn btn-outline-<?php echo $category['color']; ?> btn-sm" 
                                                onclick="addToCart(<?php echo isset($product['id']) ? $product['id'] : 0; ?>)">
                                            <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                        </button>
                                        <button class="btn btn-<?php echo $category['color']; ?> btn-sm" 
                                                onclick="openBargainModal(<?php echo isset($product['id']) ? $product['id'] : 0; ?>, '<?php echo addslashes(htmlspecialchars(isset($product['name']) ? $product['name'] : '')); ?>', <?php echo isset($product['price']) ? $product['price'] : 0; ?>)">
                                            <i class="fas fa-handshake me-1"></i>Negotiate
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-times me-1"></i>Out of Stock
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav aria-label="Category products pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $order; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>">
                        <i class="fas fa-chevron-left me-1"></i>Previous
                    </a>
                </li>
                
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                if ($startPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?slug=<?php echo $slug; ?>&page=1&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $order; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>">1</a>
                    </li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $order; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $order; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>"><?php echo $totalPages; ?></a>
                    </li>
                <?php endif; ?>
                
                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sortBy; ?>&order=<?php echo $order; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>">
                        Next<i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

    <!-- Related Categories -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="mb-4">
                <i class="fas fa-tags text-<?php echo $category['color']; ?> me-2"></i>
                Explore Other Categories
            </h4>
            <div class="row">
                <?php foreach ($categories as $catSlug => $catData): ?>
                    <?php if ($catSlug !== $slug): ?>
                        <div class="col-md-4 col-lg-2 mb-3">
                            <a href="category.php?slug=<?php echo $catSlug; ?>" class="text-decoration-none">
                                <div class="card text-center h-100 category-card">
                                    <div class="card-body">
                                        <i class="<?php echo $catData['icon']; ?> fa-2x text-<?php echo $catData['color']; ?> mb-2"></i>
                                        <h6 class="card-title"><?php echo $catData['name']; ?></h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bargaining Modal (same as other pages) -->
<?php include 'includes/bargain-modal.php'; ?>

<?php
$additionalJS = "
<script>
let currentProductPrice = 0;

function openBargainModal(productId, productName, price) {
    currentProductPrice = price;
    
    document.getElementById('bargainProductId').value = productId;
    document.getElementById('bargainProductInfo').innerHTML = \`
        <div class='d-flex align-items-center'>
            <div>
                <h6 class='mb-1'>\${productName}</h6>
                <p class='text-muted mb-0'>Original Price: <strong>\$\${price.toFixed(2)}</strong></p>
            </div>
        </div>
    \`;
    
    // Set suggested price (20% discount)
    const suggestedPrice = (price * 0.8).toFixed(2);
    document.getElementById('offeredPrice').value = suggestedPrice;
    updateDiscountPercentage();
    
    const modal = new mdb.Modal(document.getElementById('bargainModal'));
    modal.show();
}

function updateDiscountPercentage() {
    const offeredPrice = parseFloat(document.getElementById('offeredPrice').value);
    if (offeredPrice && currentProductPrice) {
        const discount = ((currentProductPrice - offeredPrice) / currentProductPrice * 100).toFixed(1);
        const discountElement = document.getElementById('discountPercentage');
        
        if (discount > 0) {
            discountElement.innerHTML = \`<i class='fas fa-arrow-down text-success'></i> \${discount}% discount\`;
            discountElement.className = 'text-success';
        } else if (discount < 0) {
            discountElement.innerHTML = \`<i class='fas fa-arrow-up text-danger'></i> \${Math.abs(discount)}% above original price\`;
            discountElement.className = 'text-danger';
        } else {
            discountElement.innerHTML = 'Same as original price';
            discountElement.className = 'text-muted';
        }
    }
}

document.getElementById('offeredPrice').addEventListener('input', updateDiscountPercentage);

// View toggle functionality
document.getElementById('gridView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('productsContainer').className = 'row';
    }
});

document.getElementById('listView').addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('productsContainer').className = 'row list-view';
    }
});
</script>
";

include 'includes/footer.php';
?>
