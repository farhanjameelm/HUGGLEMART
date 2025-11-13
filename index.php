<?php
require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/User.php';

$pageTitle = 'Home';
$product = new Product();

// Get featured products
$featuredProducts = $product->getAllProducts(8, 0, null, null, true);

// Get latest products
$latestProducts = $product->getAllProducts(8, 0);

// Categories data (you can move this to a Category class later)
$categories = [
    ['name' => 'Electronics', 'slug' => 'electronics', 'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400'],
    ['name' => 'Fashion', 'slug' => 'fashion', 'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400'],
    ['name' => 'Home & Garden', 'slug' => 'home-garden', 'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400'],
    ['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400'],
    ['name' => 'Books & Media', 'slug' => 'books-media', 'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400'],
    ['name' => 'Beauty & Health', 'slug' => 'beauty-health', 'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400']
];

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Shop Smart, <span class="text-warning">Negotiate Better!</span>
                </h1>
                <p class="lead mb-4">
                    Discover amazing products and negotiate prices directly with sellers. 
                    Get the best deals on everything you love at HugglingMart.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#featured-products" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="bargaining-guide.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-handshake me-2"></i>Learn to Bargain
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=500" 
                     alt="Shopping" class="img-fluid rounded-3 shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-primary mb-3">
                            <i class="fas fa-handshake fa-3x"></i>
                        </div>
                        <h5 class="card-title">Price Negotiation</h5>
                        <p class="card-text text-muted">
                            Chat directly with sellers and negotiate prices to get the best deals on your favorite products.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-success mb-3">
                            <i class="fas fa-shield-alt fa-3x"></i>
                        </div>
                        <h5 class="card-title">Secure Shopping</h5>
                        <p class="card-text text-muted">
                            Shop with confidence knowing your transactions are protected with our secure payment system.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card border-0 h-100 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-info mb-3">
                            <i class="fas fa-shipping-fast fa-3x"></i>
                        </div>
                        <h5 class="card-title">Fast Delivery</h5>
                        <p class="card-text text-muted">
                            Get your orders delivered quickly with our reliable shipping partners across the country.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Shop by Category</h2>
            <p class="text-muted">Explore our wide range of product categories</p>
        </div>
        
        <div class="row">
            <?php foreach ($categories as $category): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="category.php?slug=<?php echo $category['slug']; ?>" class="text-decoration-none">
                        <div class="category-card" style="background-image: url('<?php echo $category['image']; ?>');">
                            <div class="category-overlay">
                                <?php echo $category['name']; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<?php if (!empty($featuredProducts)): ?>
<section id="featured-products" class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Handpicked products with the best bargaining opportunities</p>
        </div>
        
        <div class="row">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card h-100 shadow-sm">
                        <?php if ($product['allow_bargaining']): ?>
                            <div class="bargain-badge">
                                <i class="fas fa-handshake me-1"></i>Negotiable
                            </div>
                        <?php endif; ?>
                        
                        <div class="position-relative">
                            <?php 
                            $productImage = !empty($product['images']) ? $product['images'][0] : 'https://via.placeholder.com/300x250?text=No+Image';
                            ?>
                            <img src="<?php echo $productImage; ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h6>
                            <p class="card-text text-muted small mb-3">
                                <?php echo htmlspecialchars(substr($product['short_description'], 0, 80)) . '...'; ?>
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <?php if ($product['sale_price']): ?>
                                            <span class="price-tag"><?php echo formatPrice($product['sale_price']); ?></span>
                                            <span class="original-price ms-2"><?php echo formatPrice($product['price']); ?></span>
                                        <?php else: ?>
                                            <span class="price-tag"><?php echo formatPrice($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-star text-warning"></i> 4.5
                                    </small>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <?php if ($product['allow_bargaining']): ?>
                                        <button class="btn btn-negotiate btn-sm flex-fill" 
                                                onclick="openBargainModal(<?php echo $product['id']; ?>, '<?php echo addslashes(htmlspecialchars($product['name'])); ?>', <?php echo $product['price']; ?>)">
                                            <i class="fas fa-handshake me-1"></i>Negotiate
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-primary btn-sm flex-fill" 
                                            onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="products.php?featured=1" class="btn btn-primary btn-lg">
                View All Featured Products
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Products Section -->
<?php if (!empty($latestProducts)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Latest Products</h2>
            <p class="text-muted">Discover our newest arrivals</p>
        </div>
        
        <div class="row">
            <?php foreach ($latestProducts as $product): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card h-100 shadow-sm">
                        <?php if ($product['allow_bargaining']): ?>
                            <div class="bargain-badge">
                                <i class="fas fa-handshake me-1"></i>Negotiable
                            </div>
                        <?php endif; ?>
                        
                        <div class="position-relative">
                            <?php 
                            $productImage = !empty($product['images']) ? $product['images'][0] : 'https://via.placeholder.com/300x250?text=No+Image';
                            ?>
                            <img src="<?php echo $productImage; ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h6>
                            <p class="card-text text-muted small mb-3">
                                <?php echo htmlspecialchars(substr($product['short_description'], 0, 80)) . '...'; ?>
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <?php if ($product['sale_price']): ?>
                                            <span class="price-tag"><?php echo formatPrice($product['sale_price']); ?></span>
                                            <span class="original-price ms-2"><?php echo formatPrice($product['price']); ?></span>
                                        <?php else: ?>
                                            <span class="price-tag"><?php echo formatPrice($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-star text-warning"></i> 4.5
                                    </small>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <?php if ($product['allow_bargaining']): ?>
                                        <button class="btn btn-negotiate btn-sm flex-fill" 
                                                onclick="openBargainModal(<?php echo $product['id']; ?>, '<?php echo addslashes(htmlspecialchars($product['name'])); ?>', <?php echo $product['price']; ?>)">
                                            <i class="fas fa-handshake me-1"></i>Negotiate
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-primary btn-sm flex-fill" 
                                            onclick="addToCart(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" 
                                            onclick="addToWishlist(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-outline-primary btn-lg">
                View All Products
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="fw-bold mb-2">Stay Updated!</h3>
                <p class="mb-0">Get the latest deals and bargaining tips delivered to your inbox.</p>
            </div>
            <div class="col-lg-6">
                <form class="d-flex gap-2">
                    <input type="email" class="form-control" placeholder="Enter your email address" required>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane me-2"></i>Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Bargain Modal -->
<div class="modal fade" id="bargainModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-handshake text-warning me-2"></i>Negotiate Price
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
                            <input type="number" class="form-control" id="offeredPrice" name="offered_price" 
                                   step="0.01" min="1" required>
                        </div>
                        <div class="form-text">
                            <span id="discountPercentage"></span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Message (Optional)</label>
                        <textarea class="form-control" name="message" rows="3" 
                                  placeholder="Tell the seller why you think this price is fair..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitBargain()">
                    <i class="fas fa-handshake me-2"></i>Send Offer
                </button>
            </div>
        </div>
    </div>
</div>

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
            showToast('Your bargain offer has been sent!', 'success');
            const modal = mdb.Modal.getInstance(document.getElementById('bargainModal'));
            modal.hide();
            updateCounts();
        } else {
            showToast(data.message || 'Failed to send bargain offer', 'danger');
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
