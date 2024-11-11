<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Thống kê đơn hàng hôm nay
$today = date('Y-m-d');
$today_orders = $conn->query("
    SELECT COUNT(*) as total, status
    FROM orders 
    WHERE DATE(FROM_UNIXTIME(created_at)) = '$today'
    GROUP BY status
")->fetch_all(MYSQLI_ASSOC);

// Thống kê sản phẩm
$products_stats = $conn->query("
    SELECT 
        COUNT(*) as total_products,
        SUM(quantity) as total_quantity
    FROM product
")->fetch_assoc();

// Top 5 sản phẩm được đánh giá cao nhất
$top_rated = $conn->query("
    SELECT 
        p.name,
        p.code,
        COUNT(f.id) as review_count,
        AVG(f.star) as avg_rating
    FROM product p
    LEFT JOIN feedback f ON p.id = f.product_id
    GROUP BY p.id
    HAVING review_count > 0
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 5
");

// Feedback mới nhất
$latest_feedback = $conn->query("
    SELECT 
        f.*,
        u.username,
        p.name as product_name
    FROM feedback f
    JOIN user u ON f.user_id = u.id
    JOIN product p ON f.product_id = p.id
    ORDER BY f.created_at DESC
    LIMIT 5
");
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <!-- <button type="button" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download"></i> Xuất báo cáo
                </button> -->
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Đơn hàng hôm nay</h6>
                    <h3 class="mb-0">
                        <?php
                        $total_today = 0;
                        foreach($today_orders as $order) {
                            $total_today += $order['total'];
                        }
                        echo $total_today;
                        ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6 class="card-title">Tổng sản phẩm</h6>
                    <h3 class="mb-0"><?= number_format($products_stats['total_products']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6 class="card-title">Tổng tồn kho</h6>
                    <h3 class="mb-0"><?= number_format($products_stats['total_quantity']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6 class="card-title">Đánh giá mới</h6>
                    <h3 class="mb-0"><?= $latest_feedback->num_rows ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top sản phẩm được đánh giá cao -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Top sản phẩm được đánh giá cao</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số đánh giá</th>
                                    <th class="text-center">Điểm TB</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($product = $top_rated->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                                        <small class="text-muted"><?= $product['code'] ?></small>
                                    </td>
                                    <td class="text-center"><?= $product['review_count'] ?></td>
                                    <td class="text-center">
                                        <?php 
                                        $stars = round($product['avg_rating']);
                                        for($i = 1; $i <= 5; $i++) {
                                            echo $i <= $stars ? '★' : '☆';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đánh giá mới nhất -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Đánh giá mới nhất</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Người dùng</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Đánh giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($feedback = $latest_feedback->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($feedback['username']) ?></td>
                                    <td><?= htmlspecialchars($feedback['product_name']) ?></td>
                                    <td class="text-center">
                                        <?php 
                                        for($i = 1; $i <= 5; $i++) {
                                            echo $i <= $feedback['star'] ? '★' : '☆';
                                        }
                                        ?>
                                        <div class="small text-muted"><?= htmlspecialchars($feedback['content']) ?></div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ đơn hàng theo trạng thái hôm nay -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng hôm nay theo trạng thái</h5>
                    <div class="row text-center">
                        <?php
                        $status_colors = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $status_names = [
                            'pending' => 'Chờ xử lý',
                            'processing' => 'Đang xử lý',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ];
                        foreach($today_orders as $order): ?>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="text-<?= $status_colors[$order['status']] ?> display-6">
                                        <?= $order['total'] ?>
                                    </div>
                                    <div><?= $status_names[$order['status']] ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>