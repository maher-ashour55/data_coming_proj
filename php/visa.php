<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VISA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../styles/visa.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body>
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




        <div class="laptop-btn"><a href="laptop.hpp" class="fas fa-laptop"> laptop</a></div>
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



    </nav>

    <div class="icons">
        <a href="https://www.facebook.com/profile.php?id=61556946718232" class="fab fa-facebook-f" target="_blank"></a>
        <a href="https://www.instagram.com/datac0ming?igsh=MThhMWs5ZHA5MTZneA==" class="fab fa-instagram" target="_blank"></a>
        <a href="cart.php" class="fas fa-shopping-cart"></a>
        <a href="login.php" class="fas fa-user"></a>
    </div>
</header>

<main style="padding-top: 70px">
    <div class="payment-form">
        <div class="form-title">Pay With Credit Card</div>
        <label>Name on card</label>
        <input type="text" placeholder="enter name">

        <label>Card Number</label>
        <input type="text" placeholder="1234 5678 9012 3456">

        <label>Expiry Date</label>
        <input type="month" placeholder="MM / YY">



        <label>Phone Number</label>
        <input type="tel" placeholder="05XXXXXXXX">

        <label>CVV</label>
        <input type="text" placeholder="123">

        <label>Holder ID</label>
        <input type="text" placeholder="ID number">



        <button class="confirm-btn">Confirm Payment</button>
    </div>

    <table >
        <tr><td>Order number :</td><td>180737</td></tr>
        <tr><td>Date :</td><td id="current-date"></td></tr>
        <tr><td>Total :</td><td id="order-total"></td></tr>

        <tr><td>Payment method :</td><td>Secured payment by credit card</td></tr>
    </table>
</main>




<script>
    const today = new Date();
    const formattedDate = today.toLocaleDateString('en-GB'); // dd/mm/yyyy
    document.getElementById("current-date").textContent = formattedDate;
</script>
<script>


    const total = localStorage.getItem('cartTotal') || '₪0.00';
    document.getElementById('order-total').textContent = total;


    document.querySelector('.confirm-btn').addEventListener('click', function(e) {
        e.preventDefault();

        // جلب بيانات البطاقة من الحقول
        const nameOnCard = document.querySelector('input[placeholder="enter name"]').value.trim();
        const cardNumber = document.querySelector('input[placeholder="1234 5678 9012 3456"]').value.trim();
        const expiryDate = document.querySelector('input[type="month"]').value.trim();
        const phone = document.querySelector('input[placeholder="05XXXXXXXX"]').value.trim();
        const cvv = document.querySelector('input[placeholder="123"]').value.trim();
        const holderId = document.querySelector('input[placeholder="ID number"]').value.trim();

        // تحقق مبدئي
        if (!nameOnCard || !cardNumber || !expiryDate || !phone || !cvv || !holderId) {
            alert("Please fill all payment fields.");
            return;
        }

        if (cardNumber.replace(/\s/g, '').length < 16) {
            alert("Invalid card number.");
            return;
        }

        if (cvv.length < 3 || cvv.length > 4) {
            alert("Invalid CVV.");
            return;
        }

        // استرجاع بيانات الطلب المؤقتة من localStorage
        const pendingOrder = localStorage.getItem('pendingOrder');
        if (!pendingOrder) {
            alert("Order data missing.");
            return;
        }

        const orderData = JSON.parse(pendingOrder);

        // دمج بيانات الدفع مع بيانات الطلب
        const fullOrderData = {
            ...orderData,
            paymentDetails: {
                nameOnCard,
                cardNumber,
                expiryDate,
                phone,
                cvv,
                holderId
            }
        };

        // إرسال بيانات الطلب + الدفع للسيرفر (place_order.php)
        fetch('place_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(fullOrderData)
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert("Payment successful, order placed!");
                    localStorage.removeItem('pendingOrder');
                    window.location.href = 'index.php'; // صفحة شكراً/تأكيد الطلب
                } else {
                    alert("Payment failed: " + (data.message || "Unknown error"));
                }
            })
            .catch(() => alert("Network error, please try again."));
    });


</script>

</body>
</html>