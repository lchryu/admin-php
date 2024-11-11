<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WinMart Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media (min-width: 768px) {
            .vh-100-md {
                height: 100vh !important;
            }
        }
        
        .logout-btn {
            color: rgba(255,255,255,.75);
            transition: color 0.15s ease-in-out;
        }
        
        .logout-btn:hover {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">WinMart Admin</a>
        <div class="w-100"></div>
        <button class="navbar-toggler position-absolute d-md-none collapsed" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#sidebarMenu"
                style="right: 60px; top: 8px;">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <a href="/admin/logout.php" class="nav-link px-3 logout-btn" 
                   onclick="return confirm('Bạn có chắc muốn đăng xuất?')">
                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                </a>
            </div>
        </div>
    </header>