// عرض السلايد شو
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

// كروت المنتجات
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
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
                .then(res => res.json())
                .then(data => {
                    showMessage(data.success ? "تم إضافة المنتج إلى السلة ✅" : "User not logged in ❌", data.success);
                })
                .catch(error => {
                    console.error(error);
                    showMessage("فشل الاتصال بالخادم ❌", false);
                });
        });
    });
}

function showMessage(text, success = true) {
    const messageBox = document.getElementById('messageBox');
    messageBox.textContent = text;
    messageBox.style.backgroundColor = success ? '#4CAF50' : '#f44336';
    messageBox.style.display = 'block';
    setTimeout(() => { messageBox.style.display = 'none'; }, 3000);
}

function observeCards() {
    const cards = document.querySelectorAll('.category-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            entry.target.classList.toggle('show', entry.isIntersecting);
        });
    }, { threshold: 0.1 });

    cards.forEach(card => observer.observe(card));
}

// كود عرض الصور المتحركة الصغير
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

// Search Bar Toggle
const toggleBtn = document.getElementById('search-toggle');
const searchBar = document.querySelector('.search-bar');
const searchInput = document.getElementById('search-input');

toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    searchBar.classList.toggle('active');
    if (searchBar.classList.contains('active')) {
        setTimeout(() => searchInput.focus(), 100);
    }
});

document.addEventListener('click', (e) => {
    if (!searchBar.contains(e.target)) {
        searchBar.classList.remove('active');
    }
});

// Side Menu + Hamburger Toggle
document.addEventListener("DOMContentLoaded", () => {
    showSlides(slideIndex);
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

// Optional functions if needed elsewhere
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




function updateCartCount() {
    fetch('get_cart_count.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const countSpan = document.querySelector('.cart-count');
                countSpan.textContent = data.count;
            }
        })
        .catch(err => console.error('Error fetching cart count:', err));
}
if (data.success) {
    showMessage("The product has been added to the cart ✅");
    updateCartCount();
}





document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("search-input");

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
});
