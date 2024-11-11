<?php
require_once 'config/db.php';

// Xóa toàn bộ session
session_unset();
session_destroy();

// Chuyển hướng về trang login
header('Location: index.php');
exit();
?>