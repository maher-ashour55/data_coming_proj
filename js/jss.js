let slideIndex = 1;
let slideTimer;


// ===================== المنتجات =====================
function restoreOriginalProducts() {
    const container = document.querySelector(".categories");
    container.innerHTML = originalProductsHTML;
    attachBuyNowEvents();
    observeCards();
}

function displayResults(products) {
    const container = document.querySelector(".categories");
    container.innerHTML = "";

    products.forEach(product => {
        const card = document.createElement("div");
        card.className = "category-card";
        const grayscaleClass = product.stock == 0 ? 'grayscale' : '';

        card.innerHTML = `
            <div class="card-bg">
                <div class="bg-shape ${grayscaleClass}"></div>
                <img src="../admin/uploads/${product.image}" alt="${product.name}" class="${grayscaleClass}">
            </div>
            <h3>${product.name}</h3>
            <h2>₪${product.price}</h2>
            ${product.stock == 0
            ? '<button class="buy-now-btn" disabled>OUT OF STOCK</button>'
            : `<button class="buy-now-btn" data-product-id="${product.id}">BUY NOW</button>`}
        `;
        container.appendChild(card);
    });

    attachBuyNowEvents();
    observeCards();
}



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
                }),
                credentials: 'include'
            })

                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message || "The product has been added to the cart ✅");
                    } else {
                        showMessage(data.message || "Something went wrong ❌", false);
                    }
                })

                .catch(error => {
                    console.error(error);
                    showMessage("Failed to connect to server ❌", false);
                });
        });
    });
}


function showMessage(text, success = true) {
    const messageBox = document.getElementById('messageBox');
    messageBox.textContent = text;
    messageBox.style.backgroundColor = success ? '#4CAF50' : '#f44336';
    messageBox.style.display = 'block';

    setTimeout(() => {
        messageBox.style.display = 'none';
    }, 3000);
}

// ===================== مراقبة الكروت =====================
function observeCards() {
    const cards = document.querySelectorAll('.category-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            entry.target.classList.toggle('show', entry.isIntersecting);
        });
    }, { threshold: 0.1 });

    cards.forEach(card => observer.observe(card));
}

// حفظ النسخة الأصلية من الكروت
let originalProductsHTML = "";
document.addEventListener("DOMContentLoaded", () => {
    originalProductsHTML = document.querySelector(".categories").innerHTML;
});

const toggleBtn = document.getElementById('search-toggle');
const searchBar = document.querySelector('.search-bar');
const searchInput = document.getElementById('search-input');

// فتح أو إغلاق البحث
toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    const wasActive = searchBar.classList.contains('active');
    searchBar.classList.toggle('active');

    if (searchBar.classList.contains('active')) {
        setTimeout(() => searchInput.focus(), 100);
    } else {
        searchInput.value = ""; // إفراغ الحقل
        restoreOriginalProducts(); // رجّع المنتجات
    }
});

// الضغط خارج البحث
document.addEventListener('click', (e) => {
    if (!searchBar.contains(e.target)) {
        if (searchBar.classList.contains('active')) {
            searchBar.classList.remove('active');
            searchInput.value = "";
            restoreOriginalProducts();
        }
    }
});

// عند الكتابة في الانبوت
searchInput.addEventListener("input", () => {
    const query = searchInput.value.trim();
    const resultsContainer = document.querySelector(".categories");

    if (query.length === 0) {
        restoreOriginalProducts();
        return;
    }

    fetch("search.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ query: query })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.products)) {
                displayResults(data.products);
            } else {
                resultsContainer.innerHTML = "<p style='text-align:center;'>لا يوجد نتائج</p>";
            }
        })
        .catch(err => {
            console.error("Search error:", err);
            resultsContainer.innerHTML = "<p style='text-align:center;'>حدث خطأ أثناء البحث</p>";
        });
});






// ===================== سايد منيو =====================
document.addEventListener("DOMContentLoaded", () => {
    attachBuyNowEvents();
    observeCards();

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
