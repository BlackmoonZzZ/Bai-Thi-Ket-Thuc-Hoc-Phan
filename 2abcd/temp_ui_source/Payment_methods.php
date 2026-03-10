<?php 
require_once __DIR__ . '/config/db.php';
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-credit-card"></i> Phương thức Thanh toán</h2>
</div>

<div class="row">
    <?php foreach($payment_methods as $pm): 
        $config = $pm['config'] ? json_decode($pm['config'], true) : [];
    ?>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header" style="background: <?= $pm['is_active'] ? 'linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(6, 182, 212, 0.1) 100%)' : 'rgba(107, 114, 128, 0.1)' ?>;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 style="margin: 0; color: <?= $pm['is_active'] ? '#06b6d4' : '#64748b' ?>;">
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
                            <i class="fas <?= $icons[$pm['method_code']] ?? 'fa-money-bill' ?>"></i>
                            <?= htmlspecialchars($pm['method_name']) ?>
                        </h5>
                    </div>
                    <span class="badge" style="background: <?= $pm['is_active'] ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : '#6b7280' ?>; padding: 6px 12px;">
                        <?= $pm['is_active'] ? 'Đang hoạt động' : 'Tắt' ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" class="mt-3">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $pm['id'] ?>">
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="active<?= $pm['id'] ?>" <?= $pm['is_active'] ? 'checked' : '' ?> style="cursor: pointer;">
                            <label class="form-check-label" for="active<?= $pm['id'] ?>" style="color: #e2e8f0; cursor: pointer;">
                                Kích hoạt phương thức này
                            </label>
                        </div>
                    </div>
                    
                    <?php if($pm['method_code'] == 'bank_transfer'): ?>
                        <div class="mb-3">
                            <label class="form-label">Thông tin tài khoản ngân hàng</label>
                            <textarea name="config" class="form-control" rows="6" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0; font-family: monospace;" placeholder="Ví dụ:
Ngân hàng: Vietcombank
Số tài khoản: 1234567890
Chủ tài khoản: NGUYEN VAN A
Chi nhánh: Hà Nội"><?= htmlspecialchars($pm['config'] ?: '') ?></textarea>
                            <small style="color: #94a3b8;">Thông tin này sẽ hiển thị cho khách hàng khi thanh toán</small>
                        </div>
                    <?php elseif(in_array($pm['method_code'], ['momo', 'zalopay', 'vnpay', 'paypal'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Cấu hình API (JSON)</label>
                            <textarea name="config" class="form-control" rows="6" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0; font-family: monospace;" placeholder='{"api_key": "your_api_key", "secret_key": "your_secret_key"}'><?= htmlspecialchars($pm['config'] ?: '') ?></textarea>
                            <small style="color: #94a3b8;">Nhập cấu hình API theo định dạng JSON</small>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label">Cấu hình</label>
                            <textarea name="config" class="form-control" rows="4" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;"><?= htmlspecialchars($pm['config'] ?: '') ?></textarea>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu cấu hình
                        </button>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testModal<?= $pm['id'] ?>">
                            <i class="fas fa-vial"></i> Test
                        </button>
                    </div>
                </form>
                
                <!-- Hiển thị thông tin cấu hình hiện tại -->
                <?php if($pm['config'] && $pm['method_code'] == 'bank_transfer'): ?>
                    <div class="mt-3 p-3" style="background: rgba(6, 182, 212, 0.05); border-left: 3px solid #06b6d4; border-radius: 4px;">
                        <h6 style="color: #06b6d4; margin-bottom: 10px;">
                            <i class="fas fa-info-circle"></i> Thông tin hiển thị cho KH:
                        </h6>
                        <pre style="color: #e2e8f0; margin: 0; white-space: pre-wrap; font-size: 0.9rem;"><?= htmlspecialchars($pm['config']) ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal Test Payment -->
    <div class="modal fade" id="testModal<?= $pm['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: #06b6d4;">
                        <i class="fas fa-vial"></i> Test <?= htmlspecialchars($pm['method_name']) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="color: #e2e8f0;">
                    <div class="alert alert-info" style="background: rgba(6, 182, 212, 0.1) !important; border-color: rgba(6, 182, 212, 0.3) !important;">
                        <i class="fas fa-info-circle"></i> Tính năng test thanh toán sẽ được triển khai trong phiên bản tiếp theo.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số tiền test (VNĐ)</label>
                        <input type="number" class="form-control" value="10000" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả giao dịch</label>
                        <input type="text" class="form-control" value="Test payment" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
                    </div>
                    
                    <?php if($pm['config']): ?>
                        <div class="mb-3">
                            <label class="form-label">Cấu hình hiện tại:</label>
                            <div style="background: rgba(6, 182, 212, 0.05); padding: 10px; border-radius: 6px; font-family: monospace; font-size: 0.85rem; max-height: 200px; overflow-y: auto;">
                                <?= nl2br(htmlspecialchars($pm['config'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" disabled>
                        <i class="fas fa-play"></i> Chạy test (Coming soon)
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Hướng dẫn cấu hình -->
<div class="card mt-4">
    <div class="card-header">
        <h5 style="margin: 0; color: #06b6d4;">
            <i class="fas fa-book"></i> Hướng dẫn cấu hình
        </h5>
    </div>
    <div class="card-body" style="color: #e2e8f0;">
        <div class="accordion accordion-flush" id="guideAccordion">
            <!-- Chuyển khoản ngân hàng -->
            <div class="accordion-item" style="background: transparent; border-color: rgba(6, 182, 212, 0.2);">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bank" style="background: rgba(6, 182, 212, 0.05); color: #e2e8f0;">
                        <i class="fas fa-university me-2" style="color: #06b6d4;"></i> Chuyển khoản ngân hàng
                    </button>
                </h2>
                <div id="bank" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body" style="background: rgba(6, 182, 212, 0.02);">
                        <p>Nhập thông tin tài khoản ngân hàng của bạn vào ô cấu hình. Ví dụ:</p>
                        <pre style="background: rgba(6, 182, 212, 0.05); padding: 15px; border-radius: 6px; color: #94a3b8;">Ngân hàng: Vietcombank
Số tài khoản: 1234567890
Chủ tài khoản: NGUYEN VAN A
Chi nhánh: Hà Nội
Nội dung CK: [Mã đơn hàng]</pre>
                    </div>
                </div>
            </div>
            
            <!-- MoMo -->
            <div class="accordion-item" style="background: transparent; border-color: rgba(6, 182, 212, 0.2);">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#momo" style="background: rgba(6, 182, 212, 0.05); color: #e2e8f0;">
                        <i class="fas fa-wallet me-2" style="color: #06b6d4;"></i> MoMo
                    </button>
                </h2>
                <div id="momo" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body" style="background: rgba(6, 182, 212, 0.02);">
                        <p>Để tích hợp MoMo Payment Gateway:</p>
                        <ol>
                            <li>Đăng ký tài khoản MoMo Business tại: <a href="https://business.momo.vn" target="_blank" style="color: #06b6d4;">https://business.momo.vn</a></li>
                            <li>Lấy Partner Code, Access Key, Secret Key từ MoMo Dashboard</li>
                            <li>Nhập cấu hình JSON:</li>
                        </ol>
                        <pre style="background: rgba(6, 182, 212, 0.05); padding: 15px; border-radius: 6px; color: #94a3b8;">{
  "partner_code": "YOUR_PARTNER_CODE",
  "access_key": "YOUR_ACCESS_KEY",
  "secret_key": "YOUR_SECRET_KEY",
  "endpoint": "https://test-payment.momo.vn/v2/gateway/api/create"
}</pre>
                    </div>
                </div>
            </div>
            
            <!-- VNPay -->
            <div class="accordion-item" style="background: transparent; border-color: rgba(6, 182, 212, 0.2);">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#vnpay" style="background: rgba(6, 182, 212, 0.05); color: #e2e8f0;">
                        <i class="fas fa-credit-card me-2" style="color: #06b6d4;"></i> VNPay
                    </button>
                </h2>
                <div id="vnpay" class="accordion-collapse collapse" data-bs-parent="#guideAccordion">
                    <div class="accordion-body" style="background: rgba(6, 182, 212, 0.02);">
                        <p>Để tích hợp VNPay:</p>
                        <ol>
                            <li>Đăng ký tài khoản tại: <a href="https://vnpay.vn" target="_blank" style="color: #06b6d4;">https://vnpay.vn</a></li>
                            <li>Lấy Terminal ID (TMN Code) và Hash Secret</li>
                            <li>Nhập cấu hình JSON:</li>
                        </ol>
                        <pre style="background: rgba(6, 182, 212, 0.05); padding: 15px; border-radius: 6px; color: #94a3b8;">{
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