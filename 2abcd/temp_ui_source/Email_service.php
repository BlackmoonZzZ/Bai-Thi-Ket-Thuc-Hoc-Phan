<?php
/**
 * Email Service - Hệ thống gửi email tự động
 */

class EmailService {
    private $conn;
    private $from_email = 'noreply@gamekey.com';
    private $from_name = 'GameKey Store';
    private $admin_email = 'admin@gamekey.com';
    
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }
    
    /**
     * Gửi email xác nhận đơn hàng
     */
    public function sendOrderConfirmation($order_id) {
        try {
            // Lấy thông tin đơn hàng
            $stmt = $this->conn->prepare("
                SELECT o.*, u.email, u.fullname, u.username,
                       GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') as products
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE o.id = ?
                GROUP BY o.id
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) return false;
            
            $to_email = $order['email'];
            $customer_name = $order['fullname'] ?: $order['username'];
            $subject = "Xác nhận đơn hàng #{$order['order_number']} - GameKey Store";
            
            // Nội dung email
            $message = $this->getOrderConfirmationTemplate($order, $customer_name);
            
            // Gửi email
            $sent = $this->sendEmail($to_email, $subject, $message);
            
            // Log email
            $this->logEmail($to_email, $subject, 'order_confirmation', $order_id, $sent ? 'sent' : 'failed');
            
            return $sent;
            
        } catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Gửi key cho khách hàng sau khi thanh toán
     */
    public function sendGameKeys($order_id) {
        try {
            // Lấy thông tin đơn hàng và keys
            $stmt = $this->conn->prepare("
                SELECT o.*, u.email, u.fullname, u.username
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.status = 'completed'
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) return false;
            
            // Lấy danh sách keys đã gán
            $stmt = $this->conn->prepare("
                SELECT p.name, k.key_code, k.platform
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN product_keys k ON oi.id = k.order_item_id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$order_id]);
            $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($keys)) return false;
            
            $to_email = $order['email'];
            $customer_name = $order['fullname'] ?: $order['username'];
            $subject = "Game Keys của bạn - Đơn hàng #{$order['order_number']}";
            
            // Nội dung email
            $message = $this->getGameKeysTemplate($order, $customer_name, $keys);
            
            // Gửi email
            $sent = $this->sendEmail($to_email, $subject, $message);
            
            // Log email
            $this->logEmail($to_email, $subject, 'key_delivery', $order_id, $sent ? 'sent' : 'failed');
            
            return $sent;
            
        } catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Thông báo cho admin khi có đơn hàng mới
     */
    public function notifyAdminNewOrder($order_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT o.*, u.username, u.fullname, u.email as customer_email
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) return false;
            
            $subject = "Đơn hàng mới #{$order['order_number']} - Cần xử lý";
            $customer_name = $order['fullname'] ?: $order['username'];
            
            // Nội dung email cho admin
            $message = $this->getAdminNotificationTemplate($order, $customer_name);
            
            // Gửi email cho admin
            $sent = $this->sendEmail($this->admin_email, $subject, $message);
            
            // Log email
            $this->logEmail($this->admin_email, $subject, 'admin_notification', $order_id, $sent ? 'sent' : 'failed');
            
            return $sent;
            
        } catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Template email xác nhận đơn hàng
     */
    private function getOrderConfirmationTemplate($order, $customer_name) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
                .order-info { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .order-info h3 { color: #06b6d4; margin-top: 0; }
                .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
                .info-label { font-weight: bold; color: #64748b; }
                .info-value { color: #1e293b; }
                .total { font-size: 1.3rem; font-weight: bold; color: #10b981; }
                .footer { text-align: center; padding: 20px; color: #64748b; font-size: 0.9rem; }
                .button { display: inline-block; padding: 12px 30px; background: #06b6d4; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎮 GAMEKEY STORE</h1>
                    <p style="margin: 0;">Cảm ơn bạn đã đặt hàng!</p>
                </div>
                <div class="content">
                    <p>Xin chào <strong>' . htmlspecialchars($customer_name) . '</strong>,</p>
                    <p>Chúng tôi đã nhận được đơn hàng của bạn và đang xử lý. Dưới đây là thông tin chi tiết:</p>
                    
                    <div class="order-info">
                        <h3>📋 Thông tin đơn hàng</h3>
                        <div class="info-row">
                            <span class="info-label">Mã đơn hàng:</span>
                            <span class="info-value">#' . htmlspecialchars($order['order_number']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Ngày đặt:</span>
                            <span class="info-value">' . date('d/m/Y H:i', strtotime($order['created_at'])) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sản phẩm:</span>
                            <span class="info-value">' . htmlspecialchars($order['products']) . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tổng tiền:</span>
                            <span class="info-value total">' . number_format($order['total_amount']) . 'đ</span>
                        </div>
                        <div class="info-row" style="border-bottom: none;">
                            <span class="info-label">Trạng thái:</span>
                            <span class="info-value" style="color: #f59e0b; font-weight: bold;">Đang xử lý</span>
                        </div>
                    </div>
                    
                    <p><strong>Bước tiếp theo:</strong></p>
                    <ul>
                        <li>Chúng tôi sẽ xử lý đơn hàng trong vòng 5-30 phút</li>
                        <li>Bạn sẽ nhận được email chứa game key khi đơn hàng hoàn tất</li>
                        <li>Nếu có vấn đề, vui lòng liên hệ: support@gamekey.com</li>
                    </ul>
                    
                    <center>
                        <a href="https://yourdomain.com/orders/' . $order['id'] . '" class="button">Xem chi tiết đơn hàng</a>
                    </center>
                </div>
                <div class="footer">
                    <p>Email này được gửi tự động, vui lòng không trả lời.</p>
                    <p>© 2024 GameKey Store. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Template email gửi keys
     */
    private function getGameKeysTemplate($order, $customer_name, $keys) {
        $keys_html = '';
        foreach ($keys as $key) {
            $keys_html .= '
            <div style="background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #10b981; border-radius: 6px;">
                <div style="font-weight: bold; color: #06b6d4; margin-bottom: 5px;">
                    🎮 ' . htmlspecialchars($key['name']) . '
                </div>
                <div style="color: #64748b; font-size: 0.9rem; margin-bottom: 8px;">
                    Platform: ' . htmlspecialchars($key['platform']) . '
                </div>
                <div style="background: #f1f5f9; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 1.1rem; color: #1e293b;">
                    ' . htmlspecialchars($key['key_code']) . '
                </div>
            </div>';
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
                .footer { text-align: center; padding: 20px; color: #64748b; font-size: 0.9rem; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 Game Keys Của Bạn!</h1>
                    <p style="margin: 0;">Đơn hàng #' . htmlspecialchars($order['order_number']) . ' đã hoàn tất</p>
                </div>
                <div class="content">
                    <p>Xin chào <strong>' . htmlspecialchars($customer_name) . '</strong>,</p>
                    <p>Đơn hàng của bạn đã được xử lý thành công! Dưới đây là các game key:</p>
                    
                    ' . $keys_html . '
                    
                    <div style="background: #fef3c7; border: 1px solid #fbbf24; padding: 15px; border-radius: 6px; margin: 20px 0;">
                        <strong style="color: #92400e;">⚠️ Lưu ý quan trọng:</strong>
                        <ul style="margin: 10px 0; color: #92400e;">
                            <li>Hãy lưu lại email này hoặc copy keys ngay</li>
                            <li>Mỗi key chỉ có thể kích hoạt 1 lần duy nhất</li>
                            <li>Không chia sẻ key với người khác</li>
                            <li>Liên hệ support nếu key không hoạt động</li>
                        </ul>
                    </div>
                    
                    <p style="text-align: center; color: #64748b;">
                        Cảm ơn bạn đã mua sắm tại GameKey Store!<br>
                        Chúc bạn có những giờ phút giải trí vui vẻ! 🎮
                    </p>
                </div>
                <div class="footer">
                    <p>Cần hỗ trợ? Email: support@gamekey.com</p>
                    <p>© 2024 GameKey Store. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Template thông báo cho admin
     */
    private function getAdminNotificationTemplate($order, $customer_name) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f8f9fa; }
                .header { background: #ef4444; color: white; padding: 20px; text-align: center; }
                .content { background: white; padding: 20px; }
                .info-box { background: #f1f5f9; padding: 15px; margin: 10px 0; border-radius: 6px; }
                .button { display: inline-block; padding: 10px 20px; background: #06b6d4; color: white; text-decoration: none; border-radius: 6px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>🔔 ĐƠN HÀNG MỚI CẦN XỬ LÝ</h2>
                </div>
                <div class="content">
                    <h3>Thông tin đơn hàng</h3>
                    <div class="info-box">
                        <p><strong>Mã đơn:</strong> #' . htmlspecialchars($order['order_number']) . '</p>
                        <p><strong>Khách hàng:</strong> ' . htmlspecialchars($customer_name) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($order['customer_email']) . '</p>
                        <p><strong>Tổng tiền:</strong> ' . number_format($order['total_amount']) . 'đ</p>
                        <p><strong>Thời gian:</strong> ' . date('d/m/Y H:i:s', strtotime($order['created_at'])) . '</p>
                    </div>
                    <p style="text-align: center;">
                        <a href="https://yourdomain.com/admin/orders.php?id=' . $order['id'] . '" class="button">Xem & Xử lý đơn hàng</a>
                    </p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Hàm gửi email (sử dụng PHPMailer hoặc mail() function)
     */
    private function sendEmail($to, $subject, $message) {
        // Cấu hình headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>" . "\r\n";
        
        // Gửi email
        return mail($to, $subject, $message, $headers);
        
        // Nếu sử dụng PHPMailer, uncomment code sau:
        /*
        require 'vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com';
            $mail->Password = 'your-app-password';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            
            return $mail->send();
        } catch (Exception $e) {
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
            return false;
        }
        */
    }
    
    /**
     * Log email đã gửi
     */
    private function logEmail($to_email, $subject, $email_type, $order_id, $status, $error = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO email_logs (to_email, subject, email_type, order_id, status, error_message, sent_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $sent_at = $status == 'sent' ? date('Y-m-d H:i:s') : null;
            $stmt->execute([$to_email, $subject, $email_type, $order_id, $status, $error, $sent_at]);
        } catch (Exception $e) {
            error_log("Email Log Error: " . $e->getMessage());
        }
    }
}