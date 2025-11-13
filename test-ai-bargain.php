<?php
require_once 'config/config.php';
require_once 'config/database.php';

$pageTitle = 'AI Bargain Bot Test';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

include 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-5">
        <i class="fas fa-robot text-primary me-2"></i>
        AI Bargain Bot Testing
    </h1>
    
    <!-- AI Bot Overview -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>AI Bargain Bot Features
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>🤖 AI Decision Making</h6>
                            <ul>
                                <li><strong>Smart Analysis:</strong> Analyzes discount percentage, category, customer sentiment</li>
                                <li><strong>Market Conditions:</strong> Considers demand, competition, seasonality</li>
                                <li><strong>Customer History:</strong> Reviews past orders, loyalty score, bargain success rate</li>
                                <li><strong>Inventory Pressure:</strong> Factors in stock levels and turnover</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>⚡ Automated Responses</h6>
                            <ul>
                                <li><strong>Accept:</strong> Automatically accepts reasonable offers</li>
                                <li><strong>Counter:</strong> Calculates fair counter-offers</li>
                                <li><strong>Reject:</strong> Politely declines unreasonable requests</li>
                                <li><strong>Learning:</strong> Improves decisions based on outcomes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Test Scenarios -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-flask me-2"></i>Test AI Bargain Scenarios
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Scenario 1: Reasonable Offer -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Scenario 1: Reasonable Offer</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Product:</strong> Wireless Headphones<br>
                                        <strong>Original Price:</strong> $99.99<br>
                                        <strong>Customer Offer:</strong> $89.99<br>
                                        <strong>Discount:</strong> 10%<br>
                                        <strong>Message:</strong> "Please consider my offer, thank you!"
                                    </div>
                                    <button class="btn btn-success w-100" onclick="testScenario(1)">
                                        <i class="fas fa-play me-2"></i>Test AI Response
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Scenario 2: High Discount Request -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">Scenario 2: High Discount</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Product:</strong> Smart Watch<br>
                                        <strong>Original Price:</strong> $199.99<br>
                                        <strong>Customer Offer:</strong> $149.99<br>
                                        <strong>Discount:</strong> 25%<br>
                                        <strong>Message:</strong> "I found it cheaper elsewhere"
                                    </div>
                                    <button class="btn btn-warning w-100" onclick="testScenario(2)">
                                        <i class="fas fa-play me-2"></i>Test AI Response
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Scenario 3: Unreasonable Offer -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">Scenario 3: Unreasonable Offer</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Product:</strong> Yoga Mat<br>
                                        <strong>Original Price:</strong> $39.99<br>
                                        <strong>Customer Offer:</strong> $19.99<br>
                                        <strong>Discount:</strong> 50%<br>
                                        <strong>Message:</strong> "This is overpriced"
                                    </div>
                                    <button class="btn btn-danger w-100" onclick="testScenario(3)">
                                        <i class="fas fa-play me-2"></i>Test AI Response
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Custom Test Form -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Custom AI Test
                    </h5>
                </div>
                <div class="card-body">
                    <form id="customTestForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" value="Test Product">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category">
                                    <option value="electronics">Electronics</option>
                                    <option value="fashion">Fashion</option>
                                    <option value="home-garden">Home & Garden</option>
                                    <option value="sports-outdoors">Sports & Outdoors</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="originalPrice" class="form-label">Original Price ($)</label>
                                <input type="number" class="form-control" id="originalPrice" value="100.00" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="offeredPrice" class="form-label">Customer Offer ($)</label>
                                <input type="number" class="form-control" id="offeredPrice" value="85.00" step="0.01">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customerMessage" class="form-label">Customer Message</label>
                            <textarea class="form-control" id="customerMessage" rows="3" placeholder="Enter customer's bargaining message...">I really like this product. Can you consider my offer?</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customerType" class="form-label">Customer Type</label>
                                <select class="form-select" id="customerType">
                                    <option value="new">New Customer</option>
                                    <option value="returning">Returning Customer</option>
                                    <option value="loyal">Loyal Customer</option>
                                    <option value="vip">VIP Customer</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="urgency" class="form-label">Urgency Level</label>
                                <select class="form-select" id="urgency">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="button" class="btn btn-primary btn-lg" onclick="testCustomScenario()">
                                <i class="fas fa-robot me-2"></i>Analyze with AI
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- AI Response Display -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-brain me-2"></i>AI Analysis Results
                    </h5>
                </div>
                <div class="card-body">
                    <div id="aiResults" class="text-center py-5">
                        <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Run a test scenario to see AI analysis</h5>
                        <p class="text-muted">The AI will analyze the bargain request and provide a decision with reasoning.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Test predefined scenarios
function testScenario(scenarioNumber) {
    let testData = {};
    
    switch (scenarioNumber) {
        case 1:
            testData = {
                product_name: 'Wireless Headphones',
                category: 'electronics',
                original_price: 99.99,
                offered_price: 89.99,
                message: 'Please consider my offer, thank you!',
                customer_type: 'returning'
            };
            break;
        case 2:
            testData = {
                product_name: 'Smart Watch',
                category: 'electronics',
                original_price: 199.99,
                offered_price: 149.99,
                message: 'I found it cheaper elsewhere',
                customer_type: 'new'
            };
            break;
        case 3:
            testData = {
                product_name: 'Yoga Mat',
                category: 'sports-outdoors',
                original_price: 39.99,
                offered_price: 19.99,
                message: 'This is overpriced',
                customer_type: 'new'
            };
            break;
    }
    
    runAIAnalysis(testData);
}

// Test custom scenario
function testCustomScenario() {
    const testData = {
        product_name: document.getElementById('productName').value,
        category: document.getElementById('category').value,
        original_price: parseFloat(document.getElementById('originalPrice').value),
        offered_price: parseFloat(document.getElementById('offeredPrice').value),
        message: document.getElementById('customerMessage').value,
        customer_type: document.getElementById('customerType').value,
        urgency: document.getElementById('urgency').value
    };
    
    runAIAnalysis(testData);
}

// Run AI analysis
function runAIAnalysis(testData) {
    const resultsDiv = document.getElementById('aiResults');
    
    // Show loading state
    resultsDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Analyzing...</span>
            </div>
            <h5>AI is analyzing the bargain request...</h5>
            <p class="text-muted">Processing factors: price, sentiment, market conditions, customer profile</p>
        </div>
    `;
    
    // Simulate AI processing (in real implementation, this would call the AI API)
    setTimeout(() => {
        const discountPercent = ((testData.original_price - testData.offered_price) / testData.original_price) * 100;
        const aiDecision = simulateAIDecision(testData, discountPercent);
        
        displayAIResults(testData, aiDecision, discountPercent);
    }, 2000);
}

// Simulate AI decision (simplified version of the actual AI logic)
function simulateAIDecision(data, discountPercent) {
    let decision = 'counter';
    let confidence = 0.75;
    let reasoning = '';
    let counterOffer = null;
    let response = '';
    
    // Analyze sentiment
    const message = data.message.toLowerCase();
    const isPolite = message.includes('please') || message.includes('thank') || message.includes('consider');
    const isNegative = message.includes('overpriced') || message.includes('expensive') || message.includes('cheap');
    
    // Decision logic
    if (discountPercent <= 10 && isPolite) {
        decision = 'accept';
        confidence = 0.9;
        reasoning = 'Reasonable discount request from polite customer. Good for customer relations.';
        response = 'Thank you for your polite request! We\'re happy to accept your offer.';
    } else if (discountPercent <= 20) {
        decision = 'counter';
        confidence = 0.8;
        counterOffer = data.original_price - (data.original_price * 0.15); // 15% discount
        reasoning = 'Moderate discount request. Counter-offering with 15% discount to find middle ground.';
        response = `We appreciate your interest! How about we meet at $${counterOffer.toFixed(2)}?`;
    } else if (discountPercent > 30 || isNegative) {
        decision = 'reject';
        confidence = 0.85;
        reasoning = 'Excessive discount request or negative sentiment. Maintaining price integrity.';
        response = 'We understand your budget concerns, but we\'re unable to accommodate this price point.';
    }
    
    // Adjust for customer type
    if (data.customer_type === 'loyal' || data.customer_type === 'vip') {
        confidence += 0.1;
        if (decision === 'reject' && discountPercent <= 35) {
            decision = 'counter';
            counterOffer = data.original_price * 0.8; // 20% discount for loyal customers
            reasoning += ' Adjusted for loyal customer status.';
        }
    }
    
    return {
        decision,
        confidence: Math.min(0.95, confidence),
        reasoning,
        counterOffer,
        response,
        factors: {
            discountPercent,
            sentiment: isPolite ? 'positive' : (isNegative ? 'negative' : 'neutral'),
            customerType: data.customer_type,
            category: data.category
        }
    };
}

// Display AI results
function displayAIResults(testData, aiDecision, discountPercent) {
    const resultsDiv = document.getElementById('aiResults');
    
    const decisionColors = {
        'accept': 'success',
        'counter': 'warning',
        'reject': 'danger'
    };
    
    const decisionIcons = {
        'accept': 'fas fa-check-circle',
        'counter': 'fas fa-handshake',
        'reject': 'fas fa-times-circle'
    };
    
    const color = decisionColors[aiDecision.decision];
    const icon = decisionIcons[aiDecision.decision];
    
    resultsDiv.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <div class="card border-${color} h-100">
                    <div class="card-body text-center">
                        <i class="${icon} fa-3x text-${color} mb-3"></i>
                        <h4 class="text-${color}">${aiDecision.decision.toUpperCase()}</h4>
                        <p class="mb-0">Confidence: ${Math.round(aiDecision.confidence * 100)}%</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-body">
                        <h6>Analysis Details</h6>
                        <div class="mb-3">
                            <strong>Product:</strong> ${testData.product_name}<br>
                            <strong>Original Price:</strong> $${testData.original_price.toFixed(2)}<br>
                            <strong>Customer Offer:</strong> $${testData.offered_price.toFixed(2)}<br>
                            <strong>Discount Requested:</strong> ${discountPercent.toFixed(1)}%
                            ${aiDecision.counterOffer ? `<br><strong>AI Counter Offer:</strong> $${aiDecision.counterOffer.toFixed(2)}` : ''}
                        </div>
                        
                        <h6>AI Reasoning</h6>
                        <p class="text-muted">${aiDecision.reasoning}</p>
                        
                        <h6>Suggested Response</h6>
                        <div class="alert alert-light">
                            <i class="fas fa-comment me-2"></i>${aiDecision.response}
                        </div>
                        
                        <h6>Factors Analyzed</h6>
                        <div class="row">
                            <div class="col-6">
                                <small><strong>Sentiment:</strong> ${aiDecision.factors.sentiment}</small><br>
                                <small><strong>Customer Type:</strong> ${aiDecision.factors.customerType}</small>
                            </div>
                            <div class="col-6">
                                <small><strong>Category:</strong> ${aiDecision.factors.category}</small><br>
                                <small><strong>Discount Level:</strong> ${discountPercent.toFixed(1)}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate discount percentage
    const originalPrice = document.getElementById('originalPrice');
    const offeredPrice = document.getElementById('offeredPrice');
    
    function updateDiscount() {
        const original = parseFloat(originalPrice.value) || 0;
        const offered = parseFloat(offeredPrice.value) || 0;
        const discount = original > 0 ? ((original - offered) / original * 100) : 0;
        
        // You could display this somewhere if needed
        console.log(`Discount: ${discount.toFixed(1)}%`);
    }
    
    originalPrice.addEventListener('input', updateDiscount);
    offeredPrice.addEventListener('input', updateDiscount);
});
</script>

<?php include 'includes/footer.php'; ?>
