<?php
require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Bargain.php';

if (!isset($_GET['slug'])) {
    redirect('index.php');
}

$product = new Product();
$bargain = new Bargain();

$productData = $product->getProductBySlug($_GET['slug']);

if (!$productData) {
    redirect('index.php');
}

$pageTitle = $productData['name'];

// Get related products
$relatedProducts = $product->getRelatedProducts($productData['category_id'], $productData['id'], 4);

// Get user's active bargain for this product (if logged in)
$userBargain = null;
if (isset($_SESSION['user_id'])) {
    $userBargains = $bargain->getUserBargains($_SESSION['user_id'], null);
    foreach ($userBargains as $b) {
        if ($b['product_id'] == $productData['id'] && in_array($b['status'], ['pending', 'countered'])) {
            $userBargain = $b;
            break;
        }
    }
}

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <?php if (!empty($productData['images'])): ?>
                    <!-- Main Image -->
                    <div class="main-image mb-3">
                        <img id="mainProductImage" src="<?php echo $productData['images'][0]; ?>" 
                             class="img-fluid rounded-3 shadow-sm" alt="<?php echo htmlspecialchars($productData['name']); ?>"
                             style="width: 100%; height: 400px; object-fit: cover;">
                    </div>
                    
                    <!-- Thumbnail Images -->
                    <?php if (count($productData['images']) > 1): ?>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php foreach ($productData['images'] as $index => $image): ?>
                                <img src="<?php echo $image; ?>" 
                                     class="thumbnail-image rounded-2 shadow-sm <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     alt="Product image <?php echo $index + 1; ?>"
                                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 2px solid transparent;"
                                     onclick="changeMainImage('<?php echo $image; ?>', this)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="https://via.placeholder.com/500x400?text=No+Image" 
                         class="img-fluid rounded-3" alt="No image available">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="category.php?id=<?php echo $productData['category_id']; ?>"><?php echo htmlspecialchars($productData['category_name']); ?></a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($productData['name']); ?></li>
                    </ol>
                </nav>
                
                <!-- Product Title -->
                <h1 class="h2 mb-3"><?php echo htmlspecialchars($productData['name']); ?></h1>
                
                <!-- Rating and Reviews -->
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-muted">(4.5) • 127 reviews</span>
                </div>
                
                <!-- Price -->
                <div class="price-section mb-4">
                    <?php if ($productData['sale_price']): ?>
                        <div class="d-flex align-items-center gap-3">
                            <span class="h3 text-primary mb-0"><?php echo formatPrice($productData['sale_price']); ?></span>
                            <span class="h5 text-muted text-decoration-line-through mb-0"><?php echo formatPrice($productData['price']); ?></span>
                            <span class="badge bg-success">
                                <?php echo round((($productData['price'] - $productData['sale_price']) / $productData['price']) * 100); ?>% OFF
                            </span>
                        </div>
                    <?php else: ?>
                        <span class="h3 text-primary"><?php echo formatPrice($productData['price']); ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Stock Status -->
                <div class="stock-status mb-4">
                    <?php if ($productData['stock_quantity'] > 0): ?>
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>In Stock (<?php echo $productData['stock_quantity']; ?> available)
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger">
                            <i class="fas fa-times me-1"></i>Out of Stock
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Short Description -->
                <p class="text-muted mb-4"><?php echo htmlspecialchars($productData['short_description']); ?></p>
                
                <!-- Bargaining Section -->
                <?php if ($productData['allow_bargaining'] && isset($_SESSION['user_id'])): ?>
                    <div class="bargaining-section mb-4 p-3 bg-light rounded-3">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-handshake me-2"></i>Price Negotiation Available
                        </h6>
                        
                        <?php if ($userBargain): ?>
                            <!-- Existing Bargain Status -->
                            <div class="alert alert-info mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Your Current Offer: <?php echo formatPrice($userBargain['current_price']); ?></strong>
                                        <br>
                                        <small>Status: 
                                            <span class="badge bg-<?php echo $userBargain['status'] === 'pending' ? 'warning' : 'info'; ?>">
                                                <?php echo ucfirst($userBargain['status']); ?>
                                            </span>
                                        </small>
                                    </div>
                                    <a href="bargain-chat.php?id=<?php echo $userBargain['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-comments me-1"></i>View Chat
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- New Bargain Form -->
                            <p class="small text-muted mb-3">
                                Minimum discount: <?php echo $productData['bargain_min_percentage']; ?>% • 
                                You can negotiate up to <?php echo formatPrice(($productData['sale_price'] ?: $productData['price']) * (1 - $productData['bargain_min_percentage']/100)); ?>
                            </p>
                            <button class="btn btn-warning" onclick="openBargainModal(<?php echo $productData['id']; ?>, '<?php echo addslashes(htmlspecialchars($productData['name'])); ?>', <?php echo $productData['sale_price'] ?: $productData['price']; ?>)">
                                <i class="fas fa-handshake me-2"></i>Start Negotiation
                            </button>
                        <?php endif; ?>
                    </div>
                <?php elseif ($productData['allow_bargaining'] && !isset($_SESSION['user_id'])): ?>
                    <div class="bargaining-section mb-4 p-3 bg-light rounded-3">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-handshake me-2"></i>Price Negotiation Available
                        </h6>
                        <p class="small text-muted mb-2">Login to negotiate the price for this product</p>
                        <a href="login.php" class="btn btn-warning">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Negotiate
                        </a>
                    </div>
                <?php endif; ?>
                
                <!-- Quantity and Actions -->
                <div class="product-actions">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?php echo $productData['stock_quantity']; ?>">
                                <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <?php if ($productData['stock_quantity'] > 0): ?>
                                    <button class="btn btn-primary flex-fill" onclick="addToCart(<?php echo $productData['id']; ?>, document.getElementById('quantity').value)">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                    <button class="btn btn-success" onclick="buyNow(<?php echo $productData['id']; ?>)">
                                        <i class="fas fa-bolt me-2"></i>Buy Now
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary flex-fill" disabled>
                                        <i class="fas fa-times me-2"></i>Out of Stock
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-outline-danger" onclick="addToWishlist(<?php echo $productData['id']; ?>)">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Features -->
                <div class="product-features mt-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-shipping-fast text-primary mb-2 d-block"></i>
                            <small class="text-muted">Free Shipping</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo text-success mb-2 d-block"></i>
                            <small class="text-muted">30-Day Returns</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-shield-alt text-info mb-2 d-block"></i>
                            <small class="text-muted">Warranty</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Description Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-mdb-toggle="tab" data-mdb-target="#description" type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-mdb-toggle="tab" data-mdb-target="#specifications" type="button" role="tab">
                        Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-mdb-toggle="tab" data-mdb-target="#reviews" type="button" role="tab">
                        Reviews (127)
                    </button>
                </li>
            </ul>
            
            <div class="tab-content mt-3" id="productTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="p-4">
                        <p><?php echo nl2br(htmlspecialchars($productData['description'])); ?></p>
                    </div>
                </div>
                <div class="tab-pane fade" id="specifications" role="tabpanel">
                    <div class="p-4">
                        <table class="table">
                            <tr><td><strong>SKU</strong></td><td><?php echo htmlspecialchars($productData['sku']); ?></td></tr>
                            <tr><td><strong>Category</strong></td><td><?php echo htmlspecialchars($productData['category_name']); ?></td></tr>
                            <tr><td><strong>Stock</strong></td><td><?php echo $productData['stock_quantity']; ?> units</td></tr>
                            <tr><td><strong>Bargaining</strong></td><td><?php echo $productData['allow_bargaining'] ? 'Allowed' : 'Not Allowed'; ?></td></tr>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="p-4">
                        <div class="text-center text-muted">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <p>Reviews feature coming soon!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Related Products</h3>
            <div class="row">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card product-card h-100 shadow-sm">
                            <?php if ($relatedProduct['allow_bargaining']): ?>
                                <div class="bargain-badge">
                                    <i class="fas fa-handshake me-1"></i>Negotiable
                                </div>
                            <?php endif; ?>
                            
                            <div class="position-relative">
                                <?php 
                                $relatedImage = !empty($relatedProduct['images']) ? $relatedProduct['images'][0] : 'https://via.placeholder.com/300x250?text=No+Image';
                                ?>
                                <img src="<?php echo $relatedImage; ?>" class="product-image" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-2">
                                    <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($relatedProduct['name']); ?>
                                    </a>
                                </h6>
                                
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <?php if ($relatedProduct['sale_price']): ?>
                                                <span class="price-tag"><?php echo formatPrice($relatedProduct['sale_price']); ?></span>
                                                <span class="original-price ms-2"><?php echo formatPrice($relatedProduct['price']); ?></span>
                                            <?php else: ?>
                                                <span class="price-tag"><?php echo formatPrice($relatedProduct['price']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-sm flex-fill" onclick="addToCart(<?php echo $relatedProduct['id']; ?>)">
                                            <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="addToWishlist(<?php echo $relatedProduct['id']; ?>)">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

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

function changeMainImage(imageSrc, thumbnail) {
    document.getElementById('mainProductImage').src = imageSrc;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        img.classList.remove('active');
        img.style.borderColor = 'transparent';
    });
    
    thumbnail.classList.add('active');
    thumbnail.style.borderColor = 'var(--primary-color)';
}

function changeQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    const currentQuantity = parseInt(quantityInput.value);
    const newQuantity = currentQuantity + change;
    const maxQuantity = parseInt(quantityInput.max);
    
    if (newQuantity >= 1 && newQuantity <= maxQuantity) {
        quantityInput.value = newQuantity;
    }
}

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
            
            // Reload page to show bargain status
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to send bargain offer', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

function buyNow(productId) {
    const quantity = document.getElementById('quantity').value;
    
    // Add to cart first, then redirect to checkout
    addToCart(productId, quantity);
    
    setTimeout(() => {
        window.location.href = 'checkout.php';
    }, 1000);
}
</script>
";

include 'includes/footer.php';
?>
