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

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>CART</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./styles/cart.css">
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
<main style="padding-top: 70px" class="content">
    <div id="messageBox"></div>

    <div class="container">

        <div class="info">
            <h1>Order Summary</h1>

            <div class="items-container">
                <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="item" data-product-id="<?= $item['id'] ?>" data-stock="<?= $item['stock'] ?>">
                            <img src="../admin/uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div>
                                <p class="product_info"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="product_price" data-item-id="<?= $item['id'] ?>" data-base-price="<?= $item['price'] ?>">₪<?= number_format($item['price'], 2) ?></p>
                            </div>
                            <div class="right-controls">
                                <div class="quantity-selector">
                                    <button onclick="decrease('<?= $item['id'] ?>')">-</button>
                                    <span class="quantity" data-item-id="<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                                    <button onclick="increase('<?= $item['id'] ?>')">+</button>
                                </div>
                                <button class="remove-item" onclick="removeItem('<?= $item['id'] ?>')"><i class="fas fa-times"></i></button>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p></p>
                <?php endif; ?>
            </div>


            <div class="total">
                <p>Total :</p>
                <p id="cart-total">₪0.00</p>
            </div>
        </div>

        <div class="payment">
            <h1>Payment Information</h1>
            <form>
                <div class="name-group">
                    <input type="text" required class="fname" placeholder="First name" value="<?= htmlspecialchars($user_info['first_name']) ?>">
                    <input type="text" required class="lname" placeholder="Last Name" value="<?= htmlspecialchars($user_info['last_name']) ?>">
                </div>
                <input class="inputs" required type="text" placeholder="Street / Postal code">
                <a href="https://postcode.palestine.ps/" class="aa" target="_blank">Don't know your area code?</a>
                <input class="inputs" required type="email" placeholder="Email" value="<?= htmlspecialchars($user_info['email']) ?>">
                <input class="inputs" required type="text" placeholder="Phone number">



                <select class="sel">
                    <option>Jerusalem</option>
                    <option>Ramallah</option>
                    <option>Bethlehem</option>
                    <option>Hebron</option>
                    <option>Nablus</option>
                    <option>Qalqilya</option>
                    <option>Tulkarm</option>
                    <option>Salfit</option>
                    <option>Jenin</option>
                    <option>Ariha</option>
                    <option>Tubas</option>
                </select>
                <div>
                    <label><input type="radio" name="payment" value="cash"> <i class="fas fa-money-bill-wave"></i></label>
                    <label><input type="radio" name="payment" value="visa"> <i class="fab fa-cc-visa"></i></label>
                    <label><input type="radio" name="payment" value="master"> <i class="fab fa-cc-mastercard"></i></label>
                </div>

                <textarea class="inputs"  placeholder="Your comments here..." style="height: 100px ;resize: none"></textarea>



                <button class="button" id="doneButton">Pay with Visa</button>
            </form>
        </div>

    </div>
</main>
<footer>© 2025 data coming . All Rights Reserved</footer>
<script src="./js/cart.js"></script>

</body>

</html>
