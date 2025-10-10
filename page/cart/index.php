<title>Giỏ hàng</title>
<main class="flex-1 container mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold mb-8 text-center text-orange-500">GIỎ HÀNG</h1>
  <div id="cart-container" class="space-y-4"></div>
  <div class="mt-6 text-right">
    <p class="text-xl font-semibold">Tổng cộng: <span id="total">0</span> VND</p>
    <button id="checkout-btn" class="mt-3 bg-orange-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-orange-600 transition">
      Thanh toán
    </button>
  </div>

  <div id="checkout-form" class="hidden mt-6 p-6 rounded-xl shadow space-y-4">
    <h2 class="text-2xl font-bold text-orange-500">Thông tin đặt hàng</h2>
    <input type="text" id="customer-name" placeholder="Họ và tên" class="w-full p-2 border rounded-lg">
    <input type="text" id="customer-phone" placeholder="Số điện thoại" class="w-full p-2 border rounded-lg">
    <input type="text" id="customer-address" placeholder="Địa chỉ" class="w-full p-2 border rounded-lg">
    <div class="text-center">
      <button id="confirm-order" class="font-bold p-3 rounded-xl shadow-md bg-orange-500 text-white hover:bg-orange-600">
        Xác nhận đơn hàng
      </button>
    </div>
  </div>
</main>

<script>
  const cartContainer = document.getElementById('cart-container');
  const totalEl = document.getElementById('total');
  const checkoutBtn = document.getElementById('checkout-btn');
  const checkoutForm = document.getElementById('checkout-form');
  const confirmOrderBtn = document.getElementById('confirm-order');

  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  let total = 0;

  function renderCart() {
    cartContainer.innerHTML = '';
    total = 0;

    if (cart.length === 0) {
      cartContainer.innerHTML = '<p class="text-center text-gray-500">Giỏ hàng trống</p>';
      totalEl.innerText = '0';
      return;
    }

    cart.forEach((item, index) => {
      total += item.price * item.quantity;

      const div = document.createElement('div');
      div.className = "bg-white rounded-xl shadow p-4 flex justify-between items-center gap-4";
      div.innerHTML = `
        <div class="flex-1">
          <h3 class="font-medium"><b>${item.name}</b></h3>
          <div class="flex items-center gap-2 p-1 rounded">
            <b class="font-bold">Số lượng: </b>
            <button class="decrease px-2 rounded">-</button>
            <span>${item.quantity}</span>
            <button class="increase bg-gray-200 px-2 rounded">+</button>
          </div>
          <p class="ml-3"><b> Đơn giá:</b> ${item.price.toLocaleString()} VND</p>
        </div>
        <p class="font-semibold">${(item.price * item.quantity).toLocaleString()} VND</p>
        <button class="remove-btn block bg-amber-900-500 px-3 py-1 rounded-lg">Xóa</button>
      `;

      cartContainer.appendChild(div);

      div.querySelector('.decrease').addEventListener('click', () => {
        if (item.quantity > 1) {
          item.quantity--;
        } else {
          cart.splice(index, 1);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
      });

      div.querySelector('.increase').addEventListener('click', () => {
        item.quantity++;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
      });

      div.querySelector('.remove-btn').addEventListener('click', () => {
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
      });
    });

    totalEl.innerText = total.toLocaleString();
  }

  renderCart();

  checkoutBtn.addEventListener('click', () => {
    if (cart.length === 0) {
      alert('Giỏ hàng trống!');
      return;
    }

    <?php if (!isset($_SESSION['dangnhap'])): ?>
      alert('Bạn cần đăng nhập để tiếp tục thanh toán!');
      window.location.href = 'index.php?page=login';
      <?php else: ?>
      checkoutForm.classList.remove('hidden');
      checkoutForm.scrollIntoView({behavior: 'smooth'});
    <?php endif; ?>
  });

  confirmOrderBtn.addEventListener('click', () => {
    const name = document.getElementById('customer-name').value.trim();
    const phone = document.getElementById('customer-phone').value.trim();
    const address = document.getElementById('customer-address').value.trim();

    if (!name || !phone || !address) {
      alert('Vui lòng điền đầy đủ thông tin!');
      return;
    }

    const order = {
      customer: { name, phone, address },
      items: cart,
      total: total
    };

    localStorage.setItem('currentOrder', JSON.stringify(order));
    localStorage.removeItem('cart');
    window.location.href = 'index.php?page=order-detail';
  });
</script>
