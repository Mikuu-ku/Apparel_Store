<?php
session_start();
include "config/database.php";

// Logic for Dynamic Dashboard Content
$allowed_pages = ['contact', 'faqs', 'size-guide'];
$page = isset($_GET['page']) && in_array($_GET['page'], $allowed_pages) ? $_GET['page'] : 'contact';

// User Session Logic for Header
$display_name = "USER";
if (isset($_SESSION['user_id'])) {
    $display_name = $_SESSION['first_name'] ?? 'USER';
}

// Cart Count Logic for Header
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
    $count_row = mysqli_fetch_assoc($count_res);
    $cart_count = $count_row['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* DASHBOARD LAYOUT */
        .cs-layout { display: flex; gap: 80px; margin-top: 60px; margin-bottom: 120px; align-items: flex-start; }
        
        /* MINIMALIST SIDEBAR */
        .cs-sidebar { width: 180px; flex-shrink: 0; }
        .cs-sidebar h3 { font-size: 11px; letter-spacing: 2.5px; margin-bottom: 30px; font-weight: 700; color: #000; text-transform: uppercase; }
        .cs-nav { list-style: none; padding: 0; margin: 0; }
        .cs-nav li { margin-bottom: 18px; }
        .cs-nav a { text-decoration: none; font-size: 10px; color: #aaa; letter-spacing: 1.5px; text-transform: uppercase; transition: 0.3s ease; }
        .cs-nav a:hover, .cs-nav a.active { color: #000; font-weight: 700; }
        .cs-nav a.active { border-bottom: 1.5px solid #000; padding-bottom: 3px; }
        
        /* CONTENT AREA */
        .cs-content { flex: 1; max-width: 650px; }
        .cs-content h2 { text-transform: uppercase; letter-spacing: 3px; font-size: 20px; margin-bottom: 8px; font-weight: 700; color: #000; }
        .cs-subtext { font-size: 10px; color: #888; margin-bottom: 45px; text-transform: uppercase; letter-spacing: 1.5px; }

        /* MINIMALIST FORM STYLING */
        .input-group { margin-bottom: 5px; position: relative; width: 100%; }
        .input-group input, .cs-form textarea {
            width: 100%;
            padding: 18px;
            border: 1px solid #e5e5e5;
            font-size: 11px;
            letter-spacing: 1.2px;
            box-sizing: border-box;
            font-family: inherit;
            outline: none;
            text-transform: uppercase;
        }
        .cs-form textarea { height: 200px; resize: none; margin-bottom: 0; }
        .cs-form textarea::placeholder { color: #aaa; }
        .input-group input:focus, .cs-form textarea:focus { border: 1px solid #111; }
        
        /* ERROR MESSAGE AT BOTTOM OF CONTAINER */
        .error-msg {
            color: #ff4d4d;
            font-size: 9px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 15px;
            display: block;
            min-height: 12px;
        }

        .cs-submit-btn {
            width: 100%;
            padding: 18px;
            background: #000;
            color: #fff;
            border: none;
            font-size: 12px;
            letter-spacing: 3px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            transition: 0.3s;
            margin-top: 10px;
        }
        .cs-submit-btn:hover { background: #333; }

        /* MINIMALIST TABLE */
        .size-table { width: 100%; border-collapse: collapse; font-size: 11px; letter-spacing: 1px; }
        .size-table th { text-align: left; padding: 15px 10px; border-bottom: 2px solid #000; text-transform: uppercase; }
        .size-table td { padding: 15px 10px; border-bottom: 1px solid #f2f2f2; color: #666; }
    </style>
</head>
<body>

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php"><img src="assets/images/new_logo.jpg" alt="Logo" class="header-logo"></a>
        </div>
        <div class="header-right">
            <div class="search-wrapper">
                <input type="text" id="searchInput" class="search-input" placeholder="SEARCH...">
                <a href="javascript:void(0)" class="header-icon" id="searchBtn"><i class="fas fa-search"></i></a>
            </div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <a href="javascript:void(0)" class="header-icon">
                        <i class="fas fa-user-circle"></i> 
                        <span class="user-name-text"><?= strtoupper(htmlspecialchars($display_name)) ?></span>
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" class="header-icon"><i class="fas fa-user"></i></a>
            <?php endif; ?>
            <span class="divider">|</span>
            <a href="cart.php" class="header-icon">
                <i class="fas fa-shopping-bag"></i>
                <?php if($cart_count > 0): ?><span class="cart-count"><?= $cart_count ?></span><?php endif; ?>
            </a>
        </div>
    </div>
</header>

<div class="container">
    <div class="cs-layout">
        
        <aside class="cs-sidebar">
            <h3>Help Center</h3>
            <ul class="cs-nav">
                <li><a href="customer_service.php?page=contact" class="<?= $page == 'contact' ? 'active' : '' ?>">Contact Us</a></li>
                <li><a href="customer_service.php?page=faqs" class="<?= $page == 'faqs' ? 'active' : '' ?>">FAQs</a></li>
                <li><a href="customer_service.php?page=size-guide" class="<?= $page == 'size-guide' ? 'active' : '' ?>">Size Guide</a></li>
            </ul>
        </aside>

        <section class="cs-content">
            <?php if($page == 'contact'): ?>
                <h2>Contact Us</h2>
                <p class="cs-subtext">Reach out to our support team</p>
                
                <form id="contactForm" action="process_contact.php" method="POST" class="auth-form cs-form" novalidate>
                    <div class="input-group">
                        <input type="text" name="name" id="name" placeholder="NAME">
                        <span class="error-msg" id="nameError"></span>
                    </div>

                    <div class="input-group">
                        <input type="email" name="email" id="email" placeholder="EMAIL ADDRESS">
                        <span class="error-msg" id="emailError"></span>
                    </div>

                    <div class="input-group">
                        <textarea name="message" id="message" placeholder="MESSAGE"></textarea>
                        <span class="error-msg" id="messageError"></span>
                    </div>

                    <button type="submit" class="cs-submit-btn">Send Message</button>
                </form>

            <?php elseif($page == 'faqs'): ?>
                <h2>FAQs</h2>
                <p class="cs-subtext">Common Questions & Answers</p>
                <div style="margin-bottom: 40px;">
                    <h4 style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px; color: #000;">Shipping Information</h4>
                    <p style="font-size: 13px; color: #555; line-height: 1.7;">Standard delivery takes 3-5 business days for Metro Manila.</p>
                </div>

            <?php elseif($page == 'size-guide'): ?>
                <h2>Size Guide</h2>
                <p class="cs-subtext">Find your perfect fit (Inches)</p>
                <table class="size-table">
                    <thead>
                        <tr><th>Size</th><th>Chest</th><th>Length</th><th>Shoulder</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>S</td><td>20"</td><td>27"</td><td>17.5"</td></tr>
                        <tr><td>M</td><td>21"</td><td>28"</td><td>18.5"</td></tr>
                        <tr><td>L</td><td>22"</td><td>29"</td><td>19.5"</td></tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4 class="footer-title">Customer Service</h4>
            <ul class="footer-links">
                <li><a href="customer_service.php?page=contact">Contact Us</a></li>
                <li><a href="customer_service.php?page=size-guide">Size Guide</a></li>
                <li><a href="customer_service.php?page=faqs">FAQs</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4 class="footer-title">Follow Us</h4>
            <div class="social-links">
                <a href="#" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" target="_blank" class="social-icon"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<script>
if (document.getElementById('contactForm')) {
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        let hasError = false;
        
        // Reset all error messages
        document.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
        
        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const message = document.getElementById('message');

        if (!name.value.trim()) {
            document.getElementById('nameError').innerText = 'Please fill out your name';
            hasError = true;
        }

        if (!email.value.trim()) {
            document.getElementById('emailError').innerText = 'Please fill out your email';
            hasError = true;
        } else if (!email.value.includes('@')) {
            document.getElementById('emailError').innerText = 'Please enter a valid email address';
            hasError = true;
        }

        if (!message.value.trim()) {
            document.getElementById('messageError').innerText = 'Please enter your message';
            hasError = true;
        }

        if (hasError) {
            e.preventDefault(); 
        }
    });
}
</script>

</body>
</html>