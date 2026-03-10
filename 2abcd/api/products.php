<?php
/**
 * Products API
 * Handles product listing, filtering, search, and details
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/product.php';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';
    $productModel = new Product();
    
    switch ($action) {
        case 'list':
            $filters = [
                'category_id' => $_GET['category_id'] ?? null,
                'platform_id' => $_GET['platform_id'] ?? null,
                'search' => $_GET['search'] ?? null,
                'min_price' => $_GET['min_price'] ?? null,
                'max_price' => $_GET['max_price'] ?? null,
                'sort' => $_GET['sort'] ?? 'newest'
            ];
            
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            
            if ($limit > 100) $limit = 100;
            if ($offset < 0) $offset = 0;
            
            $products = $productModel->getProducts($filters, $limit, $offset);
            
            echo json_encode([
                'success' => true,
                'products' => $products,
                'limit' => $limit,
                'offset' => $offset,
                'count' => count($products)
            ]);
            break;
        
        case 'get':
            $id = (int)($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('ID sản phẩm không hợp lệ');
            }
            
            $product = $productModel->getProduct($id);
            
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }
            
            // Get reviews
            require_once __DIR__ . '/../config/database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            $stmt = $db->prepare(
                "SELECT r.*, u.username, u.avatar FROM reviews r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.product_id = ? AND r.status = 'approved' 
                 ORDER BY r.created_at DESC LIMIT 10"
            );
            $stmt->execute([$id]);
            $product['reviews'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'product' => $product
            ]);
            break;
        
        case 'featured':
            $limit = (int)($_GET['limit'] ?? 10);
            if ($limit > 50) $limit = 50;
            
            $products = $productModel->getFeaturedProducts($limit);
            
            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products)
            ]);
            break;
        
        case 'sale':
            $limit = (int)($_GET['limit'] ?? 10);
            if ($limit > 50) $limit = 50;
            
            $products = $productModel->getSaleProducts($limit);
            
            echo json_encode([
                'success' => true,
                'products' => $products,
                'count' => count($products)
            ]);
            break;
        
        case 'categories':
            $categories = $productModel->getCategories();
            
            echo json_encode([
                'success' => true,
                'categories' => $categories,
                'count' => count($categories)
            ]);
            break;
        
        case 'platforms':
            $platforms = $productModel->getPlatforms();
            
            echo json_encode([
                'success' => true,
                'platforms' => $platforms,
                'count' => count($platforms)
            ]);
            break;
        
        case 'search':
            $query = $_GET['q'] ?? '';
            
            if (strlen($query) < 2) {
                throw new Exception('Từ khóa tìm kiếm phải ít nhất 2 ký tự');
            }
            
            $filters = ['search' => $query];
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            
            $products = $productModel->getProducts($filters, $limit, $offset);
            
            echo json_encode([
                'success' => true,
                'products' => $products,
                'query' => $query,
                'count' => count($products)
            ]);
            break;
        
        default:
            throw new Exception('Hành động không hợp lệ');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
