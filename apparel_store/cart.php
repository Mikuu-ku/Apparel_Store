<?php
session_start();
include "config/database.php";

$v = "1.7"; // Updated version

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_to_cart'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    $check_cart = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'");

    if (mysqli_num_rows($check_cart) > 0) {
        $cart_row = mysqli_fetch_assoc($check_cart);
        $new_qty = $cart_row['quantity'] + $quantity;
        mysqli_query($conn, "UPDATE cart SET quantity = $new_qty WHERE id = " . $cart_row['id']);
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, size, quantity) VALUES ($user_id, $product_id, '$size', $quantity)");
    }
    
    header("Location: cart.php");
    exit();
}

$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$count_row = mysqli_fetch_assoc($count_res);
$cart_count = $count_row['total'] ?? 0;

$cart_query = "SELECT c.*, p.name, p.price, p.image 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id";
$cart_items = mysqli_query($conn, $cart_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Bag | Apparel Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/logo.png?v=<?php echo $v; ?>" alt="Logo" class="header-logo">
            </a>
        </div>

        <div class="header-right">
            <a href="#" class="header-icon search-spacer" title="Search">
                <i class="fas fa-search"></i>
            </a>

            <a href="logout.php" class="header-icon" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>

            <span class="divider">|</span>

            <a href="cart.php" class="header-icon" title="Cart">
                <i class="fas fa-shopping-bag"></i>
                <?php if($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<main class="container">
    <?php if(isset($_GET['status']) && $_GET['status'] == 'removed'): ?>
        <div id="status-msg" style="background: #fdfdfd; border: 1px solid #000; padding: 15px; margin-bottom: 30px; text-align: center;">
            <p style="font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; margin: 0; color: #000;">
                Item successfully removed from bag
            </p>
        </div>
        <script>
            setTimeout(() => {
                const msg = document.getElementById('status-msg');
                if(msg) msg.style.display = 'none';
            }, 3000);
        </script>
    <?php endif; ?>

    <h2 style="text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px;">Your Shopping Bag</h2>
    
    <?php if(mysqli_num_rows($cart_items) > 0): ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead style="border-bottom: 2px solid #111;">
                    <tr style="text-align: left; text-transform: uppercase; font-size: 11px; letter-spacing: 1px;">
                        <th style="padding: 15px 10px;">Product</th>
                        <th style="padding: 15px 10px;">Size</th>
                        <th style="padding: 15px 10px;">Price</th>
                        <th style="padding: 15px 10px;">Quantity</th>
                        <th style="padding: 15px 10px;">Subtotal</th>
                        <th style="padding: 15px 10px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_amount = 0;
                    while($item = mysqli_fetch_assoc($cart_items)): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total_amount += $subtotal;
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px 10px; display: flex; align-items: center; gap: 15px;">
                            <img src="assets/images/<?php echo $item['image']; ?>?v=<?php echo $v; ?>" style="width: 60px; height: auto;">
                            <span style="font-weight: 500; font-size: 13px;"><?php echo htmlspecialchars($item['name']); ?></span>
                        </td>
                        <td style="padding: 15px 10px; font-size: 13px;"><?php echo $item['size']; ?></td>
                        <td style="padding: 15px 10px; font-size: 13px;">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td style="padding: 15px 10px; font-size: 13px;"><?php echo $item['quantity']; ?></td>
                        <td style="padding: 15px 10px; font-size: 13px; font-weight: 700;">₱<?php echo number_format($subtotal, 2); ?></td>
                        <td style="padding: 15px 10px; text-align: right;">
                            <a href="javascript:void(0)" 
                               onclick="openRemoveModal('<?php echo $item['id']; ?>', '<?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>')"
                               style="color: #ff0000; text-decoration: none; font-size: 10px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">
                               Remove
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="text-align: right; border-top: 2px solid #111; padding-top: 20px;">
            <h3 style="text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">Total: ₱<?php echo number_format($total_amount, 2); ?></h3>
            <button class="btn-add-cart" style="width: auto; padding: 15px 50px;">Proceed to Checkout</button>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 80px 0;">
            <p style="color: #888; text-transform: uppercase; letter-spacing: 2px;">Your bag is currently empty.</p>
            <a href="index.php" class="btn-add-cart" style="display: inline-block; width: auto; padding: 15px 30px; text-decoration: none; margin-top: 20px;">Continue Shopping</a>
        </div>
    <?php endif; ?>
</main>

<div id="removeModal" class="custom-modal">
    <div class="modal-card">
        <h3>Remove Item?</h3>
        <p id="removeText"></p>
        <div class="modal-btn-group">
            <button class="btn-modal-cancel" onclick="closeRemoveModal()">Cancel</button>
            <a id="confirmRemoveBtn" href="#" class="btn-modal-confirm">Remove</a>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<script>
    function openRemoveModal(id, name) {
        document.getElementById('removeText').innerHTML = "Are you sure you want to remove<br><strong>" + name + "</strong> from your bag?";
        document.getElementById('confirmRemoveBtn').href = "remove_cart.php?id=" + id;
        document.getElementById('removeModal').style.display = 'flex';
    }

    function closeRemoveModal() {
        document.getElementById('removeModal').style.display = 'none';
    }

    window.onclick = function(e) {
        if (e.target == document.getElementById('removeModal')) {
            closeRemoveModal();
        }
    }
</script>

</body>
</html>