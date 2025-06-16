// حفظ المنتجات الأصلية عند تحميل الصفحة
const originalProductsHTML = document.querySelector(".categories").innerHTML;
const searchInput = document.getElementById("searchInput");

// عند الكتابة في البحث
searchInput.addEventListener("input", function () {
    const query = searchInput.value.trim();

    if (query === "") {
        restoreOriginalProducts();
        return;
    }

    fetch("search.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ query })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                displayResults(data.products);
            } else {
                document.querySelector(".categories").innerHTML = "<p class='error-message'></p>";
            }
        })
        .catch(err => {
            console.error(err);
            showMessage("Search error ❌", false);
        });
});

// عند الضغط على زر إغلاق البحث
document.getElementById("closeSearch").addEventListener("click", function () {
    searchInput.value = "";
    document.getElementById("search1").style.display = "none";
    restoreOriginalProducts();
});

// استرجاع المنتجات الأصلية
function restoreOriginalProducts() {
    const container = document.querySelector(".categories");
    container.innerHTML = originalProductsHTML;
    attachBuyNowEvents();
    observeCards();
}

// عرض نتائج البحث
function displayResults(products) {
    const container = document.querySelector(".categories");
    container.innerHTML = "";

    products.forEach(product => {
        const grayscaleClass = product.stock == 0 ? 'grayscale' : '';

        // حساب السعر النهائي لكل منتج في نتائج البحث (خصم أو السعر الأصلي)
        let finalPrice = (product.discount_price !== null && parseFloat(product.discount_price) > 0 && parseFloat(product.discount_price) < parseFloat(product.price))
            ? product.discount_price
            : product.price;

        const card = document.createElement("div");
        card.className = "category-card";

        card.innerHTML = `
                <div class="card-bg">
                    <div class="bg-shape ${grayscaleClass}"></div>
                    <img src="../admin/uploads/${product.image}" alt="${product.name}" class="${grayscaleClass}">
                </div>
                <h3>${product.name}</h3>
                <h2>
                    ${ (product.discount_price !== null && parseFloat(product.discount_price) > 0 && parseFloat(product.discount_price) < parseFloat(product.price))
            ? `<span class="original-price">₪${product.price}</span><span class="discount-price">₪${product.discount_price}</span>`
            : `₪${product.price}`
        }
                </h2>
                ${product.stock == 0
            ? '<button class="buy-now-btn" disabled>OUT OF STOCK</button>'
            : `<button class="buy-now-btn" data-product-id="${product.id}" data-price="${finalPrice}">BUY NOW</button>`
        }
            `;
        container.appendChild(card);
    });

    attachBuyNowEvents();
    observeCards();
}

// تفعيل زر الشراء
function attachBuyNowEvents() {
    const buttons = document.querySelectorAll('.buy-now-btn');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-product-id');
            const price = button.getAttribute('data-price');

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                    price: price
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showMessage("The product has been added to the cart ✅");
                    } else {
                        showMessage("User not logged in ❌", false);
                    }
                })
                .catch(error => {
                    console.error(error);
                    showMessage("Failed to connect to server ❌", false);
                });
        });
    });
}

// عرض الرسالة المؤقتة
function showMessage(text, success = true) {
    const messageBox = document.getElementById('messageBox');
    messageBox.textContent = text;
    messageBox.style.backgroundColor = success ? '#4CAF50' : '#f44336';
    messageBox.style.display = 'block';

    setTimeout(() => {
        messageBox.style.display = 'none';
    }, 3000);
}

// أنميشن عند الظهور
function observeCards() {
    const cards = document.querySelectorAll('.category-card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
            } else {
                entry.target.classList.remove('show');
            }
        });
    }, {
        threshold: 0.1
    });

    cards.forEach(card => {
        observer.observe(card);
    });
}

// تحميل أولي
document.addEventListener("DOMContentLoaded", () => {
    attachBuyNowEvents();
    observeCards();
});

// JavaScript (ممكن تحطه بنهاية الصفحة)
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll('.category-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('show');
        }, index * 200); // كل كرت يظهر بعد 200ms
    });
});
