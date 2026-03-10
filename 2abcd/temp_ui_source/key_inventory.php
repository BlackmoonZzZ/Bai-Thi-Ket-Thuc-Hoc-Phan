<?php 
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php'; 

// Lấy danh sách game với số key
$search = $_GET['search'] ?? '';
$sql = "SELECT p.id, p.name, p.image, p.price,
               COUNT(pk.id) as total_keys,
               SUM(CASE WHEN pk.status='available' THEN 1 ELSE 0 END) as available_keys,
               SUM(CASE WHEN pk.status='sold' THEN 1 ELSE 0 END) as sold_keys
        FROM products p
        LEFT JOIN product_keys pk ON p.id = pk.product_id
        GROUP BY p.id";

if(!empty($search)) {
    $sql .= " HAVING p.name LIKE :search_name";
    $stmt = $conn->prepare($sql . " ORDER BY p.id DESC");
    $stmt->execute([':search_name' => "%$search%"]);
    $games = $stmt->fetchAll();
} else {
    $games = $conn->query($sql . " ORDER BY p.id DESC")->fetchAll();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 style="color: #06b6d4;"><i class="fas fa-warehouse"></i> Kho Quản lý Key Game</h2>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Tìm game..." value="<?= htmlspecialchars($search) ?>" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);">
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">GAME</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">GIÁ</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">TỔNG KEY</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">SẴN SÀNG</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">ĐÃ BÁN</th>
                        <th style="color: #06b6d4; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 15px;">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($games)): ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td colspan="6" style="padding: 20px; text-align: center; color: #a78bfa;">
                                <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                Chưa có game nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($games as $g): 
                            $total = $g['total_keys'] ?? 0;
                            $available = $g['available_keys'] ?? 0;
                            $sold = $g['sold_keys'] ?? 0;
                            $status_color = $available > 0 ? '#10b981' : '#ef4444';
                        ?>
                        <tr style="border-bottom: 1px solid rgba(6, 182, 212, 0.1);">
                            <td style="padding: 15px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="<?= $g['image'] ?>" width="40" height="40" style="border-radius: 4px; object-fit: cover; border: 1px solid rgba(6, 182, 212, 0.2);" alt="<?= $g['name'] ?>">
                                    <strong style="color: #e2e8f0;"><?= $g['name'] ?></strong>
                                </div>
                            </td>
                            <td style="padding: 15px; color: #ec4899; font-weight: 700;"><?= number_format($g['price']) ?>đ</td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); padding: 6px 12px;">
                                    <?= $total ?> Keys
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 6px 12px; color: white;">
                                    <i class="fas fa-check-circle"></i> <?= $available ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <span class="badge" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); padding: 6px 12px; color: white;">
                                    <i class="fas fa-check"></i> <?= $sold ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <div style="display: flex; gap: 6px;">
                                    <a href="product_keys.php?id=<?= $g['id'] ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; text-decoration: none; transition: all 0.3s ease;" title="Quản lý Key">
                                        <i class="fas fa-key"></i> Quản lý
                                    </a>
                                    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addKeyModal<?= $g['id'] ?>" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; font-weight: 600; padding: 6px 10px; border-radius: 6px; transition: all 0.3s ease;" title="Thêm Key">
                                        <i class="fas fa-plus"></i> Thêm
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Thêm Key -->
                        <div class="modal fade" id="addKeyModal<?= $g['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(6, 182, 212, 0.2);">
                                    <div class="modal-header" style="border-bottom: 1px solid rgba(6, 182, 212, 0.2);">
                                        <h5 class="modal-title" style="color: #06b6d4;">Thêm Key cho: <?= $g['name'] ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
                                    </div>
                                    <form method="POST" action="product_keys.php?id=<?= $g['id'] ?>">
                                        <div class="modal-body" style="color: #e2e8f0;">
                                            <div class="mb-3">
                                                <label class="form-label">Danh sách Key (Mỗi key 1 dòng)</label>
                                                <textarea name="keys" class="form-control" rows="10" placeholder="AAAA-BBBB-CCCC&#10;XXXX-YYYY-ZZZZ" required style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); color: #e2e8f0;"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="border-top: 1px solid rgba(6, 182, 212, 0.2);">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-primary">Thêm Key</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
