<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;
    private $table = 'products';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllProducts($limit = null, $offset = null, $category = null, $search = null, $featured = null) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active'";
        
        $params = [];
        
        if ($category) {
            $query .= " AND p.category_id = :category";
            $params[':category'] = $category;
        }
        
        if ($search) {
            $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        if ($featured) {
            $query .= " AND p.is_featured = 1";
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
            if ($offset) {
                $query .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Parse images JSON
        foreach ($products as &$product) {
            $product['images'] = json_decode($product['images'], true) ?: [];
        }
        
        return $products;
    }

    public function getProductById($id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['images'] = json_decode($product['images'], true) ?: [];
        }
        
        return $product;
    }

    public function getProductBySlug($slug) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.slug = :slug AND p.status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['images'] = json_decode($product['images'], true) ?: [];
        }
        
        return $product;
    }

    public function createProduct($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, slug, description, short_description, price, sale_price, sku, 
                   stock_quantity, category_id, images, is_featured, allow_bargaining, 
                   bargain_min_percentage, bargain_auto_accept_percentage) 
                  VALUES (:name, :slug, :description, :short_description, :price, :sale_price, 
                          :sku, :stock_quantity, :category_id, :images, :is_featured, 
                          :allow_bargaining, :bargain_min_percentage, :bargain_auto_accept_percentage)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':short_description', $data['short_description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':sale_price', $data['sale_price']);
        $stmt->bindParam(':sku', $data['sku']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':images', json_encode($data['images']));
        $stmt->bindParam(':is_featured', $data['is_featured']);
        $stmt->bindParam(':allow_bargaining', $data['allow_bargaining']);
        $stmt->bindParam(':bargain_min_percentage', $data['bargain_min_percentage']);
        $stmt->bindParam(':bargain_auto_accept_percentage', $data['bargain_auto_accept_percentage']);
        
        return $stmt->execute();
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, slug = :slug, description = :description, 
                      short_description = :short_description, price = :price, 
                      sale_price = :sale_price, sku = :sku, stock_quantity = :stock_quantity, 
                      category_id = :category_id, images = :images, is_featured = :is_featured, 
                      allow_bargaining = :allow_bargaining, bargain_min_percentage = :bargain_min_percentage, 
                      bargain_auto_accept_percentage = :bargain_auto_accept_percentage, 
                      status = :status, updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $data['slug']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':short_description', $data['short_description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':sale_price', $data['sale_price']);
        $stmt->bindParam(':sku', $data['sku']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':images', json_encode($data['images']));
        $stmt->bindParam(':is_featured', $data['is_featured']);
        $stmt->bindParam(':allow_bargaining', $data['allow_bargaining']);
        $stmt->bindParam(':bargain_min_percentage', $data['bargain_min_percentage']);
        $stmt->bindParam(':bargain_auto_accept_percentage', $data['bargain_auto_accept_percentage']);
        $stmt->bindParam(':status', $data['status']);
        
        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $query = "UPDATE " . $this->table . " SET status = 'inactive' WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getProductStats() {
        $query = "SELECT 
                    COUNT(*) as total_products,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_products,
                    COUNT(CASE WHEN status = 'out_of_stock' THEN 1 END) as out_of_stock,
                    COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured_products,
                    AVG(price) as avg_price
                  FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRelatedProducts($categoryId, $currentProductId, $limit = 4) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE category_id = :category_id 
                  AND id != :current_id 
                  AND status = 'active' 
                  ORDER BY RAND() 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->bindParam(':current_id', $currentProductId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as &$product) {
            $product['images'] = json_decode($product['images'], true) ?: [];
        }
        
        return $products;
    }

    public function getTotalProductCount($categoryId = null) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE status = 'active'";
        
        if ($categoryId) {
            $query .= " AND category_id = :category_id";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($categoryId) {
            $stmt->bindParam(':category_id', $categoryId);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>
