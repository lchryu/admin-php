<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID sản phẩm không hợp lệ!";
    $_SESSION['type'] = "danger";
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $conn->real_escape_string($_POST['code']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];
    $expiry = $_POST['expiry'];
    $image_url = $conn->real_escape_string(trim($_POST['image_url']));
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Cập nhật thông tin sản phẩm
        $sql = "UPDATE product SET 
                code = '$code',
                name = '$name',
                description = '$description',
                price = $price,
                discount = $discount,
                quantity = $quantity,
                category_id = $category_id,
                expiry = '$expiry'
                WHERE id = $id";
        
        $conn->query($sql);
        
        // Cập nhật URL ảnh trong bảng image
        if (!empty($image_url)) {
            $conn->query("UPDATE image SET url = '$image_url' WHERE product_id = $id");
        }
        
        $conn->commit();
        $_SESSION['message'] = "Cập nhật sản phẩm thành công!";
        $_SESSION['type'] = "success";
        header("Location: index.php");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "Có lỗi xảy ra: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }
}

// Lấy thông tin sản phẩm
$sql = "SELECT p.*, c.name as category_name, i.url as image_url
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.id
        LEFT JOIN image i ON p.id = i.product_id 
        WHERE p.id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $_SESSION['message'] = "Không tìm thấy sản phẩm!";
    $_SESSION['type'] = "danger";
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();

// Lấy danh sách danh mục cho dropdown
$categories = $conn->query("SELECT * FROM category ORDER BY name");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Sửa Sản phẩm</h1>
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

    <div class="row">
        <div class="col-md-8">
            <form method="POST" class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Mã sản phẩm</label>
                            <input type="text" name="code" class="form-control" 
                                   value="<?= htmlspecialchars($product['code']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Danh mục</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Chọn danh mục</option>
                                <?php while($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['id'] ?>" 
                                            <?= ($cat['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                        <?= $cat['name'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" 
                                  rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Giá (VNĐ)</label>
                            <input type="number" name="price" class="form-control" 
                                   value="<?= $product['price'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giảm giá (VNĐ)</label>
                            <input type="number" name="discount" class="form-control" 
                                   value="<?= $product['discount'] ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số lượng</label>
                            <input type="number" name="quantity" class="form-control" 
                                   value="<?= $product['quantity'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hạn sử dụng</label>
                            <input type="date" name="expiry" class="form-control" 
                                   value="<?= $product['expiry'] ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL Ảnh sản phẩm</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                     style="height: 30px; width: 30px; object-fit: cover;" 
                                     onerror="this.src='../assets/img/no-image.jpg'">
                            </span>
                            <input type="text" name="image_url" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($product['image_url']) ?>"
                                   placeholder="Nhập URL ảnh">
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="index.php" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.addEventListener('input', function(e) {
    if (e.target.matches('input[name="image_url"]')) {
        const img = e.target.parentNode.querySelector('img');
        if (e.target.value.trim()) {
            img.src = e.target.value;
        } else {
            img.src = '../assets/img/no-image.jpg';
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>