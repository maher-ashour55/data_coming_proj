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
    </style>
</head>
<body>
<div class="main-content">
<header>
    <a href="index.php" class="logo aa"><span class="outlined-text">Data Coming</span><span>.</span></a>
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
        <a href="https://www.facebook.com/profile.php?id=61556946718232" class="fab fa-facebook-f" target="_blank"></a>
        <a href="https://www.instagram.com/datac0ming" class="fab fa-instagram" target="_blank"></a>
        <a href="cart.php" class="fas fa-shopping-cart"></a>
        <a href="login.php" class="fas fa-user"></a>
    </div>
</header>
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
<footer>© 2024 Data Coming . All Rights Reserved</footer>
</body>
</html>
