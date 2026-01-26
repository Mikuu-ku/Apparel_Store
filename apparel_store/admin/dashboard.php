<?php
include "../config/database.php";
$product_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Apparel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <style>
        body { 
            margin: 0; 
            display: flex; 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #111;
        }
        
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #111;
            color: #fff;
            padding: 25px;
            position: fixed;
            left: 0;
            top: 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar h2 { 
            font-size: 18px; 
            letter-spacing: 2px; 
            margin-bottom: 40px; 
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
        }

        .sidebar a {
            display: block;
            color: #888;
            text-decoration: none;
            padding: 15px 0;
            transition: 0.3s;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a i { margin-right: 10px; width: 20px; }
        
        .sidebar a:hover, .active-link { 
            color: #fff !important; 
            transform: translateX(5px); 
        }

        .logout-link { 
            margin-top: auto; 
            color: #ff4444 !important; 
            padding-bottom: 20px; 
        }

        .main-content {
            margin-left: 250px; 
            width: calc(100% - 250px);
            padding: 50px;
            box-sizing: border-box; 
        }

        header h1 {
            font-size: 28px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        header p { 
            color: #888; 
            margin-bottom: 40px; 
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: #fff;
            padding: 40px 20px;
            border: 1px solid #eee;
            text-align: center;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .stat-card:hover { 
            border-color: #000; 
            transform: translateY(-5px);
        }

        .stat-card h3 { 
            font-size: 11px; 
            text-transform: uppercase; 
            color: #888; 
            margin-bottom: 15px; 
            letter-spacing: 2px; 
            font-weight: 600;
        }

        .stat-card p { 
            font-size: 36px; 
            font-weight: 700; 
            color: #111; 
            margin: 0; 
        }

        .quick-actions h3 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .btn-black {
            display: inline-block;
            padding: 15px 35px;
            background: #000;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-black:hover {
            background: #333;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>APPAREL ADMIN</h2>
        <a href="dashboard.php" class="active-link"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php"><i class="fas fa-tshirt"></i> Manage Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-user"></i> View Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard Overview</h1>
            <p>Welcome back, Admin. System is running smoothly.</p>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $product_count; ?></p>
            </div>
            <div class="stat-card">
                <h3>Active Orders</h3>
                <p>12</p>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <p>₱24,500</p>
            </div>
        </div>

        <div class="quick-actions" style="margin-top: 50px;">
            <h3>Quick Actions</h3>
            <a href="products.php" class="btn-black">Add New Product</a>
        </div>
    </div>

</body>
</html>