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

// Fetch Cart Data
$cart_query = "SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id";
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
        /* FULL-SCREEN PERFECTLY CENTERED OVERLAY */
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

        /* PAYMENT DOT INDICATORS */
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
        <p style="letter-spacing: 3px; font-size: 10px; font-weight: 700;">VERIFYING PAYMENT</p>
    </div>
    <div id="successUI" style="display: none;">
        <i class="fas fa-check" style="font-size: 20px; margin-bottom: 40px; display: block;"></i>
        <h2 style="letter-spacing: 2px; text-transform: uppercase; font-size: 24px; font-weight: 800; margin-bottom: 20px;">Order Confirmed</h2>
        <p style="font-size: 14px; color: #000; margin-bottom: 40px;">Thank you for shopping with Apparel's.</p>
        <a href="index.php" class="btn-save" style="text-decoration: none; padding: 12px 30px; font-size: 11px; background: black; color: white;">CONTINUE SHOPPING</a>
    </div>
</div>

<header class="header">
    <div class="container header-container">
        <div class="logo"><a href="index.php"><img src="assets/images/new_logo.jpg" class="header-logo"></a></div>
        <div class="header-right"><span class="user-name-text"><?= strtoupper($first_name . " " . $last_name) ?></span></div>
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
                        <input type="text" id="fname" class="form-input" value="<?= $first_name ?>" required>
                        <div class="error-text">Please fill out this field.</div>
                    </div>
                    <div class="input-box">
                        <label>LAST NAME</label>
                        <input type="text" id="lname" class="form-input" value="<?= $last_name ?>" required>
                        <div class="error-text">Please fill out this field.</div>
                    </div>
                </div>

                <div class="input-box">
                    <label>PHONE NUMBER</label>
                    <input type="tel" id="phone" class="form-input" placeholder="09XXXXXXXXX" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    <div class="error-text" id="phone-error">Please enter a valid 11-digit number starting with 09.</div>
                </div>

                <div class="input-box">
                    <label>COMPLETE ADDRESS</label>
                    <input type="text" id="address" class="form-input" placeholder="Street, Barangay, City, Province" required>
                    <div class="error-text">Please fill out this field.</div>
                </div>

                <h3 class="section-title" style="margin-top: 50px;">Payment</h3>
                <div class="pay-option active" id="cod-box" onclick="setPayment('cod')">
                    <span>CASH ON DELIVERY</span>
                    <span class="dot filled" id="cod-dot"></span>
                </div>
                <div class="pay-option" id="gcash-box" onclick="setPayment('gcash')">
                    <span>GCASH INSTANT VERIFY</span>
                    <span class="dot" id="gcash-dot"></span>
                </div>

                <div id="gcash-verification">
                    <p style="font-size: 10px; font-weight: 700; margin-bottom: 20px; color: #555;">SEND PAYMENT TO: 0912 345 6789</p>
                    <div class="input-box">
                        <label>REFERENCE NUMBER (LAST 4 DIGITS)</label>
                        <input type="text" id="gcash-ref" class="form-input" placeholder="0000" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <div class="error-text">Reference number is required.</div>
                    </div>
                </div>
            </form>
        </section>

        <aside>
            <div class="summary-sticky">
                <h3 class="section-title">Summary</h3>
                <?php while($item = mysqli_fetch_assoc($cart_items)): 
                    $price = $item['price'] * $item['quantity'];
                    $subtotal += $price;
                ?>
                <div class="summary-row"><span><?= $item['name'] ?> (x<?= $item['quantity'] ?>)</span><span style="font-weight: 700;">₱<?= number_format($price, 2) ?></span></div>
                <?php endwhile; ?>

                <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                    <div class="summary-row"><span>SHIPPING</span><span>₱<?= number_format($shipping_fee, 2) ?></span></div>
                    <div class="summary-row" style="font-size: 16px; font-weight: 800; margin-top: 15px;"><span>TOTAL</span><span>₱<?= number_format($subtotal + $shipping_fee, 2) ?></span></div>
                </div>
                <button type="button" class="btn-save" onclick="handleOrder()" style="width: 100%; margin-top: 40px; height: 50px; font-size: 11px;">PLACE ORDER</button>
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
        document.querySelectorAll('.error-text').forEach(e => e.style.display = 'none');
        document.querySelectorAll('.form-input').forEach(i => i.classList.remove('invalid'));

        // Basic Check
        ['fname', 'lname', 'address'].forEach(id => {
            const el = document.getElementById(id);
            if(!el.value.trim()){ el.nextElementSibling.style.display = 'block'; el.classList.add('invalid'); isValid = false; }
        });

        // Phone Validation
        const ph = document.getElementById('phone');
        if (ph.value.length !== 11 || !ph.value.startsWith('09')) {
            document.getElementById('phone-error').style.display = 'block';
            ph.classList.add('invalid'); isValid = false;
        }

        // GCash Validation
        if(selectedMethod === 'gcash') {
            const ref = document.getElementById('gcash-ref');
            if(ref.value.length < 4) { ref.nextElementSibling.style.display = 'block'; ref.classList.add('invalid'); isValid = false; }
        }

        if(isValid) {
            document.getElementById('processOverlay').style.display = 'flex';
            
            const formData = new FormData();
            formData.append('full_name', document.getElementById('fname').value + ' ' + document.getElementById('lname').value);
            formData.append('address', document.getElementById('address').value + ' | Phone: ' + document.getElementById('phone').value);
            formData.append('total_amount', '<?= $subtotal + $shipping_fee ?>');
            formData.append('payment_method', selectedMethod.toUpperCase() + (selectedMethod === 'gcash' ? ' (Ref: ' + document.getElementById('gcash-ref').value + ')' : ''));

            fetch('process_order.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                if(data.trim() === "success") {
                    setTimeout(() => {
                        document.getElementById('loadingUI').style.display = 'none';
                        document.getElementById('successUI').style.display = 'block';
                    }, 2000);
                }
            });
        }
    }
</script>
</body>
</html>