<!-- Bargain Modal -->
<div class="modal fade" id="bargainModal" tabindex="-1" aria-labelledby="bargainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bargainModalLabel">
                    <i class="fas fa-handshake text-primary me-2"></i>Start Negotiation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bargainForm" onsubmit="event.preventDefault(); submitBargain();">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="product_id" id="bargainProductId">
                    
                    <!-- Product Info -->
                    <div class="card mb-3">
                        <div class="card-body" id="bargainProductInfo">
                            <!-- Product details will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Offer Price -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="offeredPrice" class="form-label">Your Offer Price ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="offeredPrice" name="offered_price" 
                                       step="0.01" min="0.01" required onchange="updateDiscountPercentage()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discount</label>
                            <div class="form-control-plaintext" id="discountPercentage">
                                <!-- Discount percentage will be calculated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message -->
                    <div class="mb-3">
                        <label for="bargainMessage" class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="bargainMessage" name="message" rows="3" 
                                  placeholder="Tell us why you think this price is fair..."></textarea>
                        <div class="form-text">
                            A good message explaining your offer can help improve your chances of acceptance.
                        </div>
                    </div>
                    
                    <!-- Bargaining Tips -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Bargaining Tips:</h6>
                        <ul class="mb-0">
                            <li>Be reasonable with your offer - extreme lowballs are usually rejected</li>
                            <li>Explain why you think the price is fair</li>
                            <li>Consider the product's condition and market value</li>
                            <li>Be patient - sellers need time to consider your offer</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-handshake me-1"></i>Submit Offer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Bargain Buttons (for common discounts) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add quick discount buttons
    const offeredPriceInput = document.getElementById('offeredPrice');
    if (offeredPriceInput) {
        offeredPriceInput.addEventListener('input', updateDiscountPercentage);
        
        // Add quick discount buttons after the input
        const quickDiscounts = document.createElement('div');
        quickDiscounts.className = 'mt-2';
        quickDiscounts.innerHTML = `
            <small class="text-muted">Quick discounts:</small>
            <div class="btn-group btn-group-sm mt-1" role="group">
                <button type="button" class="btn btn-outline-secondary" onclick="setDiscount(10)">10%</button>
                <button type="button" class="btn btn-outline-secondary" onclick="setDiscount(15)">15%</button>
                <button type="button" class="btn btn-outline-secondary" onclick="setDiscount(20)">20%</button>
                <button type="button" class="btn btn-outline-secondary" onclick="setDiscount(25)">25%</button>
            </div>
        `;
        offeredPriceInput.parentNode.parentNode.appendChild(quickDiscounts);
    }
});

function setDiscount(percentage) {
    if (currentProductPrice > 0) {
        const discountedPrice = (currentProductPrice * (100 - percentage) / 100).toFixed(2);
        document.getElementById('offeredPrice').value = discountedPrice;
        updateDiscountPercentage();
    }
}
</script>
