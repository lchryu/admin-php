<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';


if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    if($conn->query("DELETE FROM feedback WHERE id = $id")) {
        $_SESSION['message'] = "Xóa đánh giá thành công!";
        $_SESSION['type'] = "success";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra: " . $conn->error;
        $_SESSION['type'] = "danger";
    }
}

header('Location: index.php');
exit();