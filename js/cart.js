function showMessage(text, success = true) {
    const messageBox = document.getElementById('messageBox');
    messageBox.textContent = text;
    messageBox.style.backgroundColor = success ? '#4CAF50' : '#f44336';
    messageBox.style.display = 'block';
    setTimeout(() => {
        messageBox.style.display = 'none';
    }, 3000);
}

function updateQuantity(productId, newQuantity) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: newQuantity })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showMessage('خطأ في تحديث الكمية.', false);
            } else {
                updateCartTotal();
            }
        })
        .catch(() => showMessage('فشل في الاتصال بالخادم.', false));
}

function removeItem(productId) {
    fetch('remove_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`.item[data-product-id="${productId}"]`).remove();
                updateCartTotal();
            } else {
                alert('خطأ في الحذف: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('فشل في الاتصال بالخادم.');
        });
}

function increase(itemId) {
    const itemElement = document.querySelector(`.item[data-product-id="${itemId}"]`);
    const stock = parseInt(itemElement.getAttribute('data-stock'));

    const quantitySpan = document.querySelector(`.quantity[data-item-id="${itemId}"]`);
    let quantity = parseInt(quantitySpan.textContent) || 1;

    if (quantity < stock) {
        quantity++;
        quantitySpan.textContent = quantity;
        updateQuantity(itemId, quantity);
    } else {
        showMessage("The quantity requested exceeds the available stock.", false);
    }
}


function decrease(itemId) {
    const quantitySpan = document.querySelector(`.quantity[data-item-id="${itemId}"]`);
    let quantity = parseInt(quantitySpan.textContent) || 1;
    if (quantity > 1) {
        quantity--;
        quantitySpan.textContent = quantity;
        updateQuantity(itemId, quantity);
    }
}

function updateCartTotal() {
    const items = document.querySelectorAll('.item');
    let total = 0;
    items.forEach(item => {
        const priceElement = item.querySelector('.product_price');
        const basePrice = parseFloat(priceElement.getAttribute('data-base-price'));
        const quantitySpan = item.querySelector('.quantity');
        const quantity = parseInt(quantitySpan.textContent) || 1;
        if (!isNaN(basePrice)) {
            total += basePrice * quantity;
        }
    });
    const formattedTotal = `₪${total.toFixed(2)}`;
    console.log('Calculated total:', formattedTotal);
    document.getElementById('cart-total').textContent = formattedTotal;

    localStorage.setItem('cartTotal', formattedTotal);
}

window.addEventListener('DOMContentLoaded', () => {
    updateCartTotal();
});

let selectedPayment = null;

document.querySelectorAll('input[name="payment"]').forEach(radio => {
    radio.addEventListener('change', function () {
        selectedPayment = this.value;
        const button = document.getElementById('doneButton');

        if (selectedPayment === 'visa') {
            button.textContent = 'Pay with Visa';
        } else if (selectedPayment === 'master') {
            button.textContent = 'Pay with MasterCard';
        } else if (selectedPayment === 'cash') {
            button.textContent = 'Pay on Delivery';
        }
    });
});

document.getElementById('doneButton').addEventListener('click', function(event) {
    event.preventDefault();

    let fname = document.querySelector('.fname').value.trim();
    let lname = document.querySelector('.lname').value.trim();
    let address = document.querySelector('input[placeholder="Street / Postal code"]').value.trim();
    let email = document.querySelector('input[placeholder="Email"]').value.trim();
    let phone = document.querySelector('input[placeholder="Phone number"]').value.trim();
    let city = document.querySelector('select.sel').value.trim();

    let payment = null;
    document.querySelectorAll('input[name="payment"]').forEach(radio => {
        if (radio.checked) payment = radio.value;
    });

    if (!fname || !lname || !address || !email || !phone || !city || !payment) {
        showMessage("Please fill in all fields and select your payment method.", false);
        return;
    }

    let total = 0;
    document.querySelectorAll('.product_price').forEach(priceEl => {
        let price = parseFloat(priceEl.dataset.basePrice);
        let itemId = priceEl.dataset.itemId;
        let quantity = parseInt(document.querySelector(`.quantity[data-item-id="${itemId}"]`).innerText);
        total += price * quantity;
    });

    if (payment === 'cash') {
        fetch('place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                fname,
                lname,
                address,
                email,
                phone,
                city,
                payment,
                total
            })
        })
            .then(async response => {
                const text = await response.text();
                console.log("Raw Response Text:", text);

                try {
                    const data = JSON.parse(text);
                    console.log("Parsed JSON:", data);

                    if (data.status === 'success') {
                        showMessage("Order submitted successfully!");
                        setTimeout(() => window.location.href = './index.php', 1500);

                    } else {
                        showMessage(data.message || "Something went wrong!", false);
                    }
                } catch (e) {
                    console.error("JSON parse error:", e);
                    showMessage("Invalid response from server.", false);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showMessage("Network error. Please try again.", false);
            });

    } else if (payment === 'visa' || payment === 'master') {
        localStorage.setItem('pendingOrder', JSON.stringify({
            fname,
            lname,
            address,
            email,
            phone,
            city,
            payment,
            total
        }));

        window.location.href = 'visa.php';
    }
});

    const hamburger = document.getElementById('hamburger');
    const sideMenu = document.getElementById('side-menu');
    const pcSideMenu = document.getElementById('pc-side-menu');
    const accessoriesSideMenu = document.getElementById('accessories-side-menu');

    hamburger.addEventListener('click', () => {
    if (sideMenu.classList.contains('active') || pcSideMenu.classList.contains('active') || accessoriesSideMenu.classList.contains('active')) {
    sideMenu.classList.remove('active');
    pcSideMenu.classList.remove('active');
    accessoriesSideMenu.classList.remove('active');
} else {
    sideMenu.classList.add('active');
}
});

    function openPcMenu() {
    sideMenu.classList.remove('active');
    pcSideMenu.classList.add('active');
}

    function openAccessoriesMenu() {
    sideMenu.classList.remove('active');
    accessoriesSideMenu.classList.add('active');
}

    function backToMainMenu() {
    pcSideMenu.classList.remove('active');
    accessoriesSideMenu.classList.remove('active');
    sideMenu.classList.add('active');
}


