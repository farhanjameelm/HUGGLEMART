<?php
require_once 'config/config.php';

$pageTitle = 'Bargaining Guide - How to Negotiate Better Prices';

include 'includes/header.php';
?>

<div class="container my-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body text-center py-5">
                    <i class="fas fa-handshake fa-4x mb-4"></i>
                    <h1 class="display-4 mb-3">Master the Art of Bargaining</h1>
                    <p class="lead mb-4">Learn how to negotiate better prices and get the best deals on HugglingMart</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#getting-started" class="btn btn-light btn-lg">
                            <i class="fas fa-play me-2"></i>Get Started
                        </a>
                        <a href="#tips" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-lightbulb me-2"></i>Pro Tips
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-percentage text-success fa-2x mb-3"></i>
                    <h4 class="text-success">15-30%</h4>
                    <p class="text-muted mb-0">Average Savings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-clock text-info fa-2x mb-3"></i>
                    <h4 class="text-info">24 Hours</h4>
                    <p class="text-muted mb-0">Response Time</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-thumbs-up text-warning fa-2x mb-3"></i>
                    <h4 class="text-warning">85%</h4>
                    <p class="text-muted mb-0">Success Rate</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-comments text-primary fa-2x mb-3"></i>
                    <h4 class="text-primary">Real-time</h4>
                    <p class="text-muted mb-0">Chat System</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Getting Started Section -->
    <div class="row mb-5" id="getting-started">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-rocket text-primary me-2"></i>Getting Started with Bargaining
            </h2>
            
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <strong>1</strong>
                                </div>
                                <h5 class="mb-0">Find Your Product</h5>
                            </div>
                            <p class="text-muted">Browse our catalog and find the product you want to purchase. Look for the "Negotiate Price" button on product pages.</p>
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Not all products are eligible for bargaining. Look for the handshake icon!
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <strong>2</strong>
                                </div>
                                <h5 class="mb-0">Make Your Offer</h5>
                            </div>
                            <p class="text-muted">Click "Negotiate Price" and enter your desired price. Be reasonable - offers too low may be automatically rejected.</p>
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Start with 10-20% below the listed price for best results.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <strong>3</strong>
                                </div>
                                <h5 class="mb-0">Wait for Response</h5>
                            </div>
                            <p class="text-muted">Our team will review your offer within 24 hours. You'll receive a notification when we respond with acceptance, rejection, or counter-offer.</p>
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted">
                                    <i class="fas fa-bell me-1"></i>
                                    Check your bargains page regularly for updates.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <strong>4</strong>
                                </div>
                                <h5 class="mb-0">Complete Purchase</h5>
                            </div>
                            <p class="text-muted">Once your bargain is accepted, add the item to your cart at the negotiated price and proceed to checkout.</p>
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted">
                                    <i class="fas fa-shopping-cart me-1"></i>
                                    Negotiated prices are valid for 48 hours after acceptance.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pro Tips Section -->
    <div class="row mb-5" id="tips">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-lightbulb text-warning me-2"></i>Pro Bargaining Tips
            </h2>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>Do's
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Be Polite:</strong> Respectful communication gets better results
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Research Prices:</strong> Know the market value before negotiating
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Explain Your Offer:</strong> Give reasons for your price request
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Be Patient:</strong> Good deals take time to negotiate
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <strong>Bundle Items:</strong> Negotiate better prices for multiple items
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-times-circle me-2"></i>Don'ts
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    <strong>Lowball Offers:</strong> Extremely low offers may be ignored
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    <strong>Be Rude:</strong> Aggressive behavior hurts your chances
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    <strong>Spam Offers:</strong> Multiple offers for same item won't help
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    <strong>Ignore Responses:</strong> Always respond to counter-offers
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-times text-danger me-2"></i>
                                    <strong>Rush Decisions:</strong> Take time to consider counter-offers
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-star me-2"></i>Expert Strategies
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-star text-info me-2"></i>
                                    <strong>Seasonal Timing:</strong> Negotiate during off-peak seasons
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-star text-info me-2"></i>
                                    <strong>Volume Discounts:</strong> Ask for bulk pricing on multiple items
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-star text-info me-2"></i>
                                    <strong>Loyalty Benefits:</strong> Mention your purchase history
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-star text-info me-2"></i>
                                    <strong>Competitor Prices:</strong> Reference lower prices elsewhere
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-star text-info me-2"></i>
                                    <strong>Payment Terms:</strong> Offer immediate payment for discounts
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bargaining Examples -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-comments text-primary me-2"></i>Bargaining Examples
            </h2>
            
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card border-success">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-success">
                                <i class="fas fa-thumbs-up me-2"></i>Good Example
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chat-example">
                                <div class="message customer mb-3">
                                    <div class="bg-light p-3 rounded">
                                        <strong>Customer:</strong> "Hi! I'm interested in the Wireless Headphones for $99. I found similar ones for $80 elsewhere. Would you consider $85? I'm ready to buy today."
                                    </div>
                                </div>
                                <div class="message admin mb-3">
                                    <div class="bg-primary text-white p-3 rounded">
                                        <strong>Admin:</strong> "Thanks for your interest! I can offer $90 for immediate purchase. These have premium features not found in cheaper alternatives."
                                    </div>
                                </div>
                                <div class="message customer">
                                    <div class="bg-light p-3 rounded">
                                        <strong>Customer:</strong> "That sounds fair! I'll take them at $90. Thank you!"
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 p-2 bg-success bg-opacity-10 rounded">
                                <small class="text-success">
                                    <i class="fas fa-check me-1"></i>
                                    <strong>Result:</strong> Successful negotiation with $9 savings
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="card border-danger">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-danger">
                                <i class="fas fa-thumbs-down me-2"></i>Poor Example
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="chat-example">
                                <div class="message customer mb-3">
                                    <div class="bg-light p-3 rounded">
                                        <strong>Customer:</strong> "Your prices are too high! I'll pay $30 for the $99 headphones. Take it or leave it."
                                    </div>
                                </div>
                                <div class="message admin">
                                    <div class="bg-primary text-white p-3 rounded">
                                        <strong>Admin:</strong> "I'm sorry, but $30 is below our cost. The lowest I can go is $85 due to the quality and features."
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 p-2 bg-danger bg-opacity-10 rounded">
                                <small class="text-danger">
                                    <i class="fas fa-times me-1"></i>
                                    <strong>Result:</strong> Negotiation failed due to unrealistic offer and poor approach
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- FAQ Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="fas fa-question-circle text-info me-2"></i>Frequently Asked Questions
            </h2>
            
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq1">
                            How long does it take to get a response to my bargain?
                        </button>
                    </h3>
                    <div id="faq1" class="accordion-collapse collapse show" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            We typically respond to bargain requests within 24 hours during business days. Complex negotiations or high-value items may take slightly longer as we ensure you get the best possible deal.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq2">
                            What happens if my bargain is rejected?
                        </button>
                    </h3>
                    <div id="faq2" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            If your initial offer is rejected, you can make a new offer or accept a counter-offer from us. You can also purchase the item at the original price if you change your mind.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq3">
                            Can I bargain for multiple items at once?
                        </button>
                    </h3>
                    <div id="faq3" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! We offer volume discounts for multiple items. When bargaining for several products, mention this in your message for better pricing opportunities.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq4">
                            Are there any products that can't be bargained for?
                        </button>
                    </h3>
                    <div id="faq4" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            Some items like clearance products, limited editions, or items already heavily discounted may not be eligible for bargaining. Look for the handshake icon on product pages to see if bargaining is available.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse" data-mdb-target="#faq5">
                            How long is my accepted bargain price valid?
                        </button>
                    </h3>
                    <div id="faq5" class="accordion-collapse collapse" data-mdb-parent="#faqAccordion">
                        <div class="accordion-body">
                            Accepted bargain prices are valid for 48 hours from the time of acceptance. This gives you time to complete your purchase while ensuring fair pricing for all customers.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body text-center py-5">
                    <i class="fas fa-handshake fa-3x mb-4"></i>
                    <h3 class="mb-3">Ready to Start Bargaining?</h3>
                    <p class="lead mb-4">Put your new skills to the test and start saving money today!</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="index.php" class="btn btn-light btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Browse Products
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="bargains.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-list me-2"></i>My Bargains
                            </a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
