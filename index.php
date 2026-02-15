<?php
session_start();
include "config/database.php";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}

$v = time(); // Changed to time() to force CSS/JS refresh during development

$display_name = "USER";
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['first_name'])) {
        $uid = $_SESSION['user_id'];
        $user_query = mysqli_query($conn, "SELECT first_name FROM users WHERE id = $uid");
        if ($user_row = mysqli_fetch_assoc($user_query)) {
            $_SESSION['first_name'] = $user_row['first_name'];
        }
    }
    $display_name = $_SESSION['first_name'] ?? 'USER';
}

$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$query_str = "SELECT * FROM products";
if ($category != '') {
    $query_str .= " WHERE category = '$category'"; 
}
$query_str .= " ORDER BY id DESC";
// Force fresh data from DB
$products = mysqli_query($conn, $query_str);

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
    <title>Apparel's Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/new_logo.jpg" alt="Apparel's Logo" class="header-logo">
            </a>
        </div>

        <div class="header-right">
            <div class="search-wrapper">
                <input type="text" id="searchInput" class="search-input" placeholder="SEARCH..." autocomplete="off">
                <a href="javascript:void(0)" class="header-icon" id="searchBtn">
                    <i class="fas fa-search"></i>
                </a>
                <div id="searchResultsPopup" class="search-results-popup"></div>
            </div>

            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <a href="javascript:void(0)" class="header-icon">
                        <i class="fas fa-user-circle"></i> 
                        <span class="user-name-text"><?= strtoupper(htmlspecialchars($display_name)) ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 8px; margin-left: 5px;"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fas fa-id-card"></i> My Profile</a>
                        <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                        <hr>
                        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="header-icon" title="Login"><i class="fas fa-user"></i></a>
            <?php endif; ?>

            <span class="divider">|</span>

            <a href="cart.php" class="header-icon" title="Bag">
                <i class="fas fa-shopping-bag"></i>
                <?php if($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<div class="category-nav-wrapper">
    <div class="container">
        <nav class="category-bar">
            <a href="index.php" class="cat-link <?php echo $category == '' ? 'active' : ''; ?>">ALL</a>
            <a href="index.php?category=Tops" class="cat-link <?php echo $category == 'Tops' ? 'active' : ''; ?>">TOPS</a>
            <a href="index.php?category=Bottoms" class="cat-link <?php echo $category == 'Bottoms' ? 'active' : ''; ?>">BOTTOMS</a>
            <a href="index.php?category=Essentials" class="cat-link <?php echo $category == 'Essentials' ? 'active' : ''; ?>">HOODIES</a>
        </nav>
    </div>
</div>

<main class="container">
    <div class="product-grid">
    <?php while($row = mysqli_fetch_assoc($products)) { 
        $stock_s = (int)($row['stock_s'] ?? 0);
        $stock_m = (int)($row['stock_m'] ?? 0);
        $stock_l = (int)($row['stock_l'] ?? 0);
        $stock_xl = (int)($row['stock_xl'] ?? 0);
        $total_stock = $stock_s + $stock_m + $stock_l + $stock_xl;
        $is_out_of_stock = ($total_stock <= 0);
    ?>
        <div class="product-card <?php echo $is_out_of_stock ? 'oos-card' : ''; ?>" 
             data-id="<?php echo $row['id']; ?>"
             data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
             data-price="<?php echo $row['price']; ?>"
             data-image="<?php echo $row['image']; ?>"
             data-desc="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
             data-s="<?php echo $stock_s; ?>"
             data-m="<?php echo $stock_m; ?>"
             data-l="<?php echo $stock_l; ?>"
             data-xl="<?php echo $stock_xl; ?>"
             onclick="<?php echo !$is_out_of_stock ? 'handleQuickView(this)' : ''; ?>">
            
            <?php if($is_out_of_stock): ?>
                <span class="badge">SOLD OUT</span>
            <?php endif; ?>

            <div class="product-link">
                <img src="assets/images/<?php echo $row['image']; ?>" alt="Product">
                <?php if(!$is_out_of_stock): ?>
                    <div class="hover-container"><span class="hover-text">Quick View</span></div>
                <?php endif; ?>
            </div>
            <h3 class="product-name"><?php echo $row['name']; ?></h3>
            <p class="price">₱<?php echo number_format($row['price'], 2); ?></p>
        </div>
    <?php } ?>
    </div>
</main> 

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
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<div id="quickViewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-body">
            <div class="modal-image" id="zoomContainer">
                <img id="modalImg" src="" alt="Product Image">
            </div>

            <div class="modal-info">
                <h2 id="modalName"></h2>
                <p id="modalPrice" class="detail-price"></p>
                <p id="modalDesc" class="detail-description"></p>
                
                <form action="add_to_cart_check.php" method="POST">
                    <input type="hidden" name="product_id" id="modalId">
                    <input type="hidden" name="add_to_cart" value="1">
                    
                    <div class="size-selection">
                        <label class="size-title">SELECT SIZE</label>
                        <div class="size-options">
                            <div class="size-item">
                                <input type="radio" name="size" value="S" id="s" required>
                                <label for="s" id="label_s">S</label>
                                <span class="stock-label" id="stock_s"></span>
                            </div>
                            <div class="size-item">
                                <input type="radio" name="size" value="M" id="m">
                                <label for="m" id="label_m">M</label>
                                <span class="stock-label" id="stock_m"></span>
                            </div>
                            <div class="size-item">
                                <input type="radio" name="size" value="L" id="l">
                                <label for="l" id="label_l">L</label>
                                <span class="stock-label" id="stock_l"></span>
                            </div>
                            <div class="size-item">
                                <input type="radio" name="size" value="XL" id="xl">
                                <label for="xl" id="label_xl">XL</label>
                                <span class="stock-label" id="stock_xl"></span>
                            </div>
                        </div>
                    </div>

                    <div class="qty-selection">
                        <label>QUANTITY</label>
                        <div class="qty-input-wrapper">
                            <input type="number" name="quantity" id="modalQtyInput" value="1" min="1">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-add-cart">
                        <?php echo isset($_SESSION['user_id']) ? 'Add to Cart' : 'Login to Add to Cart'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// --- UI Elements ---
const searchBtn = document.getElementById('searchBtn');
const searchInput = document.getElementById('searchInput');
const resultsPopup = document.getElementById('searchResultsPopup');
const quickViewModal = document.getElementById('quickViewModal');

// --- 1. Search Bar Logic ---
if (searchBtn && searchInput) {
    searchBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); 
        searchInput.classList.toggle('active');
        if (searchInput.classList.contains('active')) {
            searchInput.focus();
        } else {
            resultsPopup.style.display = 'none';
        }
    });
}

// Live Search Input
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length > 1) {
            fetch(`fetch_search.php?query=${encodeURIComponent(query)}`)
                .then(res => res.text())
                .then(data => {
                    resultsPopup.innerHTML = data;
                    resultsPopup.style.display = 'block';
                });
        } else {
            resultsPopup.style.display = 'none';
        }
    });
}

// --- 2. Product Quick View Logic ---
function handleQuickView(input) {
    // Prevent errors if modal doesn't exist
    if (!quickViewModal) return;

    // Fix: Close search bar automatically if it's open
    if (searchInput) searchInput.classList.remove('active');
    if (resultsPopup) resultsPopup.style.display = 'none';

    // Get Data from Product Card
    const data = input.dataset;
    
    // Reset Size Selection
    document.querySelectorAll('.size-item input[type="radio"]').forEach(r => {
        r.checked = false;
        r.disabled = false;
    });

    // Populate Modal Content
    document.getElementById('modalName').innerText = data.name;
    document.getElementById('modalPrice').innerText = '₱' + parseFloat(data.price).toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('modalDesc').innerText = data.desc;
    document.getElementById('modalImg').src = 'assets/images/' + data.image;
    document.getElementById('modalId').value = data.id;
    document.getElementById('modalQtyInput').value = 1;

    // Stock Management Logic
    const sizes = ['s', 'm', 'l', 'xl'];
    sizes.forEach(size => {
        const stock = parseInt(data[size]);
        const inputRadio = document.getElementById(size);
        const label = document.getElementById('label_' + size);
        const stockLabel = document.getElementById('stock_' + size);

        if (stockLabel) stockLabel.innerText = stock + " LEFT";

        if (stock <= 0) {
            if (inputRadio) inputRadio.disabled = true;
            if (label) {
                label.style.opacity = "0.2";
                label.style.textDecoration = "line-through";
            }
            if (stockLabel) stockLabel.style.color = "#ff0000"; 
        } else {
            if (label) {
                label.style.opacity = "1";
                label.style.textDecoration = "none";
            }
            if (stockLabel) stockLabel.style.color = "#888"; 
        }
    });

    quickViewModal.style.display = "block";
    document.body.style.overflow = "hidden"; 
}

function closeModal() {
    if (quickViewModal) {
        quickViewModal.style.display = "none";
        document.body.style.overflow = "auto";
    }
}

window.onclick = function(event) {
    if (event.target == quickViewModal) {
        closeModal();
    }
    if (resultsPopup && !event.target.closest('.search-wrapper')) {
        resultsPopup.style.display = 'none';
    }
}
</script>