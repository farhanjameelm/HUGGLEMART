    <!-- Footer -->
    <footer class="bg-dark text-white mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-store me-2"></i>HugglingMart
                    </h5>
                    <p class="text-muted">
                        Your ultimate shopping destination where you can negotiate prices and get the best deals. 
                        Shop smart, bargain better!
                    </p>
                    <div class="d-flex">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-muted text-decoration-none">Contact</a></li>
                        <li><a href="faq.php" class="text-muted text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="category.php?slug=electronics" class="text-muted text-decoration-none">Electronics</a></li>
                        <li><a href="category.php?slug=fashion" class="text-muted text-decoration-none">Fashion</a></li>
                        <li><a href="category.php?slug=home-garden" class="text-muted text-decoration-none">Home & Garden</a></li>
                        <li><a href="category.php?slug=sports-outdoors" class="text-muted text-decoration-none">Sports</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li><a href="shipping.php" class="text-muted text-decoration-none">Shipping Info</a></li>
                        <li><a href="returns.php" class="text-muted text-decoration-none">Returns</a></li>
                        <li><a href="bargaining-guide.php" class="text-muted text-decoration-none">Bargaining Guide</a></li>
                        <li><a href="support.php" class="text-muted text-decoration-none">Support</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Newsletter</h6>
                    <p class="text-muted small">Subscribe to get special offers and updates.</p>
                    <form class="d-flex">
                        <input type="email" class="form-control form-control-sm me-2" placeholder="Your email">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> HugglingMart. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="privacy.php" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="terms.php" class="text-muted text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- MDB JavaScript -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.js"></script>
    <!-- Main JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Update cart and bargain counts
        function updateCounts() {
            <?php if (isset($_SESSION['user_id'])): ?>
                fetch('api/get-counts.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('cartCount').textContent = data.cart_count || 0;
                            document.getElementById('bargainCount').textContent = data.active_bargains || 0;
                        }
                    })
                    .catch(error => console.error('Error updating counts:', error));
            <?php endif; ?>
        }

        // Initialize counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCounts();
            
            // Update counts every 30 seconds
            setInterval(updateCounts, 30000);
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            
            const toastElement = document.createElement('div');
            toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
            toastElement.setAttribute('role', 'alert');
            toastElement.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-mdb-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toastElement);
            
            const toast = new mdb.Toast(toastElement);
            toast.show();
            
            // Remove toast element after it's hidden
            toastElement.addEventListener('hidden.mdb.toast', function() {
                toastElement.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }

        // Note: addToCart function is now in main.js to avoid conflicts

        // Add to wishlist function
        function addToWishlist(productId) {
            const formData = new FormData();
            formData.append('product_id', productId);

            fetch('api/add-to-wishlist.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Product added to wishlist!', 'success');
                } else {
                    showToast(data.message || 'Failed to add to wishlist', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred', 'danger');
            });
        }
    </script>
    
    <?php if (isset($additionalJS)): ?>
        <?php echo $additionalJS; ?>
    <?php endif; ?>
</body>
</html>
