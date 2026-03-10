<?php
require_once __DIR__ . '/../config/database.php';

class Product
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all products with filters
    public function getProducts($filters = [], $limit = 20, $offset = 0)
    {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['platform_id'])) {
            $where[] = "p.platform_id = ?";
            $params[] = $filters['platform_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ? OR pl.name LIKE ? OR c.name LIKE ? OR p.developer LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['min_price'])) {
            $where[] = "(p.price * (100 - COALESCE(p.discount_percent, 0)) / 100) >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "(p.price * (100 - COALESCE(p.discount_percent, 0)) / 100) <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['only_sale'])) {
            $where[] = "p.discount_percent > 0";
        }

        $whereClause = implode(" AND ", $where);

        $sql = "SELECT p.*, c.name as category_name, pl.name as platform_name,
                (SELECT COUNT(*) FROM product_keys pk WHERE pk.product_id = p.id AND pk.status = 'available') as stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN platforms pl ON p.platform_id = pl.id
                WHERE $whereClause
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
        $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total count of products with filters
    public function getTotalCount($filters = [])
    {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['platform_id'])) {
            $where[] = "p.platform_id = ?";
            $params[] = $filters['platform_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ? OR pl.name LIKE ? OR c.name LIKE ? OR p.developer LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['min_price'])) {
            $where[] = "(p.price * (100 - COALESCE(p.discount_percent, 0)) / 100) >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "(p.price * (100 - COALESCE(p.discount_percent, 0)) / 100) <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['only_sale'])) {
            $where[] = "p.discount_percent > 0";
        }

        $whereClause = implode(" AND ", $where);
        $sql = "SELECT COUNT(*) FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN platforms pl ON p.platform_id = pl.id
            WHERE $whereClause";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    // Get product by ID
    public function getProduct($id)
    {
        $sql = "SELECT p.*, c.name as category_name, pl.name as platform_name,
                (SELECT COUNT(*) FROM product_keys pk WHERE pk.product_id = p.id AND pk.status = 'available') as stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN platforms pl ON p.platform_id = pl.id
                WHERE p.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get featured products (for homepage)
    public function getFeaturedProducts($limit = 10)
    {
        $sql = "SELECT p.*, c.name as category_name, pl.name as platform_name,
                (SELECT COUNT(*) FROM product_keys pk WHERE pk.product_id = p.id AND pk.status = 'available') as stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN platforms pl ON p.platform_id = pl.id
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get products on sale (with discount)
    public function getSaleProducts($limit = 10)
    {
        $sql = "SELECT p.*, c.name as category_name, pl.name as platform_name,
                (SELECT COUNT(*) FROM product_keys pk WHERE pk.product_id = p.id AND pk.status = 'available') as stock
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN platforms pl ON p.platform_id = pl.id
                WHERE p.status = 'active' AND p.discount_percent > 0
                ORDER BY p.discount_percent DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all categories
    public function getCategories()
    {
        $stmt = $this->conn->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all platforms
    public function getPlatforms()
    {
        $stmt = $this->conn->query("SELECT * FROM platforms ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add product (admin)
    public function addProduct($data)
    {
        $sql = "INSERT INTO products (name, description, price, discount_percent, image, trailer_url, category_id, platform_id, developer, release_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['discount_percent'] ?? 0,
            $data['image'] ?? '',
            $data['trailer_url'] ?? '',
            $data['category_id'] ?? null,
            $data['platform_id'] ?? null,
            $data['developer'] ?? '',
            $data['release_date'] ?? null
        ]);
    }

    // Update product (admin)
    public function updateProduct($id, $data)
    {
        $fields = [];
        $values = [];

        foreach (['name', 'description', 'price', 'discount_percent', 'image', 'trailer_url', 'category_id', 'platform_id', 'developer', 'release_date', 'status'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields))
            return false;

        $values[] = $id;
        $sql = "UPDATE products SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($values);
    }

    // Delete product (admin)
    public function deleteProduct($id)
    {
        $stmt = $this->conn->prepare("UPDATE products SET status = 'inactive' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Import keys (admin)
    public function importKeys($productId, $keys)
    {
        $count = 0;
        foreach ($keys as $key) {
            $key = trim($key);
            if (!empty($key)) {
                $stmt = $this->conn->prepare("INSERT INTO product_keys (product_id, key_code) VALUES (?, ?)");
                if ($stmt->execute([$productId, $key])) {
                    $count++;
                }
            }
        }
        return $count;
    }

    // Get available key for purchase
    public function getAvailableKey($productId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM product_keys WHERE product_id = ? AND status = 'available' LIMIT 1");
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mark key as sold
    public function markKeyAsSold($keyId)
    {
        $stmt = $this->conn->prepare("UPDATE product_keys SET status = 'sold' WHERE id = ?");
        return $stmt->execute([$keyId]);
    }
}
