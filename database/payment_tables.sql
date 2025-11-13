-- Payment system database tables

-- Payment logs table
CREATE TABLE IF NOT EXISTS `payment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','success','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `reference` varchar(100) DEFAULT NULL,
  `error_message` text,
  `response_data` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved payment methods table (for future use)
CREATE TABLE IF NOT EXISTS `saved_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `card_last_four` varchar(4) DEFAULT NULL,
  `card_type` varchar(20) DEFAULT NULL,
  `card_expiry` varchar(7) DEFAULT NULL,
  `card_token` varchar(255) DEFAULT NULL, -- Encrypted/tokenized card data
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_active` (`is_active`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payment gateway configurations table
CREATE TABLE IF NOT EXISTS `payment_gateways` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_test_mode` tinyint(1) NOT NULL DEFAULT 1,
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `webhook_url` varchar(255) DEFAULT NULL,
  `supported_currencies` json DEFAULT NULL,
  `configuration` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gateway_name` (`gateway_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add payment reference column to orders table if not exists
ALTER TABLE `orders` 
ADD COLUMN IF NOT EXISTS `payment_reference` varchar(100) DEFAULT NULL AFTER `payment_status`;

-- Insert default payment gateway configurations
INSERT IGNORE INTO `payment_gateways` (`gateway_name`, `is_active`, `is_test_mode`, `supported_currencies`, `configuration`) VALUES
('stripe', 1, 1, '["USD", "EUR", "GBP"]', '{"public_key": "", "secret_key": "", "webhook_secret": ""}'),
('paypal', 1, 1, '["USD", "EUR", "GBP"]', '{"client_id": "", "client_secret": "", "mode": "sandbox"}'),
('razorpay', 0, 1, '["INR", "USD"]', '{"key_id": "", "key_secret": ""}'),
('square', 0, 1, '["USD", "CAD", "GBP"]', '{"application_id": "", "access_token": "", "location_id": ""}');

-- Sample payment log entries for testing
INSERT IGNORE INTO `payment_logs` (`order_id`, `user_id`, `payment_method`, `amount`, `status`, `reference`, `response_data`) VALUES
(1, 2, 'credit_card', 299.99, 'success', 'CC_1699999999_1234', '{"card_type": "Visa", "last_four": "4242", "amount": 299.99}'),
(2, 3, 'paypal', 149.50, 'success', 'PP_1699999999_5678', '{"amount": 149.50, "currency": "USD"}'),
(3, 2, 'cash_on_delivery', 89.99, 'pending', 'COD_1699999999_9012', '{"amount": 89.99, "payment_due": "on_delivery"}');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_payment_logs_method_status` ON `payment_logs` (`payment_method`, `status`);
CREATE INDEX IF NOT EXISTS `idx_payment_logs_created_at` ON `payment_logs` (`created_at` DESC);
CREATE INDEX IF NOT EXISTS `idx_saved_payment_methods_user_active` ON `saved_payment_methods` (`user_id`, `is_active`);
