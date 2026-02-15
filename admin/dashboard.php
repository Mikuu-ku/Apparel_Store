<?php
session_start();
include "../config/database.php";

// Admin Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// 1. Fetch Basic Stats
// Get total product count
$product_count_query = mysqli_query($conn, "SELECT id FROM products");
$product_count = mysqli_num_rows($product_count_query);

// Fetch Pending Orders
$pending_orders_res = mysqli_query($conn, "SELECT id FROM orders WHERE status = 'Pending'");
$active_orders = mysqli_num_rows($pending_orders_res);

// REVENUE QUERY: Using COALESCE to ensure 0 is returned if no orders exist
$revenue_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders"));
$total_revenue = number_format($revenue_res['total'], 2);

// 2. Prepare Data for the Sales Graph (Last 7 Days)
$days = [];
$sales = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime($date));
    
    // Using DATE() to ignore the time part of the timestamp
    $query = "SELECT COALESCE(SUM(total_amount), 0) as daily_total FROM orders 
              WHERE DATE(created_at) = '$date'";
    $result = mysqli_fetch_assoc(mysqli_query($conn, $query));
    
    $days[] = $day_name;
    $sales[] = $result['daily_total'];
}

$js_labels = json_encode($days);
$js_data = json_encode($sales);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Apparel Admin</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="icon" type="image/jpeg" href="../assets/images/new_logo.jpg">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        
        body { margin: 0; display: flex; background-color: #fcfcfc; font-family: 'Inter', sans-serif; color: #111; }
        
        .sidebar { width: 250px; height: 100vh; background: #000; color: #fff; padding: 30px 25px; position: fixed; left: 0; top: 0; box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar h2 { font-size: 14px; letter-spacing: 4px; margin-bottom: 50px; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid #222; padding-bottom: 20px; }
        .sidebar a { display: block; color: #666; text-decoration: none; padding: 15px 0; transition: 0.3s; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; }
        .sidebar a i { margin-right: 12px; width: 20px; }
        .sidebar a:hover, .active-link { color: #fff !important; }
        .logout-link { margin-top: auto; color: #ff4444 !important; padding-bottom: 10px; }

        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 60px 80px; box-sizing: border-box; }
        header h1 { font-size: 22px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 2px; font-weight: 700; }
        header p { color: #888; margin-bottom: 50px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }

        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: #fff; padding: 35px 30px; border: 1px solid #f0f0f0; transition: 0.3s ease; }
        .stat-card:hover { border-color: #000; }
        .stat-card h3 { font-size: 9px; text-transform: uppercase; color: #aaa; margin: 0 0 10px 0; letter-spacing: 2px; font-weight: 600; }
        .stat-card p { font-size: 26px; font-weight: 700; color: #000; margin: 0; }

        .chart-container { background: #fff; padding: 40px; border: 1px solid #f0f0f0; margin-bottom: 40px; }
        .chart-container h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; color: #000; }
        
        .btn-black { display: inline-block; padding: 18px 45px; background: #000; color: #fff; text-decoration: none; text-transform: uppercase; font-weight: 700; font-size: 10px; letter-spacing: 2px; border: 1px solid #000; transition: 0.3s; }
        .btn-black:hover { background: #fff; color: #000; }
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
        <header>
            <h1>Overview</h1>
            <p>Administrative Control Panel</p>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $product_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Pending Orders</h3>
                <p><?php echo $active_orders; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p>₱<?php echo $total_revenue; ?></p>
            </div>
        </div>

        <div class="chart-container">
            <h3>Revenue Trends (Last 7 Days)</h3>
            <canvas id="salesChart" height="100"></canvas>
        </div>

        <div class="quick-actions">
            <a href="products.php" class="btn-black">Manage Inventory</a>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 0, 0, 0.05)');
        gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $js_labels; ?>,
                datasets: [{
                    label: 'Daily Revenue',
                    data: <?php echo $js_data; ?>,
                    borderColor: '#000',
                    borderWidth: 2,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#000',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { 
                            callback: value => '₱' + value.toLocaleString() 
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>