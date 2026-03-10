<?php
require_once __DIR__ . '/../config/database.php';

class Wallet
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get wallet balance
    public function getBalance($userId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? (float) $user['balance'] : 0;
        } catch (Exception $e) {
            error_log("Get balance error: " . $e->getMessage());
            return 0;
        }
    }

    // Deposit money to wallet
    public function deposit($userId, $amount, $description = 'Nạp tiền vào ví')
    {
        try {
            if ($amount <= 0) {
                return ['success' => false, 'message' => 'Số tiền phải lớn hơn 0'];
            }

            $this->conn->beginTransaction();

            // Get current balance
            $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Người dùng không tồn tại");
            }

            $balanceBefore = (float) $user['balance'];
            $balanceAfter = $balanceBefore + $amount;

            // Update balance
            $stmt = $this->conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$balanceAfter, $userId]);

            // Log transaction
            $stmt = $this->conn->prepare(
                "INSERT INTO wallet_transactions (user_id, amount, type, description, balance_before, balance_after, status) 
                 VALUES (?, ?, 'deposit', ?, ?, ?, 'completed')"
            );
            $stmt->execute([$userId, $amount, $description, $balanceBefore, $balanceAfter]);

            $transactionId = $this->conn->lastInsertId();

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Nạp tiền thành công',
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'new_balance' => $balanceAfter
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Deposit error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Transfer between wallets
    public function transfer($fromUserId, $toUserId, $amount, $description = 'Chuyển tiền')
    {
        try {
            if ($amount <= 0) {
                return ['success' => false, 'message' => 'Số tiền phải lớn hơn 0'];
            }

            $this->conn->beginTransaction();

            // Check balance
            $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$fromUserId]);
            $fromUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$fromUser || $fromUser['balance'] < $amount) {
                throw new Exception("Số dư không đủ");
            }

            // Get to user
            $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$toUserId]);
            $toUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$toUser) {
                throw new Exception("Người nhận không tồn tại");
            }

            $fromBalanceBefore = (float) $fromUser['balance'];
            $fromBalanceAfter = $fromBalanceBefore - $amount;

            $toBalanceBefore = (float) $toUser['balance'];
            $toBalanceAfter = $toBalanceBefore + $amount;

            // Update both balances
            $stmt = $this->conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$fromBalanceAfter, $fromUserId]);

            $stmt = $this->conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$toBalanceAfter, $toUserId]);

            // Log transactions
            $stmt = $this->conn->prepare(
                "INSERT INTO wallet_transactions (user_id, amount, type, description, balance_before, balance_after, status) 
                 VALUES (?, ?, 'transfer', ?, ?, ?, 'completed')"
            );
            $stmt->execute([$fromUserId, $amount, $description . ' (đi)', $fromBalanceBefore, $fromBalanceAfter]);

            $stmt = $this->conn->prepare(
                "INSERT INTO wallet_transactions (user_id, amount, type, description, balance_before, balance_after, status) 
                 VALUES (?, ?, 'transfer', ?, ?, ?, 'completed')"
            );
            $stmt->execute([$toUserId, $amount, $description . ' (đến)', $toBalanceBefore, $toBalanceAfter]);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Chuyển tiền thành công',
                'from_balance' => $fromBalanceAfter,
                'to_balance' => $toBalanceAfter
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Transfer error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get transaction history
    public function getTransactions($userId, $limit = 20, $offset = 0, $type = null)
    {
        try {
            $where = "WHERE user_id = ?";
            $params = [$userId];

            if ($type) {
                $where .= " AND type = ?";
                $params[] = $type;
            }

            $stmt = $this->conn->prepare(
                "SELECT * FROM wallet_transactions $where ORDER BY created_at DESC LIMIT ? OFFSET ?"
            );
            $paramIndex = 1;
            foreach ($params as $param) {
                $stmt->bindValue($paramIndex++, $param);
            }
            $stmt->bindValue($paramIndex++, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue($paramIndex++, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get transactions error: " . $e->getMessage());
            return [];
        }
    }

    // Get transaction count
    public function getTransactionCount($userId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM wallet_transactions WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (Exception $e) {
            error_log("Get transaction count error: " . $e->getMessage());
            return 0;
        }
    }

    // Get wallet statistics
    public function getStatistics($userId)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT 
                    SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as total_deposited,
                    SUM(CASE WHEN type = 'purchase' THEN amount ELSE 0 END) as total_spent,
                    SUM(CASE WHEN type = 'refund' THEN amount ELSE 0 END) as total_refunded,
                    SUM(CASE WHEN type = 'bonus' THEN amount ELSE 0 END) as total_bonus,
                    COUNT(*) as transaction_count
                FROM wallet_transactions WHERE user_id = ?"
            );
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get statistics error: " . $e->getMessage());
            return null;
        }
    }

    // Add bonus to wallet (admin function)
    public function addBonus($userId, $amount, $reason = 'Thưởng')
    {
        try {
            if ($amount <= 0) {
                return ['success' => false, 'message' => 'Số tiền phải lớn hơn 0'];
            }

            $this->conn->beginTransaction();

            // Get current balance
            $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Người dùng không tồn tại");
            }

            $balanceBefore = (float) $user['balance'];
            $balanceAfter = $balanceBefore + $amount;

            // Update balance
            $stmt = $this->conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$balanceAfter, $userId]);

            // Log transaction
            $stmt = $this->conn->prepare(
                "INSERT INTO wallet_transactions (user_id, amount, type, description, balance_before, balance_after, status) 
                 VALUES (?, ?, 'bonus', ?, ?, ?, 'completed')"
            );
            $stmt->execute([$userId, $amount, $reason, $balanceBefore, $balanceAfter]);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Đã thêm thưởng thành công',
                'new_balance' => $balanceAfter
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Add bonus error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Create pending deposit request (user submits after transfer)
    public function createDepositRequest($userId, $amount, $paymentMethod = 'bank', $transactionCode = null)
    {
        try {
            if ($amount < 10000) {
                return ['success' => false, 'message' => 'Số tiền tối thiểu là 10.000đ'];
            }

            // Generate unique reference code
            $referenceId = 'DEP_' . strtoupper(substr(md5(uniqid('', true)), 0, 8));

            $description = "Yêu cầu nạp tiền qua $paymentMethod";
            if ($transactionCode) {
                $description .= " - Mã GD: $transactionCode";
            }

            $stmt = $this->conn->prepare(
                "INSERT INTO wallet_transactions (user_id, amount, type, reference_id, description, status, created_at) 
                 VALUES (?, ?, 'deposit', ?, ?, 'pending', NOW())"
            );
            $stmt->execute([$userId, $amount, $referenceId, $description]);

            $requestId = $this->conn->lastInsertId();

            return [
                'success' => true,
                'message' => 'Đã gửi yêu cầu nạp tiền, chờ Admin duyệt',
                'request_id' => $requestId,
                'reference_id' => $referenceId,
                'amount' => $amount
            ];

        } catch (Exception $e) {
            error_log("Create deposit request error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()];
        }
    }

    // Get all pending deposits (for admin)
    public function getPendingDeposits($limit = 50, $offset = 0)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT wt.*, u.username, u.email, u.fullname 
                 FROM wallet_transactions wt 
                 JOIN users u ON wt.user_id = u.id 
                 WHERE wt.type = 'deposit' AND wt.status = 'pending' 
                 ORDER BY wt.created_at DESC 
                 LIMIT ? OFFSET ?"
            );
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get pending deposits error: " . $e->getMessage());
            return [];
        }
    }

    // Count pending deposits
    public function countPendingDeposits()
    {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM wallet_transactions WHERE type = 'deposit' AND status = 'pending'");
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    // Approve deposit request (admin)
    public function approveDeposit($transactionId, $adminId = null)
    {
        try {
            $this->conn->beginTransaction();

            // Get the pending transaction
            $stmt = $this->conn->prepare(
                "SELECT * FROM wallet_transactions WHERE id = ? AND type = 'deposit' AND status = 'pending'"
            );
            $stmt->execute([$transactionId]);
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$transaction) {
                throw new Exception("Yêu cầu nạp tiền không tồn tại hoặc đã được xử lý");
            }

            $userId = $transaction['user_id'];
            $amount = (float) $transaction['amount'];

            // Get current balance
            $stmt = $this->conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Người dùng không tồn tại");
            }

            $balanceBefore = (float) $user['balance'];
            $balanceAfter = $balanceBefore + $amount;

            // Update user balance
            $stmt = $this->conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$balanceAfter, $userId]);

            // Update transaction status
            $stmt = $this->conn->prepare(
                "UPDATE wallet_transactions SET status = 'completed', balance_before = ?, balance_after = ? WHERE id = ?"
            );
            $stmt->execute([$balanceBefore, $balanceAfter, $transactionId]);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Đã duyệt nạp tiền thành công',
                'amount' => $amount,
                'new_balance' => $balanceAfter
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Approve deposit error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Reject deposit request (admin)
    public function rejectDeposit($transactionId, $reason = '')
    {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE wallet_transactions SET status = 'failed', description = CONCAT(description, ' - Từ chối: ', ?) WHERE id = ? AND type = 'deposit' AND status = 'pending'"
            );
            $result = $stmt->execute([$reason, $transactionId]);

            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Yêu cầu không tồn tại hoặc đã được xử lý'];
            }

            return ['success' => true, 'message' => 'Đã từ chối yêu cầu nạp tiền'];

        } catch (Exception $e) {
            error_log("Reject deposit error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
