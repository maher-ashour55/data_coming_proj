<?php
// 1. اتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "datacoming");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
$conn->set_charset("utf8");


$sql = "SELECT * FROM product WHERE category = 'ssd'";
$result = $conn->query($sql);

if (!$result) {
    die("خطأ في الاستعلام: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../styles/cpu.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
<div class="main-content">
<header>

    <a href="index.php" class="logo aa">
        <span class="outlined-text">Data Coming</span><span>.</span>
    </a>
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
        <a href="https://www.facebook.com/profile.php?id=61556946718232" class="fab fa-facebook-f" target="_blank"></a>
        <a href="https://www.instagram.com/datac0ming?igsh=MThhMWs5ZHA5MTZneA==" class="fab fa-instagram" target="_blank"></a>
        <a href="cart.php" class="fas fa-shopping-cart"></a>
        <a href="login.php" class="fas fa-user"></a>
    </div>
</header>
    <main style="padding-top: 70px">
        <div id="messageBox"></div>
        <div class="categories">
            <?php while ($row = $result->fetch_assoc()):
                // حساب السعر النهائي بناءً على الخصم إذا موجود
                $final_price = ($row['discount_price'] !== null && floatval($row['discount_price']) > 0 && floatval($row['discount_price']) < floatval($row['price']))
                    ? $row['discount_price']
                    : $row['price'];
                ?>
                <div class="category-card">
                    <a href="product_details.php?id=<?= $row['id'] ?>" class="category-card-link">
                    <div class="card-bg">
                        <div class="bg-shape <?php echo $row['stock'] == 0 ? 'grayscale' : ''; ?>"></div>
                        <img src="../admin/uploads/<?php echo htmlspecialchars($row['image']); ?>"
                             alt="<?php echo htmlspecialchars($row['name']); ?>"
                             class="<?php echo $row['stock'] == 0 ? 'grayscale' : ''; ?>">
                    </div>
                    </a>
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                    <?php if ($row['discount_price'] !== null && floatval($row['discount_price']) > 0 && floatval($row['discount_price']) < floatval($row['price'])): ?>
                        <h2>
                            <span class="original-price">₪<?php echo htmlspecialchars($row['price']); ?></span>
                            <span class="discount-price">₪<?php echo htmlspecialchars($row['discount_price']); ?></span>
                        </h2>
                    <?php else: ?>
                        <h2>₪<?php echo htmlspecialchars($row['price']); ?></h2>
                    <?php endif; ?>

                    <?php if ($row['stock'] == 0): ?>
                        <button class="buy-now-btn" disabled>OUT OF STOCK</button>
                    <?php else: ?>
                        <button class="buy-now-btn"
                                data-product-id="<?= $row['id'] ?>"
                                data-price="<?= $final_price ?>">
                            ADD TO CART
                        </button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

<footer>© 2024 Data Coming . All Rights Reserved</footer>

<!-- سكريبت البحث والسلة -->
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
</script>
</body>
</html>