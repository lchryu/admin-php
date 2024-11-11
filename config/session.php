<?php
// config/session.php

// Kiểm tra trước khi start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        
        // Xóa message sau khi đã lấy ra
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Thêm các hàm tiện ích khác cho session
function setAdminUser($user) {
    $_SESSION['admin_user'] = $user;
    $_SESSION['admin_id'] = $user['id'];
}

function getAdminUser() {
    return isset($_SESSION['admin_user']) ? $_SESSION['admin_user'] : null;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_user']) && $_SESSION['admin_user']['role_id'] == 1;
}

function clearSession() {
    session_unset();
    session_destroy();
}
?>