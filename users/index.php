<!-- users/index.php -->
<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';
include '../includes/header.php';
include '../includes/sidebar.php';

// Xử lý tìm kiếm và lọc
$where = "1=1";
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (username LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

if(isset($_GET['role']) && !empty($_GET['role'])) {
    $role = (int)$_GET['role'];
    $where .= " AND role_id = $role";
}

// Query users với role
$sql = "SELECT u.*, r.name as role_name 
        FROM user u
        LEFT JOIN role r ON u.role_id = r.id 
        WHERE $where
        ORDER BY u.created_at DESC";
$result = $conn->query($sql);

// Lấy danh sách roles cho dropdown
$roles = $conn->query("SELECT * FROM role ORDER BY name");
?>

<!-- thư viện warning người dùng UI </head> -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- Flash Messages -->
    <?php 
    $flash = getFlashMessage();
    if ($flash): 
    ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show mt-3" role="alert">
        <?= $flash['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Quản lý Tài khoản</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-lg"></i> Thêm Tài khoản
        </button>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Tìm theo tên, email, số điện thoại..."
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tất cả vai trò</option>
                        <?php while($role = $roles->fetch_assoc()): ?>
                            <option value="<?= $role['id'] ?>" 
                                    <?= (isset($_GET['role']) && $_GET['role'] == $role['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <?php if(isset($_GET['search']) || isset($_GET['role'])): ?>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-secondary w-100">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Username</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td>
                                        <?php
                                            $role_class = '';
                                            switch($row['role_id']) {
                                                case 1: $role_class = 'danger'; break;  // Admin
                                                case 2: $role_class = 'primary'; break; // User
                                                case 3: $role_class = 'success'; break; // Editor
                                            }
                                        ?>
                                        <span class="badge bg-<?= $role_class ?>">
                                            <?= htmlspecialchars($row['role_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="editUser(<?= $row['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if($row['role_id'] != 1): ?>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="deleteUser(<?= $row['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm/Sửa Người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" method="POST" action="save.php">
                <div class="modal-body">
                    <input type="hidden" name="id" id="user_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" class="form-control" name="fullname" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" name="password">
                        <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Vai trò</label>
                        <select name="role_id" class="form-select" required>
                            <?php 
                            $roles->data_seek(0); // Reset pointer về đầu
                            while($role = $roles->fetch_assoc()): 
                            ?>
                                <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Hiển thị alert trong 3 giây rồi tự đóng
document.addEventListener('DOMContentLoaded', function() {
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(function() {
            alert.classList.remove('show');
            setTimeout(function() {
                alert.remove();
            }, 150);
        }, 3000);
    }
});

function editUser(id) {
    fetch('get_user.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            document.getElementById('user_id').value = data.id;
            document.querySelector('[name="username"]').value = data.username;
            document.querySelector('[name="fullname"]').value = data.fullname;
            document.querySelector('[name="email"]').value = data.email;
            document.querySelector('[name="phone"]').value = data.phone;
            document.querySelector('[name="address"]').value = data.address || '';
            document.querySelector('[name="role_id"]').value = data.role_id;
            
            var modal = new bootstrap.Modal(document.getElementById('addModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải thông tin người dùng');
        });
}

function deleteUser(id) {
    Swal.fire({
        title: 'Cảnh báo!',
        html: `<div class="text-start">
            <p>Xóa người dùng sẽ đồng thời xóa các dữ liệu liên quan tới người dung đó:</p>
            <ul>
                <li>Tất cả đơn hàng và chi tiết đơn hàng</li>
                <li>Lịch sử thanh toán</li>
                <li>Giỏ hàng</li>
                <li>Đánh giá sản phẩm</li>
                <li>Hình ảnh đã tải lên</li>
            </ul>
            <p class="text-danger fw-bold">Hành động này không thể khôi phục!</p>
        </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Vẫn xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete.php?id=' + id;
        }
    });
}

// Reset form khi mở modal thêm mới
document.getElementById('addModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
});
</script>

<?php include '../includes/footer.php'; ?>