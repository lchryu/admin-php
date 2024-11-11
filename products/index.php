<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Câu truy vấn cơ bản với JOIN bảng image
$sql = "SELECT p.*, c.name as category_name,
        GROUP_CONCAT(i.url) as image_urls
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.id 
        LEFT JOIN image i ON p.id = i.product_id
        WHERE 1=1";

// Thêm điều kiện tìm kiếm
if ($search) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (p.code LIKE '%$search%' OR p.name LIKE '%$search%')";
}

if ($category_filter) {
    $sql .= " AND p.category_id = '$category_filter'";
}

$sql .= " GROUP BY p.id ORDER BY p.created_at DESC";

$result = $conn->query($sql);

// Lấy danh sách categories cho filter
$categories = $conn->query("SELECT * FROM category ORDER BY name");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý Sản phẩm</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="create.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Thêm Sản phẩm
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['type'] ?> alert-dismissible fade show">
        <?= $_SESSION['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
        unset($_SESSION['message']);
        unset($_SESSION['type']);
    endif; 
    ?>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Tìm theo mã, tên..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                                <?= $cat['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <?php if($search || $category_filter): ?>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ảnh</th>
                            <th>Mã SP</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th class="text-end">Giá (VNĐ)</th>
                            <th class="text-end">Giảm giá</th>
                            <th class="text-end">Số lượng</th>
                            <th>Hạn sử dụng</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $images = explode(',', $row['image_urls']);
                                $firstImage = !empty($images[0]) ? $images[0] : '../assets/img/no-image.jpg';
                            ?>
                            <tr>
                                <td>
                                    <img src="<?= $row['image_urls'] ?>" 
                                         alt="<?= htmlspecialchars($row['name']) ?>"
                                         class="img-thumbnail"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td><?= htmlspecialchars($row['code']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['category_name']) ?></td>
                                <td class="text-end fw-bold"><?= number_format($row['price']) ?></td>
                                <td class="text-end"><?= number_format($row['discount']) ?></td>
                                <td class="text-end"><?= number_format($row['quantity']) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['expiry'])) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-warning" 
                                           title="Sửa sản phẩm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-danger" 
                                           title="Xóa sản phẩm"
                                           onclick="return confirm('Bạn có chắc muốn xóa sản phẩm \'<?= htmlspecialchars($row['name']) ?>\'?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-3 text-muted">
                                    <i class="bi bi-inbox h3 d-block"></i>
                                    Không tìm thấy sản phẩm nào
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<!-- neu xoa category id = 5-> xoa toan bo product co categorie -->