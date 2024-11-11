<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    
    if ($conn->query($sql)) {
        $_SESSION['message'] = "Cập nhật trạng thái thành công!";
        $_SESSION['type'] = "success";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra: " . $conn->error;
        $_SESSION['type'] = "danger";
    }
}

header('Location: index.php');
exit();
?>