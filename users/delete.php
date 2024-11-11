<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Không cho xóa admin
        $check = $conn->query("SELECT role_id FROM user WHERE id = $id");
        if($user = $check->fetch_assoc()) {
            if($user['role_id'] == 1) {
                setFlashMessage("Không thể xóa tài khoản Admin!", "danger");
                header('Location: index.php');
                exit();
            }
        }
        // khi 1 user bị xoá -> tất cả bảng chứa user_id

        // 1. Xóa payment liên quan đến orders của user
        $conn->query("DELETE FROM payment WHERE orders_id IN (SELECT id FROM orders WHERE user_id = $id)");

        // 2. Xóa order_details liên quan đến orders của user
        $conn->query("DELETE FROM order_details WHERE orders_id IN (SELECT id FROM orders WHERE user_id = $id)");

        // 3. Xóa orders
        $conn->query("DELETE FROM orders WHERE user_id = $id");

        // 4. Xóa cart
        $conn->query("DELETE FROM cart WHERE user_id = $id");

        // 5. Xóa feedback
        $conn->query("DELETE FROM feedback WHERE user_id = $id");

        // 6. Xóa image -> 1 1 -> produ
        $conn->query("DELETE FROM image, WHERE user_id = $id");

        // 7. Cuối cùng xóa user
        if($conn->query("DELETE FROM user WHERE id = $id")) {
            // Nếu mọi thứ OK thì commit transaction
            $conn->commit();
            setFlashMessage("Xóa người dùng thành công!");
        } else {
            throw new Exception($conn->error);
        }

    } catch (Exception $e) {
        // Nếu có lỗi thì rollback
        $conn->rollback();
        setFlashMessage("Có lỗi xảy ra: " . $e->getMessage(), "danger");
    }
}

header('Location: index.php');


// 1. Xóa payment (phụ thuộc vào orders)
// 2. Xóa order_details (phụ thuộc vào orders)
// 3. Xóa orders (phụ thuộc vào user)
// 4. Xóa cart (phụ thuộc vào user)
// 5. Xóa feedback (phụ thuộc vào user) 
// 6. Xóa image (phụ thuộc vào user)
// 7. Cuối cùng xóa user