<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Xử lý tìm kiếm
$where = "1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (f.content LIKE '%$search%')";
}

if (isset($_GET['rating']) && !empty($_GET['rating'])) {
    $rating = (int)$_GET['rating'];
    $where .= " AND f.star = $rating";
}

// Query danh sách feedback
$sql = "SELECT f.*, u.username, p.name as product_name 
        FROM feedback f
        LEFT JOIN user u ON f.user_id = u.id 
        LEFT JOIN product p ON f.product_id = p.id
        WHERE $where
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý Đánh giá</h1>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Tìm theo nội dung..."
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <select name="rating" class="form-select">
                        <option value="">Tất cả đánh giá</option>
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= (isset($_GET['rating']) && $_GET['rating'] == $i) ? 'selected' : '' ?>>
                                <?= $i ?> sao
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <?php if(isset($_GET['search']) || isset($_GET['rating'])): ?>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Feedback Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Sản phẩm</th>
                            <th>Nội dung</th>
                            <th>Đánh giá</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['content']) ?></td>
                                    <td>
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= ($i <= $row['star']) ? '-fill text-warning' : '' ?>"></i>
                                        <?php endfor; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewFeedback(<?= $row['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteFeedback(<?= $row['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết Đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="feedbackDetail">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewFeedback(id) {
    fetch(`view.php?id=${id}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('feedbackDetail').innerHTML = html;
            var modal = new bootstrap.Modal(document.getElementById('viewModal'));
            modal.show();
        })
        .catch(error => console.error('Error:', error));
}

function deleteFeedback(id) {
    if(confirm('Bạn có chắc muốn xóa đánh giá này?')) {
        window.location.href = `delete.php?id=${id}`;
    }
}
</script>

<?php include '../includes/footer.php'; ?>