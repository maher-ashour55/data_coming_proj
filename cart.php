<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
$user_info = ['first_name' => '', 'last_name' => '', 'email' => ''];

$user_stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_result->num_rows > 0) {
    $user_info = $user_result->fetch_assoc();
}
$user_stmt->close();


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT p.id, p.name, p.image, p.stock, c.quantity, c.price
        FROM cart_items c
        JOIN product p ON c.product_id = p.id
        WHERE c.user_id = ?";



$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
// ============ استعلام عدد السلة ============
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM cart_items WHERE user_id = ?");
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
    <link rel="icon" type="image/png" href="./img/data2-removebg-preview.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>CART</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./styles/cart.css">
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
                        <a href="mouse.php">MOUSE</a>
                        <a href="keyboard.php">KEYBOARD</a>
                        <a href="chair.php">CHAIR</a>
                        <a href="monitor.php">MONITOR</a>
                        <a href="hdmi.php">CABLES AND PORTS</a>
                    </div>
                </div>
            </nav>
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
<main class="main-content">
    <div id="messageBox"></div>
    <div class="cart-layout">
        <!-- Order Summary -->
        <section class="info">
            <h1>🧾 Order Summary</h1>
            <div class="items-container">
                <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="item" data-product-id="<?= $item['id'] ?>" data-stock="<?= $item['stock'] ?>">
                            <img src="../admin/uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div>
                                <p class="product_info"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="product_price" data-item-id="<?= $item['id'] ?>" data-base-price="<?= $item['price'] ?>">
                                    ₪<?= number_format($item['price'], 2) ?>
                                </p>
                            </div>
                            <div class="right-controls">
                                <div class="quantity-selector">
                                    <button onclick="decrease('<?= $item['id'] ?>')">-</button>
                                    <span class="quantity" data-item-id="<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                                    <button onclick="increase('<?= $item['id'] ?>')">+</button>
                                </div>
                                <button class="remove-item" onclick="removeItem('<?= $item['id'] ?>')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No items in cart.</p>
                <?php endif; ?>
            </div>
            <div class="total">
                <p>Total:</p>
                <p>
                    <span id="products-total">₪0.00</span>
                    <span id="delivery-fee">+₪0.00</span>
                    <br>
                    <strong>= <span id="cart-total">₪0.00</span></strong>
                </p>
            </div>


        </section>

        <!-- Payment Section -->
        <section class="payment">
            <h1>💳 Payment Info</h1>
            <form>
                <div class="name-group">
                    <input type="text" required class="fname" placeholder="First name" value="<?= htmlspecialchars($user_info['first_name']) ?>">
                    <input type="text" required class="lname" placeholder="Last name" value="<?= htmlspecialchars($user_info['last_name']) ?>">
                </div>
                <input class="inputs" required type="email" placeholder="Email" value="<?= htmlspecialchars($user_info['email']) ?>">
                <input class="inputs" required type="text" placeholder="Phone number">
                <div class="address-row">
                    <input class="inputs" required type="text" placeholder="Street / Postal Code">
                    <select class="sel" id="city-select" name="city" required onchange="updateDeliveryFeeAndTotal()">
                        <option value="select" disabled selected hidden>Select city</option>
                        <option value="jerusalem">Jerusalem</option>
                        <option value="ramallah">Ramallah</option>
                        <option value="bethlehem">Bethlehem</option>
                        <option value="hebron">Hebron</option>
                        <option value="nablus">Nablus</option>
                        <option value="qalqilya">Qalqilya</option>
                        <option value="tulkarm">Tulkarm</option>
                        <option value="salfit">Salfit</option>
                        <option value="jenin">Jenin</option>
                        <option value="ariha">Ariha</option>
                        <option value="tubas">Tubas</option>
                        <option value="arab 48">arab 48</option>
                    </select>

                </div>

                <div class="custom-radio-group">
                    <input type="radio" id="cash" name="payment" value="cash" hidden>
                    <label for="cash" class="custom-radio">
                        <i class="fas fa-money-bill-wave"></i> Pay with cash
                    </label>

                    <input type="radio" id="reflect" name="payment" value="reflect" hidden>
                    <label for="reflect" class="custom-radio">
                        <i class="fas fa-credit-card"></i> Pay with reflect
                    </label>
                </div>



                <textarea class="inputs" placeholder="Your comments here..." style="height: 100px; resize: none"></textarea>

                <button class="button" id="doneButton">Pay with cash</button>
            </form>
        </section>
    </div>
</main>

<footer>© 2025 data coming . All Rights Reserved</footer>
<script src="./js/cart.js"></script>

</body>

</html>
