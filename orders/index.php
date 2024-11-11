<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Xử lý search và filter
$where = "1=1";
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (o.code LIKE '%$search%' OR u.username LIKE '%$search%')";
}

if(isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $where .= " AND o.status = '$status'";
}

// Query orders với thông tin user
$sql = "SELECT o.*, u.username 
        FROM orders o
        LEFT JOIN user u ON o.user_id = u.id 
        WHERE $where
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý Đơn hàng</h1>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Tìm theo mã đơn hoặc tên khách hàng..."
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="processing" <?= (isset($_GET['status']) && $_GET['status'] == 'processing') ? 'selected' : '' ?>>Đang xử lý</option>
                        <option value="completed" <?= (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : '' ?>>Hoàn thành</option>
                        <option value="cancelled" <?= (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <?php if(isset($_GET['search']) || isset($_GET['status'])): ?>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['code']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= number_format($row['total_price']) ?>đ</td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        switch($row['status']) {
                                            case 'pending':
                                                $status_class = 'warning';
                                                $status_text = 'Chờ xử lý';
                                                break;
                                            case 'processing':
                                                $status_class = 'info';
                                                $status_text = 'Đang xử lý';
                                                break;
                                            case 'completed':
                                                $status_class = 'success';
                                                $status_text = 'Hoàn thành';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'danger';
                                                $status_text = 'Đã hủy';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $status_class ?>"><?= $status_text ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', $row['created_at']) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info" onclick="viewOrder(<?= $row['id'] ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="updateStatus(<?= $row['id'] ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- View Order Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết Đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetail">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cập nhật trạng thái</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm" method="POST" action="update_status.php">
                    <input type="hidden" name="order_id" id="order_id">
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select" required>
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrder(id) {
    fetch('view.php?id=' + id)
        .then(response => response.text())
        .then(html => {
            document.getElementById('orderDetail').innerHTML = html;
            var modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        });
}

function updateStatus(id) {
    document.getElementById('order_id').value = id;
    var modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}
</script>

<?php include '../includes/footer.php'; ?>