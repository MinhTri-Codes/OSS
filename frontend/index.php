<?php
/**
 * frontend/index.php
 * Giao diện chính, gọi logic backend.
 * Cần đặt file này trong thư mục frontend/
 */

// LƯU Ý QUAN TRỌNG: Gọi logic.php để xử lý request
// Cần đi ra khỏi thư mục frontend/ (bằng ../) để vào sbackend/
require_once '../sbackend/logic.php'; 

// Các biến đã được thiết lập từ logic.php: $mamon_edit, $tenmon, $soluong, $mota, $result_list, $error, $msg
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Món Ăn</title>
    <style>
        /* CSS tương tự như trước */
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; }
        h1 { text-align: center; color: #007bff; margin-bottom: 20px; }
        .card { background-color: #f9f9f9; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #ddd; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea { width: calc(100% - 22px); padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; }
        button[type="submit"], .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; transition: background-color 0.3s; margin-right: 10px; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: #333; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-info { background-color: #007bff; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #007bff; color: white; font-weight: normal; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        /* KẾT THÚC CSS */
    </style>
</head>
<body>

    <div class="container">
        <h1>🍽️ Quản Lý Thực Đơn</h1>
        <h1>Lê Hoàng Minh Trí DH52201618</h1>
        <?php 
        // HIỂN THỊ THÔNG BÁO LỖI HOẶC THÀNH CÔNG
        if ($error) {
            echo '<div class="message error">' . htmlspecialchars($error) . '</div>';
        } elseif ($msg) {
            echo '<div class="message success">' . htmlspecialchars($msg) . '</div>';
        }
        ?>
        
        <form method="POST" action="index.php" class="card">
            <h2><?php echo $mamon_edit ? 'Sửa Món Ăn (ID: ' . htmlspecialchars($mamon_edit) . ')' : 'Thêm Món Ăn Mới'; ?></h2>
            
            <input type="hidden" name="mamon" value="<?php echo htmlspecialchars($mamon_edit); ?>">
            
            <label for="tenmon">Tên Món:</label>
            <input type="text" name="tenmon" value="<?php echo htmlspecialchars($tenmon); ?>" required> 
            
            <label for="soluong">Số Lượng:</label>
            <input type="number" name="soluong" value="<?php echo htmlspecialchars($soluong); ?>" required min="1">
            
            <label for="mota">Mô Tả:</label>
            <textarea name="mota" required><?php echo htmlspecialchars($mota); ?></textarea>
            
            <button type="submit" class="btn btn-success">
                <?php echo $mamon_edit ? 'Cập Nhật Món' : 'Thêm Món'; ?>
            </button>
            
            <?php if ($mamon_edit): ?>
                <a href="index.php" class="btn btn-info" style="text-decoration: none;">Hủy Bỏ</a>
            <?php endif; ?>
        </form>

        <form method="GET" action="index.php" class="search-bar card">
            <input type="text" name="search" placeholder="Nhập tên món hoặc mô tả cần tìm..." value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit" class="btn btn-info">Tìm Kiếm</button>
            <a href="index.php" class="btn btn-info" style="text-decoration: none;">Tải Lại</a>
        </form>

        <h2>Danh Sách Món Ăn Hiện Có</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Món</th>
                        <th>Số Lượng</th>
                        <th>Mô Tả</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($result_list && $result_list->num_rows > 0):
                        while ($row = $result_list->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['mamon']); ?></td>
                        <td><?php echo htmlspecialchars($row['tenmon']); ?></td>
                        <td><?php echo htmlspecialchars($row['soluong']); ?></td>
                        <td><?php echo htmlspecialchars($row['mota']); ?></td>
                        <td>
                            <a href="index.php?edit=<?php echo htmlspecialchars($row['mamon']); ?>" class="btn btn-warning" style="text-decoration: none;">Sửa</a>
                            
                            <a href="index.php?delete=<?php echo htmlspecialchars($row['mamon']); ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa món ăn này không?')" 
                               class="btn btn-danger" style="text-decoration: none;">Xóa</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5">
                            <?php echo empty($search_term) ? 'Chưa có món ăn nào.' : 'Không tìm thấy kết quả phù hợp.'; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php $conn->close(); ?>
</body>
</html>