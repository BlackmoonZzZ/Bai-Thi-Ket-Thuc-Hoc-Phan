<?php
require_once __DIR__ . '/../config/database.php';

class Coupon {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // Get coupon by code
    public function getCouponByCode($code) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM coupons WHERE code = ? AND is_active = TRUE AND expiry_date >= CURDATE()"
            );
            $stmt->execute([$code]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get coupon error: " . $e->getMessage());
            return null;
        }
    }
    
    // Validate coupon for order
    public function validateCoupon($code, $orderAmount) {
        try {
            $coupon = $this->getCouponByCode($code);
            
            if (!$coupon) {
                return ['valid' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn'];
            }
            
            // Check usage limit
            if ($coupon['used_count'] >= $coupon['usage_limit']) {
                return ['valid' => false, 'message' => 'Mã giảm giá đã được sử dụng hết'];
            }
            
            // Check minimum order amount
            if ($orderAmount < $coupon['min_order_amount']) {
                return [
                    'valid' => false,
                    'message' => 'Đơn hàng phải tối thiểu ' . number_format($coupon['min_order_amount']) . ' VND'
                ];
            }
            
            return ['valid' => true, 'coupon' => $coupon];
            
        } catch (Exception $e) {
            error_log("Validate coupon error: " . $e->getMessage());
            return ['valid' => false, 'message' => 'Lỗi hệ thống'];
        }
    }
    
    // Calculate discount amount
    public function calculateDiscount($coupon, $amount) {
        if ($coupon['discount_type'] === 'percentage') {
            return $amount * $coupon['discount_value'] / 100;
        } else {
            return min($coupon['discount_value'], $amount);
        }
    }
    
    // Get all active coupons
    public function getActiveCoupons($limit = 20, $offset = 0) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM coupons 
                 WHERE is_active = TRUE AND expiry_date >= CURDATE()
                 ORDER BY created_at DESC
                 LIMIT ? OFFSET ?"
            );
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get active coupons error: " . $e->getMessage());
            return [];
        }
    }
    
    // Get all coupons (admin)
    public function getAllCoupons($limit = 20, $offset = 0, $filters = []) {
        try {
            $where = ["1=1"];
            $params = [];
            
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'active') {
                    $where[] = "is_active = TRUE AND expiry_date >= CURDATE()";
                } elseif ($filters['status'] === 'expired') {
                    $where[] = "expiry_date < CURDATE()";
                }
            }
            
            if (!empty($filters['code'])) {
                $where[] = "code LIKE ?";
                $params[] = '%' . $filters['code'] . '%';
            }
            
            $whereClause = implode(" AND ", $where);
            
            $sql = "SELECT * FROM coupons WHERE $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
            
            $stmt = $this->conn->prepare($sql);
            $paramIndex = 1;
            foreach ($params as $param) {
                $stmt->bindValue($paramIndex++, $param);
            }
            $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get all coupons error: " . $e->getMessage());
            return [];
        }
    }
    
    // Create coupon (admin)
    public function createCoupon($data) {
        try {
            if (empty($data['code']) || empty($data['discount_value']) || empty($data['expiry_date'])) {
                return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
            }
            
            // Check if code already exists
            $stmt = $this->conn->prepare("SELECT id FROM coupons WHERE code = ?");
            $stmt->execute([$data['code']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Mã giảm giá đã tồn tại'];
            }
            
            // Insert coupon
            $stmt = $this->conn->prepare(
                "INSERT INTO coupons (code, discount_type, discount_value, description, expiry_date, usage_limit, min_order_amount, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            
            $stmt->execute([
                strtoupper($data['code']),
                $data['discount_type'] ?? 'percentage',
                $data['discount_value'],
                $data['description'] ?? '',
                $data['expiry_date'],
                $data['usage_limit'] ?? 100,
                $data['min_order_amount'] ?? 0,
                $data['is_active'] ?? 1
            ]);
            
            return [
                'success' => true,
                'message' => 'Tạo mã giảm giá thành công',
                'coupon_id' => $this->conn->lastInsertId()
            ];
            
        } catch (Exception $e) {
            error_log("Create coupon error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Update coupon (admin)
    public function updateCoupon($id, $data) {
        try {
            $fields = [];
            $values = [];
            
            $allowedFields = ['discount_type', 'discount_value', 'description', 'expiry_date', 'usage_limit', 'min_order_amount', 'is_active'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'Không có dữ liệu để cập nhật'];
            }
            
            $values[] = $id;
            $sql = "UPDATE coupons SET " . implode(", ", $fields) . " WHERE id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);
            
            return ['success' => true, 'message' => 'Cập nhật mã giảm giá thành công'];
            
        } catch (Exception $e) {
            error_log("Update coupon error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Delete coupon (admin)
    public function deleteCoupon($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM coupons WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Xóa mã giảm giá thành công'];
        } catch (Exception $e) {
            error_log("Delete coupon error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Get coupon statistics
    public function getStatistics() {
        try {
            $stmt = $this->conn->query(
                "SELECT 
                    COUNT(*) as total_coupons,
                    SUM(CASE WHEN is_active = TRUE AND expiry_date >= CURDATE() THEN 1 ELSE 0 END) as active_coupons,
                    SUM(CASE WHEN expiry_date < CURDATE() THEN 1 ELSE 0 END) as expired_coupons,
                    SUM(used_count) as total_used
                FROM coupons"
            );
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get statistics error: " . $e->getMessage());
            return null;
        }
    }
}
