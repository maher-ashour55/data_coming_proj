<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$cart_count = 0;

// اتصال عام
$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// حساب عدد السلة فقط إذا كان مسجل دخول
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $cart_count = $row['total'] ?? 0;
    }
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>data coming | داتا كامينغ</title>
    <link rel="canonical" href="https://datacoming.store/" />

    <link rel="icon" type="image/png" href="https://datacoming.store/img/data2-removebg-preview.png">

    <meta name="description" content="data coming | وجهتك الأولى لقطع الكمبيوتر الأصلية والتجميعات الاحترافية. اكتشف أقوى المعالجات، كروت الشاشة، الذواكر، وأكثر بأسعار منافسة.">

    <meta property="og:title" content="data coming | داتا كامينغ">
    <meta property="og:description" content="data coming | وجهتك الأولى لقطع الكمبيوتر الأصلية والتجميعات الاحترافية. اكتشف أقوى المعالجات، كروت الشاشة، الذواكر، وأكثر بأسعار منافسة.">
    <meta property="og:image" content="https://datacoming.store/img/data2-removebg-preview.png">
    <meta property="og:url" content="https://datacoming.store">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="data coming | داتا كامينغ">
    <meta name="twitter:description" content="data coming | وجهتك الأولى لقطع الكمبيوتر الأصلية والتجميعات الاحترافية. اكتشف أقوى المعالجات، كروت الشاشة، الذواكر، وأكثر بأسعار منافسة.">
    <meta name="twitter:image" content="https://datacoming.store/img/data2-removebg-preview.png">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "data coming",
            "url": "https://datacoming.store",
            "logo": "https://datacoming.store/img/data2-removebg-preview.png"
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="./styles/hom1css.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>

<header class="header-glass">
    <div class="container">
        <div class="left-section">
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <a href="index.php" class="logo">
                <img src="./img/data2-removebg-preview.png" alt="Logo" class="imgg" /> <span>Data Coming</span>
            </a>
        </div>

        <div class="welcome-message">!نورتوا متجرنا</div>

        <div class="center-section">
            <nav class="nav-links">
                <div class="dropdown">
                    <a href="#">PC <i class="fas fa-chevron-down "></i></a>
                    <div class="dropdown-content">
                        <a href="case.php">CASE</a>
                        <a href="mother.php">MOTHER BOARD</a>
                        <a href="cpu.php">CPU</a>
                        <a href="gui.php">GRAPHIC CARDS</a>
                        <a href="ssd.php">SSD</a>
                        <a href="ram.php">MEMORY</a>
                        <a href="harddisk.php">HARD DISK</a>
                        <a href="power.php">POWER SUPPLY</a>
                    </div>
                </div>
                <a href="laptop.php">Laptop</a>
                <a href="xbox.php">Console</a>
                <div class="dropdown">
                    <a href="#">Accessories <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-content">
                        <a href="headset.php">HEAD SET</a>
                        <div class="sub-dropdown">
                            <a href="mouse.php">MOUSE</a>
                            <div class="sub-dropdown-content">
                                <a href="MOUSEPAD.php">MOUSEPAD</a>
                            </div>
                        </div>
                        <a href="coolerpad.php">coolerpad</a>
                        <a href="keyboard.php">KEYBOARD</a>
                        <a href="chair.php">CHAIR</a>
                        <a href="monitor.php">MONITOR</a>
                        <a href="hdmi.php">CABLES AND PORTS</a>
                        <a href="speaker.php">speaker</a>
                    </div>
                </div>
            </nav>

            <div class="search-bar">
                <button id="search-toggle"><i class="fas fa-search"></i></button>
                <div class="search-input-wrapper">
                    <input type="text" id="search-input" placeholder="Search for products..." />
                    <div class="search-dropdown" id="search-dropdown"></div> <!-- هذا ما يخرب إذا التنسيق صح -->
                </div>
            </div>

        </div>

        <div class="right-section icons">
            <a href="https://www.facebook.com/profile.php?id=61556946718232" target="_blank" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/datac0ming?igsh=MThhMWs5ZHA5MTZneA==" target="_blank" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>


            <a href="cart.php" class="cart" title="Cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            </a>

            <a href="login.php" title="Account" style="position: relative;">
                <i class="fas fa-user"></i>
                <?php if (isset($_SESSION['new_order']) && $_SESSION['new_order']): ?>
                    <span class="cart-count">1</span>
                    <?php unset($_SESSION['new_order']); ?>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<div id="side-menu" class="side-menu">
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

<div id="pc-side-menu" class="side-menu">
    <div class="back-container">
        <button class="back-button" onclick="backToMainMenu()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <span class="menu-label">PC Section</span>
    </div>
    <div class="side-menu-grid">
        <a href="case.php" class="side-menu-item"><i class="fas fa-box"></i><p>Case</p></a>
        <a href="mother.php" class="side-menu-item"><i class="fas fa-network-wired"></i><p>Motherboard</p></a>
        <a href="cpu.php" class="side-menu-item"><i class="fas fa-microchip"></i><p>CPU</p></a>
        <a href="gui.php" class="side-menu-item"><i class="fas fa-video"></i><p>Graphic Cards</p></a>
        <a href="ssd.php" class="side-menu-item"><i class="fas fa-hdd"></i><p>SSD</p></a>
        <a href="ram.php" class="side-menu-item"><i class="fas fa-memory"></i><p>Memory</p></a>
        <a href="harddisk.php" class="side-menu-item"><i class="fas fa-database"></i><p>Hard Disk</p></a>
        <a href="power.php" class="side-menu-item"><i class="fas fa-plug"></i><p>Power Supply</p></a>
    </div>
</div>

<div id="accessories-side-menu" class="side-menu" >
    <div class="back-container">
        <button class="back-button" onclick="backToMainMenu()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <span class="menu-label">Accessories Section</span>
    </div>
    <div class="side-menu-grid">
        <a href="headset.php" class="side-menu-item"><i class="fas fa-headphones"></i><p>Headset</p></a>
        <a href="mouse.php" class="side-menu-item"><i class="fas fa-mouse"></i><p>Mouse</p></a>
        <a href="keyboard.php" class="side-menu-item"><i class="fas fa-keyboard"></i><p>Keyboard</p></a>
        <a href="chair.php" class="side-menu-item"><i class="fas fa-chair"></i><p>Chair</p></a>
        <a href="monitor.php" class="side-menu-item"><i class="fas fa-desktop"></i><p>Monitor</p></a>
        <a href="hdmi.php" class="side-menu-item"><i class="fas fa-plug"></i><p>Cables and Ports</p></a>
        <a href="MOUSEPAD.php" class="side-menu-item"><i class="fas fa-mouse"></i><p>Mousepad</p></a>
        <a href="coolerpad.php" class="side-menu-item"><i class="fas fa-snowflake"></i><p>Coolerpad</p></a>
        <a href="speaker.php" class="side-menu-item"><i class="fas fa-volume-up"></i><p>Speaker</p></a> <!-- تمت الإضافة -->
    </div>


</div>




<main class="main-content">
    <div class="slideshow-container">
        <div class="mySlides fade">
            <div class="numbertext">1 / 4</div>
            <img src="./img/imgg/salee.jpg" loading="lazy" style="width:100%;">
            <div class="text"></div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">2 / 4</div>
            <img src="./img/imgg/dc%203.png" loading="lazy" style="width:100%;">
            <div class="text"></div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">3 / 4</div>
            <img src="./img/hard.jpg" loading="lazy" style="width:100%;">
            <div class="text"></div>
        </div>

        <div class="mySlides fade">
            <div class="numbertext">4 / 4</div>
            <img src="./img/imgg/epic-gamer-room-setup.jpg" loading="lazy" style="width:100%;">
            <div class="text"></div>
        </div>


        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>

        <div class="dots" style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>

        </div>
    </div>
    <div class="static-mobile-image">
        <img src="./img/imgg/saledata.jpg" style="width:100%; height: 300px; padding-top: 30px;">
    </div>
    <br>
    <div class="feature">
        <div class="container1 benefit-container">
            <div class="benefit-col glass-box">
                <span class="material-symbols-outlined ico">workspace_premium</span>
                <p><strong>كفالة مضمونة</strong><br>الكفالة الرسمية لجميع المنتجات مضمونة</p>
            </div>

            <div class="benefit-col glass-box">
                <span class="material-symbols-outlined ico">payments</span>
                <p><strong>الدفع نقداً عند التسليم</strong><br>يمكنك الدفع نقداً عند التسليم.</p>
            </div>

            <div class="benefit-col glass-box">
                <span class="material-symbols-outlined ico">local_shipping</span>
                <p><strong>توصيل سريع</strong><br>من يوم إلى ثلاثة أيام لجميع مناطق الضفة والقدس</p>
            </div>
        </div>
    </div>

    <?php
    $featured_result = $conn->query("SELECT * FROM product WHERE is_featured_offer = 1 AND stock > 0 AND is_active = 1 LIMIT 15");
    ?>

    <div class="featured-carousel-section">
        <h2 class="section-title">العروض المميزة</h2>

        <div class="carousel-wrapper">
            <button class="carousel-btn left">&#10094;</button>

            <div class="featured-carousel">
                <?php while ($row = $featured_result->fetch_assoc()): ?>
                    <div class="featured-card">
                        <div class="offer-badge">عروض</div>
                        <img src="../admin/uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price"><?php echo htmlspecialchars($row['discount_price']); ?>₪</p>
                        <a href="product_details.php?id=<?php echo $row['id']; ?>" class="browse-btn">View</a>
                    </div>
                <?php endwhile; ?>
            </div>

            <button class="carousel-btn right">&#10095;</button>
        </div>
    </div>

    <div class="container">

        <div class="categories">
            <a href="laptop.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/laptop.png" alt="Processor">
                    </div>
                    <h3>LAPTOP</h3>
                    <button onclick="location.href='laptop.php'">Browse</button>
                </div>
            </a>
            <a href="keyboard.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/keyboard.png" alt="Processor">
                    </div>
                    <h3>KEYBOARD</h3>
                    <button onclick="location.href='keyboard.php'">Browse</button>
                </div>
            </a>
            <a href="mouse.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/mouse.png" alt="Processor">
                    </div>
                    <h3>MOUSE</h3>
                    <button onclick="location.href='mouse.php'">Browse</button>
                </div>
            </a>
            <a href="monitor.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/monitor.png" alt="Processor">
                    </div>
                    <h3>MONITOR</h3>
                    <button onclick="location.href='monitor.php'">Browse</button>
                </div>
            </a>
            <a href="headset.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/headset.png" alt="Power supply">
                    </div>
                    <h3>HEAD SET</h3>
                    <button onclick="location.href='headset.php'">Browse</button>
                </div>
            </a>
            <a href="case.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/case2.png" alt="CASE">
                    </div>
                    <h3>CASE</h3>
                    <button onclick="location.href='case.php'">Browse</button>
                </div>
            </a>
            <a href="chair.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/chair.png" alt="Chair">
                    </div>
                    <h3>CHAIR</h3>
                    <button onclick="location.href='chair.php'">Browse</button>
                </div>
            </a>
            <a href="MOUSEPAD.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/imgg/mousepad.png" alt="Chair">
                    </div>
                    <h3>MOUSEPAD</h3>
                    <button onclick="location.href='MOUSEPAD.php'">Browse</button>
                </div>
            </a>
            <a href="coolerpad.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/imgg/coolerpad-removebg-preview.png" alt="Chair">
                    </div>
                    <h3>COOLERPAD</h3>
                    <button onclick="location.href='coolerpad.php'">Browse</button>
                </div>
            </a>
            <a href="hdmi.php">
                <div class="category-card">
                    <div class="card-bg">
                        <div class="bg-shape"></div>
                        <img src="./img/hdmi.png" alt="Processor">
                    </div>
                    <h3>CABLES AND PORTS</h3>
                    <button onclick="location.href='hdmi.php'">Browse</button>
                </div>
            </a>
            <a href="cpu.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/cpu.png" alt="Processor">
                </div>
                <h3>PROCESSORS</h3>
                <button onclick="location.href='cpu.php'">Browse</button>
            </div>
            </a>
            <a href="gui.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/rtx.png" alt="Graphic Card">
                </div>
                <h3>GRAPHIC CARDS</h3>
                <button onclick="location.href='gui.php'">Browse</button>
            </div>
            </a>
            <a href="ram.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/ram.png" alt="Memory">
                </div>
                <h3>MEMORY</h3>
                <button onclick="location.href='ram.php'">Browse</button>
            </div>
            </a>


            <a href="harddisk.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/harddisk.png" alt="Head set">
                </div>
                <h3>HARD DISK</h3>
                <button onclick="location.href='harddisk.php'">Browse</button>
            </div>
            </a>
            <a href="ssd.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/ssd.png" alt="Processor">
                </div>
                <h3>SSD</h3>
                <button onclick="location.href='ssd.php'">Browse</button>
            </div>
            </a>
            <a href="power.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/power.png" alt="Processor">
                </div>
                <h3>POWER SUPPLY</h3>
                <button onclick="location.href='power.php'">Browse</button>
            </div>
            </a>



            <a href="xbox.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/xbox.png" alt="Processor">
                </div>
                <h3>CONSOLES</h3>
                <button onclick="location.href='xbox.php'">Browse</button>
            </div>
            </a>

            <a href="mother.php">
            <div class="category-card">
                <div class="card-bg">
                    <div class="bg-shape"></div>
                    <img src="./img/mother.png" alt="Processor">
                </div>
                <h3>MOTHER BOARD</h3>
                <button onclick="location.href='mother.php'">Browse</button>
            </div>
            </a>
            
        </div>
    </div>
    <div class="cc">
        <div class="small-carousel-wrapper">
            <div class="small-carousel">
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.14.29_0173f0a3.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_8b34e1b2.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.34_d1cced53.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_27d0205d.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.05_1253ef4b.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.30.29_0c81c343.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.35.35_9fc233c1.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.14.29_409ff953.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_e9a817ee.jpg" loading="lazy"/>



                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.14.29_0173f0a3.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_8b34e1b2.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.34_d1cced53.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_27d0205d.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.05_1253ef4b.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.30.29_0c81c343.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.35.35_9fc233c1.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.14.29_409ff953.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_e9a817ee.jpg" loading="lazy"/>


                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.14.29_0173f0a3.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_8b34e1b2.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.34_d1cced53.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.44_27d0205d.jpg" loading="lazy"/>
                <img src="./img/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2014.25.05_1253ef4b.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.30.29_0c81c343.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.35.35_9fc233c1.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-12-21%20في%2015.14.29_409ff953.jpg" loading="lazy"/>
                <img src="./img/imgg/صورة%20واتساب%20بتاريخ%201446-11-26%20في%2013.39.35_e9a817ee.jpg" loading="lazy"/>

            </div>
        </div>
    </div>
    <a href="https://wa.me/972566720728" class="whatsapp-float" target="_blank" title="راسلنا على واتساب">
        <i class="fab fa-whatsapp"></i>
    </a>
</main>

<footer>© 2025 data coming . All Rights Reserved</footer>


<script src="./js/home.js"></script>


</body>
</html>