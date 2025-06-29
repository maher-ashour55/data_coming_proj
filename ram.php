<?php
$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
$conn->set_charset("utf8");


$sql = "SELECT * FROM product WHERE category = 'ram' AND is_active = 1";
$result = $conn->query($sql);

if (!$result) {
    die("خطأ في الاستعلام: " . $conn->error);
}

// ============ استعلام عدد السلة ============
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    if ($row = $cart_result->fetch_assoc()) {
        $cart_count = $row['total'] ?? 0;
    }

    $stmt->close();
}

// ✅ أغلق الاتصال بعد الانتهاء من كل شيء
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./img/data2-removebg-preview.png">

    <meta charset="UTF-8">
    <title>RAM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./styles/cpu.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
<div class="main-content">
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
                            <a href="mouse.php">MOUSE</a>
                            <a href="keyboard.php">KEYBOARD</a>
                            <a href="chair.php">CHAIR</a>
                            <a href="monitor.php">MONITOR</a>
                            <a href="hdmi.php">CABLES AND PORTS</a>
                        </div>
                    </div>
                </nav>

                <div class="search-bar">
                    <button id="search-toggle"><i class="fas fa-search"></i></button>
                    <div class="search-input-wrapper">
                        <input type="text" id="search-input" placeholder="Search for products..." />
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
        </div>
    </div>
    <main style="padding-top: 70px">
        <div id="messageBox"></div>
        <div class="container">
        <div class="categories">
            <?php while ($row = $result->fetch_assoc()):
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
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    </a>
                    <?php if ($row['discount_price'] !== null && floatval($row['discount_price']) > 0 && floatval($row['discount_price']) < floatval($row['price'])): ?>
                        <h2>
                            <span class="original-price">₪<?php echo htmlspecialchars($row['price']); ?></span>
                            <span class="discount-price">₪<?php echo htmlspecialchars($row['discount_price']); ?></span>
                        </h2>
                    <?php else: ?>
                        <h2>₪<?php echo htmlspecialchars($row['price']); ?></h2>
                    <?php endif; ?>

                    <?php if ($row['stock'] == 0): ?>
                        <button class="buy-now-btn" disabled>SOLD OUT</button>
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
        </div>
    </main>
    <a href="https://wa.me/972566720728" class="whatsapp-float" target="_blank" title="راسلنا على واتساب">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

<footer>© 2025 Data Coming . All Rights Reserved</footer>

<script src="./js/jss.js"></script>
</body>
</html>