<?php
session_start();
$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
$conn->set_charset("utf8");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM product WHERE id = $id");

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "No product ID provided.";
    exit;
}

$has_discount = !empty($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
$final_price = $has_discount ? $product['discount_price'] : $product['price'];
$out_of_stock = $product['stock'] == 0;
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

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="./img/data2-removebg-preview.png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> | Data Coming</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        html {
            font-size: 75%;
            scroll-behavior: smooth;
            scroll-padding-top: 6rem;
            overflow-x: hidden;
        }

        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            outline: none;
            border: none;
            text-decoration: none;
            text-transform: capitalize;
            transition: .2s linear;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            background: radial-gradient(circle at top left, #f4f4ff, #eaeaea);

        }


        .main-content {
            flex: 1;
        }


        .header-glass {
            position: fixed;
            width: 100%;
            top: 0; left: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid #ddd;
            z-index: 9999;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }

        .header-glass .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 15px 30px;
            position: relative;
        }

        .left-section {
            margin-right: auto;
        }

        .center-section {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .right-section {
            margin-left: auto;
            display: flex;
            gap: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 2rem;
            color: #9265A6;
            text-decoration: none;
        }

        .logo:hover {
            color: #b589d6;
        }

        .logo img {
            width: 60px;
            height: auto;
            filter: drop-shadow(0 0 5px #9265A6);
        }

        .nav-links {
            display: flex;
            gap: 20px;
            font-weight: 500;
        }

        .nav-links .dropdown {
            position: relative;
            cursor: pointer;
        }

        /* Dropdown main link */
        .nav-links .dropdown > a {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #444;
            text-decoration: none;
            font-size: 1.8rem;
            padding: 6px 12px;
            border-radius: 6px;
            position: relative;
            transition: background 0.3s, color 0.3s;
        }

        .nav-links > a:not(.dropdown > a) {
            color: #444;
            text-decoration: none;
            font-size: 1.8rem;
            padding: 6px 12px;
            border-radius: 6px;
            transition: background 0.3s, color 0.3s;
            position: relative;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #9265A6;
        }

        .nav-links .dropdown > a i {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        /* Rotate arrow on hover */
        .nav-links .dropdown:hover > a i {
            transform: rotate(180deg);
        }

        /* Dropdown menu */
        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            min-width: 180px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 10000;
        }

        /* Show dropdown on hover */
        .nav-links .dropdown:hover .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Dropdown links */
        .dropdown-content a {
            display: block;
            padding: 10px 18px;
            color: #444;
            font-size: 1rem;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
            border-radius: 6px;
        }

        .dropdown-content a:hover {
            background: #ddc6e4;
            color: #9265a6;
        }
        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;

        }

        .search-bar button {
            background: rgba(255, 255, 255, 0.8);
            border: none;
            padding: 10px;
            border-radius: 50%;
            color: #9265a6;
            font-size: 1.55rem;
            cursor: pointer;
            transition: background 0.3s;
            z-index: 2;
            position: relative;
        }

        .search-input-wrapper {
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%) scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease, opacity 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 1;
            width: 500px;
            max-width: 80vw;

        }

        /* حقل البحث */
        #search-input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 25px;
            border: 1.5px solid #ddd;
            font-size: 1.55rem;
            background: #fff;
            color: #333;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* عند التفعيل */
        .search-bar.active .search-input-wrapper {
            transform: translateY(-50%) scaleX(1);
            opacity: 1;
            visibility: visible;
        }

        .nav-links a {
            position: relative;
            padding-bottom: 6px;
            text-decoration: none;
            color: #444;
            font-size: 1.9rem;
            transition: color 0.3s ease;
        }
        .nav-links a:hover{
            color: #9265A6;
        }
        .nav-links a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0%;
            height: 2px;
            background-color: #9265A6;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a.active::after {
            width: 100%;
        }


        .icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icons a,
        .icons button {
            color: #555;
            font-size: 2.5rem;
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            user-select: none;
            transition: color 0.3s;
        }

        .icons a:hover,
        .icons button:hover {
            color: #9265A6;
        }

        .cart {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #9265A6;
            width: 20px;
            height: 20px;
            font-size: 1.6rem;
            border-radius: 50%;
            color: white;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 5px #9265A6;
        }

        .menu-toggle {
            display: none;
            font-size: 2rem;
            background: none;
            border: none;
            color: #9265A6;
            cursor: pointer;
        }

        .mobile-nav {
            display: none;
            background: rgba(255,255,255,0.9);
            backdrop-filter: saturate(180%) blur(20px);
            flex-direction: column;
            gap: 12px;
            padding: 15px 30px;
            border-top: 1px solid #ddd;
        }

        /* Mobile dropdown button */
        .mobile-dropdown-btn {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            padding: 10px 0;
            font-size: 1.4rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #444;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .mobile-dropdown-btn:hover {
            background: #9265A655;
            color: #9265A6;
        }

        /* Mobile dropdown content */
        .mobile-dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-left: 15px;
        }

        .mobile-dropdown-content a {
            color: #444;
            font-size: 1.3rem;
            text-decoration: none;
            padding: 6px 0;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .mobile-dropdown-content a:hover {
            background: #9265A655;
            color: #9265A6;
        }


        .side-menu {
            left: -100%;
            transition: 0.3s;
            position: fixed;
            top: 70px;
            width: 100%;
            height: calc(100% - 70px);
            background: #f1f1f1;
            overflow-y: auto;
            z-index: 1000;
        }
        .side-menu.active {
            left: 0;
        }
        .side-menu-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding: 20px;
            justify-content: center;
        }
        .side-menu-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            width: 120px;
            height: 110px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            text-align: center;
            text-decoration: none;
        }

        .side-menu-item:hover {
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
            transform: translateY(-3px);
        }

        .side-menu-item i {
            font-size: 30px;
            color: #333;
            margin-bottom: 8px;
        }

        .side-menu-item p {
            font-size: 14px;
            color: #333;
            margin: 0;
        }

        @media (max-width: 600px) {
            .side-menu-item {
                width: 45%;
            }
        }

        @media (max-width: 400px) {
            .side-menu-item {
                width: 100%;
            }
        }
        .back-container {
            padding: 15px 20px 5px 20px;
        }

        .back-container {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
        }
        .back-container span{
            font-size: 25px;
        }
        .side-menu .outlined-text{
            background: transparent;
            border: none;
            color: #9265A6;
            font-weight: 600;
            font-size: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            transition: color 0.3s ease;
        }
        .back-button {
            background: transparent;
            border: none;
            color: #9265A6;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            transition: color 0.3s ease;
        }

        .back-button i {
            font-size: 18px;
        }

        .back-button:hover {
            color: #6f497a;
            text-decoration: underline;
        }

        .menu-label {
            color: #555;
            font-weight: 600;
            font-size: 16px;
            user-select: none;
        }


        /* الرسالة ترحب بالزوار وتكون مخفية بشكل افتراضي */
        .welcome-message {
            display: none; /* مخفية افتراضياً */
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            font-weight: bold;
            color: #9265A6;
            text-align: center;
            white-space: nowrap; /* لمنع التفاف النص */
            animation: wiggle 7s ease infinite;
            opacity: 0; /* يبدأ مخفي */
            z-index: 10000;
        }

        @keyframes wiggle {
            0%, 100% {
                opacity: 0;
                transform: translate(-50%, -50%) translateX(0);
            }
            20%, 60% {
                opacity: 1;
                transform: translate(-50%, -50%) translateX(-10px);
            }
            40%, 80% {
                opacity: 1;
                transform: translate(-50%, -50%) translateX(10px);
            }
        }

        @media (max-width: 1285px) {
            .center-section {
                display: none;
            }
            .welcome-message {
                display: block;
            }
        }
        @media (max-width: 935px) {
            .logo {
                display: none;
            }
        }
        @media (max-width: 770px) {
            .social-link {
                display: none;
            }
        }

        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 30px;
            height: 25px;
            cursor: pointer;
            z-index: 1001;
            gap: 5px;
            color: #9265A6;

        }
        .hamburger.active span:nth-child(1) {
            transform: translateY(9px) rotate(45deg);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }
        @media (max-width: 1285px) {



            /* عرض زر الهامبرغر */
            .hamburger {
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 5px;
                cursor: pointer;
                margin-left: 10px;
                color: #9265A6;
            }
            .hamburger span {
                width: 25px;
                height: 3px;
                background-color: #9265A6; /* غير اللون إذا بدك */
                border-radius: 2px;
                transition: 0.3s;
            }
            /* ترتيب اللوغو والهمبرغر */
            .left-section {
                display: flex;
                align-items: center;
                gap: 10px; /* مسافة بين اللوغو والهمبرغر */
            }
        }
        .product-details {
            max-width: 1000px;
            margin: 60px auto;
            padding: 40px;
            border-radius: 24px;
            background: linear-gradient(145deg, #ffffff, #f7f4fc);
            box-shadow:
                    0 12px 30px rgba(146, 101, 166, 0.15),
                    0 8px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #ececec;

            display: flex;
            gap: 50px;
            align-items: flex-start;
            position: relative;

            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-details:hover {
            transform: translateY(-6px);
            box-shadow:
                    0 20px 40px rgba(146, 101, 166, 0.2),
                    0 10px 20px rgba(0, 0, 0, 0.05);
        }
        .product-details::before {
            content: '';
            position: absolute;
            top: 0;
            left: 15px;
            width: calc(100% - 30px);
            height: 6px;
            background: linear-gradient(to right, #9265A6, #b48fd2);
            border-radius: 4px;
        }





        .image-container {
            position: relative;
            width: 400px;
            flex-shrink: 0;
        }
        .image-container img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            filter: <?= $out_of_stock ? 'grayscale(100%)' : 'none' ?>;
        }
        .out-of-stock-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(80, 80, 80, 0.6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 300px;
            pointer-events: none;
        }
        .product-info {
            flex: 1;
        }
        .product-info h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }
        .product-info p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
        }
        .price {
            font-size: 24px;
            margin: 20px 0;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 10px;
        }
        .discount-price {
            color: #9265A6;
            font-weight: bold;
        }
        .btn {
            background-color: #9265A6;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #7a4d90;
        }
        .stock-status {
            font-size: 18px;
            color: red;
            margin-top: 20px;
        }
        #messageBox {
            display: none;
            position: fixed;
            top: 95px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            font-weight: 700;
            font-size: 18px;
            z-index: 1001;
            max-width: 90%;
            text-align: center;
            font-family: 'Cairo', sans-serif;
            border: 2px solid black;
        }


        @media (max-width: 768px) {
            .product-details {
                flex-direction: column;
                text-align: center;
            }
            .image-container {
                width: 100%;
            }
            .image-container img {
                width: 100%;
            }
        }

        .main-content {
            flex: 1;
        }


        footer {
            background-color: #9265A6;
            padding: 10px;
            text-align: center;
            color: white;
        }
        footer {
            background-color: #9265A6;
            padding: 10px;
            text-align: center;
            color: white;
        }







        @media (max-width: 768px) {
            .product-details {
                flex-direction: column;
                text-align: center;
                padding: 20px;
                gap: 20px;
            }

            .image-container {
                width: 100%;
                margin-bottom: 20px;
            }

            .image-container img {
                width: 100%;
                height: auto;
                max-height: 300px;
                object-fit: contain;
            }

            .product-info {
                width: 100%;
            }

            .product-info h1 {
                font-size: 22px;
            }

            .product-info p {
                font-size: 15px;
                padding: 0 10px;
            }

            .price {
                font-size: 18px;
            }

            .btn {
                width: 90%;
                font-size: 16px;
                padding: 10px;
                margin-top: 15px;
            }

            .stock-status {
                font-size: 14px;
                margin-top: 15px;
            }

            .out-of-stock-overlay {
                font-size: 100px;
            }
        }




    </style>
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
                            <a href="keayboard.php">KEYBOARD</a>
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
            <a href="keayboard.php" class="side-menu-item"><i class="fas fa-keyboard"></i><p>Keyboard</p></a>
            <a href="chair.php" class="side-menu-item"><i class="fas fa-chair"></i><p>Chair</p></a>
            <a href="monitor.php" class="side-menu-item"><i class="fas fa-desktop"></i><p>Monitor</p></a>
            <a href="hdmi.php" class="side-menu-item"><i class="fas fa-plug"></i><p>Cables and Ports</p></a>
        </div>
    </div>
<main style="padding-top: 70px">
    <div id="messageBox"></div>
<div class="product-details">
    <div class="image-container">
        <img src="../admin/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <?php if ($out_of_stock): ?>
            <div class="out-of-stock-overlay"></div>
        <?php endif; ?>
    </div>

    <div class="product-info">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

        <div class="price">
            <?php if ($has_discount): ?>
                <span class="original-price">₪<?= htmlspecialchars($product['price']) ?></span>
                <span class="discount-price">₪<?= htmlspecialchars($product['discount_price']) ?></span>
            <?php else: ?>
                <span class="discount-price">₪<?= htmlspecialchars($product['price']) ?></span>
            <?php endif; ?>
        </div>

        <?php if ($out_of_stock): ?>
            <div class="stock-status">❌ SOLD OUT</div>
        <?php else: ?>
            <form id="addToCartForm">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="price" value="<?= $final_price ?>">
                <button
                    type="button"
                    class="btn"
                    data-product-id="<?= $id ?>"
                    data-price="<?= $final_price ?>">
                    ADD TO CART
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
    </main>

</div>

<script>
    function attachBuyNowEvents() {
        const buttons = document.querySelectorAll('.btn');

        buttons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();

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
                            showMessage(data.message || "User not logged in ❌", false);
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

    document.addEventListener("DOMContentLoaded", () => {
        attachBuyNowEvents();
    });
</script>
<script>
    const hamburger = document.getElementById('hamburger');
    const sideMenu = document.getElementById('side-menu');
    const pcSideMenu = document.getElementById('pc-side-menu');
    const accessoriesSideMenu = document.getElementById('accessories-side-menu');

    hamburger.addEventListener('click', () => {
        if (sideMenu.classList.contains('active') || pcSideMenu.classList.contains('active') || accessoriesSideMenu.classList.contains('active')) {
            sideMenu.classList.remove('active');
            pcSideMenu.classList.remove('active');
            accessoriesSideMenu.classList.remove('active');
        } else {
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
<footer>© 2025 Data Coming . All Rights Reserved</footer>
</body>
</html>
