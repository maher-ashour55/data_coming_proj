<?php
$conn = new mysqli("localhost", "u251541401_maher_user", "Datacoming12345", "u251541401_datacoming");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("Product ID is required");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM product WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows !== 1) {
    die("Product not found");
}

$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Edit Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <style>
        :root {
            --main-color: #9265a6;
            --accent-color: #ffb74d;
            --input-bg: #f4f4f4;
            --text-color: #333;
            --border-radius: 10px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            direction: ltr;
            border: #9265a6 2px solid;
        }
        h2 {
            text-align: left;
            color: var(--main-color);
            margin-bottom: 30px;
        }
        form {
            display: flex;
            gap: 30px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .left-col { flex: 1 1 55%; display: flex; flex-direction: column; gap: 40px; }
        .left-col > div, .right-col > div { display: flex; flex-direction: column; }
        .right-col { flex: 1 1 40%; display: flex; flex-direction: column; gap: 20px; }
        .image-upload {
            background: #eee;
            border: 2px dashed #aaa;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s;
            min-height: 320px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .image-upload:hover { background: #ddd; }
        .image-upload input[type="file"] { display: none; }
        #preview {
            max-width: 100%;
            max-height: 240px;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            object-fit: contain;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .image-text { font-size: 18px; color: #666; user-select: none; }
        label { margin-bottom: 8px; color: var(--text-color); font-weight: 600; }
        textarea, input[type="text"], input[type="number"], select {
            padding: 12px;
            background: var(--input-bg);
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
            font-size: 16px;
            width: 100%;
        }
        textarea { resize: vertical; min-height: 120px; }
        .submit-btn {
            margin-top: 30px;
            padding: 15px;
            background: var(--main-color);
            color: white;
            font-size: 18px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        .submit-btn:hover { background: #7b4d8f; }
        @media (max-width: 768px) {
            form { flex-direction: column; }
            .left-col, .right-col { flex: 1 1 100%; }
        }
        #message-box {
            display: none;
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            font-weight: 600;
            z-index: 1000;
            max-width: 90%;
            text-align: center;
            font-family: 'Cairo', sans-serif;
            border: black 1px solid;
        }
        .logout {
            margin-top: auto;
            padding-top: 40px;
        }

        .logout a {
            background-color: #e74c3c;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }

        .logout a:hover {
            background-color: #c0392b;
        }

        .logout i {
            margin-right: 8px;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #9265A6, #6d4c82);
            color: white;
            padding: 30px 20px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .sidebar h2 {
            margin-bottom: 40px;
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: white;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 8px;
            transition: background 0.3s ease;
            font-size: 16px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar a i {
            margin-right: 12px;
            font-size: 22px;
        }
    </style>
</head>
<body>
<div id="message-box"></div>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php"><i class='bx bx-home'></i> HOME</a>
    <a href="add.html"><i class='bx bx-plus-circle'></i> Add Product</a>
    <a href="manage_products.php" class="active"><i class='bx bx-edit'></i> Edit Products</a>
    <a href="view_orders.php"><i class='bx bx-cart'></i> View Orders</a>

    <div class="logout">
        <a href="../logout.php"><i class='bx bx-log-out'></i> Logout</a>
    </div>
</div>

<div class="container">
    <h2>Edit Product</h2>
    <form id="editForm" method="POST" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id" value="<?= $product['id'] ?>" />
        <div class="left-col">
            <div>
                <label for="name">Product Name</label>
                <input type="text" id="name" name="product_name" value="<?= htmlspecialchars($product['name']) ?>" required />
            </div>
            <div>
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" min="1" value="<?= $product['price'] ?>" required />
            </div>


            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="discount_price" style="font-weight: 600; color: var(--text-color); white-space: nowrap;">Discount Price</label>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input
                            type="number"
                            id="discount_price"
                            name="discount_price"
                            step="0.01"
                            min="0"
                            value="<?= $product['discount_price'] ?>"
                        <?= ($product['discount_price'] > 0) ? '' : 'disabled' ?>
                            style="flex-grow: 1; padding: 12px; border-radius: var(--border-radius); border: 1px solid #ccc; font-size: 16px; height: 40px; box-sizing: border-box;"
                    />
                    <label for="enable_discount" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 600; color: var(--text-color); user-select: none; white-space: nowrap;">
                        <input
                                type="checkbox"
                                id="enable_discount"
                                name="enable_discount"
                            <?= ($product['discount_price'] > 0) ? 'checked' : '' ?>
                                style="cursor: pointer; width: 18px; height: 18px;"
                        />
                        Enable Discount
                    </label>
                </div>
                <small id="discount-error" style="color: red; display: none; margin-top: 4px;">Discount price must be less than the original price.</small>
            </div>

            <div>
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" min="0" value="<?= $product['stock'] ?>" required />
            </div>
            <div>
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <?php
                    $categories = ["Processors", "Graphics Cards", "Case", "Power supply", "SSD", "ram", "Hard Disk", "Mother board", "Monitor", "Laptop", "Mouse", "Keyboard", "Head set", "Chair", "console", "CABLES AND PORTS"];
                    foreach ($categories as $cat) {
                        $selected = $product['category'] === $cat ? "selected" : "";
                        echo "<option value=\"$cat\" $selected>$cat</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="right-col">
            <div class="image-upload" onclick="document.getElementById('product_image').click()">
                <img id="preview" src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Image Preview" />
                <div class="image-text">📷 Click to select new image (optional)</div>
                <input type="file" id="product_image" name="image" accept="image/*" />
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
        </div>
        <button type="submit" class="submit-btn">💾 Update Product</button>
    </form>
</div>

<script>
    const discountInput = document.getElementById('discount_price');
    const enableDiscount = document.getElementById('enable_discount');
    const priceInput = document.getElementById('price');
    const discountError = document.getElementById('discount-error');

    function toggleDiscountInput() {
        if (enableDiscount.checked) {
            discountInput.disabled = false;
        } else {
            discountInput.disabled = true;
            discountInput.value = '';
            discountError.style.display = 'none';
        }
    }

    enableDiscount.addEventListener('change', toggleDiscountInput);

    document.getElementById('product_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    });

    toggleDiscountInput();

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const price = parseFloat(priceInput.value);
        const discount = parseFloat(discountInput.value);
        const discountEnabled = enableDiscount.checked;

        if (discountEnabled) {
            if (isNaN(discount) || discount <= 0) {
                discountError.textContent = "Discount price must be greater than 0.";
                discountError.style.display = 'block';
                discountInput.focus();
                return;
            }
            if (discount >= price) {
                discountError.textContent = "Discount price must be less than the original price.";
                discountError.style.display = 'block';
                discountInput.focus();
                return;
            }
        } else {
            discountError.style.display = 'none';
        }

        if (!discountEnabled) {
            discountInput.value = 0;
        }

        const formData = new FormData(this);

        fetch('update_product.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                showMessage(data.msg, data.success);
                if(data.success){
                    setTimeout(() => {
                        window.location.href = 'manage_products.php';
                    }, 2000);
                }
            })
            .catch(() => {
                showMessage('An error occurred while updating.', false);
            });
    });
    const messageBox = document.getElementById('message-box');

    function showMessage(text, success = true) {
        messageBox.textContent = text;
        messageBox.style.backgroundColor = success ? '#4CAF50' : '#f44336';
        messageBox.style.display = 'block';

        setTimeout(() => {
            messageBox.style.display = 'none';
            window.location.href = 'manage_products.php';
        }, 2500);
    }
</script>

</body>
</html>
