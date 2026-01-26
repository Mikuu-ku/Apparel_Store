<?php
include "../config/database.php";

if (isset($_POST['update_status'])) {
    $id = $_POST['order_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$id");
    header("Location: orders.php");
}

$orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - Apparel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <style>
        /* Keeping your original styles... */
        body { margin: 0; display: flex; background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; color: #111; }
        .sidebar { width: 250px; height: 100vh; background: #111; color: #fff; padding: 25px; position: fixed; left: 0; top: 0; box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar h2 { font-size: 18px; letter-spacing: 2px; margin-bottom: 40px; text-transform: uppercase; border-bottom: 1px solid #333; padding-bottom: 15px; }
        .sidebar a { display: block; color: #888; text-decoration: none; padding: 15px 0; transition: 0.3s; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar a i { margin-right: 10px; width: 20px; }
        .sidebar a:hover, .active-link { color: #fff !important; transform: translateX(5px); }
        .active-link { font-weight: bold; }
        .logout-link { margin-top: auto; color: #ff4444 !important; padding-bottom: 20px; }
        .main-content { margin-left: 250px; padding: 50px; width: calc(100% - 250px); box-sizing: border-box; }
        header h1 { font-size: 28px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 40px; }
        .inventory-card { background: #fff; padding: 30px; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 12px; border-bottom: 2px solid #000; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; }
        td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .status-badge { padding: 5px 10px; font-size: 11px; font-weight: bold; text-transform: uppercase; border-radius: 2px; }
        .pending { background: #fff3cd; color: #856404; }
        .shipped { background: #d1ecf1; color: #0c5460; }
        .delivered { background: #d4edda; color: #155724; }
        .cancelled { background: #f8d7da; color: #721c24; }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); }
        .modal-content { background: #fff; margin: 10% auto; padding: 30px; width: 400px; position: relative; }
        .close-btn { position: absolute; right: 20px; top: 15px; font-size: 24px; cursor: pointer; color: #888; }
        select, .btn-black { width: 100%; padding: 12px; margin-top: 10px; border: 1px solid #ddd; font-family: inherit; }
        .btn-black { background: #000; color: #fff; border: none; cursor: pointer; text-transform: uppercase; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>APPAREL ADMIN</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php"><i class="fas fa-tshirt"></i> Manage Products</a>
        <a href="orders.php" class="active-link"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-user"></i> View Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1>Customer Orders</h1>
        </header>

        <div class="inventory-card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><small><?php echo $row['address']; ?></small></td>
                        <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="javascript:void(0)" 
                               onclick="openOrderModal(<?php echo $row['id']; ?>, '<?php echo $row['status']; ?>')" 
                               style="color: #2196F3; text-decoration: none; font-weight: bold; font-size: 11px; text-transform: uppercase;">
                               Update
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3 style="margin-top: 0; text-transform: uppercase; font-size: 16px; letter-spacing: 1px;">Update Order Status</h3>
            <form method="POST">
                <input type="hidden" name="order_id" id="modal-order-id">
                <label style="font-size: 11px; color: #888; text-transform: uppercase;">Select New Status</label>
                <select name="status" id="modal-status">
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn-black">Save Update</button>
            </form>
        </div>
    </div>

    <script>
        function openOrderModal(id, currentStatus) {
            document.getElementById('modal-order-id').value = id;
            document.getElementById('modal-status').value = currentStatus;
            document.getElementById('orderModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('orderModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>