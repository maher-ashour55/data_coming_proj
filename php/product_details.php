<?php
session_start();
$conn = new mysqli("localhost", "root", "", "datacoming");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> | Data Coming</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }
        .product-details {
            max-width: 1000px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
            display: flex;
            gap: 40px;
            align-items: flex-start;
            position: relative;
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
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            color: white;
            border-radius: 8px;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
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


        html {
            font-size: 62.5%;
            scroll-behavior: smooth;
            scroll-padding-top: 6rem;
            overflow-x: hidden;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #9265A6;
            padding: 1.5rem 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            box-shadow: 0 .1rem 1rem rgba(0, 0, 0, 1);
        }

        header .logo {
            font-size: 3rem;
            color: #81C3C2;
            font-weight: bold;
            shadow: 0 .1rem 1rem rgba(0, 0, 0, 1);
            z-index: 1001;
        }

        header span {
            color: #FFFFFF;
            position: relative;
            top: -3px;
            left: -5px;
        }

        header .navbar {
            display: flex;
            gap: 10px;
            align-items: center;
            z-index: 1;
            margin-right: 10%;
        }

        header .navbar a {
            font-size: 1.7rem;
            padding: 0 1.1rem;
            color: #ffffff;
            text-shadow: 0.8px 0.8px #81C3C2;
            transition: all 0.3s ease;
            display: inline-block;
            background-size: 150%;
        }

        header .navbar a:hover {
            transform: scale(1.1);
            color: #81C3C2;
        }

        header .icons a {
            font-size: 3rem;
            margin-left: 2rem;
            color: white;
            text-shadow: 1px 1px #81C3C2;
            transition: all 0.3s ease;
            display: inline-block;
        }

        header .icons a:hover {
            color: #81C3C2;
            transform: scale(1.2);
        }

        .laptop-btn {
            position: absolute;
            top: 0;
            right: 775px;
            background: rgb(146, 101, 166);
            color: white !important;
            padding: 0;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-shadow: 1px 1px #81C3C2;
            font-size: 1.7rem;
        }
        .home-btn {
            position: absolute;
            top: 0;
            right: 970px;
            background: rgb(146, 101, 166);
            color: white !important;
            padding: 0;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-shadow: 1px 1px #81C3C2;
            font-size: 1.7rem;
        }
        .pc-btn {
            position: absolute;
            top: 0;
            right: 896px;
            background: rgb(146, 101, 166);
            color: white !important;
            padding: 0;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-shadow: 1px 1px #81C3C2;
            font-size: 1.7rem;
        }
        .console-btn {
            position: absolute;
            top: 0;
            right: 642px;
            background: rgb(146, 101, 166);
            color: white !important;
            padding: 0;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-shadow: 1px 1px #81C3C2;
            font-size: 1.7rem;
        }
        .cata-btn {
            position: absolute;
            top: 0;
            right: 475px;
            background: rgb(146, 101, 166);
            color: white !important;
            padding: 0;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-shadow: 1px 1px #81C3C2;
            font-size: 1.7rem;
        }
        .dropdown-multicolumn {

            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.4s ease, transform 0.4s ease;
            pointer-events: none;
            position: absolute;
            top: 55px;
            left: 0;
            background: linear-gradient(to bottom right, #bbaeca, #9265a6);
            color: #000000;
            padding: 20px;
            border-radius: 8px;
            gap: 40px;
            z-index: 1000;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            display: flex;
            visibility: hidden;
        }

        .dropdown-multicolumn .column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .dropdown-multicolumn a {
            color: #ffffff !important;
            font-size: 1.3rem;
            text-decoration: none;
            font-weight: bold;
        }

        .dropdown-multicolumn a:hover {
            background-color: #9265A6;

            transform: translateX(5px);
            transition: all 0.3s ease;
            border-radius: 6px;
        }

        .pc-btn:hover .dropdown-multicolumn {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
            visibility: visible;
        }



        .dropdown-multicolumn2 {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
            transition: opacity 0.4s ease, transform 0.4s ease;
            position: absolute;
            top: 55px;
            left: 0;
            background: linear-gradient(to bottom right, #bbaeca, #9265a6);
            color: #000000;
            padding: 20px;
            border-radius: 8px;
            gap: 40px;
            z-index: 1000;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            display: flex;
            visibility: hidden;
        }


        .dropdown-multicolumn2 .column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .dropdown-multicolumn2 a {
            color: #ffffff !important;
            font-size: 1.3rem;
            text-decoration: none;
            font-weight: bold;
        }

        .dropdown-multicolumn2 a:hover {
            background-color: #9265A6;

            transform: translateX(5px);
            transition: all 0.3s ease;
            border-radius: 6px;
        }


        .cata-btn:hover .dropdown-multicolumn2 {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
            visibility: visible;
        }
        #messageBox {
            display: none;
            position: fixed;
            top: 80px;
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


            nav.navbar {
                display: none;
            }

            .menu-toggle {
                display: block;
            }
            .slideshow-container {
                display: none;
            }

            .static-mobile-image {
                display: block;
            }
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar {
                flex-direction: column;
                width: 100%;
            }

            .navbar a {
                padding: 1rem;
                width: 100%;
                text-align: left;
            }

            .search, .laptop-btn, .pc-btn, .console-btn, .cata-btn, .home-btn {
                position: static;
                width: 100%;
                margin: 5px 0;
            }

            .dropdown-multicolumn,
            .dropdown-multicolumn2 {
                flex-direction: column;
                gap: 20px;
            }

            .mySlides img,
            .static-mobile-image {
                height: auto;
            }
        }
        .categories {
            display: grid;
            gap: 30px;
            justify-content: center;
            padding: 40px 0;
        }

        @media (min-width: 1201px) {
            .categories {
                grid-template-columns: repeat(4, 1fr);
            }
            .card-bg{

            }
        }

        @media (min-width: 901px) and (max-width: 1200px) {
            .categories {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .categories {
                grid-template-columns: repeat(2, 1fr);
            }
            .card-bg img{
                width: 220px;
            }
            .category-card{
                height: 430px;
            }
            .card-bg{
                height: 200px;
            }
        }





        .icons a {
            display: inline-block;
            font-size: 20px;
            margin-left: 10px;
            color: #333;
        }

        @media (max-width: 768px) {
            .icons .social-icon {
                display: none !important;
            }

            .icons {
                display: flex;
                gap: 12px;
                position: absolute;
                top: 15px;
                right: 15px;
                z-index: 1002;
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



        }

        .hamburger span {
            display: block;
            height: 3px;
            background-color: #FFFFFF;
            border-radius: 2px;
            width: 25px;
            font-size: 3rem;

            color: white;
            text-shadow: 1px 1px #81C3C2;

        }


        .side-menu {
            left: -100%;
            transition: 0.3s;
            position: fixed;
            top: 60px;
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
        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }
            nav.navbar {
                display: none;
            }
            .logo {
                display: none;
            }
        }
        @media (max-width: 1352px ){

            .logo {
                display: none;
            }

        }
        @media (max-width: 1070px) {
            .hamburger {
                display: flex;
            }
            nav.navbar {
                display: none;
            }
            .logo {
                display: none;
            }

        }
        .main-content {
            padding-top: 70px;
        }
        @media (max-width: 768px) {
            .main-content {
                padding-top: 30px;
            }
        }

        .welcome-message {
            display: none;
        }

        @media (max-width: 767px) {
            .welcome-message {
                display: block;
                position: fixed;
                top: 5px;
                left: 45%;
                transform: translateX(-50%);
                background: rgba(146, 101, 166, 0.9);
                color: white;
                padding: 8px 15px;
                border-radius: 20px;
                font-size: 2.3rem;
                font-weight: 600;
                z-index: 9999;
                animation: zigzagSlideDown 7s ease infinite;
                pointer-events: none;
                box-shadow: 0 0 10px rgba(146, 101, 166, 0.7);
                white-space: nowrap;
            }
        }

        @media (max-width: 1065px) and (min-width: 768px){
            .welcome-message {
                display: block;
                position: fixed;
                top: 2px;
                left: 45%;
                transform: translateX(-50%);
                background: rgba(146, 101, 166, 0.9);
                color: white;
                padding: 8px 15px;
                border-radius: 20px;
                font-size: 3rem;
                font-weight: 600;
                z-index: 9999;
                animation: zigzagSlideDown 7s ease infinite;
                pointer-events: none;
                box-shadow: 0 0 10px rgba(146, 101, 166, 0.7);
                white-space: nowrap;
            }
        }

        @keyframes zigzagSlideDown {
            0% {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
            20% {
                opacity: 1;
                transform: translateX(calc(-50% - 10px)) translateY(0);
            }
            40% {
                opacity: 1;
                transform: translateX(calc(-50% + 10px)) translateY(0);
            }
            60% {
                opacity: 1;
                transform: translateX(calc(-50% - 10px)) translateY(0);
            }
            80% {
                opacity: 1;
                transform: translateX(calc(-50% + 10px)) translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
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
            <div class="stock-status">❌ Out of Stock</div>
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
<footer>© 2024 Data Coming . All Rights Reserved</footer>
</body>
</html>
