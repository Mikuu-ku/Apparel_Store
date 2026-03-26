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
    header("Location: orders.php?msg=Status Updated");
    exit;
}

// Search Logic
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    // Search by full_name or order ID
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

        body { margin: 0; display: flex; background-color: #fcfcfc; font-family: 'Inter', sans-serif; color: #111; }

        /* Sidebar */
        .sidebar { width: 250px; height: 100vh; background: #000; color: #fff; padding: 30px 25px; position: fixed; left: 0; top: 0; box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar h2 { font-size: 14px; letter-spacing: 4px; margin-bottom: 50px; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid #222; padding-bottom: 20px; }
        .sidebar a { display: block; color: #666; text-decoration: none; padding: 15px 0; transition: 0.3s; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; }
        .sidebar a i { margin-right: 12px; width: 20px; font-size: 14px; }
        .sidebar a:hover, .active-link { color: #fff !important; }
        .logout-link { margin-top: auto; color: #ff4444 !important; padding-bottom: 10px; }

        /* Main Workspace */
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 40px 60px; box-sizing: border-box; }
        .header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
        header h1 { font-size: 22px; text-transform: uppercase; letter-spacing: 2px; margin: 0; font-weight: 700; }

        .search-box { position: relative; }
        .search-box input { padding: 12px 15px 12px 40px; border: 1px solid #eee; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; outline: none; width: 220px; transition: 0.3s; }
        .search-box input:focus { border-color: #000; width: 280px; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #bbb; }

        .inventory-card { background: #fff; border: 1px solid #f0f0f0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 20px; background: #fafafa; border-bottom: 1px solid #eee; font-size: 9px; letter-spacing: 1.5px; color: #888; text-transform: uppercase; }
        td { padding: 18px 20px; border-bottom: 1px solid #f9f9f9; font-size: 12px; }

        /* Status Pills */
        .status-badge { display: inline-block; padding: 4px 10px; font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; border-radius: 2px; }
        .pending { background: #fff4e5; color: #b76e00; }
        .shipped { background: #e8f4fd; color: #1a73e8; }
        .completed { background: #e6f7ed; color: #1e7e34; }
        .cancelled { background: #fbe9e9; color: #d93025; }

        .action-btn { background: #fff; border: 1px solid #eee; padding: 8px 12px; cursor: pointer; font-size: 9px; text-transform: uppercase; font-weight: 700; transition: 0.2s; }
        .action-btn:hover { border-color: #000; background: #000; color: #fff; }

        .modal { display: none; position: fixed; z-index: 2000; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .modal-content { background: #fff; margin: 5vh auto; padding: 40px; width: 450px; border: 1px solid #000; max-height: 85vh; overflow-y: auto; }
        
        .order-items-list { margin: 20px 0; border-top: 1px solid #eee; padding-top: 20px; }
        .item-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 10px; text-transform: uppercase; }
        .item-details { color: #888; font-size: 10px; }

        select { width: 100%; padding: 12px; margin: 15px 0; border: 1px solid #eee; text-transform: uppercase; font-size: 11px; }
        .btn-save { width: 100%; padding: 15px; background: #000; color: #fff; border: none; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; cursor: pointer; }
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
                <p style="font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px;">Live Transaction Feed</p>
            </header>

            <form method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search Order ID or Name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </form>
        </div>

        <div class="inventory-card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($orders)): 
                        $oid = $row['id'];
                        $item_query = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $oid");
                        $items = [];
                        while($item = mysqli_fetch_assoc($item_query)) { $items[] = $item; }
                    ?>
                    <tr>
                        <td style="font-weight:700;">#<?php echo $oid; ?></td>
                        <td>
                            <div style="font-weight:600;"><?php echo htmlspecialchars($row['full_name']); ?></div>
                            <div style="font-size:9px; color:#aaa; max-width:150px;"><?php echo htmlspecialchars($row['address']); ?></div>
                        </td>
                        <td><span style="font-size: 10px; color: #666;"><?php echo count($items); ?> Item(s)</span></td>
                        <td style="font-weight:700;">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td style="font-size: 10px; text-transform: uppercase;"><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <button class="action-btn" onclick='openOrderModal(<?php echo $oid; ?>, "<?php echo $row['status']; ?>", <?php echo json_encode($items); ?>)'>
                                Manage
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="orderModal" class="modal">
        <div class="modal-content">
            <h3 style="text-transform: uppercase; font-size: 14px; letter-spacing: 2px; margin-bottom: 5px;">Order Details</h3>
            <p style="font-size: 10px; color: #aaa; text-transform: uppercase;">Reference: <span id="display-id" style="color:#000; font-weight:700;"></span></p>
            
            <div class="order-items-list" id="items-container"></div>

            <form method="POST">
                <input type="hidden" name="order_id" id="modal-order-id">
                <label style="font-size: 9px; font-weight: 700; color: #aaa; text-transform: uppercase;">Update Status</label>
                <select name="status" id="modal-status">
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn-save">Update Transaction</button>
            </form>
        </div>
    </div>

    <script>
        function openOrderModal(id, currentStatus, items) {
    document.getElementById('modal-order-id').value = id;
    document.getElementById('display-id').innerText = '#' + id;
    document.getElementById('modal-status').value = currentStatus;
    
    const container = document.getElementById('items-container');
    container.innerHTML = ''; 
    
    items.forEach(item => {
        // Log to console so you can see the data structure if it still fails
        console.log("Item data:", item);

        // Ensure we are getting a valid number from the 'price' column
        const rawPrice = item.price || 0;
        const price = parseFloat(rawPrice);
        const formattedPrice = isNaN(price) ? "0.00" : price.toLocaleString(undefined, {minimumFractionDigits: 2});

        // Handle size: if it's missing in the DB, show 'N/A'
        const sizeDisplay = item.size ? item.size : 'N/A';

        const div = document.createElement('div');
        div.className = 'item-row';
        div.innerHTML = `
            <div>
                <strong>${item.name}</strong><br>
                <span class="item-details">Size: ${sizeDisplay} | Qty: ${item.quantity}</span>
            </div>
            <div style="font-weight:700;">₱${formattedPrice}</div>
        `;
        container.appendChild(div);
    });

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