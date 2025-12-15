<?php
/**
 * sbackend/logic.php
 * Chứa toàn bộ logic xử lý CRUD với prepared statements để tăng cường bảo mật.
 */

require_once 'db_config.php'; 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Khởi tạo biến
$tenmon = "";
$soluong = 0;
$mota = "";
$mamon_edit = "";
$error = null;
$msg = null;
$search_term = $_GET['search'] ?? '';

// --- Xử lý chức năng XÓA (DELETE) ---
if (isset($_GET['delete'])) {
    $mamon = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM monan WHERE mamon = ?");
    $stmt->bind_param("i", $mamon);
    if ($stmt->execute()) {
        $_SESSION['msg'] = "Xóa món ăn thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi xóa món: " . $stmt->error;
    }
    $stmt->close();
    header("Location: index.php"); 
    exit();
}

// --- Xử lý chức năng CHUẨN BỊ SỬA (PREPARE EDIT) ---
if (isset($_GET['edit'])) {
    $mamon_edit_get = $_GET['edit'];
    $stmt = $conn->prepare("SELECT tenmon, soluong, mota, mamon FROM monan WHERE mamon = ?");
    $stmt->bind_param("i", $mamon_edit_get);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    
    if ($result_edit->num_rows == 1) {
        $row = $result_edit->fetch_assoc();
        $tenmon = $row['tenmon'];
        $soluong = $row['soluong'];
        $mota = $row['mota'];
        $mamon_edit = $row['mamon'];
    } else {
        $_SESSION['error'] = "Không tìm thấy món ăn cần sửa.";
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}

// --- Xử lý chức năng THÊM / CẬP NHẬT (CREATE / UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenmon_post = $_POST['tenmon'] ?? '';
    $soluong_post = $_POST['soluong'] ?? 0;
    $mota_post = $_POST['mota'] ?? '';
    $mamon_post = $_POST['mamon'] ?? '';

    if (empty($tenmon_post) || empty($soluong_post) || empty($mota_post)) {
        $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin.";
    } else {
        if (!empty($mamon_post)) {
            // CẬP NHẬT (UPDATE)
            $stmt = $conn->prepare("UPDATE monan SET tenmon = ?, soluong = ?, mota = ? WHERE mamon = ?");
            $stmt->bind_param("sisi", $tenmon_post, $soluong_post, $mota_post, $mamon_post);
            $success_msg = "Cập nhật món ăn thành công!";
        } else {
            // THÊM MỚI (CREATE)
            $stmt = $conn->prepare("INSERT INTO monan (tenmon, soluong, mota) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $tenmon_post, $soluong_post, $mota_post);
            $success_msg = "Thêm món ăn thành công!";
        }

        if ($stmt->execute()) {
            $_SESSION['msg'] = $success_msg;
        } else {
            $_SESSION['error'] = "Lỗi xử lý database: " . $stmt->error;
        }
        $stmt->close();
    }
    
    header("Location: index.php"); 
    exit();
}

// --- Xử lý chức năng ĐỌC/TÌM KIẾM (READ / SEARCH) ---
if (!empty($search_term)) {
    $search_param = "%" . $search_term . "%";
    $stmt = $conn->prepare("SELECT mamon, tenmon, soluong, mota FROM monan WHERE tenmon LIKE ? OR mota LIKE ? ORDER BY mamon DESC");
    $stmt->bind_param("ss", $search_param, $search_param);
} else {
    $stmt = $conn->prepare("SELECT mamon, tenmon, soluong, mota FROM monan ORDER BY mamon DESC");
}
$stmt->execute();
$result_list = $stmt->get_result();
$stmt->close();

// --- Lấy thông báo sau khi chuyển hướng ---
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Không đóng kết nối ở đây, vì nó sẽ được dùng bởi file frontend
// $conn->close();
?>