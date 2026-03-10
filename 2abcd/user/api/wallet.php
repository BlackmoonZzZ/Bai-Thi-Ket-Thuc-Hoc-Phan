<?php
/**
 * User Wallet API
 * Handles deposit operations for user section
 */

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/wallet.php';
require_once __DIR__ . '/../../config/db.php';

try {
    if (!Auth::isLoggedIn()) {
        throw new Exception('Bạn cần đăng nhập');
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    $userId = $_SESSION['user_id'];

    switch ($action) {
        case 'request_deposit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            $amount = intval($_POST['amount'] ?? 0);
            if ($amount < 10000) {
                throw new Exception('Số tiền tối thiểu là 10.000đ');
            }

            $wallet = new Wallet();
            $result = $wallet->createDepositRequest($userId, $amount, 'bank');

            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'reference_id' => $result['reference_id'],
                    'amount' => $result['amount'],
                    'message' => $result['message']
                ]);
            } else {
                throw new Exception($result['message']);
            }
            break;

        // NOTE: Direct deposit endpoint removed for security.
        // Use 'request_deposit' instead (requires admin approval).

        case 'balance':
            $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $balance = $stmt->fetchColumn();

            echo json_encode([
                'success' => true,
                'balance' => $balance
            ]);
            break;

        case 'history':
            $limit = intval($_GET['limit'] ?? 20);
            $stmt = $conn->prepare("SELECT * FROM wallet_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
            $stmt->bindValue(1, $userId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'transactions' => $transactions
            ]);
            break;

        default:
            throw new Exception('Hành động không hợp lệ');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}