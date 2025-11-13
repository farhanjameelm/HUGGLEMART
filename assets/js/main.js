// Main JavaScript file for HugglingMart
// Handles all button interactions and AJAX calls

// Helper function to get correct API URL
function getApiUrl(endpoint) {
    // Check if we're in admin folder
    const isAdmin = window.location.pathname.includes('/admin/');
    const baseUrl = isAdmin ? '../api/' : 'api/';
    return baseUrl + endpoint;
}

// Helper function to get correct base URL
function getBaseUrl() {
    const isAdmin = window.location.pathname.includes('/admin/');
    return isAdmin ? '../' : '';
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeComponents();
    updateCounts();
    
    // Initialize AI bargain bot if on admin page
    if (window.location.pathname.includes('/admin/')) {
        initializeAIBargainBot();
    }
    
    // Update counts every 30 seconds
    setInterval(updateCounts, 30000);
});

// Initialize all JavaScript components
function initializeComponents() {
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize MDB components if available
    if (typeof mdb !== 'undefined') {
        // Auto-initialize MDB components
        mdb.Ripple.init();
    }
}

// ============ CART FUNCTIONS ============

// Add to cart function
function addToCart(productId, quantity = 1) {
    if (!productId) {
        showToast('Invalid product ID', 'danger');
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch(getApiUrl('add-to-cart.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added to cart!', 'success');
            updateCounts();
        } else {
            showToast(data.message || 'Failed to add to cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while adding to cart', 'danger');
    });
}

// Buy now function
function buyNow(productId) {
    if (!productId) {
        showToast('Invalid product ID', 'danger');
        return;
    }
    
    // Add to cart first, then redirect to checkout
    addToCart(productId);
    
    // Wait a moment for the cart to update, then redirect
    setTimeout(() => {
        window.location.href = getBaseUrl() + 'checkout.php';
    }, 1000);
}

// Update cart quantity
function updateCartQuantity(productId, quantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('action', 'update');

    fetch(getApiUrl('cart.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to show updated cart
        } else {
            showToast(data.message || 'Failed to update cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

// Remove from cart
function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', 'remove');

    fetch(getApiUrl('cart.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Item removed from cart', 'success');
            location.reload(); // Refresh to show updated cart
        } else {
            showToast(data.message || 'Failed to remove item', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

// ============ WISHLIST FUNCTIONS ============

// Add to wishlist function
function addToWishlist(productId) {
    if (!productId) {
        showToast('Invalid product ID', 'danger');
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', 'add');

    fetch(getApiUrl('wishlist.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added to wishlist!', 'success');
            // Update wishlist icon if it exists
            const wishlistBtn = document.querySelector(`[onclick="addToWishlist(${productId})"]`);
            if (wishlistBtn) {
                wishlistBtn.innerHTML = '<i class="fas fa-heart text-danger"></i>';
                wishlistBtn.setAttribute('onclick', `removeFromWishlist(${productId})`);
                wishlistBtn.setAttribute('title', 'Remove from Wishlist');
            }
        } else {
            showToast(data.message || 'Failed to add to wishlist', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

// Remove from wishlist
function removeFromWishlist(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', 'remove');

    fetch(getApiUrl('wishlist.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product removed from wishlist', 'info');
            // Update wishlist icon if it exists
            const wishlistBtn = document.querySelector(`[onclick="removeFromWishlist(${productId})"]`);
            if (wishlistBtn) {
                wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
                wishlistBtn.setAttribute('onclick', `addToWishlist(${productId})`);
                wishlistBtn.setAttribute('title', 'Add to Wishlist');
            }
        } else {
            showToast(data.message || 'Failed to remove from wishlist', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

// ============ BARGAINING FUNCTIONS ============

let currentProductPrice = 0;

// Open bargain modal
function openBargainModal(productId, productName, price) {
    currentProductPrice = price;
    
    // Check if user is logged in
    if (!isUserLoggedIn()) {
        showToast('Please login to start negotiating', 'warning');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
        return;
    }
    
    const modal = document.getElementById('bargainModal');
    if (!modal) {
        showToast('Bargain modal not found', 'danger');
        return;
    }
    
    document.getElementById('bargainProductId').value = productId;
    document.getElementById('bargainProductInfo').innerHTML = `
        <div class='d-flex align-items-center'>
            <div>
                <h6 class='mb-1'>${productName}</h6>
                <p class='text-muted mb-0'>Original Price: <strong>$${price.toFixed(2)}</strong></p>
            </div>
        </div>
    `;
    
    // Set suggested price (20% discount)
    const suggestedPrice = (price * 0.8).toFixed(2);
    document.getElementById('offeredPrice').value = suggestedPrice;
    updateDiscountPercentage();
    
    // Show modal
    if (typeof bootstrap !== 'undefined') {
        const bargainModal = new bootstrap.Modal(modal);
        bargainModal.show();
    } else if (typeof mdb !== 'undefined') {
        const bargainModal = new mdb.Modal(modal);
        bargainModal.show();
    } else {
        modal.style.display = 'block';
        modal.classList.add('show');
    }
}

// Update discount percentage
function updateDiscountPercentage() {
    const offeredPrice = parseFloat(document.getElementById('offeredPrice').value);
    const discountElement = document.getElementById('discountPercentage');
    
    if (!discountElement) return;
    
    if (offeredPrice && currentProductPrice) {
        const discount = ((currentProductPrice - offeredPrice) / currentProductPrice * 100).toFixed(1);
        
        if (discount > 0) {
            discountElement.innerHTML = `<i class='fas fa-arrow-down text-success'></i> ${discount}% discount`;
            discountElement.className = 'text-success';
        } else if (discount < 0) {
            discountElement.innerHTML = `<i class='fas fa-arrow-up text-danger'></i> ${Math.abs(discount)}% above original price`;
            discountElement.className = 'text-danger';
        } else {
            discountElement.innerHTML = 'Same as original price';
            discountElement.className = 'text-muted';
        }
    }
}

// Submit bargain
function submitBargain() {
    const form = document.getElementById('bargainForm');
    if (!form) return;
    
    const formData = new FormData(form);
    
    fetch(getApiUrl('bargains.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Bargain request submitted successfully!', 'success');
            // Close modal
            const modal = document.getElementById('bargainModal');
            if (modal) {
                if (typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getInstance(modal).hide();
                } else if (typeof mdb !== 'undefined') {
                    mdb.Modal.getInstance(modal).hide();
                } else {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                }
            }
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

// ============ ADMIN FUNCTIONS ============

// Export functions
function exportUsers() {
    window.location.href = 'export-users.php';
}

function exportOrders() {
    window.location.href = 'export-orders.php';
}

function exportProducts() {
    window.location.href = 'export-products.php';
}

function exportCarts() {
    window.location.href = 'export-carts.php';
}

// Admin user functions
function viewUser(userId) {
    window.location.href = `user-detail.php?id=${userId}`;
}

function editUser(userId) {
    window.location.href = `edit-user.php?id=${userId}`;
}

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type='hidden' name='csrf_token' value='${getCSRFToken()}'>
        <input type='hidden' name='action' value='delete_user'>
        <input type='hidden' name='user_id' value='${userId}'>
    `;
    document.body.appendChild(form);
    form.submit();
}

// Admin product functions
function viewProduct(productId) {
    window.location.href = `../product.php?id=${productId}`;
}

function editProduct(productId) {
    window.location.href = `edit-product.php?id=${productId}`;
}

function duplicateProduct(productId) {
    if (!confirm('Are you sure you want to duplicate this product?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type='hidden' name='csrf_token' value='${getCSRFToken()}'>
        <input type='hidden' name='action' value='duplicate_product'>
        <input type='hidden' name='product_id' value='${productId}'>
    `;
    document.body.appendChild(form);
    form.submit();
}

function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type='hidden' name='csrf_token' value='${getCSRFToken()}'>
        <input type='hidden' name='action' value='delete_product'>
        <input type='hidden' name='product_id' value='${productId}'>
    `;
    document.body.appendChild(form);
    form.submit();
}

// ============ PAYMENT FUNCTIONS ============

// Process payment
function processPayment(orderId, paymentMethod, amount, paymentData = {}) {
    const formData = new FormData();
    formData.append('action', 'process_payment');
    formData.append('order_id', orderId);
    formData.append('payment_method', paymentMethod);
    formData.append('amount', amount);
    
    // Add payment-specific data
    Object.keys(paymentData).forEach(key => {
        formData.append(key, paymentData[key]);
    });
    
    return fetch(getApiUrl('payment.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Payment processed successfully!', 'success');
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = getBaseUrl() + data.redirect;
                }, 1500);
            }
        } else {
            showToast(data.message || 'Payment failed', 'danger');
        }
        return data;
    })
    .catch(error => {
        console.error('Payment Error:', error);
        showToast('Payment processing error occurred', 'danger');
        throw error;
    });
}

// Validate credit card
function validateCreditCard(cardNumber, expiryDate, cvv) {
    const formData = new FormData();
    formData.append('action', 'validate_card');
    formData.append('card_number', cardNumber);
    formData.append('expiry_date', expiryDate);
    formData.append('cvv', cvv);
    
    return fetch(getApiUrl('payment.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json());
}

// Real-time card validation
function validateCardInput(cardNumber) {
    // Remove spaces and non-digits
    cardNumber = cardNumber.replace(/\D/g, '');
    
    // Basic length check
    if (cardNumber.length < 13 || cardNumber.length > 19) {
        return { valid: false, type: 'unknown' };
    }
    
    // Determine card type
    let cardType = 'unknown';
    if (/^4/.test(cardNumber)) {
        cardType = 'visa';
    } else if (/^5[1-5]/.test(cardNumber)) {
        cardType = 'mastercard';
    } else if (/^3[47]/.test(cardNumber)) {
        cardType = 'amex';
    } else if (/^6(?:011|5)/.test(cardNumber)) {
        cardType = 'discover';
    }
    
    // Luhn algorithm validation
    let sum = 0;
    let alternate = false;
    
    for (let i = cardNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(cardNumber.charAt(i), 10);
        
        if (alternate) {
            digit *= 2;
            if (digit > 9) {
                digit = (digit % 10) + 1;
            }
        }
        
        sum += digit;
        alternate = !alternate;
    }
    
    return {
        valid: (sum % 10) === 0,
        type: cardType
    };
}

// Format card number with spaces
function formatCardNumber(cardNumber) {
    // Remove all non-digits
    cardNumber = cardNumber.replace(/\D/g, '');
    
    // Add spaces every 4 digits
    return cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');
}

// Format expiry date
function formatExpiryDate(expiry) {
    // Remove all non-digits
    expiry = expiry.replace(/\D/g, '');
    
    // Add slash after 2 digits
    if (expiry.length >= 2) {
        expiry = expiry.substring(0, 2) + '/' + expiry.substring(2, 4);
    }
    
    return expiry;
}

// Save payment method
function savePaymentMethod(paymentMethod, cardData = {}) {
    const formData = new FormData();
    formData.append('action', 'save_payment_method');
    formData.append('payment_method', paymentMethod);
    
    Object.keys(cardData).forEach(key => {
        formData.append(`card_data[${key}]`, cardData[key]);
    });
    
    return fetch(getApiUrl('payment.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Payment method saved successfully!', 'success');
        } else {
            showToast(data.message || 'Failed to save payment method', 'danger');
        }
        return data;
    })
    .catch(error => {
        console.error('Save Payment Method Error:', error);
        showToast('An error occurred while saving payment method', 'danger');
        throw error;
    });
}

// Load saved payment methods
function loadSavedPaymentMethods() {
    const formData = new FormData();
    formData.append('action', 'get_saved_methods');
    
    return fetch(getApiUrl('payment.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            return data.methods;
        } else {
            console.error('Failed to load saved payment methods');
            return [];
        }
    })
    .catch(error => {
        console.error('Load Payment Methods Error:', error);
        return [];
    });
}

// Initialize payment form
function initializePaymentForm() {
    // Card number formatting and validation
    const cardNumberInput = document.getElementById('card_number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            this.value = formatCardNumber(value);
            
            // Real-time validation
            const validation = validateCardInput(value);
            const cardIcon = document.getElementById('card_icon');
            
            if (cardIcon) {
                cardIcon.className = `fab fa-cc-${validation.type}`;
                cardIcon.style.color = validation.valid ? '#28a745' : '#dc3545';
            }
            
            // Update input styling
            this.classList.toggle('is-valid', validation.valid && value.length >= 13);
            this.classList.toggle('is-invalid', !validation.valid && value.length >= 13);
        });
    }
    
    // Expiry date formatting
    const expiryInput = document.getElementById('expiry_date');
    if (expiryInput) {
        expiryInput.addEventListener('input', function() {
            this.value = formatExpiryDate(this.value);
            
            // Validate expiry date
            const isValid = /^(0[1-9]|1[0-2])\/\d{2}$/.test(this.value);
            this.classList.toggle('is-valid', isValid);
            this.classList.toggle('is-invalid', !isValid && this.value.length >= 5);
        });
    }
    
    // CVV validation
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function() {
            const isValid = /^\d{3,4}$/.test(this.value);
            this.classList.toggle('is-valid', isValid);
            this.classList.toggle('is-invalid', !isValid && this.value.length >= 3);
        });
    }
    
    // Payment method change handler
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            const cardDetails = document.getElementById('card_details');
            const paypalDetails = document.getElementById('paypal_details');
            const codDetails = document.getElementById('cod_details');
            
            // Hide all payment details
            if (cardDetails) cardDetails.style.display = 'none';
            if (paypalDetails) paypalDetails.style.display = 'none';
            if (codDetails) codDetails.style.display = 'none';
            
            // Show relevant payment details
            switch (this.value) {
                case 'credit_card':
                    if (cardDetails) cardDetails.style.display = 'block';
                    break;
                case 'paypal':
                    if (paypalDetails) paypalDetails.style.display = 'block';
                    break;
                case 'cash_on_delivery':
                    if (codDetails) codDetails.style.display = 'block';
                    break;
            }
        });
    });
}

// ============ AI BARGAIN BOT FUNCTIONS ============

// Process bargain with AI
function processAIBargain(bargainId, useAI = true) {
    const formData = new FormData();
    formData.append('action', 'process_bargain');
    formData.append('bargain_id', bargainId);
    formData.append('use_ai', useAI);
    
    return fetch(getApiUrl('ai-bargain-bot.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayAIDecision(data);
            return data;
        } else {
            showToast(data.message || 'AI processing failed', 'danger');
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('AI Bargain Error:', error);
        showToast('AI bargain processing error', 'danger');
        throw error;
    });
}

// Get AI suggestion for bargain
function getAISuggestion(bargainId) {
    const formData = new FormData();
    formData.append('action', 'get_ai_suggestion');
    formData.append('bargain_id', bargainId);
    
    return fetch(getApiUrl('ai-bargain-bot.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            return data.suggestion;
        } else {
            throw new Error(data.message);
        }
    });
}

// Display AI decision in UI
function displayAIDecision(aiData) {
    const modal = document.getElementById('aiDecisionModal');
    if (!modal) {
        createAIDecisionModal();
        return displayAIDecision(aiData);
    }
    
    // Update modal content
    document.getElementById('aiDecision').textContent = aiData.ai_decision.toUpperCase();
    document.getElementById('aiConfidence').textContent = Math.round(aiData.confidence * 100) + '%';
    document.getElementById('aiReasoning').textContent = aiData.reasoning;
    document.getElementById('aiResponse').textContent = aiData.suggested_response;
    
    if (aiData.counter_offer) {
        document.getElementById('aiCounterOffer').textContent = '$' + parseFloat(aiData.counter_offer).toFixed(2);
        document.getElementById('counterOfferSection').style.display = 'block';
    } else {
        document.getElementById('counterOfferSection').style.display = 'none';
    }
    
    // Set decision color
    const decisionElement = document.getElementById('aiDecision');
    const colors = {
        'ACCEPT': 'text-success',
        'COUNTER': 'text-warning', 
        'REJECT': 'text-danger'
    };
    decisionElement.className = colors[aiData.ai_decision.toUpperCase()] || 'text-info';
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

// Create AI decision modal
function createAIDecisionModal() {
    const modalHTML = `
        <div class="modal fade" id="aiDecisionModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-robot text-primary me-2"></i>AI Bargain Analysis
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h6>AI Decision</h6>
                                        <h3 id="aiDecision" class="mb-0">-</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h6>Confidence Level</h6>
                                        <h3 id="aiConfidence" class="mb-0 text-info">-</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="counterOfferSection" class="alert alert-warning" style="display: none;">
                            <h6><i class="fas fa-handshake me-2"></i>Suggested Counter Offer</h6>
                            <h4 id="aiCounterOffer" class="mb-0">-</h4>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-brain me-2"></i>AI Reasoning</h6>
                            <p id="aiReasoning" class="text-muted">-</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="fas fa-comment me-2"></i>Suggested Response</h6>
                            <div class="bg-light p-3 rounded">
                                <p id="aiResponse" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                        <button type="button" class="btn btn-success" onclick="acceptAIDecision()">
                            <i class="fas fa-check me-2"></i>Accept AI Decision
                        </button>
                        <button type="button" class="btn btn-primary" onclick="customizeResponse()">
                            <i class="fas fa-edit me-2"></i>Customize Response
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Accept AI decision
function acceptAIDecision() {
    const decision = document.getElementById('aiDecision').textContent.toLowerCase();
    const response = document.getElementById('aiResponse').textContent;
    const counterOffer = document.getElementById('aiCounterOffer').textContent.replace('$', '');
    
    // Process the decision (you can integrate with existing bargain response system)
    showToast('AI decision accepted and response sent!', 'success');
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('aiDecisionModal'));
    modal.hide();
}

// Customize AI response
function customizeResponse() {
    const currentResponse = document.getElementById('aiResponse').textContent;
    
    const customModal = `
        <div class="modal fade" id="customResponseModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Customize Response</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customResponseText" class="form-label">Response Message</label>
                            <textarea class="form-control" id="customResponseText" rows="4">${currentResponse}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quick Templates</label>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="useTemplate('polite')">
                                    Polite Decline
                                </button>
                                <button class="btn btn-outline-success btn-sm" onclick="useTemplate('enthusiastic')">
                                    Enthusiastic Accept
                                </button>
                                <button class="btn btn-outline-warning btn-sm" onclick="useTemplate('negotiation')">
                                    Open to Negotiation
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="sendCustomResponse()">
                            <i class="fas fa-paper-plane me-2"></i>Send Response
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', customModal);
    const modal = new bootstrap.Modal(document.getElementById('customResponseModal'));
    modal.show();
}

// Use response template
function useTemplate(type) {
    const templates = {
        'polite': "Thank you for your interest in our product. While we appreciate your offer, we're unable to accommodate this price point at this time. We'd be happy to notify you of any future promotions!",
        'enthusiastic': "Fantastic! We're thrilled to accept your offer. Thank you for choosing our product - we're confident you'll love it!",
        'negotiation': "We appreciate your offer and would like to work with you. How about we meet somewhere in the middle? We're open to finding a price that works for both of us."
    };
    
    document.getElementById('customResponseText').value = templates[type];
}

// Send custom response
function sendCustomResponse() {
    const customText = document.getElementById('customResponseText').value;
    
    if (!customText.trim()) {
        showToast('Please enter a response message', 'warning');
        return;
    }
    
    // Process custom response (integrate with existing system)
    showToast('Custom response sent successfully!', 'success');
    
    // Close modals
    const customModal = bootstrap.Modal.getInstance(document.getElementById('customResponseModal'));
    const aiModal = bootstrap.Modal.getInstance(document.getElementById('aiDecisionModal'));
    
    customModal.hide();
    aiModal.hide();
}

// Initialize AI bargain bot
function initializeAIBargainBot() {
    // Add AI buttons to existing bargain interface
    const bargainCards = document.querySelectorAll('.bargain-card');
    
    bargainCards.forEach(card => {
        const bargainId = card.dataset.bargainId;
        if (bargainId) {
            addAIButton(card, bargainId);
        }
    });
}

// Add AI button to bargain card
function addAIButton(card, bargainId) {
    const existingActions = card.querySelector('.bargain-actions');
    if (existingActions && !existingActions.querySelector('.ai-analyze-btn')) {
        const aiButton = document.createElement('button');
        aiButton.className = 'btn btn-info btn-sm ai-analyze-btn me-2';
        aiButton.innerHTML = '<i class="fas fa-robot me-1"></i>AI Analyze';
        aiButton.onclick = () => processAIBargain(bargainId);
        
        existingActions.insertBefore(aiButton, existingActions.firstChild);
    }
}

// Get AI analytics
function getAIAnalytics() {
    const formData = new FormData();
    formData.append('action', 'get_ai_analytics');
    
    return fetch(getApiUrl('ai-bargain-bot.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayAIAnalytics(data.analytics);
            return data.analytics;
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('AI Analytics Error:', error);
        showToast('Error loading AI analytics', 'danger');
    });
}

// Display AI analytics
function displayAIAnalytics(analytics) {
    const analyticsHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary">${analytics.total_processed}</h3>
                        <p class="mb-0">Total Processed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success">${analytics.acceptance_rate}%</h3>
                        <p class="mb-0">Acceptance Rate</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning">${analytics.avg_discount_given}%</h3>
                        <p class="mb-0">Avg Discount</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info">${analytics.customer_satisfaction}%</h3>
                        <p class="mb-0">Satisfaction</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const analyticsContainer = document.getElementById('aiAnalyticsContainer');
    if (analyticsContainer) {
        analyticsContainer.innerHTML = analyticsHTML;
    }
}

// ============ UTILITY FUNCTIONS ============

// Update cart and bargain counts
function updateCounts() {
    if (!isUserLoggedIn()) return;
    
    fetch(getApiUrl('get-counts.php'))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCount = document.getElementById('cartCount');
                const bargainCount = document.getElementById('bargainCount');
                
                if (cartCount) cartCount.textContent = data.cart_count || 0;
                if (bargainCount) bargainCount.textContent = data.active_bargains || 0;
            }
        })
        .catch(error => console.error('Error updating counts:', error));
}

// Check if user is logged in
function isUserLoggedIn() {
    // This should be set by PHP in the header
    return typeof window.userLoggedIn !== 'undefined' && window.userLoggedIn;
}

// Get CSRF token
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
}

// Toast notification function
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toastElement = document.createElement('div');
    toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
    toastElement.setAttribute('role', 'alert');
    toastElement.setAttribute('aria-live', 'assertive');
    toastElement.setAttribute('aria-atomic', 'true');
    
    toastElement.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toastElement);
    
    // Initialize and show toast
    if (typeof bootstrap !== 'undefined') {
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    } else if (typeof mdb !== 'undefined') {
        const toast = new mdb.Toast(toastElement);
        toast.show();
    } else {
        // Fallback for when Bootstrap/MDB is not available
        toastElement.style.display = 'block';
        setTimeout(() => {
            toastElement.remove();
        }, 5000);
    }
    
    // Remove toast after it's hidden
    toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
    });
}

// Create toast container if it doesn't exist
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Form submission helpers
function submitForm(formId, successMessage = 'Operation completed successfully!') {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const formData = new FormData(form);
    
    fetch(form.action || window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(successMessage, 'success');
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        } else {
            showToast(data.message || 'Operation failed', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred', 'danger');
    });
}

// Initialize quantity controls
function initQuantityControls() {
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentNode.querySelector('.quantity-input');
            const isIncrement = this.classList.contains('increment');
            let value = parseInt(input.value) || 0;
            
            if (isIncrement) {
                value++;
            } else {
                value = Math.max(1, value - 1);
            }
            
            input.value = value;
            
            // Trigger change event if needed
            if (input.dataset.productId) {
                updateCartQuantity(input.dataset.productId, value);
            }
        });
    });
}
