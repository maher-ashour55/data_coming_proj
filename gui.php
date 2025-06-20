<?php
$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
$conn->set_charset("utf8");


$sql = "SELECT * FROM product WHERE category = 'Graphics Cards'";
$result = $conn->query($sql);

if (!$result) {
    die("خطأ في الاستعلام: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta charset="UTF-8">
    <title>Graphics Cards</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./styles/cpu.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
<div class="main-content">
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
        <a href="#" class="logo aa">
            <span class="outlined-text">PC Section</span><span>.</span>
        </a>

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
                <i class="fas fa-chair"></i>
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
        </div>
    </main>
</div>

<footer>© 2024 Data Coming . All Rights Reserved</footer>

<script src="./js/jss.js"></script>
</body>
</html>