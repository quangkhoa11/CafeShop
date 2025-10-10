const buttons = document.querySelectorAll('.add-to-cart');
buttons.forEach(btn => {
  btn.addEventListener('click', () => {
    const card = btn.closest('div.bg-white');
    const name = card.querySelector('h3').innerText;
    const priceText = card.querySelector('p').innerText;
    const price = parseInt(priceText.replace(/\D/g, ''));
    const quantity = parseInt(card.querySelector('.quantity-input').value);

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const index = cart.findIndex(item => item.name === name);

    if(index > -1){
      cart[index].quantity += quantity;
    } else {
      cart.push({name, price, quantity});
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert(`${name} đã được thêm vào giỏ hàng!`);
  });
});