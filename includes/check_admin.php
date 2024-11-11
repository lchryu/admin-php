<?php
// includes/check_admin.php

// Bắt đầu session nếu chưa bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_user'])) {
    header('Location: /admin/');
    exit();
}

// Kiểm tra role (chỉ cho phép admin - role_id = 1)
if($_SESSION['admin_user']['role_id'] != 1) {
    header('Location: /admin/');
    exit();
}
?>