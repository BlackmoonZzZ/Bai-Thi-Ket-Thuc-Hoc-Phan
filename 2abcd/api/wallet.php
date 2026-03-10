<?php
/**
 * Wallet API
 * Handles balance checking, deposits, transfers, and transaction history
 */

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/wallet.php';

try {
    if (!Auth::isLoggedIn()) {
        throw new Exception('Bạn cần đăng nhập');
    }
    
    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    $wallet = new Wallet();
    
    if (!$action) {
        throw new Exception('Hành động không được chỉ định');
    }
    
    switch ($action) {
        case 'balance':
            $balance = $wallet->getBalance($_SESSION['user_id']);
            
            echo json_encode([
                'success' => true,
                'balance' => $balance,
                'formatted' => number_format($balance, 0, ',', '.')
            ]);
            break;
        
        case 'deposit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $amount = (float)($_POST['amount'] ?? 0);
            $description = $_POST['description'] ?? 'Nạp tiền vào ví';
            
            if ($amount <= 0 || $amount > 1000000000) {
                throw new Exception('Số tiền không hợp lệ');
            }
            
            // In production, integrate with payment gateway here
            $result = $wallet->deposit($_SESSION['user_id'], $amount, $description);
            
            if ($result['success']) {
                $_SESSION['balance'] = $result['new_balance'];
            }
            
            echo json_encode($result);
            break;

        case 'request_deposit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $amount = (float)($_POST['amount'] ?? 0);
            $paymentMethod = $_POST['payment_method'] ?? 'bank';
            $transactionCode = $_POST['transaction_code'] ?? null;
            
            if ($amount < 10000) {
                throw new Exception('Số tiền tối thiểu là 10.000đ');
            }
            
            $result = $wallet->createDepositRequest($_SESSION['user_id'], $amount, $paymentMethod, $transactionCode);
            echo json_encode($result);
            break;

        case 'approve_deposit':
            // Admin only
            require_once __DIR__ . '/../includes/auth.php';
            if (empty($_SESSION['admin_id'])) {
                throw new Exception('Không có quyền thực hiện');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            $transactionId = (int)($_POST['transaction_id'] ?? 0);
            if (!$transactionId) throw new Exception('ID giao dịch không hợp lệ');
            $result = $wallet->approveDeposit($transactionId, $_SESSION['admin_id']);
            echo json_encode($result);
            break;

        case 'reject_deposit':
            require_once __DIR__ . '/../includes/auth.php';
            if (empty($_SESSION['admin_id'])) {
                throw new Exception('Không có quyền thực hiện');
            }
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            $transactionId = (int)($_POST['transaction_id'] ?? 0);
            $reason = $_POST['reason'] ?? '';
            if (!$transactionId) throw new Exception('ID giao dịch không hợp lệ');
            $result = $wallet->rejectDeposit($transactionId, $reason);
            echo json_encode($result);
            break;
        
        case 'transfer':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }
            
            $toUserId = (int)($_POST['to_user_id'] ?? 0);
            $amount = (float)($_POST['amount'] ?? 0);
            $description = $_POST['description'] ?? 'Chuyển tiền';
            
            if ($toUserId <= 0) {
                throw new Exception('Người nhận không hợp lệ');
            }
            
            if ($amount <= 0) {
                throw new Exception('Số tiền không hợp lệ');
            }
            
            $result = $wallet->transfer($_SESSION['user_id'], $toUserId, $amount, $description);
            
            if ($result['success']) {
                $_SESSION['balance'] = $result['from_balance'];
            }
            
            echo json_encode($result);
            break;
        
        case 'transactions':
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            $type = $_GET['type'] ?? null;
            
            if ($limit > 100) $limit = 100;
            if ($offset < 0) $offset = 0;
            
            $transactions = $wallet->getTransactions($_SESSION['user_id'], $limit, $offset, $type);
            $total = $wallet->getTransactionCount($_SESSION['user_id']);
            
            echo json_encode([
                'success' => true,
                'transactions' => $transactions,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
            break;
        
        case 'history':
            $limit = (int)($_GET['limit'] ?? 10);
            $offset = (int)($_GET['offset'] ?? 0);
            
            if ($limit > 50) $limit = 50;
            if ($offset < 0) $offset = 0;
            
            $transactions = $wallet->getTransactions($_SESSION['user_id'], $limit, $offset);
            
            echo json_encode([
                'success' => true,
                'transactions' => $transactions,
                'count' => count($transactions)
            ]);
            break;
        
        case 'statistics':
            $stats = $wallet->getStatistics($_SESSION['user_id']);
            
            echo json_encode([
                'success' => true,
                'statistics' => $stats
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
