<?php
// Only show if the user is logged in AND is an admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): 
?>
<style>
    .admin-quick-nav {
        background: #000;
        color: #fff;
        height: 40px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 50px;
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
        position: sticky;
        top: 0;
        z-index: 9999;
        border-bottom: 1px solid #333;
    }
    .admin-quick-nav .nav-left {
        display: flex;
        gap: 25px;
        align-items: center;
    }
    .admin-quick-nav a {
        color: #aaa;
        text-decoration: none;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 600;
        transition: 0.3s;
    }
    .admin-quick-nav a:hover {
        color: #fff;
    }
    .admin-quick-nav i {
        margin-right: 5px;
        font-size: 12px;
    }
    .tag-admin {
        background: #fff;
        color: #000;
        padding: 2px 8px;
        font-size: 9px;
        font-weight: 800;
        border-radius: 2px;
        margin-right: 10px;
    }
</style>

<div class="admin-quick-nav">
    <div class="nav-left">
        <span class="tag-admin">ADMIN VIEW</span>
        <a href="admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="admin/products.php"><i class="fas fa-tshirt"></i> Inventory</a>
        <a href="admin/orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
    </div>
    <div class="nav-right">
        <a href="admin/logout.php" style="color: #ff4444;">Logout</a>
    </div>
</div>
<?php endif; ?>