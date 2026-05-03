<?php
session_start();
include "../config/database.php";

// 1. ACCESS CONTROL
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// 2. UPDATE LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    if ($status === 'Completed') {
        $update_sql = "UPDATE orders SET status='$status', completed_at = NOW() WHERE id=$id";
    } else {
        $update_sql = "UPDATE orders SET status='$status', completed_at = NULL WHERE id=$id";
    }
    
    if(mysqli_query($conn, $update_sql)) {
        header("Location: orders.php?success=1&tab=" . strtolower($status));
        exit;
    }
}

// 3. TAB & SEARCH FILTERS
$current_tab = isset($_GET['tab']) ? mysqli_real_escape_string($conn, $_GET['tab']) : 'all';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where_clauses = [];
if ($current_tab !== 'all') {
    $where_clauses[] = "status = '$current_tab'";
}
if (!empty($search)) {
    $where_clauses[] = "(id LIKE '%$search%' OR full_name LIKE '%$search%')";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
$orders = mysqli_query($conn, "SELECT * FROM orders $where_sql ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Apparel Admin</title>
    <link rel="icon" type="image/jpeg" href="../assets/images/new_logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { margin: 0; display: flex; background-color: #fcfcfc; font-family: 'Inter', sans-serif; color: #111; }

        /* Sidebar */
        .sidebar { width: 250px; height: 100vh; background: #000; color: #fff; padding: 30px 25px; position: fixed; left: 0; top: 0; box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar h2 { font-size: 14px; letter-spacing: 4px; margin-bottom: 50px; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid #222; padding-bottom: 20px; }
        .sidebar a { display: block; color: #666; text-decoration: none; padding: 15px 0; transition: 0.3s; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; }
        .sidebar a:hover, .active-link { color: #fff !important; }
        .logout-link { margin-top: auto; color: #ff4444 !important; }

        /* Content */
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 40px 60px; box-sizing: border-box; }
        .header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; }
        header h1 { font-size: 22px; text-transform: uppercase; letter-spacing: 2px; margin: 0; font-weight: 700; }

        /* Tabs */
        .tab-container { display: flex; gap: 30px; border-bottom: 1px solid #eee; margin-bottom: 30px; }
        .tab { padding: 15px 5px; font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700; color: #aaa; text-decoration: none; border-bottom: 2px solid transparent; }
        .tab.active { color: #000; border-bottom: 2px solid #000; }

        /* Search Box */
        .search-box { position: relative; }
        .search-box input { padding: 12px 15px 12px 40px; border: 1px solid #eee; font-size: 10px; outline: none; width: 250px; transition: 0.3s; text-transform: uppercase; }
        .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #bbb; }
        .search-box input:focus { border-color: #000; width: 300px; }

        /* Table */
        .inventory-card { background: #fff; border: 1px solid #f0f0f0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 20px; background: #fafafa; border-bottom: 1px solid #eee; font-size: 9px; color: #888; text-transform: uppercase; }
        td { padding: 18px 20px; border-bottom: 1px solid #f9f9f9; font-size: 12px; }

        .status-badge { display: inline-block; padding: 4px 10px; font-size: 8px; font-weight: 800; text-transform: uppercase; border-radius: 2px; }
        .pending { background: #fff4e5; color: #b76e00; }
        .shipped { background: #e8f4fd; color: #1a73e8; }
        .completed { background: #e6f7ed; color: #1e7e34; }
        .cancelled { background: #fbe9e9; color: #d93025; }

        .action-btn { background: #fff; border: 1px solid #eee; padding: 8px 12px; cursor: pointer; font-size: 9px; text-transform: uppercase; font-weight: 700; transition: 0.2s; }
        .action-btn:hover { border-color: #000; background: #000; color: #fff; }

        /* Modal Settings */
        .modal { display: none; position: fixed; z-index: 1050; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .modal-content { background: #fff; margin: 5vh auto; padding: 40px; width: 450px; border: 1px solid #000; position: relative; }
        .modal-close { position: absolute; top: 20px; right: 20px; font-size: 18px; cursor: pointer; color: #aaa; }
        .order-items-list { margin: 20px 0; border-top: 1px solid #eee; padding-top: 20px; }
        .item-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 10px; text-transform: uppercase; }
        select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #eee; font-size: 11px; text-transform: uppercase; }
        .btn-save { width: 100%; padding: 15px; background: #000; color: #fff; border: none; text-transform: uppercase; font-weight: 700; cursor: pointer; }
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
                <p style="font-size: 10px; color: #888; text-transform: uppercase;">Manage Customer Transactions</p>
            </header>

            <!-- Added oninput to automatically reset when cleared -->
            <form method="GET" id="searchForm" class="search-box">
                <input type="hidden" name="tab" value="<?php echo $current_tab; ?>">
                <i class="fas fa-search"></i>
                <input type="text" name="search" id="searchInput" 
                       placeholder="SEARCH NAME OR ORDER ID..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       oninput="autoResetSearch(this)">
            </form>
        </div>

        <div class="tab-container">
            <a href="?tab=all&search=<?php echo $search; ?>" class="tab <?php echo ($current_tab == 'all') ? 'active' : ''; ?>">All</a>
            <a href="?tab=pending&search=<?php echo $search; ?>" class="tab <?php echo ($current_tab == 'pending') ? 'active' : ''; ?>">Pending</a>
            <a href="?tab=shipped&search=<?php echo $search; ?>" class="tab <?php echo ($current_tab == 'shipped') ? 'active' : ''; ?>">Shipping</a>
            <a href="?tab=completed&search=<?php echo $search; ?>" class="tab <?php echo ($current_tab == 'completed') ? 'active' : ''; ?>">Completed</a>
            <a href="?tab=cancelled&search=<?php echo $search; ?>" class="tab <?php echo ($current_tab == 'cancelled') ? 'active' : ''; ?>">Cancelled</a>
        </div>

        <div class="inventory-card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Order Date</th>
                        <th>Date Completed</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($orders)): 
                        $oid = $row['id'];
                        $status = $row['status'];
                        $item_query = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $oid");
                        $items = [];
                        while($item = mysqli_fetch_assoc($item_query)) { $items[] = $item; }
                    ?>
                    <tr>
                        <td style="font-weight:700;">#<?php echo $oid; ?></td>
                        <td>
                            <div style="font-weight:600; text-transform: uppercase;"><?php echo htmlspecialchars($row['full_name']); ?></div>
                            <div style="font-size:9px; color:#aaa;"><?php echo count($items); ?> Item(s)</div>
                        </td>
                        <td style="font-size: 11px; color: #666;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        <td style="font-size: 11px; color: #1e7e34; font-weight: 600;">
                            <?php echo (!empty($row['completed_at'])) ? date('M d, Y', strtotime($row['completed_at'])) : '---'; ?>
                        </td>
                        <td style="font-weight:700;">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower($status); ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <?php if ($status !== 'Completed' && $status !== 'Cancelled'): ?>
                                <button class="action-btn" onclick='openOrderModal(<?php echo $oid; ?>, "<?php echo $status; ?>", <?php echo json_encode($items); ?>)'>
                                    Manage
                                </button>
                            <?php else: ?>
                                <span style="font-size: 10px; color: #bbb; text-transform: uppercase;">
                                    <i class="fas fa-lock"></i> Finalized
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Management Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h3 style="text-transform: uppercase; font-size: 14px; letter-spacing: 2px;">Order Details</h3>
            <p style="font-size: 10px; color: #aaa;">Reference: <span id="display-id" style="color:#000; font-weight:700;"></span></p>
            <div class="order-items-list" id="items-container"></div>
            <form method="POST" id="updateStatusForm">
                <input type="hidden" name="order_id" id="modal-order-id">
                <input type="hidden" name="update_status" value="1">
                <label style="font-size: 9px; font-weight: 700; color: #aaa; text-transform: uppercase;">Update Status</label>
                <select name="status" id="modal-status">
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button type="button" onclick="confirmUpdate()" class="btn-save">Update Transaction</button>
            </form>
        </div>
    </div>

    <script>
        // Automatic Reset Logic
        function autoResetSearch(input) {
            if (input.value.trim() === "") {
                // If search is cleared, go back to the current tab with no search query
                const currentTab = "<?php echo $current_tab; ?>";
                window.location.href = "orders.php?tab=" + currentTab;
            }
        }

        // Success Toast
        if (new URLSearchParams(window.location.search).has('success')) {
            Swal.fire({
                icon: 'success',
                title: 'Transaction Updated',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
        }

        function confirmUpdate() {
            const status = document.getElementById('modal-status').value;
            Swal.fire({
                title: 'Confirm Changes?',
                text: `Set order status to ${status}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#000',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('updateStatusForm').submit();
                }
            });
        }

        function openOrderModal(id, currentStatus, items) {
            document.getElementById('modal-order-id').value = id;
            document.getElementById('display-id').innerText = '#' + id;
            document.getElementById('modal-status').value = currentStatus;
            const container = document.getElementById('items-container');
            container.innerHTML = ''; 
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'item-row';
                div.innerHTML = `<div><strong>${item.name}</strong><br><small>Qty: ${item.quantity}</small></div><div>₱${parseFloat(item.price).toLocaleString()}</div>`;
                container.appendChild(div);
            });
            document.getElementById('orderModal').style.display = 'block';
        }

        function closeModal() { document.getElementById('orderModal').style.display = 'none'; }
    </script>
</body>
</html>