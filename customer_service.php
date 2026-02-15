<?php
session_start();
include "config/database.php";

$allowed_pages = ['contact', 'faqs', 'size-guide'];
$page = isset($_GET['page']) && in_array($_GET['page'], $allowed_pages) ? $_GET['page'] : 'contact';

$display_name = "USER";
if (isset($_SESSION['user_id'])) {
    $display_name = $_SESSION['first_name'] ?? 'USER';
}

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
    <title>Customer Service | Apparel's</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* HEADER - ALL ELEMENTS RIGHT ALIGNED */
        .header {
            background: #fff;
            padding: 25px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .header-container {
            display: flex;
            justify-content: flex-end; /* Pushes everything to the right */
            align-items: center;
            gap: 20px;
        }

        .header-logo {
            height: 22px; /* Small logo as seen in image */
            width: auto;
        }

        .header-right-group {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-link {
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: #000;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .divider {
            height: 24px;
            width: 1px;
            background: #000;
            margin: 0 10px;
        }

        .header-icon {
            color: #000;
            font-size: 18px;
            text-decoration: none;
        }

        /* PAGE LAYOUT */
        .cs-layout { display: flex; gap: 80px; margin-top: 60px; margin-bottom: 150px; }
        .cs-sidebar { width: 180px; flex-shrink: 0; }
        .cs-sidebar h3 { font-size: 10px; letter-spacing: 3px; margin-bottom: 30px; text-transform: uppercase; font-weight: 800; }
        .cs-nav { list-style: none; padding: 0; }
        .cs-nav li { margin-bottom: 20px; }
        .cs-nav a { text-decoration: none; font-size: 11px; color: #888; letter-spacing: 1px; text-transform: uppercase; transition: 0.3s; }
        .cs-nav a:hover, .cs-nav a.active { color: #000; font-weight: 800; }

        .cs-content { flex: 1; max-width: 650px; }
        .cs-header-title { text-transform: uppercase; letter-spacing: 4px; font-size: 22px; font-weight: 800; margin-bottom: 5px; }
        .cs-subtext { font-size: 10px; color: #999; margin-bottom: 50px; text-transform: uppercase; letter-spacing: 2px; }

        /* FORM STYLING */
        .cs-input-group { margin-bottom: 30px; }
        .cs-input-group label { display: block; font-size: 9px; font-weight: 700; color: #000; letter-spacing: 1px; margin-bottom: 8px; }
        .cs-form-input {
            width: 100%; padding: 12px 0; border: none; border-bottom: 1px solid #e0e0e0;
            font-size: 12px; outline: none; transition: 0.3s; background: transparent;
        }
        .cs-form-input:focus { border-bottom-color: #000; }
        .cs-submit-btn {
            background: #000; color: #fff; border: none; padding: 18px 45px;
            font-size: 11px; letter-spacing: 2px; font-weight: 700; text-transform: uppercase;
            cursor: pointer; transition: 0.3s;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="cs-layout">
        <aside class="cs-sidebar">
            <h3>Support</h3>
            <ul class="cs-nav">
                <li><a href="?page=contact" class="<?= $page == 'contact' ? 'active' : '' ?>">Contact Us</a></li>
                <li><a href="?page=faqs" class="<?= $page == 'faqs' ? 'active' : '' ?>">Common FAQs</a></li>
                <li><a href="?page=size-guide" class="<?= $page == 'size-guide' ? 'active' : '' ?>">Size Guide</a></li>
            </ul>
        </aside>

        <section class="cs-content">
            <?php if($page == 'contact'): ?>
                <h2 class="cs-header-title">Get in Touch</h2>
                <p class="cs-subtext">We'd love to hear from you.</p>
                <form id="contactForm" action="process_contact.php" method="POST">
                    <div class="cs-input-group">
                        <label>FULL NAME</label>
                        <input type="text" name="name" id="name" class="cs-form-input">
                    </div>
                    <div class="cs-input-group">
                        <label>EMAIL ADDRESS</label>
                        <input type="email" name="email" id="email" class="cs-form-input">
                    </div>
                    <div class="cs-input-group">
                        <label>MESSAGE</label>
                        <textarea name="message" id="message" class="cs-form-input" style="height: 80px; resize: none;"></textarea>
                    </div>
                    <button type="submit" class="cs-submit-btn">Send Inquiry</button>
                </form>
            <?php elseif($page == 'faqs'): ?>
                <h2 class="cs-header-title">FAQs</h2>
                <p class="cs-subtext">Common Questions</p>
                <?php elseif($page == 'size-guide'): ?>
                <h2 class="cs-header-title">Size Guide</h2>
                <p class="cs-subtext">Measurements</p>
                <?php endif; ?>
        </section>
    </div>
</div>

</body>
</html>