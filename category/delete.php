<?php
// require_once '../includes/check_admin.php';
// require_once '../config/db.php';

// if(isset($_GET['id'])) {
//     $id = (int)$_GET['id'];
    
//     // Bắt đầu transaction
//     $conn->begin_transaction();
    
//     try {
//         // Xóa các sản phẩm thuộc danh mục này trước
//         $conn->query("DELETE FROM product WHERE category_id = $id");
        
//         // Sau đó xóa danh mục
//         if($conn->query("DELETE FROM category WHERE id = $id")) {
//             $conn->commit();
//             setFlashMessage("Xóa danh mục thành công!");
//         } else {
//             throw new Exception($conn->error);
//         }
//     } catch (Exception $e) {
//         $conn->rollback();
//         setFlashMessage("Có lỗi xảy ra: " . $e->getMessage(), "danger");
//     }
// }

// header('Location: index.php');
?>
<?php
require_once '../includes/check_admin.php';
require_once '../config/db.php';

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Lấy danh sách product_id thuộc category này
        $product_ids = $conn->query("SELECT id FROM product WHERE category_id = $id");
        
        if($product_ids->num_rows > 0) {
            // Chuyển thành mảng các id
            $ids = [];
            while($row = $product_ids->fetch_assoc()) {
                $ids[] = $row['id'];
            }
            $product_id_list = implode(',', $ids);
            
            // Xóa các bản ghi liên quan theo thứ tự
            $conn->query("DELETE FROM cart WHERE product_id IN ($product_id_list)");
            $conn->query("DELETE FROM order_details WHERE product_id IN ($product_id_list)");
            $conn->query("DELETE FROM feedback WHERE product_id IN ($product_id_list)");
            $conn->query("DELETE FROM image WHERE product_id IN ($product_id_list)");
            
            // Sau đó xóa các sản phẩm
            $conn->query("DELETE FROM product WHERE category_id = $id");
        }
        
        // Cuối cùng xóa category
        if($conn->query("DELETE FROM category WHERE id = $id")) {
            $conn->commit();
            setFlashMessage("Xóa danh mục và các sản phẩm liên quan thành công!");
        } else {
            throw new Exception($conn->error);
        }
        
    } catch (Exception $e) {
        $conn->rollback();
        setFlashMessage("Có lỗi xảy ra: " . $e->getMessage(), "danger");
    }
}

header('Location: index.php');
/*
1.Xóa theo thứ tự:
    Xóa cart trước (vì có khóa ngoại đến product)
    Xóa order_details (vì có khóa ngoại đến product)
    Xóa feedback (vì có khóa ngoại đến product)
    Xóa image (vì có khóa ngoại đến product)
    Sau đó mới xóa product
    Cuối cùng xóa category


2.Sử dụng transaction để đảm bảo:

    Hoặc là tất cả cùng xóa thành công
    Hoặc là không xóa cái nào cả (rollback)
*/
?>

