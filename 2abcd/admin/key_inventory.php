<?php 
require_once __DIR__ . '/../config/db.php';
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
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: none;
    margin-bottom: 30px;
}
.search-box {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 10px 15px;
    color: var(--text-dark);
}
.search-box:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
.table th {
    color: var(--text-gray);
    font-weight: 700;
    border-bottom: 2px solid #EEF2F7;
    padding: 15px;
    text-transform: uppercase;
    font-size: 0.85rem;
    background: transparent;
}
.table td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #EEF2F7;
    color: var(--text-dark);
    font-weight: 500;
}
.table-hover tbody tr:hover {
    background-color: #F8F9FB;
}
.badge-total { background: rgba(72, 128, 255, 0.15); color: var(--primary); padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
.badge-available { background: rgba(0, 182, 155, 0.15); color: var(--success); padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }
.badge-sold { background: rgba(97, 100, 107, 0.15); color: var(--text-gray); padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem; }

.btn-action {
    padding: 6px 10px;
    border-radius: 6px;
    border: none;
    font-weight: 600;
    transition: 0.2s;
}
.btn-manage { background: rgba(72, 128, 255, 0.1); color: var(--primary); }
.btn-manage:hover { background: var(--primary); color: white; }
.btn-add { background: rgba(254, 197, 61, 0.1); color: var(--warning); }
.btn-add:hover { background: var(--warning); color: white; }

/* Modal Styling */
.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.modal-header {
    background: #F8F9FB;
    border-bottom: 1px solid #EEF2F7;
    border-radius: 16px 16px 0 0;
    padding: 20px 25px;
}
.modal-title {
    font-weight: 800;
    color: var(--text-dark);
}
.modal-body {
    padding: 25px;
    color: var(--text-dark);
}
.modal-footer {
    border-top: 1px solid #EEF2F7;
    padding: 20px 25px;
}
.form-label {
    font-weight: 600;
    color: var(--text-dark);
}
.form-control, .form-select {
    background: #F8F9FB;
    border: 1px solid #EEF2F7;
    border-radius: 10px;
    padding: 10px 15px;
    color: var(--text-dark);
}
.form-control:focus, .form-select:focus {
    background: #FFFFFF;
    border-color: var(--primary);
    box-shadow: 0 0 0 0.25rem rgba(72, 128, 255, 0.25);
}
</style>

<div class="page-header mt-2">
    <h2 class="page-title"><i class="fas fa-warehouse text-primary me-2"></i> Key Inventory</h2>
</div>

<div class="row mb-4">
    <div class="col-md-5">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control search-box" placeholder="Search games..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">GAME</th>
                        <th>PRICE</th>
                        <th>TOTAL KEYS</th>
                        <th>AVAILABLE</th>
                        <th>SOLD</th>
                        <th class="text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($games)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-gray fw-bold">
                                <div class="mb-3"><i class="fas fa-inbox fa-3x"></i></div>
                                No games found in inventory.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($games as $g): 
                            $total = $g['total_keys'] ?? 0;
                            $available = $g['available_keys'] ?? 0;
                            $sold = $g['sold_keys'] ?? 0;
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= htmlspecialchars($g['image']) ?>" width="45" height="45" class="rounded-3 shadow-sm" style="object-fit: cover; border: 1px solid #EEF2F7;" alt="<?= htmlspecialchars($g['name']) ?>">
                                    <strong class="text-dark fs-6"><?= htmlspecialchars($g['name']) ?></strong>
                                </div>
                            </td>
                            <td class="text-primary fw-bold fs-6"><?= number_format($g['price']) ?>đ</td>
                            <td>
                                <span class="badge-total d-inline-block">
                                    <?= $total ?> Keys
                                </span>
                            </td>
                            <td>
                                <span class="badge-available d-inline-block">
                                    <i class="fas fa-check-circle me-1"></i> <?= $available ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-sold d-inline-block">
                                    <i class="fas fa-shopping-cart me-1"></i> <?= $sold ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="product_keys.php?id=<?= $g['id'] ?>" class="btn-action btn-manage text-decoration-none d-inline-flex align-items-center" title="Manage Keys">
                                        <i class="fas fa-key me-1"></i> Manage
                                    </a>
                                    <button class="btn-action btn-add d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addKeyModal<?= $g['id'] ?>" title="Add Keys">
                                        <i class="fas fa-plus me-1"></i> Add
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Thêm Key -->
                        <div class="modal fade" id="addKeyModal<?= $g['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Keys for: <?= htmlspecialchars($g['name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="product_keys.php?id=<?= $g['id'] ?>">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Key List (One key per line) <span class="text-danger">*</span></label>
                                                <textarea name="keys" class="form-control font-monospace" rows="10" placeholder="AAAA-BBBB-CCCC&#10;XXXX-YYYY-ZZZZ" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light rounded-bottom-4">
                                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary fw-bold">Add Keys</button>
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

