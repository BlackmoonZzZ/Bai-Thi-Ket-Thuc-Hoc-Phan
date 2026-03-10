<?php
require_once __DIR__ . '/../config/database.php';

class Auth
{
    /** @var PDO */
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register new user with validation
    public function register($username, $email, $password, $fullname = '')
    {
        try {
            // Input validation
            if (empty($username) || empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
            }

            if (strlen($username) < 3 || strlen($username) > 50) {
                return ['success' => false, 'message' => 'Tên đăng nhập phải từ 3-50 ký tự'];
            }

            if (strlen($password) < 6) {
                return ['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự'];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Email không hợp lệ'];
            }

            // Check if email/username already exists
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);

            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email hoặc tên đăng nhập đã tồn tại'];
            }

            // Hash password with bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

            // Insert new user
            $stmt = $this->conn->prepare(
                "INSERT INTO users (username, email, password, fullname, role, status, balance) 
                 VALUES (?, ?, ?, ?, 'customer', 'active', 0)"
            );

            if ($stmt->execute([$username, $email, $hashedPassword, $fullname])) {
                $userId = $this->conn->lastInsertId();

                // Log registration bonus in wallet_transactions
                $stmt = $this->conn->prepare(
                    "INSERT INTO wallet_transactions (user_id, amount, type, description, balance_before, balance_after, status) 
                     VALUES (?, 0, 'bonus', 'Đăng ký tài khoản', 0, 0, 'completed')"
                );
                $stmt->execute([$userId]);

                return ['success' => true, 'message' => 'Đăng ký thành công', 'user_id' => $userId];
            }

            return ['success' => false, 'message' => 'Đã xảy ra lỗi, vui lòng thử lại'];

        } catch (PDOException $e) {
            error_log("Register error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Login user with last_login tracking
    public function login($email, $password)
    {
        try {
            if (empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Vui lòng nhập email và mật khẩu'];
            }

            // Find user by email or username
            $stmt = $this->conn->prepare(
                "SELECT id, username, email, password, fullname, avatar, balance, role, status, last_login 
                 FROM users WHERE email = ? OR username = ?"
            );
            $stmt->execute([$email, $email]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
            }

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check account status
            if ($user['status'] === 'locked') {
                return ['success' => false, 'message' => 'Tài khoản của bạn đã bị khóa'];
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
            }

            // Update last_login
            $stmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['avatar'] = $user['avatar'];
            $_SESSION['balance'] = (float) $user['balance'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['phone'] = $user['phone'] ?? null;
            $_SESSION['status'] = $user['status'] ?? 'active';
            $_SESSION['created_at'] = $user['created_at'] ?? null;
            $_SESSION['last_login'] = date('Y-m-d H:i:s');
            $_SESSION['logged_in'] = true;

            return [
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'fullname' => $user['fullname'],
                    'avatar' => $user['avatar'],
                    'balance' => $user['balance'],
                    'role' => $user['role']
                ]
            ];

        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Logout user
    public static function logout()
    {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Đăng xuất thành công'];
    }

    // Update profile
    public function updateProfile($userId, $fullname, $phone = null)
    {
        try {
            if (empty($fullname)) {
                return ['success' => false, 'message' => 'Tên đầy đủ không được để trống'];
            }

            $stmt = $this->conn->prepare("UPDATE users SET fullname = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$fullname, $phone, $userId]);

            // Update session
            $_SESSION['fullname'] = $fullname;

            return ['success' => true, 'message' => 'Cập nhật thông tin thành công'];

        } catch (PDOException $e) {
            error_log("Update profile error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Change password
    public function changePassword($userId, $oldPassword, $newPassword)
    {
        try {
            if (empty($oldPassword) || empty($newPassword)) {
                return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
            }

            if (strlen($newPassword) < 6) {
                return ['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự'];
            }

            // Get current password
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($oldPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng'];
            }

            // Update password
            $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
            $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newHashedPassword, $userId]);

            return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];

        } catch (PDOException $e) {
            error_log("Change password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống'];
        }
    }

    // Check if user is logged in
    public static function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    // Check if user is admin
    public static function isAdmin()
    {
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    // Get current user
    public static function getUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        // Always fetch the latest balance from DB so it reflects admin-approved deposits
        $balance = $_SESSION['balance'] ?? 0;
        try {
            require_once __DIR__ . '/../config/database.php';
            /** @var PDO $db */
            $db = (new Database())->getConnection();
            $stmt = $db->prepare('SELECT balance FROM users WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                $balance = (float)$row['balance'];
                $_SESSION['balance'] = $balance; // refresh session cache
            }
        } catch (Exception $e) {
            // fallback to session balance if DB fails
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'fullname' => $_SESSION['fullname'] ?? '',
            'avatar' => $_SESSION['avatar'] ?? '',
            'balance' => $balance,
            'role' => $_SESSION['role'] ?? 'customer',
            'phone' => $_SESSION['phone'] ?? null,
            'status' => $_SESSION['status'] ?? 'active',
            'created_at' => $_SESSION['created_at'] ?? null,
            'last_login' => $_SESSION['last_login'] ?? null
        ];
    }

    // Get current user ID
    public static function getUserId()
    {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }
}
