<?php
session_start();
include "../config/database.php";

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$result = mysqli_query($conn, "SELECT id, first_name, last_name, email, contact_no, role FROM users ORDER BY id DESC");
$user_count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users - Apparel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <style>
        body { margin: 0; display: flex; background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; color: #111; }
        .sidebar { width: 250px; height: 100vh; background: #111; color: #fff; padding: 25px; position: fixed; box-sizing: border-box; display: flex; flex-direction: column; }
        .sidebar h2 { font-size: 18px; letter-spacing: 2px; margin-bottom: 40px; text-transform: uppercase; border-bottom: 1px solid #333; padding-bottom: 15px; }
        .sidebar a { display: block; color: #888; text-decoration: none; padding: 15px 0; transition: 0.3s; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar a:hover, .active-link { color: #fff !important; transform: translateX(5px); }
        .logout-link { margin-top: auto; color: #ff4444 !important; padding-bottom: 20px; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 50px; box-sizing: border-box; }
        
        .table-card { background: #fff; border: 1px solid #eee; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; font-size: 11px; text-transform: uppercase; color: #888; border-bottom: 1px solid #eee; letter-spacing: 1px; }
        td { padding: 15px; font-size: 14px; border-bottom: 1px solid #eee; }
        .role-badge { padding: 4px 10px; font-size: 10px; text-transform: uppercase; font-weight: bold; background: #f1f1f1; border: 1px solid #ddd; }
        .role-admin { background: #000; color: #fff; border-color: #000; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>APPAREL ADMIN</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php"><i class="fas fa-tshirt"></i> Manage Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php" class="active-link"><i class="fas fa-user"></i> View Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1 style="font-size: 28px; text-transform: uppercase; letter-spacing: 1px;">Registered Users</h1>
            <p style="color: #888; font-size: 14px; margin-bottom: 40px;">Currently managing <?php echo $user_count; ?> members.</p>
        </header>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['contact_no']; ?></td>
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