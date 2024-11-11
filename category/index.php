<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Xử lý tìm kiếm
$where = "1=1";
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND c.name LIKE '%$search%'";
}

// Query categories với số lượng sản phẩm
$sql = "SELECT c.*, COUNT(p.id) as product_count 
        FROM category c
        LEFT JOIN product p ON c.id = p.category_id 
        WHERE $where
        GROUP BY c.id
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- Flash Messages -->
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mt-3" role="alert">
        <?= $flash['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý Danh mục</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg"></i> Thêm Danh mục
        </button>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Tìm kiếm danh mục..."
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <?php if(isset($_GET['search'])): ?>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên danh mục</th>
                            <th class="text-center">Số sản phẩm</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            <?= $row['product_count'] ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="editCategory(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="deleteCategory(<?= $row['id'] ?>, <?= $row['product_count'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm/Sửa Danh mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" method="POST" action="save.php">
                <div class="modal-body">
                    <input type="hidden" name="id" id="category_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" name="name" id="category_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name) {
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = name;
    new bootstrap.Modal(document.getElementById('addModal')).show();
}

function deleteCategory(id, productCount) {
    let message = 'Bạn có chắc muốn xóa danh mục này?';
    if(productCount > 0) {
        message = `Cảnh báo: Danh mục này đang có ${productCount} sản phẩm. Nếu xóa danh mục, tất cả sản phẩm thuộc danh mục này cũng sẽ bị xóa!\n\nBao gồm bảng(cart, oder_details, feedback, image)\n\nBạn sẽ mất mát rất nhiều dữ liệu trong db \n\nBạn có chắc chắn muốn xóa?`;
    }
    if(confirm(message)) {
        window.location.href = 'delete.php?id=' + id;
    }
}

// Reset form khi mở modal thêm mới
document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('categoryForm').reset();
    document.getElementById('category_id').value = '';
});

// Auto hide alert
document.addEventListener('DOMContentLoaded', function() {
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(function() {
            alert.classList.remove('show');
            setTimeout(function() {
                alert.remove();
            }, 150);
        }, 3000);
    }
});
</script>

<?php include '../includes/footer.php'; ?>