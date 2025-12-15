<?php
/**
 * sbackend/db_config.php
 * Cấu hình kết nối database MySQL cho InfinityFree
 */

// THÔNG TIN KẾT NỐI DATABASE (Bạn cần thay thế các giá trị sau)
define('DB_SERVER', 'sql313.infinityfree.com'); 
define('DB_USERNAME', 'if0_40683473');    
define('DB_PASSWORD', 'Ue3jwnEcEl'); 
define('DB_NAME', 'if0_40683473_XXX'); 

// Tạo kết nối
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối database: " . $conn->connect_error);
}

// Thiết lập mã hóa
$conn->set_charset("utf8mb4");
?>