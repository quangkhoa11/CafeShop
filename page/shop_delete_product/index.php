<?php
if (!isset($_SESSION['dangnhap']) || $_SESSION['role'] !== 'shop') {
    header("Location: index.php?page=login");
    exit;
}

$db = new database();
$idshop = $_SESSION['idshop'];

$idsp = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idsp <= 0) {
    echo "<script>alert('❌ ID sản phẩm không hợp lệ!'); window.location='index.php?page=shop_products';</script>";
    exit;
}

$sqlCheck = "SELECT * FROM sanpham WHERE idsp = '$idsp' AND idshop = '$idshop'";
$sp = $db->xuatdulieu($sqlCheck);

if (!$sp || count($sp) === 0) {
    echo "<script>alert('❌ Không tìm thấy sản phẩm hoặc bạn không có quyền xóa!'); window.location='index.php?page=shop_products';</script>";
    exit;
}

$sp = $sp[0];

$sqlDelete = "DELETE FROM sanpham WHERE idsp = '$idsp' AND idshop = '$idshop'";
if ($db->themxoasua($sqlDelete)) {
    if (!empty($sp['hinhanh']) && file_exists("assets/images/" . $sp['hinhanh'])) {
        @unlink("assets/images/" . $sp['hinhanh']);
    }
    echo "<script>alert('✅ Xóa sản phẩm thành công!'); window.location='index.php?page=shop_products';</script>";
} else {
    echo "<script>alert('❌ Có lỗi xảy ra khi xóa sản phẩm!'); window.location='index.php?page=shop_products';</script>";
}
?>
