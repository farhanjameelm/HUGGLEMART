<?php
require_once 'config/config.php';
require_once 'classes/Product.php';

$query = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
$sortBy = $_GET['sort'] ?? 'relevance';
$page = (int)($_GET['page'] ?? 1);
$limit = 12;
$offset = ($page - 1) * $limit;

$pageTitle = !empty($query) ? 'Search Results for "' . htmlspecialchars($query) . '"' : 'Search Products';

$product = new Product();
$searchResults = [];
$totalResults = 0;
$categories = [];

// Get all categories for filter
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

// Perform search if query is provided
if (!empty($query) || !empty($category) || !empty($minPrice) || !empty($maxPrice)) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Build search query
        $searchQuery = "SELECT p.*, c.name as category_name, 
                       MATCH(p.name, p.description) AGAINST (? IN NATURAL LANGUAGE MODE) as relevance_score
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.status = 'active'";
        
        $params = [];
        
        if (!empty($query)) {
            $searchQuery .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
            $params[] = $query;
            $params[] = "%$query%";
            $params[] = "%$query%";
            $params[] = "%$query%";
        } else {
            $params[] = '';
        }
        
        if (!empty($category)) {
            $searchQuery .= " AND p.category_id = ?";
            $params[] = $category;
        }
        
        if (!empty($minPrice)) {
            $searchQuery .= " AND p.price >= ?";
            $params[] = $minPrice;
        }
        
        if (!empty($maxPrice)) {
            $searchQuery .= " AND p.price <= ?";
            $params[] = $maxPrice;
        }
        
        // Add sorting
        switch ($sortBy) {
            case 'price_low':
                $searchQuery .= " ORDER BY p.price ASC";
                break;
            case 'price_high':
                $searchQuery .= " ORDER BY p.price DESC";
                break;
            case 'name':
                $searchQuery .= " ORDER BY p.name ASC";
                break;
            case 'newest':
                $searchQuery .= " ORDER BY p.created_at DESC";
                break;
            default:
                $searchQuery .= !empty($query) ? " ORDER BY relevance_score DESC, p.created_at DESC" : " ORDER BY p.created_at DESC";
        }
        
        // Get total count
        $countQuery = str_replace("SELECT p.*, c.name as category_name, MATCH(p.name, p.description) AGAINST (? IN NATURAL LANGUAGE MODE) as relevance_score", "SELECT COUNT(*)", $searchQuery);
        $countQuery = preg_replace('/ORDER BY.*/', '', $countQuery);
        
        $countStmt = $conn->prepare($countQuery);
        $countStmt->execute($params);
        $totalResults = $countStmt->fetchColumn();
        
        // Get paginated results
        $searchQuery .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $searchStmt = $conn->prepare($searchQuery);
        $searchStmt->execute($params);
        $searchResults = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process product images
        foreach ($searchResults as &$product) {
            $product['images'] = !empty($product['images']) ? explode(',', $product['images']) : [];
        }
        
    } catch (Exception $e) {
        $searchResults = [];
        $totalResults = 0;
    }
}

$totalPages = ceil($totalResults / $limit);

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Search Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card sticky-top" style="top: 100px;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Search Filters
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="search.php" id="searchForm">
                        <!-- Search Query -->
                        <div class="mb-4">
                            <label class="form-label">Search Keywords</label>
                            <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Enter keywords...">
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-4">
                            <label class="form-label">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="min_price" value="<?php echo htmlspecialchars($minPrice); ?>" placeholder="Min" min="0" step="0.01">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="max_price" value="<?php echo htmlspecialchars($maxPrice); ?>" placeholder="Max" min="0" step="0.01">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sort By -->
                        <div class="mb-4">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" name="sort">
                                <option value="relevance" <?php echo $sortBy === 'relevance' ? 'selected' : ''; ?>>Relevance</option>
                                <option value="price_low" <?php echo $sortBy === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_high" <?php echo $sortBy === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                                <option value="newest" <?php echo $sortBy === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            </select>
                        </div>
                        
                        <!-- Filter Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>
                            <a href="search.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear All
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Search Results -->
        <div class="col-lg-9">
            <!-- Search Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <?php if (!empty($query)): ?>
                        <h2 class="mb-1">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
                        <p class="text-muted mb-0"><?php echo $totalResults; ?> product<?php echo $totalResults !== 1 ? 's' : ''; ?> found</p>
                    <?php elseif (!empty($category)): ?>
                        <?php 
                        $selectedCategory = array_filter($categories, function($cat) use ($category) {
                            return $cat['id'] == $category;
                        });
                        $categoryName = !empty($selectedCategory) ? reset($selectedCategory)['name'] : 'Category';
                        ?>
                        <h2 class="mb-1"><?php echo htmlspecialchars($categoryName); ?></h2>
                        <p class="text-muted mb-0"><?php echo $totalResults; ?> product<?php echo $totalResults !== 1 ? 's' : ''; ?> found</p>
                    <?php else: ?>
                        <h2 class="mb-1">Search Products</h2>
                        <p class="text-muted mb-0">Use the filters to find what you're looking for</p>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Sort -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-mdb-toggle="dropdown">
                        <i class="fas fa-sort me-2"></i>Sort
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo updateUrlParam('sort', 'relevance'); ?>">Relevance</a></li>
                        <li><a class="dropdown-item" href="<?php echo updateUrlParam('sort', 'price_low'); ?>">Price: Low to High</a></li>
                        <li><a class="dropdown-item" href="<?php echo updateUrlParam('sort', 'price_high'); ?>">Price: High to Low</a></li>
                        <li><a class="dropdown-item" href="<?php echo updateUrlParam('sort', 'name'); ?>">Name A-Z</a></li>
                        <li><a class="dropdown-item" href="<?php echo updateUrlParam('sort', 'newest'); ?>">Newest First</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Active Filters -->
            <?php if (!empty($query) || !empty($category) || !empty($minPrice) || !empty($maxPrice)): ?>
                <div class="mb-4">
                    <h6 class="mb-2">Active Filters:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if (!empty($query)): ?>
                            <span class="badge bg-primary">
                                Search: "<?php echo htmlspecialchars($query); ?>"
                                <a href="<?php echo removeUrlParam('q'); ?>" class="text-white ms-1">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($category)): ?>
                            <?php 
                            $selectedCategory = array_filter($categories, function($cat) use ($category) {
                                return $cat['id'] == $category;
                            });
                            $categoryName = !empty($selectedCategory) ? reset($selectedCategory)['name'] : 'Category';
                            ?>
                            <span class="badge bg-success">
                                Category: <?php echo htmlspecialchars($categoryName); ?>
                                <a href="<?php echo removeUrlParam('category'); ?>" class="text-white ms-1">×</a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($minPrice) || !empty($maxPrice)): ?>
                            <span class="badge bg-warning">
                                Price: <?php echo !empty($minPrice) ? '$' . $minPrice : '$0'; ?> - <?php echo !empty($maxPrice) ? '$' . $maxPrice : '∞'; ?>
                                <a href="<?php echo removeUrlParam(['min_price', 'max_price']); ?>" class="text-white ms-1">×</a>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Search Results Grid -->
            <?php if (empty($searchResults) && (!empty($query) || !empty($category) || !empty($minPrice) || !empty($maxPrice))): ?>
                <!-- No Results -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No Products Found</h4>
                        <p class="text-muted mb-4">We couldn't find any products matching your search criteria.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="search.php" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>New Search
                            </a>
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-home me-2"></i>Browse All Products
                            </a>
                        </div>
                        
                        <!-- Search Suggestions -->
                        <div class="mt-4">
                            <h6 class="text-muted mb-3">Try these suggestions:</h6>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <a href="search.php?q=smartphone" class="btn btn-outline-secondary btn-sm">Smartphones</a>
                                <a href="search.php?q=laptop" class="btn btn-outline-secondary btn-sm">Laptops</a>
                                <a href="search.php?q=headphones" class="btn btn-outline-secondary btn-sm">Headphones</a>
                                <a href="search.php?q=watch" class="btn btn-outline-secondary btn-sm">Watches</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif (!empty($searchResults)): ?>
                <!-- Products Grid -->
                <div class="row">
                    <?php foreach ($searchResults as $productItem): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
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
                    <nav aria-label="Search results pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', $page - 1); ?>">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', $i); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo updateUrlParam('page', $page + 1); ?>">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <!-- Default Search Page -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-4x text-primary mb-4"></i>
                        <h4 class="mb-3">Search Our Products</h4>
                        <p class="text-muted mb-4">Find exactly what you're looking for with our advanced search filters.</p>
                        
                        <!-- Popular Searches -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Popular Searches:</h6>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <a href="search.php?q=smartphone" class="btn btn-outline-primary btn-sm">Smartphones</a>
                                <a href="search.php?q=laptop" class="btn btn-outline-primary btn-sm">Laptops</a>
                                <a href="search.php?q=headphones" class="btn btn-outline-primary btn-sm">Headphones</a>
                                <a href="search.php?q=watch" class="btn btn-outline-primary btn-sm">Watches</a>
                                <a href="search.php?q=camera" class="btn btn-outline-primary btn-sm">Cameras</a>
                            </div>
                        </div>
                        
                        <!-- Categories -->
                        <div>
                            <h6 class="text-muted mb-3">Browse by Category:</h6>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <?php foreach (array_slice($categories, 0, 6) as $cat): ?>
                                    <a href="search.php?category=<?php echo $cat['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
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

<?php
// Helper functions for URL manipulation
function updateUrlParam($param, $value) {
    $params = $_GET;
    $params[$param] = $value;
    return 'search.php?' . http_build_query($params);
}

function removeUrlParam($param) {
    $params = $_GET;
    if (is_array($param)) {
        foreach ($param as $p) {
            unset($params[$p]);
        }
    } else {
        unset($params[$param]);
    }
    return 'search.php' . (!empty($params) ? '?' . http_build_query($params) : '');
}

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
</script>
";

include 'includes/footer.php';
?>
