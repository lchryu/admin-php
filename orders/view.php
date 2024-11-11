<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if(!isset($_GET['id'])) {
    die('ID không hợp lệ');
}

$id = (int)$_GET['id'];

// Query thông tin đơn hàng
$sql = "SELECT o.*, u.username, u.phone, u.email 
        FROM orders o
        LEFT JOIN user u ON o.user_id = u.id 
        WHERE o.id = $id";
$result = $conn->query($sql);
$order = $result->fetch_assoc();

// Query chi tiết đơn hàng
$sql = "SELECT od.*, p.name as product_name 
        FROM order_details od
        LEFT JOIN product p ON od.product_id = p.id 
        WHERE od.orders_id = $id";
$details = $conn->query($sql);

if($order):
?>
    <div class="row">
        <div class="col-md-6">
            <h6 class="border-bottom pb-2">Thông tin khách hàng</h6>
            <table class="table table-sm">
                <tr>
                    <th width="30%">Họ tên:</th>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                </tr>
                <tr>
                    <th>Điện thoại:</th>
                    <td><?= htmlspecialchars($order['phone']) ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?= htmlspecialchars($order['email']) ?></td>
                </tr>
                <tr>
                    <th>Địa chỉ:</th>
                    <td><?= htmlspecialchars($order['address']) ?></td>
                </tr>
                <tr>
                    <th>Ghi chú:</th>
                    <td><?= htmlspecialchars($order['note']) ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="border-bottom pb-2">Thông tin đơn hàng</h6>
            <table class="table table-sm">
                <tr>
                    <th width="30%">Mã đơn:</th>
                    <td><?= htmlspecialchars($order['code']) ?></td>
                </tr>
                <tr>
                    <th>Trạng thái:</th>
                    <td>
                        <?php
                        switch($order['status']) {
                            case 'pending':
                                echo '<span class="badge bg-warning">Chờ xử lý</span>';
                                break;
                            case 'processing':
                                echo '<span class="badge bg-info">Đang xử lý</span>';
                                break;
                            case 'completed':
                                echo '<span class="badge bg-success">Hoàn thành</span>';
                                break;
                            case 'cancelled':
                                echo '<span class="badge bg-danger">Đã hủy</span>';
                                break;
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Ngày tạo:</th>
                    <td><?= date('d/m/Y H:i', $order['created_at']) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <h6 class="border-bottom pb-2 mt-4">Chi tiết đơn hàng</h6>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Số lượng</th>
                    <th class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php while($detail = $details->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($detail['product_name']) ?></td>
                        <td class="text-end"><?= number_format($detail['price']) ?>đ</td>
                        <td class="text-end"><?= number_format($detail['quantity']) ?></td>
                        <td class="text-end"><?= number_format($detail['price'] * $detail['quantity']) ?>đ</td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Tổng tiền:</td>
                    <td class="text-end fw-bold"><?= number_format($order['total_price']) ?>đ</td>
                </tr>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-danger">Không tìm thấy đơn hàng!</div>
<?php endif; ?>