<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if(!isset($_GET['id'])) {
    exit('Invalid request');
}

$id = (int)$_GET['id'];
$sql = "SELECT f.*, u.username, p.name as product_name 
        FROM feedback f
        LEFT JOIN user u ON f.user_id = u.id 
        LEFT JOIN product p ON f.product_id = p.id
        WHERE f.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()):
?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <td width="30%">Người dùng:</td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                    </tr>
                    <tr>
                        <td>Sản phẩm:</td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                    </tr>
                    <tr>
                        <td>Đánh giá:</td>
                        <td>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?= ($i <= $row['star']) ? '-fill text-warning' : '' ?>"></i>
                            <?php endfor; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Nội dung:</td>
                        <td><?= nl2br(htmlspecialchars($row['content'])) ?></td>
                    </tr>
                    <tr>
                        <td>Ngày đánh giá:</td>
                        <td><?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php
else:
    echo '<div class="alert alert-danger">Không tìm thấy đánh giá!</div>';
endif;
?>