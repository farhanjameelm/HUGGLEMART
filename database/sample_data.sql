-- Sample data for HugglingMart
USE hugglingmart;

-- Insert sample products
INSERT INTO products (name, slug, description, short_description, price, sale_price, sku, stock_quantity, category_id, images, is_featured, allow_bargaining, bargain_min_percentage) VALUES
('iPhone 15 Pro Max', 'iphone-15-pro-max', 'The latest iPhone with advanced camera system and A17 Pro chip. Experience the power of titanium design with incredible performance.', 'Latest iPhone with titanium design and A17 Pro chip', 1199.00, 1099.00, 'IP15PM001', 25, 1, '["https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400", "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400"]', TRUE, TRUE, 5.00),

('Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Premium Android smartphone with S Pen, incredible camera zoom, and all-day battery life.', 'Premium Android with S Pen and amazing camera', 1299.00, NULL, 'SGS24U001', 18, 1, '["https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400", "https://images.unsplash.com/photo-1580910051074-3eb694886505?w=400"]', TRUE, TRUE, 8.00),

('MacBook Air M3', 'macbook-air-m3', 'Supercharged by the M3 chip, MacBook Air is a portable powerhouse. Get things done anywhere with up to 18 hours of battery life.', 'Lightweight laptop with M3 chip and all-day battery', 1099.00, 999.00, 'MBA13M3001', 12, 1, '["https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=400", "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400"]', TRUE, TRUE, 10.00),

('Sony WH-1000XM5', 'sony-wh-1000xm5', 'Industry-leading noise canceling headphones with exceptional sound quality and 30-hour battery life.', 'Premium noise-canceling headphones with 30h battery', 399.00, 349.00, 'SWXM5001', 35, 1, '["https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400", "https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400"]', FALSE, TRUE, 15.00),

('Nike Air Jordan 1 Retro High', 'nike-air-jordan-1-retro-high', 'Classic basketball shoe with premium leather construction and iconic design that never goes out of style.', 'Iconic basketball shoe with premium leather construction', 170.00, NULL, 'NAJ1RH001', 42, 2, '["https://images.unsplash.com/photo-1556906781-9a412961c28c?w=400", "https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400"]', TRUE, TRUE, 12.00),

('Levi\'s 501 Original Jeans', 'levis-501-original-jeans', 'The original blue jean since 1873. Crafted with premium denim and classic straight fit.', 'Classic straight-fit jeans since 1873', 89.00, 69.00, 'L501OJ001', 68, 2, '["https://images.unsplash.com/photo-1542272604-787c3835535d?w=400", "https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=400"]', FALSE, TRUE, 20.00),

('Adidas Ultraboost 22', 'adidas-ultraboost-22', 'Running shoes with responsive BOOST midsole and Primeknit upper for ultimate comfort and performance.', 'Premium running shoes with BOOST technology', 190.00, 159.00, 'AUB22001', 28, 4, '["https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400", "https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400"]', TRUE, TRUE, 10.00),

('KitchenAid Stand Mixer', 'kitchenaid-stand-mixer', 'Professional-grade stand mixer with 10 speeds and multiple attachments for all your baking needs.', '10-speed stand mixer for professional baking', 449.00, 399.00, 'KASM001', 15, 3, '["https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=400", "https://images.unsplash.com/photo-1574781330855-d0db2706b3d0?w=400"]', FALSE, TRUE, 8.00),

('Dyson V15 Detect', 'dyson-v15-detect', 'Cordless vacuum with laser dust detection and intelligent suction adjustment for deep cleaning.', 'Cordless vacuum with laser dust detection', 749.00, NULL, 'DV15D001', 8, 3, '["https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400", "https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?w=400"]', TRUE, TRUE, 5.00),

('The Great Gatsby', 'the-great-gatsby', 'F. Scott Fitzgerald\'s masterpiece about the Jazz Age, love, and the American Dream.', 'Classic American novel by F. Scott Fitzgerald', 14.99, 12.99, 'TGG001', 150, 5, '["https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400", "https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400"]', FALSE, FALSE, 0.00),

('Wireless Gaming Mouse', 'wireless-gaming-mouse', 'High-precision wireless gaming mouse with RGB lighting and programmable buttons for competitive gaming.', 'High-precision wireless mouse with RGB lighting', 79.99, 59.99, 'WGM001', 45, 1, '["https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400", "https://images.unsplash.com/photo-1563297007-0686b7003af7?w=400"]', FALSE, TRUE, 25.00),

('Yoga Mat Premium', 'yoga-mat-premium', 'Non-slip premium yoga mat with extra cushioning for comfortable practice and meditation.', 'Non-slip premium yoga mat with extra cushioning', 49.99, 39.99, 'YMP001', 75, 4, '["https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=400", "https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400"]', FALSE, TRUE, 30.00);

-- Insert some sample bargains
INSERT INTO bargains (user_id, product_id, original_price, offered_price, current_price, status, expires_at) VALUES
(1, 1, 1199.00, 1000.00, 1050.00, 'countered', DATE_ADD(NOW(), INTERVAL 12 HOUR)),
(1, 3, 1099.00, 950.00, 950.00, 'pending', DATE_ADD(NOW(), INTERVAL 18 HOUR)),
(1, 5, 170.00, 140.00, 140.00, 'accepted', DATE_ADD(NOW(), INTERVAL -2 HOUR));

-- Insert bargain messages
INSERT INTO bargain_messages (bargain_id, sender_id, sender_type, message_type, message, offered_price) VALUES
(1, 1, 'customer', 'offer', 'I would like to buy this iPhone for $1000. I am a loyal customer.', 1000.00),
(1, 1, 'admin', 'counter_offer', 'Thank you for your interest! I can offer it for $1050. This is the best I can do.', 1050.00),
(2, 1, 'customer', 'offer', 'Can you do $950 for the MacBook? I need it for my studies.', 950.00),
(3, 1, 'customer', 'offer', 'Would you accept $140 for these Jordans?', 140.00),
(3, 1, 'admin', 'accept', 'Deal! $140 is acceptable. Thank you for your business!', 140.00);
