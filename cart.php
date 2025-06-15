<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CART</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="./styles/cart.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />


</head>
<body>

<header>

    <a href="./php/index.php" class="logo aa">
        <span class="outlined-text">Data Coming</span><span>.</span>
    </a>
    <nav class="navbar">
        <div class="home-btn"><a href="./php/index.php" class="fas fa-home"> Home</a></div>

        <div class="pc-btn">
            <a href="#" class="fas fa-desktop"> PC</a>
            <div class="dropdown-multicolumn">
                <div class="column">
                    <a href="./php/case.php">CASE</a>
                    <a href="./php/mother.php">MOTHER BOARD</a>
                    <a href="./php/cpu.php">CPU</a>
                    <a href="./php/gui.php">GRAPHIC CARDS</a>

                </div>
                <div class="column">
                    <a href="./php/ssd.php">SSD</a>
                    <a href="./php/ram.php">MEMORY</a>
                    <a href="./php/harddisk.php">HARD DISK</a>
                    <a href="./php/power.php">POWER SUPPLY</a>
                </div>
            </div>
        </div>




        <div class="laptop-btn"><a href="./php/laptop.php" class="fas fa-laptop"> laptop</a></div>
        <div class="console-btn"><a href="./php/xbox.php" class="fab fa-xbox"> console</a></div>
        <div class="cata-btn">
            <a href="#" class="fas fa-mouse"> ACCESSORIES</a>
            <div class="dropdown-multicolumn2">
                <div class="column">
                    <a href="./php/headset.php">HEAD SET</a>
                    <a href="./php/mouse.php">MOUSE</a>
                    <a href="./php/keayboard.php">KEYBOARD</a>

                </div>
                <div class="column">
                    <a href="./php/chair.php">CHAIR</a>
                    <a href="./php/monitor.php">MONITOR</a>
                    <a href="./php/hdmi.php">CABLES AND PORTS</a>
                </div>
            </div>
        </div>
        <div class="search-btn"><a href="#" onclick="document.getElementById('search1').style.display='block'; return false;" class="fas fa-search "></a></div>


        <div class="search" id="search1">
            <input type="text" placeholder="search...">
            <i class="fas fa-search"></i>
            <button id="closeSearch" class="close-btn">✖</button>
        </div>
    </nav>

    <div class="icons">
        <a href="https://www.facebook.com/profile.php?id=61556946718232" class="fab fa-facebook-f" target="_blank"></a>
        <a href="https://www.instagram.com/datac0ming?igsh=MThhMWs5ZHA5MTZneA==" class="fab fa-instagram" target="_blank"></a>
        <a href="cart.html" class="fas fa-shopping-cart"></a>
        <a href="./php/login.php" class="fas fa-user"></a>
    </div>
</header>

<main style="padding-top: 70px" class="content">
    <div id="messageBox">

    </div>

    <div class="container">

        <div class="info">
            <h1>Order Summary</h1>


            <div class="items-container">

            </div>



            <div class="total">
                <p>total :</p>
                <p id="cart-total">₪0.00</p>
            </div>
        </div>

        <div class="payment">
            <h1>Payment Information</h1>
            <form>
                <div class="name-group">
                    <input type="text" required  class="fname" placeholder="First name">
                    <input type="text" required  class="lname" placeholder="Last Name">
                </div>
                <input class="inputs" required type="text" placeholder="Street / Postal code">
                <a href="https://postcode.palestine.ps/" class="aa" target="_blank">Don't know your area code?</a>
                <input class="inputs" required type="text" placeholder="Email">
                <input class="inputs" required type="text" placeholder="Phone number">



                <select class="sel">
                    <option>القدس</option>
                    <option>رام الله</option>
                    <option>بيت لحم</option>
                    <option>الخليل</option>
                    <option>نابلس</option>
                    <option>قلقيلية</option>
                    <option>طولكرم</option>
                    <option>سلفيت</option>
                    <option>جنين</option>
                    <option>أريحا</option>
                    <option>طوباس</option>
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
<footer>© 2024 data coming . All Rights Reserved</footer>
<script src="./js/cart.js"></script>
</body>
</html>