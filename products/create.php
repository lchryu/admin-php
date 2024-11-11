<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

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
        // Thêm sản phẩm mới
        $sql = "INSERT INTO product (code, name, description, price, discount, quantity, 
                category_id, expiry, year, created_at) 
                VALUES ('$code', '$name', '$description', $price, $discount, $quantity, 
                $category_id, '$expiry', NOW(), NOW())";
        
        $conn->query($sql);
        $product_id = $conn->insert_id;
        
        // Thêm URL ảnh vào bảng image
        if (!empty($image_url)) {
            $conn->query("INSERT INTO image (url, user_id, product_id, created_at) 
                         VALUES ('$image_url', {$_SESSION['admin_id']}, $product_id, NOW())");
        }
        
        $conn->commit();
        $_SESSION['message'] = "Thêm sản phẩm thành công!";
        $_SESSION['type'] = "success";
        header("Location: index.php");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = "Có lỗi xảy ra: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }
}

// Lấy danh sách danh mục cho dropdown
$categories = $conn->query("SELECT * FROM category ORDER BY name");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Thêm Sản phẩm Mới</h1>
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
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Danh mục</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Chọn danh mục</option>
                                <?php while($cat = $categories->fetch_assoc()): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Giá (VNĐ)</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giảm giá (VNĐ)</label>
                            <input type="number" name="discount" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Số lượng</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hạn sử dụng</label>
                            <input type="date" name="expiry" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">URL Ảnh sản phẩm</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <img src="../assets/img/no-image.jpg" 
                                     style="height: 30px; width: 30px; object-fit: cover;"
                                     onerror="handleImageError(this)"
                                     data-original-url="">
                            </span>
                            <input type="text" name="image_url" 
                                   class="form-control" 
                                   placeholder="Nhập URL ảnh">
                        </div>
                        <div class="alert alert-danger image-error" style="display: none; font-size: 12px; margin-top: 5px; padding: 5px 10px;"></div>
                    </div>

                    <div class="text-end">
                        <a href="index.php" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Thêm Sản phẩm</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function handleImageError(img) {
    const originalUrl = img.getAttribute('data-original-url');
    img.src = '../assets/img/no-image.jpg';
    
    if (originalUrl) {
        const errorAlert = img.closest('.mb-3').querySelector('.image-error');
        errorAlert.textContent = `Link ảnh không hợp lệ: ${originalUrl}`;
        errorAlert.style.display = 'block';
    }
}

document.addEventListener('input', function(e) {
    if (e.target.matches('input[name="image_url"]')) {
        const container = e.target.closest('.mb-3');
        const img = container.querySelector('img');
        const errorAlert = container.querySelector('.image-error');
        const newUrl = e.target.value.trim();
        
        errorAlert.style.display = 'none';
        
        if (newUrl) {
            img.setAttribute('data-original-url', newUrl);
            img.src = newUrl;
        } else {
            img.src = '../assets/img/no-image.jpg';
            img.setAttribute('data-original-url', '');
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>