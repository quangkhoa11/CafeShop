Đ<title>Chi tiết đơn hàng</title>
<main class="flex-1 container mx-auto px-4 py-12">
      <h1 class="text-3xl font-bold mb-8 text-center text-orange-500">CHI TIẾT ĐƠN HÀNG</h1>

      <div id="order-info" class="bg-white p-6 rounded-xl shadow space-y-4">
        <h2 class="text-2xl font-bold text-orange-500">Thông tin khách hàng</h2>
        <p><b>Họ và tên:</b> <span id="cust-name"></span></p>
        <p><b>Số điện thoại:</b> <span id="cust-phone"></span></p>
        <p><b>Địa chỉ:</b> <span id="cust-address"></span></p>

        <h2 class="text-2xl font-bold text-orange-500 mt-4">Danh sách món</h2>
        <div id="order-items" class="space-y-2"></div>

        <p class="text-xl font-semibold mt-4">Tổng cộng: <span id="order-total"></span> VND</p>
      </div>
      <div class="text-center mt-6"><a href="index.php?page=menu" class="inline-block bg-orange-500 text-white font-semibold py-2 px-6 rounded-lg shadow hover:bg-orange-600 transition transform hover:scale-105 p-5">Quay lại</a>
</div>

    </main>

<script src="./assets/js/order-detail.js"></script>