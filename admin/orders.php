<?php
session_start();
include "../config/database.php";

// Check Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Update Order Status Logic
if (isset($_POST['update_status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$id");
    header("Location: orders.php?success=1");
    exit;
}

// Search Logic
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $search_query = "WHERE full_name LIKE '%$search%' OR id LIKE '%$search%'";
}

$orders = mysqli_query($conn, "SELECT * FROM orders $search_query ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Apparel Admin</title>
    <link rel="icon" type="image/jpeg" href="../assets/images/new_logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body { 
            margin: 0; 
            display: flex; 
            background-color: #fcfcfc; 
            font-family: 'Inter', sans-serif; 
            color: #111; 
        }

        /* Sidebar - Consistent Branding */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #000;
            color: #fff;
            padding: 30px 25px;
            position: fixed;
            left: 0; top: 0;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar h2 { 
            font-size: 14px; 
            letter-spacing: 4px; 
            margin-bottom: 50px; 
            text-transform: uppercase;
            font-weight: 800;
            border-bottom: 1px solid #222;
            padding-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: #666;
            text-decoration: none;
            padding: 15px 0;
            transition: 0.3s;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .sidebar a i { margin-right: 12px; width: 20px; font-size: 14px; }
        .sidebar a:hover, .active-link { color: #fff !important; }
        .logout-link { margin-top: auto; color: #ff4444 !important; padding-bottom: 10px; }

        /* Main Workspace */
        .main-content {
            margin-left: 250px; 
            width: calc(100% - 250px);
            padding: 60px 80px;
            box-sizing: border-box; 
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 40px;
        }

        header h1 {
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
            font-weight: 700;
        }

        /* Minimalist Search */
        .search-box { position: relative; }
        .search-box input {
            padding: 12px 15px 12px 40px;
            border: 1px solid #eee;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            outline: none;
            width: 220px;
            transition: 0.4s ease;
            font-family: 'Inter', sans-serif;
        }
        .search-box input:focus { border-color: #000; width: 300px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #bbb; }

        /* Table Design */
        .inventory-card {
            background: #fff;
            border: 1px solid #f0f0f0;
        }

        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 20px;
            background: #fafafa;
            border-bottom: 1px solid #eee;
            font-size: 10px;
            letter-spacing: 1.5px;
            color: #888;
            text-transform: uppercase;
        }

        td {
            padding: 20px;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13px;
            vertical-align: middle;
        }

        /* Status Pills */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 2px;
        }
        .pending { background: #fcf4dd; color: #a38100; }
        .shipped { background: #e3f2fd; color: #0d47a1; }
        .completed { background: #e8f5e9; color: #2e7d32; }
        .cancelled { background: #ffebee; color: #c62828; }

        /* Action Buttons */
        .action-btn {
            background: #fff;
            border: 1px solid #eee;
            padding: 10px 14px;
            cursor: pointer;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 700;
            color: #000;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .action-btn:hover { border-color: #000; background: #000; color: #fff; }

        /* Modal Overlay */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: #fff;
            margin: 12% auto;
            padding: 50px;
            width: 350px;
            border: 1px solid #000;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        select {
            width: 100%;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #eee;
            font-family: 'Inter', sans-serif;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
        }

        .btn-save {
            width: 100%;
            padding: 18px;
            background: #000;
            color: #fff;
            border: none;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 2px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>APPAREL</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
        <a href="orders.php" class="active-link"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-user"></i> Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="header-flex">
            <header>
                <h1>Order Queue</h1>
                <p style="font-size: 11px; color: #888; text-transform: uppercase; margin-top: 5px; letter-spacing: 1px;">
                    Managing <?php echo mysqli_num_rows($orders); ?> total transactions
                </p>
            </header>

            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search ID or Customer..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            </form>
        </div>

        <div class="inventory-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client Details</th>
                        <th>Total Amount</th>
                        <th>Current Status</th>
                        <th>Order Date</th>
                        <th style="text-align:right;">Management</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($orders) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td style="font-weight:700; color: #aaa;">#<?php echo $row['id']; ?></td>
                            <td>
                                <div style="font-weight:600;"><?php echo $row['full_name']; ?></div>
                                <div style="font-size:10px; color:#aaa; text-transform:uppercase; margin-top:4px; max-width: 200px; line-height: 1.4;">
                                    <?php echo $row['address']; ?>
                                </div>
                            </td>
                            <td style="font-weight:700;">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td style="color:#888; font-size:12px;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td style="text-align:right;">
                                <button class="action-btn" onclick="openOrderModal(<?php echo $row['id']; ?>, '<?php echo $row['status']; ?>')">
                                    <i class="fas fa-sync-alt"></i> Update
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:100px; color:#bbb; text-transform:uppercase; font-size:10px; letter-spacing:2px;">
                                No records found in the database
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="orderModal" class="modal">
        <div class="modal-content">
            <h3 style="text-transform: uppercase; font-size: 13px; letter-spacing: 3px; margin: 0 0 10px 0;">Modify Status</h3>
            <p style="font-size: 10px; color: #aaa; text-transform: uppercase; margin-bottom: 30px;">Order Reference: <span id="display-id" style="color: #000; font-weight: 700;"></span></p>
            
            <form method="POST">
                <input type="hidden" name="order_id" id="modal-order-id">
                <select name="status" id="modal-status">
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openOrderModal(id, currentStatus) {
            document.getElementById('modal-order-id').value = id;
            document.getElementById('display-id').innerText = '#' + id;
            document.getElementById('modal-status').value = currentStatus;
            document.getElementById('orderModal').style.display = 'block';
        }

        window.onclick = function(e) {
            if (e.target.className == 'modal') {
                document.getElementById('orderModal').style.display = 'none';
            }
        }
    </script>
</body>
</html>