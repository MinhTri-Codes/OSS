<?php
/**
 * sbackend/logic.php
 * Chứa toàn bộ logic xử lý CRUD.
 */

// Đường dẫn tương đối để gọi db_config.php từ file này:
require_once 'db_config.php'; 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function safe_input($conn, $data) {
    return $conn->real_escape_string($data);
}

// Khởi tạo biến để tránh lỗi NULL:
$tenmon = "";
$soluong = 0;
$mota = "";
$mamon_edit = ""; // SỬA LỖI: Khởi tạo là chuỗi rỗng thay vì null
$error = null;
$msg = null;

// --- Xử lý chức năng XÓA (DELETE) ---
if (isset($_GET['delete'])) {
    $mamon = safe_input($conn, $_GET['delete']);
    $sql_delete = "DELETE FROM monan WHERE mamon = '$mamon'";
    if ($conn->query($sql_delete) === TRUE) {
        $_SESSION['msg'] = "Xóa món ăn thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi xóa món: " . $conn->error;
    }
    // CHUYỂN HƯỚNG ĐÚNG: Quay về thư mục frontend/
    header("Location: frontend/index.php"); 
    exit();
}

// --- Xử lý chức năng CHUẨN BỊ SỬA (PREPARE EDIT) ---
if (isset($_GET['edit'])) {
    $mamon_edit_get = safe_input($conn, $_GET['edit']);
    $sql_select_edit = "SELECT * FROM monan WHERE mamon = '$mamon_edit_get'";
    $result_edit = $conn->query($sql_select_edit);
    
    if ($result_edit && $result_edit->num_rows == 1) {
        $row = $result_edit->fetch_assoc();
        $tenmon = $row['tenmon'];
        $soluong = $row['soluong'];
        $mota = $row['mota'];
        $mamon_edit = $row['mamon']; // Gán ID đã lấy được
    } else {
        $_SESSION['error'] = "Không tìm thấy món ăn cần sửa.";
        header("Location: frontend/index.php");
        exit();
    }
}


// --- Xử lý chức năng THÊM / CẬP NHẬT (CREATE / UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenmon_post = safe_input($conn, $_POST['tenmon']);
    $soluong_post = safe_input($conn, $_POST['soluong']);
    $mota_post = safe_input($conn, $_POST['mota']);
    $mamon_post = safe_input($conn, $_POST['mamon'] ?? ''); // Lấy ID, nếu không có sẽ là chuỗi rỗng

    if (empty($tenmon_post) || empty($soluong_post) || empty($mota_post)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin.";
    } elseif (!empty($mamon_post)) {
        // CẬP NHẬT (UPDATE)
        $sql = "UPDATE monan SET tenmon='$tenmon_post', soluong='$soluong_post', mota='$mota_post' WHERE mamon='$mamon_post'";
        $success_msg = "Cập nhật món ăn thành công!";
    } else {
        // THÊM MỚI (CREATE)
        $sql = "INSERT INTO monan (tenmon, soluong, mota) VALUES ('$tenmon_post', '$soluong_post', '$mota_post')";
        $success_msg = "Thêm món ăn thành công!";
    }

    if (!isset($_SESSION['error'])) {
        if ($conn->query($sql) === TRUE) {
            $_SESSION['msg'] = $success_msg;
        } else {
            $_SESSION['error'] = "Lỗi xử lý database: " . $conn->error;
        }
    }
    // CHUYỂN HƯỚNG ĐÚNG: Quay về thư mục frontend/
    header("Location: frontend/index.php"); 
    exit();
}

// --- Xử lý chức năng ĐỌC/TÌM KIẾM (READ / SEARCH) ---
$search_term = safe_input($conn, $_GET['search'] ?? '');
$where_clause = "";
if (!empty($search_term)) {
    $where_clause = " WHERE tenmon LIKE '%$search_term%' OR mota LIKE '%$search_term%'";
}

$sql_select = "SELECT mamon, tenmon, soluong, mota FROM monan" . $where_clause . " ORDER BY mamon DESC";
$result_list = $conn->query($sql_select);

// --- Lấy thông báo sau khi chuyển hướng ---
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$conn->close();
?>