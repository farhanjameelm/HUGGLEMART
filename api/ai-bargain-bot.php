<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$database = new Database();
$pdo = $database->getConnection();

$action = $_POST['action'] ?? '';
$userId = $_SESSION['user_id'];

try {
    switch ($action) {
        case 'process_bargain':
            $bargainId = $_POST['bargain_id'] ?? 0;
            $useAI = $_POST['use_ai'] ?? false;
            
            if (!$bargainId) {
                echo json_encode(['success' => false, 'message' => 'Bargain ID required']);
                exit();
            }
            
            // Get bargain details
            $bargainQuery = "SELECT b.*, p.name as product_name, p.price as original_price, p.category, u.username, u.email 
                            FROM bargains b 
                            JOIN products p ON b.product_id = p.id 
                            JOIN users u ON b.user_id = u.id 
                            WHERE b.id = :bargain_id";
            $bargainStmt = $pdo->prepare($bargainQuery);
            $bargainStmt->execute([':bargain_id' => $bargainId]);
            $bargain = $bargainStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$bargain) {
                echo json_encode(['success' => false, 'message' => 'Bargain not found']);
                exit();
            }
            
            if ($useAI) {
                $aiResponse = processAIBargain($bargain);
                echo json_encode($aiResponse);
            } else {
                echo json_encode(['success' => true, 'bargain' => $bargain]);
            }
            break;
            
        case 'get_ai_suggestion':
            $bargainId = $_POST['bargain_id'] ?? 0;
            $suggestion = getAISuggestion($pdo, $bargainId);
            echo json_encode($suggestion);
            break;
            
        case 'update_ai_settings':
            $settings = $_POST['settings'] ?? [];
            $result = updateAISettings($pdo, $userId, $settings);
            echo json_encode($result);
            break;
            
        case 'get_ai_analytics':
            $analytics = getAIAnalytics($pdo);
            echo json_encode($analytics);
            break;
            
        case 'train_ai_model':
            $trainingData = $_POST['training_data'] ?? [];
            $result = trainAIModel($pdo, $trainingData);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log('AI Bargain Bot Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'AI processing error occurred']);
}

// AI Bargain Processing Functions
function processAIBargain($bargain) {
    $originalPrice = floatval($bargain['original_price']);
    $offeredPrice = floatval($bargain['offered_price']);
    $discountPercentage = (($originalPrice - $offeredPrice) / $originalPrice) * 100;
    
    // AI Decision Logic
    $aiDecision = makeAIDecision($bargain, $discountPercentage);
    
    return [
        'success' => true,
        'ai_decision' => $aiDecision['decision'],
        'counter_offer' => $aiDecision['counter_offer'],
        'reasoning' => $aiDecision['reasoning'],
        'confidence' => $aiDecision['confidence'],
        'suggested_response' => $aiDecision['response_message']
    ];
}

function makeAIDecision($bargain, $discountPercentage) {
    $originalPrice = floatval($bargain['original_price']);
    $offeredPrice = floatval($bargain['offered_price']);
    $category = $bargain['category'];
    $customerMessage = $bargain['message'];
    
    // AI Decision Matrix based on various factors
    $factors = [
        'discount_level' => analyzeDiscountLevel($discountPercentage),
        'category_flexibility' => getCategoryFlexibility($category),
        'customer_sentiment' => analyzeSentiment($customerMessage),
        'market_conditions' => getMarketConditions($category),
        'inventory_level' => getInventoryPressure($bargain['product_id']),
        'customer_history' => getCustomerHistory($bargain['user_id'])
    ];
    
    // Calculate AI decision
    $decision = calculateAIDecision($factors, $originalPrice, $offeredPrice);
    
    return $decision;
}

function analyzeDiscountLevel($discountPercentage) {
    if ($discountPercentage <= 5) {
        return ['score' => 0.9, 'level' => 'minimal', 'action' => 'accept'];
    } elseif ($discountPercentage <= 15) {
        return ['score' => 0.7, 'level' => 'reasonable', 'action' => 'consider'];
    } elseif ($discountPercentage <= 25) {
        return ['score' => 0.4, 'level' => 'significant', 'action' => 'counter'];
    } else {
        return ['score' => 0.1, 'level' => 'excessive', 'action' => 'reject'];
    }
}

function getCategoryFlexibility($category) {
    $flexibility = [
        'electronics' => 0.6,
        'fashion' => 0.8,
        'home-garden' => 0.7,
        'sports-outdoors' => 0.7,
        'books' => 0.9,
        'toys' => 0.8,
        'automotive' => 0.5
    ];
    
    return $flexibility[$category] ?? 0.6;
}

function analyzeSentiment($message) {
    if (empty($message)) {
        return ['score' => 0.5, 'sentiment' => 'neutral'];
    }
    
    $positiveWords = ['please', 'thank', 'appreciate', 'love', 'great', 'excellent', 'wonderful'];
    $negativeWords = ['expensive', 'overpriced', 'cheap', 'poor', 'bad', 'terrible', 'awful'];
    $urgentWords = ['urgent', 'asap', 'immediately', 'quickly', 'soon', 'today'];
    
    $message = strtolower($message);
    $positiveCount = 0;
    $negativeCount = 0;
    $urgentCount = 0;
    
    foreach ($positiveWords as $word) {
        if (strpos($message, $word) !== false) $positiveCount++;
    }
    
    foreach ($negativeWords as $word) {
        if (strpos($message, $word) !== false) $negativeCount++;
    }
    
    foreach ($urgentWords as $word) {
        if (strpos($message, $word) !== false) $urgentCount++;
    }
    
    $sentiment = 'neutral';
    $score = 0.5;
    
    if ($positiveCount > $negativeCount) {
        $sentiment = 'positive';
        $score = 0.7 + ($positiveCount * 0.1);
    } elseif ($negativeCount > $positiveCount) {
        $sentiment = 'negative';
        $score = 0.3 - ($negativeCount * 0.1);
    }
    
    if ($urgentCount > 0) {
        $score += 0.1; // Slight boost for urgency
    }
    
    return ['score' => max(0, min(1, $score)), 'sentiment' => $sentiment];
}

function getMarketConditions($category) {
    // Simulate market conditions (in real implementation, this could connect to market APIs)
    $conditions = [
        'electronics' => ['demand' => 0.8, 'competition' => 0.7, 'seasonality' => 0.6],
        'fashion' => ['demand' => 0.7, 'competition' => 0.9, 'seasonality' => 0.8],
        'home-garden' => ['demand' => 0.6, 'competition' => 0.6, 'seasonality' => 0.7],
        'sports-outdoors' => ['demand' => 0.7, 'competition' => 0.7, 'seasonality' => 0.9],
    ];
    
    return $conditions[$category] ?? ['demand' => 0.6, 'competition' => 0.6, 'seasonality' => 0.6];
}

function getInventoryPressure($productId) {
    // Simulate inventory pressure (in real implementation, check actual stock levels)
    return rand(1, 10) / 10; // Random value between 0.1 and 1.0
}

function getCustomerHistory($userId) {
    // Simulate customer history analysis
    return [
        'total_orders' => rand(0, 20),
        'avg_order_value' => rand(50, 500),
        'loyalty_score' => rand(1, 10) / 10,
        'bargain_success_rate' => rand(1, 10) / 10
    ];
}

function calculateAIDecision($factors, $originalPrice, $offeredPrice) {
    $discountLevel = $factors['discount_level'];
    $categoryFlex = $factors['category_flexibility'];
    $sentiment = $factors['customer_sentiment'];
    $market = $factors['market_conditions'];
    $inventory = $factors['inventory_level'];
    $customer = $factors['customer_history'];
    
    // Weighted decision calculation
    $acceptanceScore = 0;
    $acceptanceScore += $discountLevel['score'] * 0.3;
    $acceptanceScore += $categoryFlex * 0.2;
    $acceptanceScore += $sentiment['score'] * 0.15;
    $acceptanceScore += $market['demand'] * 0.1;
    $acceptanceScore += $inventory * 0.1;
    $acceptanceScore += $customer['loyalty_score'] * 0.15;
    
    $confidence = min(0.95, max(0.6, $acceptanceScore));
    
    // Decision logic
    if ($acceptanceScore >= 0.7) {
        $decision = 'accept';
        $counterOffer = $offeredPrice;
        $reasoning = "Customer offer is reasonable based on market conditions and customer profile.";
        $responseMessage = generateAcceptanceMessage($sentiment['sentiment']);
    } elseif ($acceptanceScore >= 0.4) {
        $decision = 'counter';
        $counterOffer = calculateCounterOffer($originalPrice, $offeredPrice, $acceptanceScore);
        $reasoning = "Offer requires negotiation. Counter-offer calculated based on market analysis.";
        $responseMessage = generateCounterMessage($counterOffer, $sentiment['sentiment']);
    } else {
        $decision = 'reject';
        $counterOffer = null;
        $reasoning = "Offer is below acceptable threshold considering current market conditions.";
        $responseMessage = generateRejectionMessage($sentiment['sentiment']);
    }
    
    return [
        'decision' => $decision,
        'counter_offer' => $counterOffer,
        'reasoning' => $reasoning,
        'confidence' => $confidence,
        'response_message' => $responseMessage,
        'factors_analyzed' => $factors
    ];
}

function calculateCounterOffer($originalPrice, $offeredPrice, $acceptanceScore) {
    // Calculate a fair counter-offer based on acceptance score
    $minAcceptable = $originalPrice * 0.85; // Minimum 15% discount
    $maxDiscount = $originalPrice * 0.75; // Maximum 25% discount
    
    $counterOffer = $originalPrice - (($originalPrice - $offeredPrice) * $acceptanceScore);
    
    return max($maxDiscount, min($minAcceptable, $counterOffer));
}

function generateAcceptanceMessage($sentiment) {
    $messages = [
        'positive' => "Thank you for your interest! We're happy to accept your offer. Your positive approach is appreciated!",
        'neutral' => "We've reviewed your offer and are pleased to accept it. Thank you for choosing our product!",
        'negative' => "We understand your concerns and are willing to accept your offer to ensure your satisfaction."
    ];
    
    return $messages[$sentiment] ?? $messages['neutral'];
}

function generateCounterMessage($counterOffer, $sentiment) {
    $messages = [
        'positive' => "We appreciate your enthusiasm! While we can't accept the initial offer, we'd like to propose $" . number_format($counterOffer, 2) . " as a fair compromise.",
        'neutral' => "Thank you for your offer. We'd like to counter with $" . number_format($counterOffer, 2) . " which reflects the product's value and current market conditions.",
        'negative' => "We understand your price concerns. To address them, we can offer $" . number_format($counterOffer, 2) . " as our best price."
    ];
    
    return $messages[$sentiment] ?? $messages['neutral'];
}

function generateRejectionMessage($sentiment) {
    $messages = [
        'positive' => "We truly appreciate your interest, but unfortunately we cannot accommodate this price point while maintaining our quality standards.",
        'neutral' => "Thank you for your offer. Unfortunately, we're unable to accept this price as it doesn't align with our current pricing structure.",
        'negative' => "We understand your budget constraints, but we're unable to meet this price point. We'd be happy to notify you of any future promotions."
    ];
    
    return $messages[$sentiment] ?? $messages['neutral'];
}

function getAISuggestion($pdo, $bargainId) {
    try {
        $bargainQuery = "SELECT b.*, p.name as product_name, p.price as original_price, p.category 
                        FROM bargains b 
                        JOIN products p ON b.product_id = p.id 
                        WHERE b.id = :bargain_id";
        $bargainStmt = $pdo->prepare($bargainQuery);
        $bargainStmt->execute([':bargain_id' => $bargainId]);
        $bargain = $bargainStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$bargain) {
            return ['success' => false, 'message' => 'Bargain not found'];
        }
        
        $aiAnalysis = processAIBargain($bargain);
        
        return [
            'success' => true,
            'suggestion' => $aiAnalysis,
            'quick_responses' => [
                'accept' => "Great offer! We're happy to accept your price.",
                'counter' => "How about we meet in the middle at $" . number_format($aiAnalysis['counter_offer'] ?? 0, 2) . "?",
                'reject' => "Unfortunately, we can't go that low, but we appreciate your interest!"
            ]
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error getting AI suggestion'];
    }
}

function updateAISettings($pdo, $userId, $settings) {
    try {
        // Save AI settings for the user/admin
        $settingsJson = json_encode($settings);
        
        $query = "INSERT INTO ai_bargain_settings (user_id, settings, updated_at) 
                  VALUES (:user_id, :settings, NOW()) 
                  ON DUPLICATE KEY UPDATE settings = :settings, updated_at = NOW()";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':user_id' => $userId,
            ':settings' => $settingsJson
        ]);
        
        return ['success' => true, 'message' => 'AI settings updated successfully'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating AI settings'];
    }
}

function getAIAnalytics($pdo) {
    try {
        // Get AI bargaining analytics
        $analytics = [
            'total_processed' => 0,
            'acceptance_rate' => 0,
            'avg_discount_given' => 0,
            'customer_satisfaction' => 0,
            'processing_time' => 0
        ];
        
        // Simulate analytics (in real implementation, query actual data)
        $analytics['total_processed'] = rand(100, 1000);
        $analytics['acceptance_rate'] = rand(60, 85);
        $analytics['avg_discount_given'] = rand(10, 20);
        $analytics['customer_satisfaction'] = rand(75, 95);
        $analytics['processing_time'] = rand(1, 5);
        
        return ['success' => true, 'analytics' => $analytics];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error getting analytics'];
    }
}

function trainAIModel($pdo, $trainingData) {
    try {
        // Save training data for AI model improvement
        foreach ($trainingData as $data) {
            $query = "INSERT INTO ai_training_data (bargain_id, decision, outcome, feedback, created_at) 
                      VALUES (:bargain_id, :decision, :outcome, :feedback, NOW())";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':bargain_id' => $data['bargain_id'],
                ':decision' => $data['decision'],
                ':outcome' => $data['outcome'],
                ':feedback' => $data['feedback']
            ]);
        }
        
        return ['success' => true, 'message' => 'AI model training data saved'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error saving training data'];
    }
}
?>
