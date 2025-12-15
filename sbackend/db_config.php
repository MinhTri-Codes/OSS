<?php
/**
 * sbackend/db_config.php
 * Cấu hình kết nối database MySQL cho InfinityFree
 */

// THÔNG TIN KẾT NỐI DATABASE (Bạn cần thay thế các giá trị sau)
define('DB_SERVER', 'sql202.infinityfree.com'); 
define('DB_USERNAME', 'if0_40685953');    
define('DB_PASSWORD', 'SclyNBdU8S'); 
define('DB_NAME', 'if0_40685953_monan'); 

// Tạo kết nối
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// Thiết lập mã hóa
$conn->set_charset("utf8mb4");
?>