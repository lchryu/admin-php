<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $username = $conn->real_escape_string($_POST['username']);
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $role_id = (int)$_POST['role_id'];
    
    // Kiểm tra username đã tồn tại chưa
    $check = $conn->query("SELECT id FROM user WHERE username = '$username' AND id != $id");
    if($check->num_rows > 0) {
        setFlashMessage("Username đã tồn tại!", "danger");
        header('Location: index.php');
        exit();
    }
    
    if($id > 0) {
        // Update
        $sql = "UPDATE user SET 
                username = '$username',
                fullname = '$fullname',
                email = '$email',
                phone = '$phone',
                address = '$address',
                role_id = $role_id";
        
        // Nếu có nhập mật khẩu mới
        if(!empty($_POST['password'])) {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $hashed_password = $conn->real_escape_string($hashed_password);
            $sql .= ", password = '$hashed_password'";
        }
        
        $sql .= " WHERE id = $id";
    } else {
        // Create
        if(empty($_POST['password'])) {
            setFlashMessage("Mật khẩu không được để trống!", "danger");
            header('Location: index.php');
            exit();
        }
        
        // Mã hoá mật khẩu trước khi lưu
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $hashed_password = $conn->real_escape_string($hashed_password);
        
        $sql = "INSERT INTO user (username, fullname, email, phone, address, password, role_id, created_at)
                VALUES ('$username', '$fullname', '$email', '$phone', '$address', '$hashed_password', $role_id, NOW())";
    }
    
    if($conn->query($sql)) {
        setFlashMessage(($id > 0) ? "Cập nhật thành công!" : "Thêm mới thành công!");
    } else {
        setFlashMessage("Có lỗi xảy ra: " . $conn->error, "danger");
    }
}

header('Location: index.php');

// new code