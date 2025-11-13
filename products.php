<?php
require_once 'config/config.php';
require_once 'classes/Product.php';

$pageTitle = 'All Products';
$product = new Product();

// Get filter parameters
$category = $_GET['category'] ?? '';
$sortBy = $_GET['sort'] ?? 'newest';
$page = (int)($_GET['page'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;

// Get categories for filter
$categories = [];
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $categoryQuery = "SELECT * FROM categories ORDER BY name";
    $categoryStmt = $conn->prepare($categoryQuery);
    $categoryStmt->execute();
    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
}

// Get products
$allProducts = $product->getAllProducts($limit, $offset, $category, $sortBy);
$totalProducts = $product->getTotalProductCount($category);
$totalPages = ceil($totalProducts / $limit);

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card sticky-top" style="top: 100px;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Products
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="products.php">
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Sort By -->
                        <div class="mb-4">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" name="sort" onchange="this.form.submit()">
                                <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="oldest" <?php echo $sortBy === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                                <option value="price_low" <?php echo $sortBy === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sortBy === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                            </select>
                        </div>
                        
                        <!-- Clear Filters -->
                        <div class="d-grid">
                            <a href="products.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-box text-primary me-2"></i>All Products
                    </h2>
                    <p class="text-muted mb-0">
                        <?php echo $totalProducts; ?> product<?php echo $totalProducts !== 1 ? 's' : ''; ?> found
                        <?php if (!empty($category)): ?>
                            <?php 
                            $selectedCategory = array_filter($categories, function($cat) use ($category) {
                                return $cat['id'] == $category;
                            });
                            $categoryName = !empty($selectedCategory) ? reset($selectedCategory)['name'] : 'Category';
                            ?>
                            in <?php echo htmlspecialchars($categoryName); ?>
                        <?php endif; ?>
                    </p>
                </div>
                
                <!-- View Toggle -->
                <div class="btn-group">
                    <button class="btn btn-outline-secondary active" id="gridView">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="btn btn-outline-secondary" id="listView">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            
            <?php if (empty($allProducts)): ?>
                <!-- No Products -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No Products Found</h4>
                        <p class="text-muted mb-4">
                            <?php if (!empty($category)): ?>
                                No products found in this category.
                            <?php else: ?>
                                No products are currently available.
                            <?php endif; ?>
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="products.php" class="btn btn-primary">
                                <i class="fas fa-refresh me-2"></i>View All Products
                            </a>
                            <a href="categories.php" class="btn btn-outline-primary">
                                <i class="fas fa-th-large me-2"></i>Browse Categories
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Products Grid -->
                <div class="row" id="productsContainer">
                    <?php foreach ($allProducts as $productItem): ?>
                        <div class="col-lg-4 col-md-6 mb-4 product-item">
                            <div class="card h-100 product-card">
                                <div class="position-relative">
                                    <?php 
                                    $productImage = !empty($productItem['images']) ? $productItem['images'][0] : 'https://via.placeholder.com/300x200?text=No+Image';
                                    ?>
                                    <img src="<?php echo $productImage; ?>" class="card-img-top" alt="Product" style="height: 200px; object-fit: cover;">
                                    
                                    <!-- Stock Status -->
                                    <?php if ($productItem['stock_quantity'] <= 0): ?>
                                        <span class="position-absolute top-0 end-0 badge bg-danger m-2">Out of Stock</span>
                                    <?php elseif ($productItem['stock_quantity'] <= 5): ?>
                                        <span class="position-absolute top-0 end-0 badge bg-warning m-2">Low Stock</span>
                                    <?php endif; ?>
                                    
                                    <!-- Bargaining Available -->
                                    <?php if ($productItem['allow_bargaining']): ?>
                                        <span class="position-absolute top-0 start-0 badge bg-success m-2">
                                            <i class="fas fa-handshake me-1"></i>Bargain
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Wishlist Button -->
                                    <button class="btn btn-outline-light position-absolute" style="top: 10px; right: 50px;" onclick="addToWishlist(<?php echo $productItem['id']; ?>)">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <small class="text-muted"><?php echo htmlspecialchars($productItem['category_name']); ?></small>
                                    </div>
                                    <h6 class="card-title">
                                        <a href="product.php?id=<?php echo $productItem['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($productItem['name']); ?>
                                        </a>
                                    </h6>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo htmlspecialchars(substr($productItem['description'], 0, 100)); ?>...
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="price">
                                                <h5 class="text-primary mb-0"><?php echo formatPrice($productItem['price']); ?></h5>
                                            </div>
                                            <div class="rating">
                                                <small class="text-muted">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <?php echo number_format($productItem['rating'] ?? 4.5, 1); ?>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="btn-group w-100">
                                            <a href="product.php?id=<?php echo $productItem['id']; ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <?php if ($productItem['allow_bargaining']): ?>
                                                <button class="btn btn-outline-success" onclick="openBargainModal(<?php echo $productItem['id']; ?>, '<?php echo addslashes(htmlspecialchars($productItem['name'])); ?>', <?php echo $productItem['price']; ?>)">
                                                    <i class="fas fa-handshake me-1"></i>Bargain
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-primary" onclick="addToCart(<?php echo $productItem['id']; ?>)">
                                                <i class="fas fa-cart-plus me-1"></i>Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Products pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&category=<?php echo $category; ?>&sort=<?php echo $sortBy; ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category; ?>&sort=<?php echo $sortBy; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&category=<?php echo $category; ?>&sort=<?php echo $sortBy; ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bargain Modal (same as in index.php) -->
<div class="modal fade" id="bargainModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-handshake text-primary me-2"></i>Negotiate Price
                </h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="bargainProductInfo" class="mb-3"></div>
                
                <form id="bargainForm">
                    <input type="hidden" id="bargainProductId" name="product_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Your Offer Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="offeredPrice" name="offered_price" step="0.01" min="1" required>
                        </div>
                        <div class="form-text">
                            <span id="discountPercentage"></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Message (Optional)</label>
                        <textarea class="form-control" name="message" rows="3" 
                                  placeholder="Tell us why you think this price is fair..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBargain()">
                    <i class="fas fa-handshake me-2"></i>Submit Offer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.list-view .product-item {
    width: 100%;
}

.list-view .product-card {
    flex-direction: row;
}

.list-view .product-card img {
    width: 200px;
    height: 150px;
}
</style>

<?php
$additionalJS = "
<script>
let currentProductPrice = 0;

// View toggle functionality
document.getElementById('gridView').addEventListener('click', function() {
    document.getElementById('productsContainer').className = 'row';
    this.classList.add('active');
    document.getElementById('listView').classList.remove('active');
});

document.getElementById('listView').addEventListener('click', function() {
    document.getElementById('productsContainer').className = 'row list-view';
    this.classList.add('active');
    document.getElementById('gridView').classList.remove('active');
});

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
    const suggestedPrice = (price * 0.8).toFixed(2);
    document.getElementById('offeredPrice').value = suggestedPrice;
    updateDiscountPercentage();
    const modal = new mdb.Modal(document.getElementById('bargainModal'));
    modal.show();
}

function updateDiscountPercentage() {
    const offeredPrice = parseFloat(document.getElementById('offeredPrice').value) || 0;
    const discount = ((currentProductPrice - offeredPrice) / currentProductPrice) * 100;
    const discountElement = document.getElementById('discountPercentage');
    
    if (discount > 0) {
        discountElement.innerHTML = \`<span class='text-success'>You're asking for \${discount.toFixed(1)}% discount</span>\`;
    } else if (discount < 0) {
        discountElement.innerHTML = \`<span class='text-danger'>Offer is higher than original price</span>\`;
    } else {
        discountElement.innerHTML = '';
    }
}

document.getElementById('offeredPrice').addEventListener('input', updateDiscountPercentage);

function submitBargain() {
    const form = document.getElementById('bargainForm');
    const formData = new FormData(form);
    
    fetch('api/create-bargain.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Bargain offer submitted successfully!', 'success');
            const modal = mdb.Modal.getInstance(document.getElementById('bargainModal'));
            modal.hide();
            form.reset();
        } else {
            showToast(data.message || 'Failed to submit bargain', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

function addToWishlist(productId) {
    // Wishlist functionality - to be implemented
    showToast('Wishlist feature coming soon!', 'info');
}
</script>
";

include 'includes/footer.php';
?>
