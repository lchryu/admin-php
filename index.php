<?php
require_once 'config/db.php';

// Nếu đã đăng nhập thì chuyển đến dashboard
if(isset($_SESSION['admin_user'])) {
    header('Location: dashboard/');
    exit();
}

// Xử lý đăng nhập
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM user WHERE username = '$username' AND role_id = 1";
    $result = $conn->query($sql);
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Sử dụng password_verify để kiểm tra mật khẩu đã hash
        if(password_verify($password, $user['password'])) {
            $_SESSION['admin_user'] = $user;
            $_SESSION['admin_id'] = $user['id'];
            
            header('Location: dashboard/');
            exit();
        }
    }
    
    $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - WinMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .brand {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand h1 {
            color: #333;
            font-size: 24px;
        }
        .login-form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn-login {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="brand">
                <h1>WinMart Admin</h1>
                <p class="text-muted">Đăng nhập để quản lý hệ thống</p>
            </div>
            
            <div class="login-form">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $error ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Tên đăng nhập</label>
                        <input type="text" 
                               class="form-control" 
                               name="username" 
                               required 
                               autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" 
                               class="form-control" 
                               name="password" 
                               required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login">
                        Đăng nhập
                    </button>
                </form>
            </div>
            
            <div class="text-center mt-3">
                <a href="../" class="text-decoration-none">
                    &larr; Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- password chính xác -> rehash -> ko chính xác -->