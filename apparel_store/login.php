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
        $error = "INVALID EMAIL OR PASSWORD";
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
        .login-success-overlay { text-align: center; padding: 40px 20px; }
        .checkmark-circle {
            width: 80px; height: 80px; border-radius: 50%;
            background: #000; color: #fff; display: flex;
            align-items: center; justify-content: center;
            font-size: 40px; margin: 0 auto 20px;
        }
        .pass-wrapper { position: relative; width: 100%; margin-bottom: 5px; }
        .pass-wrapper input { margin-bottom: 0 !important; padding-right: 45px !important; }
        .toggle-pass {
            position: absolute; right: 15px; top: 50%;
            transform: translateY(-50%); cursor: pointer;
            color: #888; font-size: 14px;
        }
        /* Style for individual warnings */
        .field-warning {
            color: #ff0000;
            font-size: 9px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 15px;
            display: none; /* Hidden by default */
            align-items: center;
            gap: 5px;
        }
        .input-group { margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        <?php if($success): ?>
            <div class="login-success-overlay">
                <div class="checkmark-circle"><i class="fas fa-check"></i></div>
                <h3 style="letter-spacing: 1px; text-transform: uppercase;">Welcome back, <?= htmlspecialchars($_SESSION['first_name']) ?></h3>
                <p style="font-size: 11px; color: #888; margin-top: 10px; letter-spacing: 1px; text-transform: uppercase;">
                    <?php echo isset($_SESSION['pending_cart']) ? "Saving your items to bag..." : "Authenticating credentials..."; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="auth-logo">
                <a href="index.php">
                    <img src="assets/images/new_logo.jpg" alt="Logo">
                </a>
            </div>

            <h2 style="text-transform: uppercase; letter-spacing: 2px;">Sign In</h2>
            <p class="auth-subtext" style="letter-spacing: 1px;">Access your account</p>

            <form method="POST" class="auth-form" id="loginForm" novalidate>
                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="EMAIL ADDRESS" required 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; outline: none; font-size: 12px; letter-spacing: 1px; box-sizing: border-box;">
                    <div id="email-warning" class="field-warning">
                        <i class="fas fa-exclamation-triangle"></i> PLEASE INPUT EMAIL
                    </div>
                </div>
                
                <div class="input-group">
                    <div class="pass-wrapper">
                        <input type="password" name="password" id="password" placeholder="PASSWORD" required 
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; outline: none; font-size: 12px; letter-spacing: 1px; box-sizing: border-box;">
                        <i class="fas fa-eye toggle-pass" id="togglePass"></i>
                    </div>
                    <div id="pass-warning" class="field-warning">
                        <i class="fas fa-exclamation-triangle"></i> PLEASE INPUT PASSWORD
                    </div>
                </div>

                <?php if(!empty($error)): ?>
                    <div class="field-warning" style="display: flex; justify-content: center; margin-top: 10px;">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <button type="submit" name="login" 
                        style="width: 100%; padding: 14px; background: #000; color: #fff; border: none; text-transform: uppercase; letter-spacing: 2px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px;">
                    Login
                </button>
            </form>

            <div class="auth-footer" style="margin-top: 25px; font-size: 10px; letter-spacing: 1.5px; color: #888; text-align: center;">
                NO ACCOUNT? <a href="register.php" style="color: #000; font-weight: 700; text-decoration: none; border-bottom: 1px solid #000;">JOIN NOW</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const emailInput = document.getElementById('email');
    const passInput = document.getElementById('password');
    const emailWarning = document.getElementById('email-warning');
    const passWarning = document.getElementById('pass-warning');
    
    // Reset styles
    emailWarning.style.display = 'none';
    passWarning.style.display = 'none';
    emailInput.style.borderColor = "#ddd";
    passInput.style.borderColor = "#ddd";

    let hasError = false;

    if (!emailInput.value.trim()) {
        e.preventDefault();
        emailWarning.style.display = 'flex';
        emailInput.style.borderColor = "#ff0000";
        hasError = true;
    } 
    
    if (!passInput.value.trim()) {
        e.preventDefault();
        passWarning.style.display = 'flex';
        passInput.style.borderColor = "#ff0000";
        hasError = true;
    }

    if(hasError && !emailInput.value.trim()) {
        emailInput.focus();
    } else if (hasError && !passInput.value.trim()) {
        passInput.focus();
    }
});

const togglePass = document.querySelector('#togglePass');
const passwordInput = document.querySelector('#password');

togglePass.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
</script>
</body>
</html>