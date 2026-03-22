<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? '';
$last_name = $_SESSION['last_name'] ?? ''; 

// Fetch Cart Data - We specifically pull 'size' and 'price' here
$cart_query = "SELECT c.*, p.name, p.price, p.image 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id";
$cart_items = mysqli_query($conn, $cart_query);

if (mysqli_num_rows($cart_items) == 0) {
    header("Location: cart.php");
    exit();
}

$shipping_fee = 50.00; 
$subtotal = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Apparel's</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* OVERLAY STYLES */
        #processOverlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #fff;
            z-index: 10000;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .checkout-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 50px; margin-top: 50px; }
        .section-title { text-transform: uppercase; letter-spacing: 3px; font-size: 12px; font-weight: 800; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 30px; }

        .input-box { margin-bottom: 25px; position: relative; }
        .input-box label { display: block; font-size: 9px; font-weight: 700; letter-spacing: 1px; margin-bottom: 8px; color: #888; }
        .form-input { width: 100%; padding: 12px 0; border: none; border-bottom: 1px solid #ddd; font-family: inherit; font-size: 14px; outline: none; background: transparent; transition: 0.3s; }
        .form-input:focus { border-bottom-color: #000; }
        
        .error-text { color: #ff0000; font-size: 9px; font-weight: 700; letter-spacing: 1px; margin-top: 5px; display: none; text-transform: uppercase; }
        .form-input.invalid { border-bottom-color: #ff0000; }

        .pay-option { padding: 20px; border: 1px solid #eee; margin-bottom: 10px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: 0.2s; }
        .pay-option.active { border-color: #000; background: #fafafa; }
        .pay-option span { font-size: 11px; font-weight: 700; letter-spacing: 1px; }

        .dot { height: 8px; width: 8px; border-radius: 50%; border: 1px solid #000; display: inline-block; }
        .dot.filled { background-color: #000; }

        #gcash-verification { display: none; padding: 25px; background: #f9f9f9; border: 1px solid #eee; margin-top: -10px; margin-bottom: 20px; }

        .summary-sticky { position: sticky; top: 40px; background: #fff; padding: 20px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 13px; }

        .loader { width: 30px; height: 30px; border: 2px solid #f3f3f3; border-top: 2px solid #000; border-radius: 50%; animation: spin 0.8s linear infinite; margin-bottom: 20px; }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        @media (max-width: 900px) { .checkout-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div id="processOverlay">
    <div id="loadingUI">
        <div class="loader"></div>
        <p style="letter-spacing: 3px; font-size: 10px; font-weight: 700;">PROCESSING YOUR ORDER</p>
    </div>
    <div id="successUI" style="display: none;">
        <i class="fas fa-check-circle" style="font-size: 40px; color: #000; margin-bottom: 20px;"></i>
        <h2 style="letter-spacing: 2px; text-transform: uppercase; font-size: 24px; font-weight: 800; margin-bottom: 20px;">Order Confirmed</h2>
        <p style="font-size: 14px; color: #666; margin-bottom: 40px;">Thank you for shopping with Apparel's.</p>
        <a href="index.php" style="text-decoration: none; padding: 15px 40px; font-size: 11px; background: black; color: white; font-weight: 700; letter-spacing: 1px;">CONTINUE SHOPPING</a>
    </div>
</div>

<header class="header">
    <div class="container header-container" style="display: flex; align-items: center; justify-content: space-between; padding: 20px 0;">
        <div class="logo"><a href="index.php"><img src="assets/images/new_logo.jpg" style="height: 40px;"></a></div>
        <div class="header-right"><span style="font-size: 10px; font-weight: 800; letter-spacing: 1px;"><?= strtoupper($first_name . " " . $last_name) ?></span></div>
    </div>
</header>

<main class="container">
    <div class="checkout-grid">
        <section>
            <h3 class="section-title">Shipping Information</h3>
            <form id="checkoutForm" novalidate>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                    <div class="input-box">
                        <label>FIRST NAME</label>
                        <input type="text" id="fname" class="form-input" value="<?= htmlspecialchars($first_name) ?>" required>
                        <div class="error-text">Required field</div>
                    </div>
                    <div class="input-box">
                        <label>LAST NAME</label>
                        <input type="text" id="lname" class="form-input" value="<?= htmlspecialchars($last_name) ?>" required>
                        <div class="error-text">Required field</div>
                    </div>
                </div>

                <div class="input-box">
                    <label>PHONE NUMBER</label>
                    <input type="tel" id="phone" class="form-input" placeholder="09XXXXXXXXX" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    <div class="error-text" id="phone-error">Enter valid 11-digit number (09...)</div>
                </div>

                <div class="input-box">
                    <label>COMPLETE ADDRESS</label>
                    <input type="text" id="address" class="form-input" placeholder="House No, Street, Brgy, City, Province" required>
                    <div class="error-text">Required field</div>
                </div>

                <h3 class="section-title" style="margin-top: 50px;">Payment Method</h3>
                <div class="pay-option active" id="cod-box" onclick="setPayment('cod')">
                    <span>CASH ON DELIVERY</span>
                    <span class="dot filled" id="cod-dot"></span>
                </div>
                <div class="pay-option" id="gcash-box" onclick="setPayment('gcash')">
                    <span>GCASH (MANUAL VERIFY)</span>
                    <span class="dot" id="gcash-dot"></span>
                </div>

                <div id="gcash-verification">
                    <p style="font-size: 10px; font-weight: 700; margin-bottom: 20px; color: #555;">GCASH NAME: APPAREL STORE ADMIN<br>NUMBER: 0912 345 6789</p>
                    <div class="input-box">
                        <label>REFERENCE NUMBER (LAST 4 DIGITS)</label>
                        <input type="text" id="gcash-ref" class="form-input" placeholder="0000" maxlength="4">
                        <div class="error-text">Last 4 digits required</div>
                    </div>
                </div>
            </form>
        </section>

        <aside>
            <div class="summary-sticky">
                <h3 class="section-title">Order Summary</h3>
                <?php 
                mysqli_data_seek($cart_items, 0); // Reset pointer
                while($item = mysqli_fetch_assoc($cart_items)): 
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                <div class="summary-row">
                    <span><?= htmlspecialchars($item['name']) ?> (<?= $item['size'] ?>) x<?= $item['quantity'] ?></span>
                    <span style="font-weight: 700;">₱<?= number_format($item_total, 2) ?></span>
                </div>
                <?php endwhile; ?>

                <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                    <div class="summary-row" style="color: #888;"><span>SUBTOTAL</span><span>₱<?= number_format($subtotal, 2) ?></span></div>
                    <div class="summary-row" style="color: #888;"><span>SHIPPING</span><span>₱<?= number_format($shipping_fee, 2) ?></span></div>
                    <div class="summary-row" style="font-size: 18px; font-weight: 900; margin-top: 15px; border-top: 2px solid #000; padding-top: 15px;">
                        <span>TOTAL</span><span>₱<?= number_format($subtotal + $shipping_fee, 2) ?></span>
                    </div>
                </div>
                <button type="button" class="btn-save" onclick="handleOrder()" style="width: 100%; margin-top: 40px; height: 55px; font-size: 11px; background: #000; color: #fff; border: none; font-weight: 800; cursor: pointer; letter-spacing: 2px;">PLACE ORDER</button>
            </div>
        </aside>
    </div>
</main>

<script>
    let selectedMethod = 'cod';

    function setPayment(type) {
        selectedMethod = type;
        document.getElementById('cod-box').classList.toggle('active', type === 'cod');
        document.getElementById('gcash-box').classList.toggle('active', type === 'gcash');
        document.getElementById('cod-dot').classList.toggle('filled', type === 'cod');
        document.getElementById('gcash-dot').classList.toggle('filled', type === 'gcash');
        document.getElementById('gcash-verification').style.display = (type === 'gcash') ? 'block' : 'none';
    }

    function handleOrder() {
        let isValid = true;
        
        // Reset errors
        document.querySelectorAll('.error-text').forEach(e => e.style.display = 'none');
        document.querySelectorAll('.form-input').forEach(i => i.classList.remove('invalid'));

        // Validate Inputs
        ['fname', 'lname', 'address'].forEach(id => {
            const el = document.getElementById(id);
            if(!el.value.trim()){ el.nextElementSibling.style.display = 'block'; el.classList.add('invalid'); isValid = false; }
        });

        const ph = document.getElementById('phone');
        if (ph.value.length !== 11 || !ph.value.startsWith('09')) {
            document.getElementById('phone-error').style.display = 'block';
            ph.classList.add('invalid'); isValid = false;
        }

        if(selectedMethod === 'gcash') {
            const ref = document.getElementById('gcash-ref');
            if(ref.value.length < 4) { ref.nextElementSibling.style.display = 'block'; ref.classList.add('invalid'); isValid = false; }
        }

        if(isValid) {
            document.getElementById('processOverlay').style.display = 'flex';
            
            const formData = new FormData();
            formData.append('full_name', document.getElementById('fname').value + ' ' + document.getElementById('lname').value);
            formData.append('address', document.getElementById('address').value);
            formData.append('phone', document.getElementById('phone').value);
            formData.append('payment_method', selectedMethod.toUpperCase() + (selectedMethod === 'gcash' ? ' (Ref: ' + document.getElementById('gcash-ref').value + ')' : ''));

            fetch('process_order.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === "success") {
                    setTimeout(() => {
                        document.getElementById('loadingUI').style.display = 'none';
                        document.getElementById('successUI').style.display = 'block';
                    }, 2000);
                } else {
                    alert("Order Failed: " + data);
                    document.getElementById('processOverlay').style.display = 'none';
                }
            })
            .catch(err => {
                console.error(err);
                alert("Connection Error.");
                document.getElementById('processOverlay').style.display = 'none';
            });
        }
    }
</script>
</body>
</html>