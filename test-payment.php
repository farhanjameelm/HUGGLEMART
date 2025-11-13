<?php
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = 'Payment Method Test';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-5">
        <i class="fas fa-credit-card text-primary me-2"></i>
        Payment Method Testing
    </h1>
    
    <!-- Test Credit Card Numbers -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Test Credit Card Numbers
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Valid Test Cards:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Card Type</th>
                                            <th>Number</th>
                                            <th>CVV</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="fab fa-cc-visa text-primary"></i> Visa</td>
                                            <td><code>4242 4242 4242 4242</code></td>
                                            <td><code>123</code></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fab fa-cc-mastercard text-warning"></i> MasterCard</td>
                                            <td><code>5555 5555 5555 4444</code></td>
                                            <td><code>123</code></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fab fa-cc-amex text-info"></i> American Express</td>
                                            <td><code>3782 822463 10005</code></td>
                                            <td><code>1234</code></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fab fa-cc-discover text-success"></i> Discover</td>
                                            <td><code>6011 1111 1111 1117</code></td>
                                            <td><code>123</code></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Test Details:</h6>
                            <ul>
                                <li><strong>Expiry Date:</strong> Any future date (e.g., 12/25)</li>
                                <li><strong>Name on Card:</strong> Any name</li>
                                <li><strong>Billing Address:</strong> Any valid address</li>
                            </ul>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> These are test card numbers for development. 
                                No real charges will be made.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Method Test Form -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-test-tube me-2"></i>Test Payment Methods
                    </h5>
                </div>
                <div class="card-body">
                    <form id="paymentTestForm">
                        <!-- Payment Method Selection -->
                        <div class="mb-4">
                            <label class="form-label">Select Payment Method:</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="test_credit_card" value="credit_card" checked>
                                        <label class="form-check-label" for="test_credit_card">
                                            <i class="fas fa-credit-card me-2"></i>Credit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="test_paypal" value="paypal">
                                        <label class="form-check-label" for="test_paypal">
                                            <i class="fab fa-paypal me-2"></i>PayPal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="test_cod" value="cash_on_delivery">
                                        <label class="form-check-label" for="test_cod">
                                            <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Test Amount -->
                        <div class="mb-3">
                            <label for="test_amount" class="form-label">Test Amount ($)</label>
                            <input type="number" class="form-control" id="test_amount" value="99.99" step="0.01" min="0.01">
                        </div>
                        
                        <!-- Credit Card Details -->
                        <div id="test_card_details">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="test_card_number" class="form-label">Card Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="test_card_number" 
                                               placeholder="4242 4242 4242 4242" maxlength="23">
                                        <span class="input-group-text">
                                            <i id="test_card_icon" class="fab fa-cc-visa"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="test_expiry_date" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="test_expiry_date" 
                                           placeholder="12/25" maxlength="5">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="test_cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="test_cvv" 
                                           placeholder="123" maxlength="4">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="test_card_name" class="form-label">Name on Card</label>
                                <input type="text" class="form-control" id="test_card_name" 
                                       placeholder="John Doe">
                            </div>
                        </div>
                        
                        <!-- Test Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-outline-secondary" onclick="fillTestData()">
                                <i class="fas fa-magic me-2"></i>Fill Test Data
                            </button>
                            <button type="button" class="btn btn-primary" onclick="testCardValidation()">
                                <i class="fas fa-check me-2"></i>Test Validation
                            </button>
                            <button type="button" class="btn btn-success" onclick="simulatePayment()">
                                <i class="fas fa-play me-2"></i>Simulate Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Results -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>Test Results
                    </h5>
                </div>
                <div class="card-body">
                    <div id="test_results" class="bg-dark text-light p-3 rounded" style="height: 300px; overflow-y: auto; font-family: monospace;">
                        <div class="text-success">Payment testing console initialized...</div>
                    </div>
                    <button class="btn btn-secondary btn-sm mt-2" onclick="clearTestResults()">
                        <i class="fas fa-trash me-1"></i>Clear Results
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize payment form
    initializePaymentForm();
    
    // Set up test card number input
    const testCardNumber = document.getElementById('test_card_number');
    if (testCardNumber) {
        testCardNumber.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            this.value = formatCardNumber(value);
            
            const validation = validateCardInput(value);
            const cardIcon = document.getElementById('test_card_icon');
            
            if (cardIcon) {
                cardIcon.className = `fab fa-cc-${validation.type}`;
                cardIcon.style.color = validation.valid ? '#28a745' : '#dc3545';
            }
            
            this.classList.toggle('is-valid', validation.valid && value.length >= 13);
            this.classList.toggle('is-invalid', !validation.valid && value.length >= 13);
        });
    }
    
    // Set up expiry date input
    const testExpiryDate = document.getElementById('test_expiry_date');
    if (testExpiryDate) {
        testExpiryDate.addEventListener('input', function() {
            this.value = formatExpiryDate(this.value);
        });
    }
});

// Fill test data
function fillTestData() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    
    if (paymentMethod === 'credit_card') {
        document.getElementById('test_card_number').value = '4242 4242 4242 4242';
        document.getElementById('test_expiry_date').value = '12/25';
        document.getElementById('test_cvv').value = '123';
        document.getElementById('test_card_name').value = 'John Doe';
        
        // Trigger validation
        document.getElementById('test_card_number').dispatchEvent(new Event('input'));
    }
    
    logTestResult('Test data filled successfully', 'success');
}

// Test card validation
function testCardValidation() {
    const cardNumber = document.getElementById('test_card_number').value.replace(/\s/g, '');
    const expiryDate = document.getElementById('test_expiry_date').value;
    const cvv = document.getElementById('test_cvv').value;
    
    logTestResult('Testing card validation...', 'info');
    
    validateCreditCard(cardNumber, expiryDate, cvv)
        .then(result => {
            if (result.success) {
                logTestResult(`✅ Card validation passed - Card Type: ${result.card_type}`, 'success');
            } else {
                logTestResult(`❌ Card validation failed: ${result.errors.join(', ')}`, 'error');
            }
        })
        .catch(error => {
            logTestResult(`❌ Validation error: ${error.message}`, 'error');
        });
}

// Simulate payment
function simulatePayment() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const amount = document.getElementById('test_amount').value;
    
    logTestResult(`Starting payment simulation...`, 'info');
    logTestResult(`Payment Method: ${paymentMethod}`, 'info');
    logTestResult(`Amount: $${amount}`, 'info');
    
    let paymentData = {};
    
    if (paymentMethod === 'credit_card') {
        paymentData = {
            card_number: document.getElementById('test_card_number').value.replace(/\s/g, ''),
            expiry_date: document.getElementById('test_expiry_date').value,
            cvv: document.getElementById('test_cvv').value,
            card_name: document.getElementById('test_card_name').value
        };
    }
    
    // Simulate API call
    const formData = new FormData();
    formData.append('action', 'process_payment');
    formData.append('order_id', '999'); // Test order ID
    formData.append('payment_method', paymentMethod);
    formData.append('amount', amount);
    
    Object.keys(paymentData).forEach(key => {
        formData.append(key, paymentData[key]);
    });
    
    fetch('api/payment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            logTestResult(`✅ Payment simulation successful!`, 'success');
            logTestResult(`Reference: ${data.reference}`, 'success');
            logTestResult(`Message: ${data.message}`, 'success');
        } else {
            logTestResult(`❌ Payment simulation failed: ${data.message}`, 'error');
        }
    })
    .catch(error => {
        logTestResult(`❌ Payment simulation error: ${error.message}`, 'error');
    });
}

// Log test results
function logTestResult(message, type = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const testResults = document.getElementById('test_results');
    
    const colors = {
        'info': '#17a2b8',
        'success': '#28a745',
        'error': '#dc3545',
        'warning': '#ffc107'
    };
    
    const resultDiv = document.createElement('div');
    resultDiv.style.color = colors[type] || colors.info;
    resultDiv.textContent = `[${timestamp}] ${message}`;
    
    testResults.appendChild(resultDiv);
    testResults.scrollTop = testResults.scrollHeight;
}

// Clear test results
function clearTestResults() {
    document.getElementById('test_results').innerHTML = '<div class="text-success">Payment testing console cleared...</div>';
}
</script>

<?php include 'includes/footer.php'; ?>
