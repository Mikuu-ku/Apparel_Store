<?php
session_start();
include "../config/database.php";

// Admin Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// 1. Fetch Basic Stats
$product_count_query = mysqli_query($conn, "SELECT id FROM products");
$product_count = mysqli_num_rows($product_count_query);

$pending_orders_res = mysqli_query($conn, "SELECT id FROM orders WHERE status = 'Pending'");
$active_orders = mysqli_num_rows($pending_orders_res);

$revenue_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders"));
$total_revenue = number_format($revenue_res['total'], 2);

$user_count_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role = 'user'"));
$total_users = $user_count_res['total'];

// 2. Low Stock Alerts
$low_stock_res = mysqli_query($conn, "SELECT name, (stock_s + stock_m + stock_l + stock_xl) as total_qty 
                                      FROM products 
                                      HAVING total_qty < 10 
                                      ORDER BY total_qty ASC LIMIT 5");

// 3. Best Sellers (Top 3 Products)
$top_products_res = mysqli_query($conn, "SELECT p.name, SUM(oi.quantity) as total_sold 
                                         FROM order_items oi 
                                         JOIN products p ON oi.product_id = p.id 
                                         GROUP BY oi.product_id 
                                         ORDER BY total_sold DESC LIMIT 3");

// 4. Recent Orders
$recent_orders_res = mysqli_query($conn, "SELECT id, total_amount, status, created_at 
                                          FROM orders ORDER BY created_at DESC LIMIT 5");

// 5. Data for Sales Graph (Last 7 Days)
$days = []; $sales = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $days[] = date('D', strtotime($date));
    $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE DATE(created_at) = '$date'"));
    $sales[] = $res['total'];
}

// 6. Data for Order Status Pie Chart
$status_counts = [];
$statuses = ['Pending', 'Completed', 'Cancelled'];
foreach($statuses as $s) {
    $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as cnt FROM orders WHERE status = '$s'"));
    $status_counts[] = $res['cnt'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Apparel Elite</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/jpeg" href="../assets/images/new_logo.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { margin: 0; display: flex; background-color: #fcfcfc; font-family: 'Inter', sans-serif; color: #111; }
        .sidebar { width: 250px; height: 100vh; background: #000; color: #fff; padding: 30px 25px; position: fixed; box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar h2 { font-size: 14px; letter-spacing: 4px; margin-bottom: 50px; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid #222; padding-bottom: 20px; }
        .sidebar a { display: block; color: #666; text-decoration: none; padding: 15px 0; transition: 0.3s; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; }
        .sidebar a i { margin-right: 12px; width: 20px; }
        .sidebar a:hover, .active-link { color: #fff !important; }
        .logout-link { margin-top: auto; color: #ff4444 !important; }

        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 40px 60px; box-sizing: border-box; }
        .system-status { display: flex; gap: 20px; margin-bottom: 20px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #aaa; }
        .status-item i { color: #2ecc71; margin-right: 5px; }
        
        header h1 { font-size: 22px; margin: 0; text-transform: uppercase; letter-spacing: 2px; font-weight: 700; }
        header p { color: #888; margin: 5px 0 30px 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 25px; border: 1px solid #f0f0f0; transition: 0.3s ease; }
        .stat-card:hover { border-color: #000; }
        .stat-card h3 { font-size: 9px; text-transform: uppercase; color: #aaa; margin: 0 0 10px 0; letter-spacing: 1px; }
        .stat-card p { font-size: 22px; font-weight: 700; margin: 0; }

        .charts-row { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 30px; }
        .chart-container { background: #fff; padding: 30px; border: 1px solid #f0f0f0; }
        .chart-container h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; }

        .secondary-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 25px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .data-table th { text-align: left; padding: 10px 0; color: #aaa; border-bottom: 1px solid #eee; }
        .data-table td { padding: 12px 0; border-bottom: 1px solid #f9f9f9; }
        
        .badge { padding: 3px 6px; font-size: 9px; font-weight: 700; border-radius: 2px; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-success { background: #d4edda; color: #155724; }

        .list-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 12px; }
        .text-danger { color: #ff4444; font-weight: 700; }
        .text-success { color: #2ecc71; font-weight: 700; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>APPAREL</h2>
        <a href="dashboard.php" class="active-link"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-user"></i> Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="system-status">
            <span class="status-item"><i class="fas fa-circle"></i> System Online</span>
            <span><i class="far fa-clock"></i> Server Time: <?php echo date('H:i'); ?></span>
        </div>

        <header>
            <h1>Overview</h1>
            <p>Administrative Control Panel</p>
        </header>

        <div class="stats-grid">
            <div class="stat-card"><h3>Total Products</h3><p><?php echo $product_count; ?></p></div>
            <div class="stat-card"><h3>Pending Orders</h3><p><?php echo $active_orders; ?></p></div>
            <div class="stat-card"><h3>Total Revenue</h3><p>₱<?php echo $total_revenue; ?></p></div>
            <div class="stat-card"><h3>Total Customers</h3><p><?php echo $total_users; ?></p></div>
        </div>

        <div class="charts-row">
            <div class="chart-container">
                <h3>Revenue Trends (7 Days)</h3>
                <canvas id="salesChart" height="120"></canvas>
            </div>
            <div class="chart-container">
                <h3>Order Status</h3>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="secondary-grid">
            <div class="chart-container">
                <h3>Recent Orders</h3>
                <table class="data-table">
                    <thead><tr><th>Order</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php while($o = mysqli_fetch_assoc($recent_orders_res)): ?>
                        <tr>
                            <td>#<?php echo $o['id']; ?></td>
                            <td><span class="badge <?php echo ($o['status']=='Pending')?'badge-pending':'badge-success'; ?>"><?php echo $o['status']; ?></span></td>
                            <td><?php echo date('M d', strtotime($o['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="chart-container">
                <h3>Best Sellers</h3>
                <?php while($tp = mysqli_fetch_assoc($top_products_res)): ?>
                <div class="list-item">
                    <span><?php echo $tp['name']; ?></span>
                    <span class="text-success"><?php echo $tp['total_sold']; ?> Sold</span>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="chart-container">
                <h3 style="color: #ff4444;">Restock Needed</h3>
                <?php while($ls = mysqli_fetch_assoc($low_stock_res)): ?>
                <div class="list-item">
                    <span><?php echo $ls['name']; ?></span>
                    <span class="text-danger"><?php echo $ls['total_qty']; ?> Left</span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <script>
        // Sales Line Chart
        const sCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(sCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($days); ?>,
                datasets: [{
                    data: <?php echo json_encode($sales); ?>,
                    borderColor: '#000',
                    borderWidth: 2,
                    fill: true,
                    backgroundColor: 'rgba(0,0,0,0.02)',
                    tension: 0.4
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // Status Pie Chart
        const pCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(pCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Completed', 'Cancelled'],
                datasets: [{
                    data: <?php echo json_encode($status_counts); ?>,
                    backgroundColor: ['#f1c40f', '#2ecc71', '#e74c3c'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } } }
        });
    </script>
</body>
</html>