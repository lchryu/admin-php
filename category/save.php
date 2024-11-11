<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = $conn->real_escape_string($_POST['name']);
    
    if($id > 0) {
        // Update
        $sql = "UPDATE category SET name = '$name' WHERE id = $id";
    } else {
        // Create
        $sql = "INSERT INTO category (name, created_at) VALUES ('$name', NOW())";
    }
    
    if($conn->query($sql)) {
        setFlashMessage(($id > 0) ? "Cập nhật danh mục thành công!" : "Thêm danh mục mới thành công!");
    } else {
        setFlashMessage("Có lỗi xảy ra: " . $conn->error, "danger");
    }
}

header('Location: index.php');