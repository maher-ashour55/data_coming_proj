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

function updateCartTotal(deliveryFee = null) {
    const items = document.querySelectorAll('.item');
    let productsTotal = 0;
    items.forEach(item => {
        const priceElement = item.querySelector('.product_price');
        const basePrice = parseFloat(priceElement.getAttribute('data-base-price'));
        const quantitySpan = item.querySelector('.quantity');
        const quantity = parseInt(quantitySpan.textContent) || 1;
        if (!isNaN(basePrice)) {
            productsTotal += basePrice * quantity;
        }
    });

    if (deliveryFee === null) {
        const city = document.getElementById("city-select")?.value || document.querySelector("select.sel").value;
        deliveryFee = getDeliveryFee(city);
    }

    const total = productsTotal + deliveryFee;

    document.getElementById('products-total').textContent = `₪${productsTotal.toFixed(2)}`;
    document.getElementById('delivery-fee').textContent = `+₪${deliveryFee.toFixed(2)}`;
    document.getElementById('cart-total').textContent = `₪${total.toFixed(2)}`;

    localStorage.setItem('cartTotal', total.toFixed(2));
}

let total = parseFloat(document.getElementById('cart-total').textContent.replace("₪", "")) || 0;

window.addEventListener('DOMContentLoaded', () => {
    updateCartTotal();
});

let selectedPayment = null;

document.querySelectorAll('input[name="payment"]').forEach(radio => {
    radio.addEventListener('change', function () {
        selectedPayment = this.value;
        const button = document.getElementById('doneButton');

        if (selectedPayment === 'cash') {
            button.textContent = 'Pay with cash';
        } else if (selectedPayment === 'reflect') {
            button.textContent = 'Pay with Reflect';
        }
    });
});

document.getElementById('doneButton').addEventListener('click', function(event) {
    event.preventDefault();

    if (document.querySelectorAll('.item').length === 0) {
        showMessage("🚫 There are no products in the cart!", false);
        return;
    }

    let fname = document.querySelector('.fname').value.trim();
    let lname = document.querySelector('.lname').value.trim();
    let address = document.querySelector('input[placeholder="Street / Postal Code"]').value.trim();
    let email = document.querySelector('input[placeholder="Email"]').value.trim();
    let phone = document.querySelector('input[placeholder="Phone number"]').value.trim();
    let city = document.querySelector('select.sel').value.trim();
    let payment = document.querySelector('input[name="payment"]:checked')?.value;
    let comments = document.querySelector('textarea.inputs').value.trim();

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

    const deliveryFee = getDeliveryFee(city);
    total += deliveryFee;

    const orderData = {
        fname,
        lname,
        address,
        email,
        phone,
        city,
        payment,
        total,
        comments
    };

    if (payment === 'cash') {
        // كاش
        fetch('place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showMessage("✅ The order has been placed successfully!");
                    setTimeout(() => window.location.href = './index.php', 3000);
                } else {
                    showMessage(data.message || "❌ حدث خطأ أثناء تنفيذ الطلب.", false);
                }
            })
            .catch(() => showMessage("❌ فشل الاتصال بالخادم.", false));
    }

    else if (payment === 'reflect') {
        // overlay
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        `;

        // confirm box
        const confirmBox = document.createElement('div');
        confirmBox.style.cssText = `
            background: #fff;
            border-radius: 16px;
            padding: 25px;
            max-width: 380px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            text-align: center;
            font-family: 'Arial', sans-serif;
        `;

        confirmBox.innerHTML = `
            <div style="font-size: 18px; margin-bottom: 12px; color: #333;">
                <span style="color: #9265A6; font-weight: bold;">💳 الدفع عن طريق ريفلكت لهذا الرقم</span>
            </div>
            <div style="
                display: inline-block;
                margin: 12px 0;
                padding: 10px 16px;
                background-color: #25D366;
                color: white;
                border-radius: 8px;
                font-weight: bold;
                transition: background 0.3s;
            " onmouseover="this.style.backgroundColor='#1ebe5d'" onmouseout="this.style.backgroundColor='#25D366'">
                💳 0595340025
            </div>
            <div style="margin-top: 16px;">
                <button id="confirmReflectBtn" style="
                    padding: 10px 20px;
                    background: #9265A6;
                    color: white;
                    font-weight: bold;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: background 0.3s;
                " onmouseover="this.style.backgroundColor='#7a4f8b'" onmouseout="this.style.backgroundColor='#9265A6'">
                    تم الدفع عبر Reflect
                </button>
            </div>
        `;

        overlay.appendChild(confirmBox);
        document.body.appendChild(overlay);

        document.getElementById('confirmReflectBtn').addEventListener('click', () => {
            fetch('place_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        overlay.remove();
                        showMessage("✅ Order confirmed via Reflect!");
                        setTimeout(() => window.location.href = './index.php', 3000);
                    } else {
                        overlay.remove();
                        showMessage(data.message || "❌ حدث خطأ أثناء تنفيذ الطلب.", false);
                    }
                })
                .catch(() => {
                    overlay.remove();
                    showMessage("❌ فشل الاتصال بالخادم.", false);
                });
        });
    }
});





let slideIndex = 1;
let slideTimer;

document.addEventListener("DOMContentLoaded", () => {


    const hamburger = document.getElementById("hamburger");
    const sideMenu = document.getElementById("side-menu");
    const pcSideMenu = document.getElementById("pc-side-menu");
    const accessoriesSideMenu = document.getElementById("accessories-side-menu");

    hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");

        const anyMenuOpen = sideMenu?.classList.contains("active") ||
            pcSideMenu?.classList.contains("active") ||
            accessoriesSideMenu?.classList.contains("active");

        if (anyMenuOpen) {
            sideMenu?.classList.remove("active");
            pcSideMenu?.classList.remove("active");
            accessoriesSideMenu?.classList.remove("active");
        } else {
            sideMenu?.classList.add("active");
        }
    });
});

function openPcMenu() {
    document.getElementById("side-menu")?.classList.remove("active");
    document.getElementById("pc-side-menu")?.classList.add("active");
}

function openAccessoriesMenu() {
    document.getElementById("side-menu")?.classList.remove("active");
    document.getElementById("accessories-side-menu")?.classList.add("active");
}

function backToMainMenu() {
    document.getElementById("pc-side-menu")?.classList.remove("active");
    document.getElementById("accessories-side-menu")?.classList.remove("active");
    document.getElementById("side-menu")?.classList.add("active");
}


function getDeliveryFee(city) {
    switch (city.toLowerCase()) {
        case "tulkarm": return 10;
        case "jerusalem": return 30;
        case "arab 48": return 65;
        case "select": return 0;
        default: return 20;
    }
}



function updateDeliveryFeeAndTotal() {
    const city = document.getElementById("city-select")?.value || document.querySelector("select.sel").value;
    const fee = getDeliveryFee(city);

    showMessage(`Delivery price: ${fee}₪`, true);

    updateCartTotal(fee);
}
