<?php
session_start();
include "config/database.php";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$success = false;
$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pass  = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['role']       = $user['role']; 
        $_SESSION['name']       = $user['first_name']; 
        $_SESSION['first_name'] = $user['first_name']; 
        $_SESSION['last_name']  = $user['last_name'];

        $success = true; 

        if ($user['role'] === 'admin') {
            $redirect = "admin/dashboard.php";
        } 
        elseif (isset($_SESSION['pending_cart'])) {
            $redirect = "process_cart.php";
        } 
        else {
            $redirect = "index.php";
        }

        header("Refresh: 1.5; url=$redirect"); 
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <style>
        input.input-error { border-color: #ff0000 !important; }
        .login-success-overlay {
            text-align: center;
            padding: 40px 20px;
        }
        .checkmark-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        <?php if($success): ?>
            <div class="login-success-overlay">
                <div class="checkmark-circle"><i class="fas fa-check"></i></div>
                <h3 style="letter-spacing: 1px; text-transform: uppercase;">Welcome back, <?= htmlspecialchars($_SESSION['first_name']) ?></h3>
                <p style="font-size: 12px; color: #888; margin-top: 10px; letter-spacing: 1px;">
                    <?php echo isset($_SESSION['pending_cart']) ? "SAVING YOUR ITEMS TO BAG..." : "AUTHENTICATING CREDENTIALS..."; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="auth-logo">
                <a href="index.php">
                    <img src="assets/images/new_logo.jpg" alt="Apparel's Logo">
                </a>
            </div>

            <h2 style="text-transform: uppercase; letter-spacing: 2px;">Welcome Back</h2>
            <p class="auth-subtext" style="letter-spacing: 1px;">Sign in to continue</p>

            <div id="js-error" class="error-msg" style="display:none; color: #ff0000; font-size: 11px; margin-bottom: 15px; letter-spacing: 1px;"></div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'please_login'): ?>
                <div class="info-msg" style="background: #f0f0f0; color: #333; padding: 12px; font-size: 10px; margin-bottom: 15px; border-left: 3px solid #000; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fas fa-shopping-bag"></i> Login to save items to your bag
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'logged_out'): ?>
                <div class="logout-msg" style="font-size: 11px; color: #555; margin-bottom: 15px; letter-spacing: 1px;">
                    <i class="fas fa-info-circle"></i> You have been logged out.
                </div>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
                <div class="error-msg" style="color: #ff0000; font-size: 11px; margin-bottom: 15px; letter-spacing: 1px; text-transform: uppercase;"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form" id="loginForm" novalidate>
                <input type="email" name="email" placeholder="EMAIL ADDRESS" required 
                       style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; outline: none; font-size: 12px; letter-spacing: 1px;">
                
                <input type="password" name="password" placeholder="PASSWORD" required 
                       style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; outline: none; font-size: 12px; letter-spacing: 1px;">
                
                <button type="submit" name="login" 
                        style="width: 100%; padding: 14px; background: #000; color: #fff; border: none; text-transform: uppercase; letter-spacing: 2px; font-weight: bold; cursor: pointer; transition: 0.3s;">
                    Login
                </button>
            </form>

            <div class="auth-footer" style="margin-top: 25px; font-size: 11px; letter-spacing: 1px; color: #888;">
                NEW HERE? <a href="register.php" style="color: #000; font-weight: bold; text-decoration: none;">CREATE AN ACCOUNT</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const errorDiv = document.getElementById('js-error');
    const inputs = this.querySelectorAll('input[required]');
    let hasEmpty = false;

    errorDiv.style.display = 'none';
    inputs.forEach(i => {
        i.classList.remove('input-error');
        i.style.borderColor = "#ddd";
    });

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('input-error');
            input.style.borderColor = "#ff0000";
            hasEmpty = true;
        }
    });

    if (hasEmpty) {
        e.preventDefault();
        errorDiv.innerText = "PLEASE FILL OUT ALL FIELDS";
        errorDiv.style.display = 'block';
    }
});
</script>
</body>
</html>