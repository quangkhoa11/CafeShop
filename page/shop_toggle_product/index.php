<?php
if (!isset($_SESSION['dangnhap']) || $_SESSION['role'] !== 'shop') {
    header("Location: index.php?page=login");
    exit;
}

$db = new database();
$idshop = $_SESSION['idshop'];

$idsp = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if ($idsp <= 0 || !in_array($action, ['hide', 'show'])) {
    echo "<script>alert('❌ Dữ liệu không hợp lệ!'); window.location='index.php?page=shop_products';</script>";
    exit;
}

$newStatus = ($action === 'hide') ? 0 : 1;
$sql = "UPDATE sanpham SET trangthai = '$newStatus' WHERE idsp = '$idsp' AND idshop = '$idshop'";

if ($db->themxoasua($sql)) {
    $msg = ($newStatus == 1) ? '✅ Đã hiển thị lại sản phẩm!' : '👁️ Đã ẩn sản phẩm!';
    echo "<script>alert('$msg'); window.location='index.php?page=shop_products';</script>";
} else {
    echo "<script>alert('❌ Có lỗi xảy ra!'); window.location='index.php?page=shop_products';</script>";
}
?>
