// ===================== سلايدر =====================
let slideIndex = 1;
let slideTimer;

function showSlides(n) {
    const slides = document.getElementsByClassName("mySlides");
    const dots = document.getElementsByClassName("dot");

    if (n > slides.length) slideIndex = 1;
    if (n < 1) slideIndex = slides.length;

    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove("active");
    }

    slides[slideIndex - 1].style.display = "block";
    dots[slideIndex - 1].classList.add("active");

    clearTimeout(slideTimer);
    slideTimer = setTimeout(() => plusSlides(1), 6000);
}

function plusSlides(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

// ===================== المنتجات =====================
function restoreOriginalProducts() {
    const container = document.querySelector(".categories");
    container.innerHTML = originalProductsHTML;
    attachBuyNowEvents();
    observeCards();
}

function displayResults(products) {
    const dropdown = document.getElementById("search-dropdown");
    dropdown.innerHTML = "";

    if (products.length === 0) {
        dropdown.innerHTML = "<div class='dropdown-item'>لا يوجد نتائج</div>";
    } else {
        products.forEach(product => {
            const item = document.createElement("div");
            item.className = "dropdown-item";
            item.innerHTML = `
                <strong>${product.name}</strong> - ₪${product.price}
                ${product.stock == 0 ? "<span style='color:red;'>(نفذ)</span>" : ""}
            `;

            // لما المستخدم يضغط على المنتج
            item.addEventListener("click", () => {
                window.location.href = `product.php?id=${product.id}`;
            });

            dropdown.appendChild(item);
        });
    }

    dropdown.style.display = "block";
}


// ===================== إضافة للسلة =====================
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

                        // تحديث عدد المنتجات في السلة مباشرة
                        const cartCountSpan = document.querySelector('.cart-count');
                        if (cartCountSpan && data.cart_count !== undefined) {
                            cartCountSpan.textContent = data.cart_count;
                        }
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

document.addEventListener("DOMContentLoaded", () => {
    fetch('get_cart_count.php', { credentials: 'include' })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.cart_count !== undefined) {
                const cartCountSpan = document.querySelector('.cart-count');
                if (cartCountSpan) {
                    cartCountSpan.textContent = data.cart_count;
                }
            }
        })
        .catch(err => {
            console.error('Failed to load cart count:', err);
        });
});

function showMessage(text, success = true) {
    const messageBox = document.getElementById('messageBox');
    messageBox.textContent = text;
    messageBox.style.backgroundColor = success ? '#4CAF50' : '#f44336';
    messageBox.style.display = 'block';
    setTimeout(() => { messageBox.style.display = 'none'; }, 3000);
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

// ===================== الكاروسيل =====================
const carousel = document.querySelector('.small-carousel');
if (carousel) {
    const imgs = Array.from(carousel.children);
    const imgCount = imgs.length;
    const visibleImgs = 4;
    const imgWidth = imgs[0].offsetWidth;

    for (let i = 0; i < visibleImgs; i++) {
        const clone = imgs[i].cloneNode(true);
        carousel.appendChild(clone);
    }

    let index = 0;
    function slide() {
        index++;
        carousel.style.transition = 'transform 0.5s ease-in-out';
        carousel.style.transform = `translateX(-${index * imgWidth}px)`;

        if (index >= imgCount) {
            setTimeout(() => {
                carousel.style.transition = 'none';
                carousel.style.transform = 'translateX(0)';
                index = 0;
            }, 600);
        }
    }
    setInterval(slide, 5000);
}
// ===================== شريط البحث =====================
let originalProductsHTML = "";
document.addEventListener("DOMContentLoaded", () => {
    originalProductsHTML = document.querySelector(".categories").innerHTML;
});

const toggleBtn = document.getElementById('search-toggle');
const searchBar = document.querySelector('.search-bar');
const searchInput = document.getElementById('search-input');
const searchDropdown = document.getElementById("search-dropdown");

// فتح أو إغلاق البحث
toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    const wasActive = searchBar.classList.contains('active');
    searchBar.classList.toggle('active');

    if (searchBar.classList.contains('active')) {
        setTimeout(() => searchInput.focus(), 100);
    } else {
        searchInput.value = "";
        searchDropdown.style.display = "none";
        restoreOriginalProducts();
    }
});

// الضغط خارج البحث يغلقه
document.addEventListener('click', (e) => {
    if (!searchBar.contains(e.target)) {
        if (searchBar.classList.contains('active')) {
            searchBar.classList.remove('active');
            searchInput.value = "";
            searchDropdown.style.display = "none";
            restoreOriginalProducts();
        }
    }
});

// كتابة في الحقل
searchInput.addEventListener("input", () => {
    const query = searchInput.value.trim();

    if (query.length === 0) {
        searchDropdown.style.display = "none";
        return;
    }

    fetch("search.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ query })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.products)) {
                displayDropdownResults(data.products);
            } else {
                searchDropdown.innerHTML = `<div class="no-results">لا يوجد نتائج</div>`;
                searchDropdown.style.display = "block";
            }
        })
        .catch(err => {
            console.error("Search error:", err);
            searchDropdown.innerHTML = `<div class="no-results">حدث خطأ</div>`;
            searchDropdown.style.display = "block";
        });
});

// عرض نتائج الدروب داون
function displayDropdownResults(products) {
    if (products.length === 0) {
        searchDropdown.innerHTML = `<div class="no-results">لا يوجد نتائج</div>`;
    } else {
        searchDropdown.innerHTML = products.map(product => `
            <div class="result-item" onclick="window.location='product.php?id=${product.id}'">
                <img src="admin/uploads/${product.image}" alt="${product.name}">
                <div class="info">
                    <h4>${product.name}</h4>
                    <span>${product.price} ₪</span>
                </div>
            </div>
        `).join('');
    }
    searchDropdown.style.display = "block";
}



// ===================== سايد منيو =====================
document.addEventListener("DOMContentLoaded", () => {
    showSlides(slideIndex);
    attachBuyNowEvents();
    observeCards();
    updateCartCount(); // عند تحميل الصفحة

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


window.addEventListener('scroll', function () {
    const waBtn = document.querySelector('.whatsapp-float');
    if (window.scrollY > 100) {
        waBtn.classList.add('whatsapp-visible');
    } else {
        waBtn.classList.remove('whatsapp-visible');
    }
});