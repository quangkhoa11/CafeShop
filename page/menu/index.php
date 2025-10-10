<title>Menu</title>
<main class="flex-1 py-10">
  <div class="container mx-auto px-4">
    <?php
    $obj = new database();

    $loaisp = $obj->xuatdulieu("SELECT * FROM loaisp");
if ($loaisp) {
    echo '<ul class="category-list flex flex-wrap justify-center gap-3 mb-8 mt-6">'; 
    echo '<li><a href="index.php?page=menu" 
                   class="px-4 py-2 bg-white border border-orange-300 rounded-full 
                          text-orange-600 font-medium shadow-sm hover:bg-orange-100 
                          transition">
                    Tất cả
                </a></li>';  
    foreach ($loaisp as $loai) {    
        echo '<li>
                  <a href="index.php?page=menu&cate=' . $loai['idloai'] . '" 
                   class="px-4 py-2 bg-white border border-orange-300 rounded-full 
                          text-orange-600 font-medium shadow-sm hover:bg-orange-100 
                          transition">
                    ' . $loai['tenloai'] . '
                </a>     
              </li>';
    }
    echo '</ul>';
}

    $cate = isset($_GET['cate']) ? $_GET['cate'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $where = [];
    if ($cate) $where[] = "idloai='$cate'";
    if ($search) $where[] = "tensp LIKE '%$search%'";

    $sql = "SELECT * FROM sanpham";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sanpham = $obj->xuatdulieu($sql);

    if($sanpham) {
        echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-6">';
        foreach($sanpham as $sp) {
            echo '
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg p-5">
                <img class="h-56 w-full object-cover rounded-lg" src="assets/images/' . $sp["hinhanh"] . '" alt="' . $sp["tensp"] . '">
                <h3 class="mt-4 font-semibold text-lg text-gray-800 text-center">' . $sp["tensp"] . '</h3>
                <p class="text-base text-orange-600 mt-2 font-bold text-center">' . number_format($sp["gia"]) . ' VND</p>
                <div class="grid grid-cols-3 gap-3 mt-4">
                    <input type="number" min="1" value="1" 
                class="col-span-1 quantity-input text-center rounded-lg border border-orange-300 focus:ring-2 focus:ring-orange-400 focus:outline-none p-2">
                <button class="col-span-2 add-to-cart bg-orange-500 text-white font-medium py-2 rounded-lg 
                 hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg">
                Thêm vào giỏ</button>
</div>

            </div>';
        }
        echo '</div>';
    } else {
        echo '<p class="text-center text-gray-500 mt-10">Hiện tại chưa có sản phẩm nào</p>';
    }
    ?>
  </div>
</main>

<script src="assets/js/menu.js"></script>