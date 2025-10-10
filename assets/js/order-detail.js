    const order = JSON.parse(localStorage.getItem('currentOrder'));
    if(!order){
      alert('Không có đơn hàng nào!');
      window.location.href = 'index.php?page=cart';
    } else {
      document.getElementById('cust-name').innerText = order.customer.name;
      document.getElementById('cust-phone').innerText = order.customer.phone;
      document.getElementById('cust-address').innerText = order.customer.address;

      const itemsContainer = document.getElementById('order-items');
      order.items.forEach(item => {
        const div = document.createElement('div');
        div.className = "flex justify-between border-b pb-1";
        div.innerHTML = `<span>${item.name} x ${item.quantity}</span> <span>${(item.price * item.quantity).toLocaleString()} VND</span>`;
        itemsContainer.appendChild(div);
      });

      document.getElementById('order-total').innerText = order.total.toLocaleString();

      localStorage.removeItem('currentOrder');
    }