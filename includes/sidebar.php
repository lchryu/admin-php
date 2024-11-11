<?php
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($current_path, '/'));
$current_page = !empty($segments[1]) ? $segments[1] : 'dashboard';
?>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light collapse vh-100-md border-end p-0">
            <div class="list-group list-group-flush mt-2">
                <a href="../dashboard"
                    class="list-group-item list-group-item-action py-3 <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-house-door me-2"></i>
                    Dashboard
                </a>

                <a href="../category"
                    class="list-group-item list-group-item-action py-3 <?= $current_page === 'category' ? 'active' : '' ?>">
                      <i class="bi bi-grid me-2"></i>
                    Danh mục
                </a>

                <a href="../products"
                    class="list-group-item list-group-item-action py-3 <?= $current_page === 'products' ? 'active' : '' ?>">
                    <i class="bi bi-box me-2"></i>
                    Sản phẩm
                </a>

                <a href="../orders"
                    class="list-group-item list-group-item-action py-3 <?= $current_page === 'orders' ? 'active' : '' ?>">
                    <i class="bi bi-cart me-2"></i>
                    Đơn hàng
                </a>

                <a href="../users"
                    class="list-group-item list-group-item-action py-3 <?= $current_page === 'users' ? 'active' : '' ?>">
                    <i class="bi bi-people me-2"></i>
                    Tài khoản
                </a>
                <a href="../revenue"
                    class="list-group-item list-group-item-action py-3 <?= $current_page === 'revenue' ? 'active' : '' ?>">
                    <i class="bi bi-graph-up me-2"></i>
                    Doanh thu
                </a>

                <a href="../feedbacks"
                    class="list-group-item list-group-item-action py-3 <?= $current_page === 'feedbacks' ? 'active' : '' ?>">
                    <i class="bi bi-chat-dots me-2"></i>
                    Đánh giá
                </a>
            </div>
        </nav>