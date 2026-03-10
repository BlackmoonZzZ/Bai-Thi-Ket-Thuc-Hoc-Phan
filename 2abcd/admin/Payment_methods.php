<?php 
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/includes/header.php'; 

// Xử lý cập nhật phương thức thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update') {
        $id = $_POST['id'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $config = $_POST['config'] ?? '';
        
        $stmt = $conn->prepare("UPDATE payment_methods SET is_active = ?, config = ? WHERE id = ?");
        $stmt->execute([$is_active, $config, $id]);
        
        echo "<script>alert('Cập nhật thành công!'); window.location='Payment_methods.php';</script>";
    }
}

// Lấy danh sách phương thức thanh toán
$payment_methods = $conn->query("SELECT * FROM payment_methods ORDER BY display_order")->fetchAll();
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.page-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
}
.content-card {
    background: var(--bg-surface);
    border-radius: 14px;
    padding: 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: none;
    margin-bottom: 30px;
}
.card-header-styled {
    padding: 20px 25px;
    border-bottom: 1px solid #EEF2F7;
    border-radius: 14px 14px 0 0;
}
.active-header {
    background: rgba(0, 182, 155, 0.05);
}
.inactive-header {
    background: #F8F9FB;
}
.method-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
}
.method-title-active { color: var(--text-dark); }
.method-title-inactive { color: var(--text-gray); }

.badge-status { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
.badge-active { background: rgba(0, 182, 155, 0.15); color: var(--success); }
.badge-inactive { background: #EEF2F7; color: var(--text-gray); }

.card-body-styled {
    padding: 25px;
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
}
.form-control {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 10px 15px;
    color: var(--text-dark);
    font-family: monospace;
}
.form-control:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}

.form-check-label {
    font-weight: 600;
    color: var(--text-dark);
}
.accordion-button {
    font-weight: 600;
    color: var(--text-dark);
    background: var(--bg-surface);
    box-shadow: none !important;
}
.accordion-button:not(.collapsed) {
    color: var(--primary);
    background: #F8F9FB;
}
.accordion-body {
    color: var(--text-dark);
    background: #FFFFFF;
}
.guide-pre {
    background: #F8F9FB;
    padding: 15px;
    border-radius: 10px;
    color: var(--text-dark);
    border: 1px solid #EEF2F7;
    font-family: monospace;
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title"><i class="fas fa-credit-card text-primary me-2"></i> Payment Methods</h2>
</div>

<div class="row">
    <?php foreach($payment_methods as $pm): 
        $config = $pm['config'] ? json_decode($pm['config'], true) : [];
    ?>
    <div class="col-md-6 mb-4">
        <div class="content-card">
            <div class="card-header-styled <?= $pm['is_active'] ? 'active-header' : 'inactive-header' ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="method-title <?= $pm['is_active'] ? 'method-title-active' : 'method-title-inactive' ?>">
                            <?php
                            $icons = [
                                'bank_transfer' => 'fa-university',
                                'momo' => 'fa-wallet',
                                'zalopay' => 'fa-wallet',
                                'vnpay' => 'fa-credit-card',
                                'paypal' => 'fa-paypal',
                                'balance' => 'fa-coins'
                            ];
                            ?>
                            <i class="fas <?= $icons[$pm['method_code']] ?? 'fa-money-bill' ?> me-2 text-primary"></i>
                            <?= htmlspecialchars($pm['method_name']) ?>
                        </h5>
                    </div>
                    <span class="badge-status <?= $pm['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $pm['is_active'] ? '<i class="fas fa-check-circle me-1"></i> Active' : 'Inactive' ?>
                    </span>
                </div>
            </div>
            <div class="card-body-styled">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $pm['id'] ?>">
                    
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="active<?= $pm['id'] ?>" <?= $pm['is_active'] ? 'checked' : '' ?> style="cursor: pointer; width: 2.5em; height: 1.25em;">
                            <label class="form-check-label ms-2 mt-1" for="active<?= $pm['id'] ?>" style="cursor: pointer;">
                                Enable this payment method
                            </label>
                        </div>
                    </div>
                    
                    <?php if($pm['method_code'] == 'bank_transfer'): ?>
                        <div class="mb-4">
                            <label class="form-label text-gray">Bank Account Information</label>
                            <textarea name="config" class="form-control" rows="6" placeholder="Example:
Bank: Vietcombank
Account No: 1234567890
Name: NGUYEN VAN A
Branch: Hanoi"><?= htmlspecialchars($pm['config'] ?: '') ?></textarea>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i>This information will be displayed to customers during checkout.</small>
                        </div>
                    <?php elseif(in_array($pm['method_code'], ['momo', 'zalopay', 'vnpay', 'paypal'])): ?>
                        <div class="mb-4">
                            <label class="form-label text-gray">API Configuration (JSON)</label>
                            <textarea name="config" class="form-control" rows="6" placeholder='{"api_key": "your_api_key", "secret_key": "your_secret_key"}'><?= htmlspecialchars($pm['config'] ?: '') ?></textarea>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-code me-1"></i>Enter API configuration in JSON format.</small>
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <label class="form-label text-gray">Configuration</label>
                            <textarea name="config" class="form-control" rows="4"><?= htmlspecialchars($pm['config'] ?: '') ?></textarea>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="fas fa-save me-1"></i> Save Config
                        </button>
                        <button type="button" class="btn btn-info fw-bold text-white px-4" data-bs-toggle="modal" data-bs-target="#testModal<?= $pm['id'] ?>">
                            <i class="fas fa-vial me-1"></i> Test
                        </button>
                    </div>
                </form>
                
                <!-- Hiển thị thông tin cấu hình hiện tại -->
                <?php if($pm['config'] && $pm['method_code'] == 'bank_transfer'): ?>
                    <div class="mt-4 p-3 rounded-3" style="background: #F8F9FB; border-left: 4px solid var(--primary);">
                        <h6 class="text-primary fw-bold mb-2">
                            <i class="fas fa-eye me-1"></i> Customer View:
                        </h6>
                        <pre class="m-0 text-dark" style="white-space: pre-wrap; font-size: 0.9rem; font-family: monospace;"><?= htmlspecialchars($pm['config']) ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal Test Payment -->
    <div class="modal fade" id="testModal<?= $pm['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header" style="background: #F8F9FB; border-bottom: 1px solid #EEF2F7; border-radius: 16px 16px 0 0; padding: 20px 25px;">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-vial text-info me-2"></i> Test <?= htmlspecialchars($pm['method_name']) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-dark">
                    <div class="alert alert-info rounded-3 mb-4" style="background: rgba(0, 188, 212, 0.1); border: 1px solid rgba(0, 188, 212, 0.2); color: #00bcd4;">
                        <i class="fas fa-info-circle me-1"></i> Payment testing feature will be available in the next version.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Test Amount (VNĐ)</label>
                        <input type="number" class="form-control bg-light" value="10000" style="border-radius: 8px;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Transaction Description</label>
                        <input type="text" class="form-control bg-light" value="Test payment" style="border-radius: 8px;">
                    </div>
                    
                    <?php if($pm['config']): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Config:</label>
                            <div class="guide-pre" style="max-height: 200px; overflow-y: auto;">
                                <?= nl2br(htmlspecialchars($pm['config'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer bg-light" style="border-top: 1px solid #EEF2F7; border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary fw-bold" disabled>
                        <i class="fas fa-play me-1"></i> Run Test (Coming soon)
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Hướng dẫn cấu hình -->
<div class="content-card mt-2">
    <div class="card-header-styled bg-white">
        <h5 class="fw-bold text-dark m-0">
            <i class="fas fa-book text-primary me-2"></i> Configuration Guide
        </h5>
    </div>
    <div class="card-body-styled">
        <div class="accordion accordion-flush" id="guideAccordion">
            <!-- Chuyển khoản ngân hàng -->
            <div class="accordion-item border-0 mb-2 rounded-3" style="overflow: hidden; border: 1px solid #EEF2F7 !important;">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#bank">
                        <i class="fas fa-university me-2 text-primary"></i> Bank Transfer
                    </button>
                </h2>
                <div id="bank" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p class="mb-3 text-gray fw-500">Enter your bank account information in the configuration box. Example:</p>
                        <pre class="guide-pre">Bank: Vietcombank
Account No: 1234567890
Name: NGUYEN VAN A
Branch: Hanoi
Transfer Content: [Order_ID]</pre>
                    </div>
                </div>
            </div>
            
            <!-- MoMo -->
            <div class="accordion-item border-0 mb-2 rounded-3" style="overflow: hidden; border: 1px solid #EEF2F7 !important;">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#momo">
                        <i class="fas fa-wallet me-2 text-primary"></i> MoMo
                    </button>
                </h2>
                <div id="momo" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p class="mb-3 text-gray fw-500">To integrate MoMo Payment Gateway:</p>
                        <ol class="mb-3 text-gray fw-500">
                            <li>Register for a MoMo Business account at: <a href="https://business.momo.vn" target="_blank" class="text-primary fw-bold text-decoration-none">business.momo.vn</a></li>
                            <li>Get the Partner Code, Access Key, and Secret Key from the MoMo Dashboard.</li>
                            <li>Enter the JSON configuration:</li>
                        </ol>
                        <pre class="guide-pre">{
  "partner_code": "YOUR_PARTNER_CODE",
  "access_key": "YOUR_ACCESS_KEY",
  "secret_key": "YOUR_SECRET_KEY",
  "endpoint": "https://test-payment.momo.vn/v2/gateway/api/create"
}</pre>
                    </div>
                </div>
            </div>
            
            <!-- VNPay -->
            <div class="accordion-item border-0 rounded-3" style="overflow: hidden; border: 1px solid #EEF2F7 !important;">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#vnpay">
                        <i class="fas fa-credit-card me-2 text-primary"></i> VNPay
                    </button>
                </h2>
                <div id="vnpay" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body">
                        <p class="mb-3 text-gray fw-500">To integrate VNPay:</p>
                        <ol class="mb-3 text-gray fw-500">
                            <li>Register for an account at: <a href="https://vnpay.vn" target="_blank" class="text-primary fw-bold text-decoration-none">vnpay.vn</a></li>
                            <li>Get the Terminal ID (TMN Code) and Hash Secret.</li>
                            <li>Enter the JSON configuration:</li>
                        </ol>
                        <pre class="guide-pre">{
  "tmn_code": "YOUR_TMN_CODE",
  "hash_secret": "YOUR_HASH_SECRET",
  "url": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html",
  "return_url": "https://yourdomain.com/payment/vnpay_return.php"
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
