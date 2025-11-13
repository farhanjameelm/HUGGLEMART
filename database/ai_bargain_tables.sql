-- AI Bargain Bot Database Tables

-- Use the hugglingmart database
USE `hugglingmart`;

-- AI bargain settings table
CREATE TABLE IF NOT EXISTS `ai_bargain_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `settings` json NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI training data table
CREATE TABLE IF NOT EXISTS `ai_training_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bargain_id` int(11) NOT NULL,
  `decision` enum('accept','counter','reject') NOT NULL,
  `outcome` enum('successful','failed','pending') NOT NULL DEFAULT 'pending',
  `feedback` text,
  `confidence_score` decimal(3,2) DEFAULT NULL,
  `factors_analyzed` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bargain_id` (`bargain_id`),
  KEY `decision` (`decision`),
  KEY `outcome` (`outcome`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`bargain_id`) REFERENCES `bargains` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI decision logs table
CREATE TABLE IF NOT EXISTS `ai_decision_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bargain_id` int(11) NOT NULL,
  `ai_decision` enum('accept','counter','reject') NOT NULL,
  `confidence_level` decimal(3,2) NOT NULL,
  `reasoning` text,
  `suggested_response` text,
  `counter_offer_amount` decimal(10,2) DEFAULT NULL,
  `factors_considered` json DEFAULT NULL,
  `processing_time_ms` int(11) DEFAULT NULL,
  `human_override` tinyint(1) NOT NULL DEFAULT 0,
  `final_decision` enum('accept','counter','reject') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bargain_id` (`bargain_id`),
  KEY `ai_decision` (`ai_decision`),
  KEY `confidence_level` (`confidence_level`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`bargain_id`) REFERENCES `bargains` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI performance metrics table
CREATE TABLE IF NOT EXISTS `ai_performance_metrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `total_bargains_processed` int(11) NOT NULL DEFAULT 0,
  `auto_accepted` int(11) NOT NULL DEFAULT 0,
  `counter_offered` int(11) NOT NULL DEFAULT 0,
  `rejected` int(11) NOT NULL DEFAULT 0,
  `human_overrides` int(11) NOT NULL DEFAULT 0,
  `avg_processing_time_ms` decimal(8,2) DEFAULT NULL,
  `avg_confidence_score` decimal(3,2) DEFAULT NULL,
  `customer_satisfaction_score` decimal(3,2) DEFAULT NULL,
  `revenue_impact` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`),
  KEY `date_idx` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AI model versions table
CREATE TABLE IF NOT EXISTS `ai_model_versions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(20) NOT NULL,
  `model_data` longtext,
  `training_data_count` int(11) NOT NULL DEFAULT 0,
  `accuracy_score` decimal(5,4) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `version` (`version`),
  KEY `is_active` (`is_active`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer interaction patterns table
CREATE TABLE IF NOT EXISTS `customer_interaction_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_bargains` int(11) NOT NULL DEFAULT 0,
  `successful_bargains` int(11) NOT NULL DEFAULT 0,
  `avg_discount_requested` decimal(5,2) DEFAULT NULL,
  `avg_discount_received` decimal(5,2) DEFAULT NULL,
  `preferred_categories` json DEFAULT NULL,
  `bargaining_style` enum('aggressive','moderate','polite','urgent') DEFAULT 'moderate',
  `response_time_preference` enum('immediate','within_hour','within_day','flexible') DEFAULT 'flexible',
  `loyalty_score` decimal(3,2) DEFAULT 0.50,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `loyalty_score` (`loyalty_score`),
  KEY `bargaining_style` (`bargaining_style`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default AI settings
INSERT IGNORE INTO `ai_bargain_settings` (`user_id`, `settings`) VALUES
(1, '{
  "maxDiscount": 15,
  "minDiscount": 5,
  "categoryFlexibility": {
    "electronics": 0.6,
    "fashion": 0.8,
    "home-garden": 0.7,
    "sports-outdoors": 0.7,
    "books": 0.9,
    "toys": 0.8,
    "automotive": 0.5
  },
  "autoProcess": true,
  "learningMode": true,
  "sentimentAnalysis": true,
  "confidenceThreshold": 0.7,
  "responseTemplates": {
    "accept": "Thank you for your offer! We are happy to accept this price.",
    "counter": "We appreciate your interest. How about we meet at ${counterOffer}?",
    "reject": "Unfortunately, we cannot accommodate this price point at this time."
  }
}');

-- Insert initial AI model version
INSERT IGNORE INTO `ai_model_versions` (`version`, `description`, `is_active`, `accuracy_score`) VALUES
('v1.0.0', 'Initial AI bargain bot model with basic decision logic', 1, 0.7500);

-- Insert sample AI performance metrics
INSERT IGNORE INTO `ai_performance_metrics` 
(`date`, `total_bargains_processed`, `auto_accepted`, `counter_offered`, `rejected`, `avg_processing_time_ms`, `avg_confidence_score`) VALUES
(CURDATE(), 0, 0, 0, 0, 0, 0.75),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 25, 8, 12, 5, 1250, 0.78),
(DATE_SUB(CURDATE(), INTERVAL 2 DAY), 32, 10, 15, 7, 1180, 0.76);

-- Insert sample customer interaction patterns
INSERT IGNORE INTO `customer_interaction_patterns` 
(`user_id`, `total_bargains`, `successful_bargains`, `avg_discount_requested`, `avg_discount_received`, `bargaining_style`, `loyalty_score`) VALUES
(2, 5, 3, 18.50, 12.30, 'moderate', 0.75),
(3, 8, 6, 22.10, 15.80, 'polite', 0.85);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_ai_decision_logs_confidence` ON `ai_decision_logs` (`confidence_level` DESC);
CREATE INDEX IF NOT EXISTS `idx_ai_training_data_outcome` ON `ai_training_data` (`outcome`, `decision`);
CREATE INDEX IF NOT EXISTS `idx_ai_performance_date_range` ON `ai_performance_metrics` (`date` DESC);
CREATE INDEX IF NOT EXISTS `idx_customer_patterns_loyalty` ON `customer_interaction_patterns` (`loyalty_score` DESC);

-- Create views for AI analytics
CREATE OR REPLACE VIEW `ai_bargain_analytics` AS
SELECT 
    DATE(b.created_at) as bargain_date,
    COUNT(*) as total_bargains,
    COUNT(adl.id) as ai_processed,
    SUM(CASE WHEN adl.ai_decision = 'accept' THEN 1 ELSE 0 END) as ai_accepted,
    SUM(CASE WHEN adl.ai_decision = 'counter' THEN 1 ELSE 0 END) as ai_countered,
    SUM(CASE WHEN adl.ai_decision = 'reject' THEN 1 ELSE 0 END) as ai_rejected,
    AVG(adl.confidence_level) as avg_confidence,
    AVG(adl.processing_time_ms) as avg_processing_time
FROM bargains b
LEFT JOIN ai_decision_logs adl ON b.id = adl.bargain_id
WHERE b.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(b.created_at)
ORDER BY bargain_date DESC;

-- Create view for customer bargaining insights
CREATE OR REPLACE VIEW `customer_bargaining_insights` AS
SELECT 
    u.id as user_id,
    u.username,
    u.email,
    cip.total_bargains,
    cip.successful_bargains,
    ROUND((cip.successful_bargains / cip.total_bargains * 100), 2) as success_rate,
    cip.avg_discount_requested,
    cip.avg_discount_received,
    cip.bargaining_style,
    cip.loyalty_score,
    COUNT(b.id) as recent_bargains
FROM users u
LEFT JOIN customer_interaction_patterns cip ON u.id = cip.user_id
LEFT JOIN bargains b ON u.id = b.user_id AND b.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
WHERE cip.total_bargains > 0
GROUP BY u.id, u.username, u.email, cip.total_bargains, cip.successful_bargains, 
         cip.avg_discount_requested, cip.avg_discount_received, cip.bargaining_style, cip.loyalty_score
ORDER BY cip.loyalty_score DESC, cip.total_bargains DESC;

-- Success message
SELECT 'AI Bargain Bot database setup completed successfully!' as message;
SELECT 'AI bot is ready to process bargaining requests automatically.' as status;
