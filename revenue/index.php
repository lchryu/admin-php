<?php
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Thống kê đơn hàng theo trạng thái
$status_stats = $conn->query("
    SELECT 
        status,
        COUNT(*) as total_orders,
        SUM(total_price) as total_amount
    FROM orders 
    GROUP BY status
");

// Thống kê theo ngày hiện tại
$current_date = date('Y-m-d');
$daily_revenue = $conn->query("
    SELECT SUM(total_price) as revenue
    FROM orders 
    WHERE status = 'completed'
    AND DATE(FROM_UNIXTIME(created_at)) = '$current_date'
")->fetch_assoc();

// Thống kê theo tháng hiện tại
$current_month = date('Y-m');
$monthly_revenue = $conn->query("
    SELECT SUM(total_price) as revenue
    FROM orders 
    WHERE status = 'completed'
    AND DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m') = '$current_month'
")->fetch_assoc();

// Thống kê theo năm hiện tại
$current_year = date('Y');
$yearly_revenue = $conn->query("
    SELECT SUM(total_price) as revenue
    FROM orders 
    WHERE status = 'completed'
    AND YEAR(FROM_UNIXTIME(created_at)) = '$current_year'
")->fetch_assoc();

// Top 5 sản phẩm bán chạy
$top_products = $conn->query("
    SELECT 
        p.name,
        COUNT(od.id) as order_count,
        SUM(od.quantity) as total_quantity,
        SUM(od.quantity * od.price) as total_revenue
    FROM order_details od
    JOIN orders o ON od.orders_id = o.id
    JOIN product p ON od.product_id = p.id
    WHERE o.status = 'completed'
    GROUP BY p.id
    ORDER BY total_revenue DESC
    LIMIT 5
");

// Thống kê doanh thu 12 tháng gần nhất
$monthly_stats = $conn->query("
    SELECT 
        DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m') as month,
        SUM(total_price) as revenue,
        COUNT(*) as order_count
    FROM orders 
    WHERE status = 'completed'
    AND created_at >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 12 MONTH))
    GROUP BY month
    ORDER BY month DESC
");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Thống kê doanh thu</h1>
    </div>

    <!-- Thống kê theo ngày/tháng/năm -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-info mb-3">
                <div class="card-body">
                    <h5 class="card-title text-info">Doanh thu hôm nay</h5>
                    <h3 class="card-text"><?= number_format($daily_revenue['revenue']) ?>đ</h3>
                    <p class="text-muted"><?= date('d/m/Y') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success mb-3">
                <div class="card-body">
                    <h5 class="card-title text-success">Doanh thu tháng này</h5>
                    <h3 class="card-text"><?= number_format($monthly_revenue['revenue']) ?>đ</h3>
                    <p class="text-muted">Tháng <?= date('m/Y') ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title text-primary">Doanh thu năm nay</h5>
                    <h3 class="card-text"><?= number_format($yearly_revenue['revenue']) ?>đ</h3>
                    <p class="text-muted">Năm <?= date('Y') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê đơn hàng theo trạng thái -->
    <div class="row mb-4">
        <?php while($stat = $status_stats->fetch_assoc()): 
            $status_class = '';
            $status_text = '';
            switch($stat['status']) {
                case 'pending':
                    $status_class = 'warning';
                    $status_text = 'Chờ xử lý';
                    break;
                case 'processing':
                    $status_class = 'info';
                    $status_text = 'Đang xử lý';
                    break;
                case 'completed':
                    $status_class = 'success';
                    $status_text = 'Hoàn thành';
                    break;
                case 'cancelled':
                    $status_class = 'danger';
                    $status_text = 'Đã hủy';
                    break;
            }
        ?>
        <div class="col-md-3">
            <div class="card border-<?= $status_class ?> mb-3">
                <div class="card-body">
                    <h5 class="card-title text-<?= $status_class ?>"><?= $status_text ?></h5>
                    <p class="card-text">
                        Số đơn: <?= number_format($stat['total_orders']) ?><br>
                        Doanh thu: <?= number_format($stat['total_amount']) ?>đ
                    </p>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Thống kê doanh thu 12 tháng -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Doanh thu 12 tháng gần nhất</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th class="text-end">Số đơn hàng</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($month = $monthly_stats->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('m/Y', strtotime($month['month'] . '-01')) ?></td>
                            <td class="text-end"><?= number_format($month['order_count']) ?></td>
                            <td class="text-end"><?= number_format($month['revenue']) ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top sản phẩm bán chạy -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Top 5 sản phẩm bán chạy</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Số đơn hàng</th>
                            <th class="text-end">Số lượng bán</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($product = $top_products->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td class="text-end"><?= number_format($product['order_count']) ?></td>
                            <td class="text-end"><?= number_format($product['total_quantity']) ?></td>
                            <td class="text-end"><?= number_format($product['total_revenue']) ?>đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>