<?php
session_start();
include "../config/database.php";

// Check Admin Access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch Users
$result = mysqli_query($conn, "SELECT id, first_name, last_name, email, contact_no, role FROM users ORDER BY id DESC");
$user_count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Apparel Admin</title>
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

        /* Sidebar - Consistent with Dashboard */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #000;
            color: #fff;
            padding: 30px 25px;
            position: fixed;
            left: 0;
            top: 0;
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

        /* Main Content */
        .main-content {
            margin-left: 250px; 
            width: calc(100% - 250px);
            padding: 60px 80px;
            box-sizing: border-box; 
        }

        header h1 {
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .user-stats {
            color: #888;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 40px;
            display: block;
        }

        /* Table Design */
        .table-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 0;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 20px;
            background: #fafafa;
            border-bottom: 1px solid #eee;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1.5px;
            color: #888;
        }

        td {
            padding: 20px;
            border-bottom: 1px solid #f9f9f9;
            font-size: 13px;
            color: #333;
        }

        /* Role Badges */
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 2px;
            background: #f1f1f1;
            color: #666;
            border: 1px solid #eee;
        }

        .role-admin {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        /* Avatar Placeholder */
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-circle {
            width: 32px;
            height: 32px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>APPAREL</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php" class="active-link"><i class="fas fa-user"></i> Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1>Registered Users</h1>
            <span class="user-stats">Database: <?php echo $user_count; ?> active members</span>
        </header>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Profile</th>
                        <th>Email Address</th>
                        <th>Contact No.</th>
                        <th>Access Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td style="font-weight: 700; color: #aaa;">#<?php echo $row['id']; ?></td>
                        <td>
                            <div class="user-info">
                                <div class="avatar-circle">
                                    <?php echo substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1); ?>
                                </div>
                                <div style="font-weight: 600; color: #111;">
                                    <?php echo $row['first_name'] . " " . $row['last_name']; ?>
                                </div>
                            </div>
                        </td>
                        <td style="color: #666;"><?php echo $row['email']; ?></td>
                        <td style="font-family: monospace; letter-spacing: 0.5px;"><?php echo $row['contact_no']; ?></td>
                        <td>
                            <span class="role-badge <?php echo ($row['role'] == 'admin') ? 'role-admin' : ''; ?>">
                                <?php echo $row['role']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>