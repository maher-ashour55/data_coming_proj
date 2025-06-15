<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>data coming</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../styles/hom1css.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

</head>
<body>

<header>

    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>


    <a href="index.php" class="logo aa">
        <span class="outlined-text">Data Coming</span><span>.</span>
    </a>
    <div class="welcome-message">!نورتوا متجرنا</div>

    <nav class="navbar">
        <div class="home-btn"><a href="index.php" class="fas fa-home"> Home</a></div>

        <div class="pc-btn">
            <a href="#" class="fas fa-desktop"> PC</a>
            <div class="dropdown-multicolumn">
                <div class="column">
                    <a href="case.php">CASE</a>
                    <a href="mother.php">MOTHER BOARD</a>
                    <a href="cpu.php">CPU</a>
                    <a href="gui.php">GRAPHIC CARDS</a>

                </div>
                <div class="column">
                    <a href="ssd.php">SSD</a>
                    <a href="ram.php">MEMORY</a>
                    <a href="harddisk.php">HARD DISK</a>
                    <a href="power.php">POWER SUPPLY</a>
                </div>
            </div>
        </div>




        <div class="laptop-btn"><a href="laptop.php" class="fas fa-laptop"> laptop</a></div>
        <div class="console-btn"><a href="xbox.php" class="fab fa-xbox"> console</a></div>
        <div class="cata-btn">
            <a href="#" class="fas fa-mouse"> ACCESSORIES</a>
            <div class="dropdown-multicolumn2">
                <div class="column">
                    <a href="headset.php">HEAD SET</a>
                    <a href="mouse.php">MOUSE</a>
                    <a href="keayboard.php">KEYBOARD</a>

                </div>
                <div class="column">
                    <a href="chair.php">CHAIR</a>
                    <a href="monitor.php">MONITOR</a>
                    <a href="hdmi.php">CABLES AND PORTS</a>
                </div>
            </div>
        </div>
        <div class="search-btn"><a href="#" onclick="document.getElementById('search1').style.display='block'; return false;" class="fas fa-search "></a></div>

        <div class="search" id="search1">
            <input type="text" id="searchInput" placeholder="search...">
            <i class="fas fa-search"></i>
            <button id="closeSearch" class="close-btn">✖</button>
        </div>
    </nav>

    <div class="icons">
        <a href="https://www.facebook.com/profile.php?id=61556946718232" class="fab fa-facebook-f social-icon" target="_blank"></a>
        <a href="https://www.instagram.com/datac0ming?igsh=MThhMWs5ZHA5MTZneA==" class="fab fa-instagram social-icon" target="_blank"></a>
        <a href="cart.php" class="fas fa-shopping-cart main-icon"></a>
        <a href="login.php" class="fas fa-user main-icon"></a>
    </div>
</header>

<div id="side-menu" class="side-menu">
    <a href="#" class="logo aa">
        <span class="outlined-text">PC Section</span><span>.</span>
    </a>

    <!-- زر الرجوع -->
    <div class="back-container">
        <span class="outlined-text">Data Coming</span><span>.</span>
    </div>

    <div class="side-menu-grid">
        <a href="index.php" class="side-menu-item">
            <i class="fas fa-home"></i>
            <p>Home</p>
        </a>

        <a href="#" class="side-menu-item" onclick="openPcMenu(); return false;">
            <i class="fas fa-desktop"></i>
            <p>PC</p>
        </a>




        <a href="laptop.php" class="side-menu-item">
            <i class="fas fa-laptop"></i>
            <p>Laptop</p>
        </a>

        <a href="xbox.php" class="side-menu-item">
            <i class="fab fa-xbox"></i>
            <p>Console</p>
        </a>

        <a href="#" class="side-menu-item" onclick="openAccessoriesMenu(); return false;">
            <i class="fas fa-mouse"></i>
            <p>Accessories</p>
        </a>
    </div>
</div>
<!-- قائمة خاصة بـ PC -->
<div id="pc-side-menu" class="side-menu">
    <a href="#" class="logo aa">
        <span class="outlined-text">PC Section</span><span>.</span>
    </a>

    <!-- زر الرجوع -->
    <div class="back-container">
        <button class="back-button" onclick="backToMainMenu()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <span class="menu-label">PC Section</span>
    </div>



    <div class="side-menu-grid">
        <a href="case.php" class="side-menu-item">
            <i class="fas fa-box"></i>
            <p>Case</p>
        </a>

        <a href="mother.php" class="side-menu-item">
            <i class="fas fa-network-wired"></i>
            <p>Motherboard</p>
        </a>

        <a href="cpu.php" class="side-menu-item">
            <i class="fas fa-microchip"></i>
            <p>CPU</p>
        </a>

        <a href="gui.php" class="side-menu-item">
            <i class="fas fa-video"></i>
            <p>Graphic Cards</p>
        </a>

        <a href="ssd.php" class="side-menu-item">
            <i class="fas fa-hdd"></i>
            <p>SSD</p>
        </a>

        <a href="ram.php" class="side-menu-item">
            <i class="fas fa-memory"></i>
            <p>Memory</p>
        </a>

        <a href="harddisk.php" class="side-menu-item">
            <i class="fas fa-database"></i>
            <p>Hard Disk</p>
        </a>

        <a href="power.php" class="side-menu-item">
            <i class="fas fa-plug"></i>
            <p>Power Supply</p>
        </a>
    </div>
</div>
<div id="accessories-side-menu" class="side-menu" >
    <a href="#" class="logo aa">
        <span class="outlined-text">Accessories</span><span>.</span>
    </a>

    <div class="back-container">
        <button class="back-button" onclick="backToMainMenu()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <span class="menu-label">Accessories Section</span>
    </div>

    <div class="side-menu-grid">
        <a href="headset.php" class="side-menu-item">
            <i class="fas fa-headphones"></i>
            <p>Headset</p>
        </a>

        <a href="mouse.php" class="side-menu-item">
            <i class="fas fa-mouse"></i>
            <p>Mouse</p>
        </a>

        <a href="keayboard.php" class="side-menu-item">
            <i class="fas fa-keyboard"></i>
            <p>Keyboard</p>
        </a>

        <a href="chair.php" class="side-menu-item">
            <i class="fas fa-chair"></i> <!-- ممكن تستبدل بأيقونة مناسبة -->
            <p>Chair</p>
        </a>

        <a href="monitor.php" class="side-menu-item">
            <i class="fas fa-desktop"></i>
            <p>Monitor</p>
        </a>

        <a href="hdmi.php" class="side-menu-item">
            <i class="fas fa-plug"></i>
            <p>Cables and Ports</p>
        </a>
    </div>
</div>






<main class="main-content">
    <!-- slideshow       -->
    <div class="slideshow-container">

        <div class="mySlides fade">
            <div class="numbertext">1 / 5</div>
            <img src="../img/linus-mimietz-gvptKmonylk-unsplash.jpg" style="width:100%; height: 650px;">
            <div class="text"></div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">2 / 5</div>
            <img src="../img/hard.jpg" style="width:100%; height: 650px;">
            <div class="text"></div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">3 / 5</div>
            <img src="../img/sam-pak-X6QffKLwyoQ-unsplash.jpg" style="width:100%; height: 650px;">
            <div class="text"></div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">4 / 5</div>
            <img src="../img/martin-katler-7wCxlBfGMdk-unsplash.jpg" style="width:100%; height: 650px;">
            <div class="text"></div>
        </div>
        <div class="mySlides fade">
            <div class="numbertext">5 / 5</div>
            <img src="../img/arto-suraj-cIxE9fk7B2c-unsplash.jpg" style="width:100%; height: 650px;">
            <div class="text"></div>
        </div>


        <!-- أزرار التالي والسابق -->
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>

        <!-- الدوائر -->
        <div class="dots" style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
            <span class="dot" onclick="currentSlide(5)"></span>
        </div>
    </div>
    <div class="static-mobile-image">
        <img src="../img/datacom (4).jpg" style="width:100%; height: 300px; padding-top: 30px;">
    </div>
    <br>
    <!-- feature       -->
    <div class="feature">
        <div class="container">
            <div class="benefit-col">
                <span class="material-symbols-outlined ico"> workspace_premium</span>
                <p><strong>كفالة مضمونة  </strong>   الكفالة الرسمية لجميع المنتجات مضمونة</p>
            </div>

            <div class="benefit-col">
                <span class="material-symbols-outlined ico"> payments</span>
                <p><strong> الدفع نقداً عند التسليم </strong>   يمكنك الدفع نقداً عند التسليم.</p>
            </div>

            <div class="benefit-col">
                <span class="material-symbols-outlined ico"> local_shipping</span>
                <p><strong>توصيل سريع  </strong>   توصيل من يوم إلى ثلاثة أيام لجميع مناطق الضفة و القدس</p>
            </div>
        </div>
    </div>

    <div class="cc">
        <div class="small-carousel-wrapper">
            <div class="small-carousel">
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.34_d1cced53.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_8b34e1b2.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.23_27cf2ef6.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.36_ae9ba192.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.43_fab4329b.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_27d0205d.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_b51dccc6.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.05_1253ef4b.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.13_9b7c894b.jpg" loading="lazy"/>


                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.34_d1cced53.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_8b34e1b2.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.23_27cf2ef6.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.36_ae9ba192.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.43_fab4329b.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_27d0205d.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_b51dccc6.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.05_1253ef4b.jpg" loading="lazy"/>
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.13_9b7c894b.jpg" loading="lazy"/>


                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.34_d1cced53.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_8b34e1b2.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.23_27cf2ef6.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.36_ae9ba192.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.43_fab4329b.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_27d0205d.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_b51dccc6.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.05_1253ef4b.jpg" />
                <img src="../img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.13_9b7c894b.jpg" />

            </div>
        </div>
    </div>
    <br>
    <br>
    <!--  product      -->
    <div class="container">

        <div class="categories">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/cpu.png" alt="Processor">
                </div>
                <h3>PROCESSORS</h3>
                <button onclick="location.href='cpu.php'">Browse</button>
            </div>

            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/rtx.png" alt="Graphic Card">
                </div>
                <h3>GRAPHIC CARDS</h3>
                <button onclick="location.href='gui.php'">Browse</button>
            </div>

            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/ram.png" alt="Memory">
                </div>
                <h3>MEMORY</h3>
                <button onclick="location.href='ram.php'">Browse</button>
            </div>

            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/case2.png" alt="CASE">
                </div>
                <h3>CASE</h3>
                <button onclick="location.href='case.php'">Browse</button>
            </div>

            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/headset.png" alt="Power supply">
                </div>
                <h3>HEAD SET</h3>
                <button onclick="location.href='headset.php'">Browse</button>
            </div>

            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/harddisk.png" alt="Head set">
                </div>
                <h3>HARD DISK</h3>
                <button onclick="location.href='harddisk.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/ssd.png" alt="Processor">
                </div>
                <h3>SSD</h3>
                <button onclick="location.href='ssd.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/power.png" alt="Processor">
                </div>
                <h3>POWER SUPPLY</h3>
                <button onclick="location.href='power.php'">Browse</button>
            </div>

            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/chair.png" alt="Chair">
                </div>
                <h3>Chair</h3>
                <button onclick="location.href='chair.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/laptop.png" alt="Processor">
                </div>
                <h3>LAPTOP</h3>
                <button onclick="location.href='laptop.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/keyboard.png" alt="Processor">
                </div>
                <h3>KEYBOARD</h3>
                <button onclick="location.href='keayboard.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/mouse.png" alt="Processor">
                </div>
                <h3>MOUSE</h3>
                <button onclick="location.href='mouse.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/monitor.png" alt="Processor">
                </div>
                <h3>MONITOR</h3>
                <button onclick="location.href='monitor.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/xbox.png" alt="Processor">
                </div>
                <h3>CONSOLES</h3>
                <button onclick="location.href='xbox.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/hdmi.png" alt="Processor">
                </div>
                <h3>CABLES AND PORTS</h3>
                <button onclick="location.href='hdmi.php'">Browse</button>
            </div>
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="../img/mother.png" alt="Processor">
                </div>
                <h3>MOTHER BOARD</h3>
                <button onclick="location.href='mother.php'">Browse</button>
            </div>
        </div>
    </div>
</main>

<footer>© 2025 data coming . All Rights Reserved</footer>


<script>
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

        // Reset timer
        clearTimeout(slideTimer);
        slideTimer = setTimeout(() => plusSlides(1), 6000);
    }

    function plusSlides(n) {
        showSlides(slideIndex += n);
    }

    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    // أول تحميل
    window.onload = () => {
        showSlides(slideIndex);
    }
</script>
<script>
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
                : `<button class="buy-now-btn" data-product-id="${product.id}">BUY NOW</button>`
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

                    fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
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
</script>

<script>
    const carousel = document.querySelector('.small-carousel');
    const imgs = Array.from(carousel.children);
    const imgCount = imgs.length;
    const visibleImgs = 4;
    const imgWidth = imgs[0].offsetWidth;

    // نكرر الصور في نهاية الشريط لكي نعطي إحساس الدوران
    for (let i = 0; i < visibleImgs; i++) {
        const clone = imgs[i].cloneNode(true);
        carousel.appendChild(clone);
    }

    let index = 0;

    function slide() {
        index++;
        carousel.style.transition = 'transform 0.5s ease-in-out';
        carousel.style.transform = translateX(-${index * imgWidth}px);

        // عند الوصول إلى نهاية الصور المكررة، نعيد الوضع للبدء من الأصل بدون حركة انتقالية لعدم ملاحظة الفجوة
        if (index >= imgCount) {
            setTimeout(() => {
                carousel.style.transition = 'none';
                carousel.style.transform = 'translateX(0)';
                index = 0;
            }, 600); // 600ms أكثر من وقت الانتقال
        }
    }

    setInterval(slide, 5000);

</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.category-card');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                } else {
                    entry.target.classList.remove('show'); // هيك بيروح الترانزيشن لما يطلع
                }
            });
        }, {
            threshold: 0.1
        });

        cards.forEach(card => {
            observer.observe(card);
        });
    });

</script>
<script>
    const hamburger = document.getElementById('hamburger');
    const sideMenu = document.getElementById('side-menu');
    const pcSideMenu = document.getElementById('pc-side-menu');
    const accessoriesSideMenu = document.getElementById('accessories-side-menu');

    hamburger.addEventListener('click', () => {
        if (sideMenu.classList.contains('active') || pcSideMenu.classList.contains('active') || accessoriesSideMenu.classList.contains('active')) {
            // إغلاق كل القوائم
            sideMenu.classList.remove('active');
            pcSideMenu.classList.remove('active');
            accessoriesSideMenu.classList.remove('active');
        } else {
            // فتح القائمة الرئيسية
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


</script>







</body>
</html>