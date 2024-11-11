<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "ID sản phẩm không hợp lệ!";
    $_SESSION['type'] = "danger";
    header("Location: index.php");
    exit();
}

$id = $conn->real_escape_string($_GET['id']);

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // 1. Xóa các record trong cart
    $conn->query("DELETE FROM cart WHERE product_id = $id");
    
    // 2. Xóa các record trong feedback
    $conn->query("DELETE FROM feedback WHERE product_id = $id");
    
    // 3. Xóa các record trong order_details
    $conn->query("DELETE FROM order_details WHERE product_id = $id");
    
    // 4. Xóa các record trong image
    $conn->query("DELETE FROM image WHERE product_id = $id");
    
    // 5. Cuối cùng mới xóa product
    $conn->query("DELETE FROM product WHERE id = $id");
    
    // Nếu mọi thứ OK thì commit
    $conn->commit();
    
    $_SESSION['message'] = "Đã xóa sản phẩm thành công!";
    $_SESSION['type'] = "success";
    
} catch (Exception $e) {
    // Nếu có lỗi thì rollback
    $conn->rollback();
    
    $_SESSION['message'] = "Có lỗi xảy ra: " . $e->getMessage();
    $_SESSION['type'] = "danger";
}

header("Location: index.php");
exit();
?>