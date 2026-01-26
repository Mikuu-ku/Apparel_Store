<?php
include "config/database.php";

$success = false; 
if (isset($_POST['register'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pass  = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $contact = mysqli_real_escape_string($conn, $_POST['contact_no']);

    // --- 1. DUPLICATION CHECK ---
    $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");

    if (mysqli_num_rows($check_email) > 0) {
        $error = "THIS EMAIL IS ALREADY REGISTERED!";
    } 
    else if ($pass !== $confirm_pass) {
        $error = "PASSWORDS DO NOT MATCH!";
    } 
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $error = "PLEASE ENTER A VALID EMAIL ADDRESS!";
    }
    else if (!preg_match("/^09\d{9}$/", $contact)) {
        $error = "CONTACT NUMBER MUST BE 11 DIGITS AND START WITH 09";
    } 
    else {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, email, password, contact_no, role)
                VALUES ('$fname','$lname','$email','$hashed_pass','$contact', 'user')";

        if (mysqli_query($conn, $sql)) {
            $success = true;
            header("Refresh: 2; url=login.php?msg=registered");
        } else {
            $error = "ERROR: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <style>
        input.input-error { border-color: #ff0000 !important; }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        
        <?php if($success): ?>
            <div class="login-success-overlay">
                <div class="checkmark-circle"><i class="fas fa-check"></i></div>
                <h3 style="letter-spacing: 1px;">ACCOUNT CREATED</h3>
                <p style="font-size: 12px; color: #888;">Welcome to the Apparel family.</p>
                <p style="font-size: 11px; margin-top: 15px; color: #aaa; text-transform: uppercase;">Redirecting to login...</p>
            </div>
        <?php else: ?>
            <div class="auth-logo">
                <a href="index.php">
                    <img src="assets/images/new_logo.jpg" alt="Apparel's Logo">
                </a>
            </div>

            <h2>Create Account</h2>
            <p class="auth-subtext">Join our clothing line today</p>

            <div id="js-error" class="error-msg" style="display:none;"></div>

            <?php if(isset($error)): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form" id="registerForm" novalidate>
                <input type="text" name="first_name" placeholder="FIRST NAME" required>
                <input type="text" name="last_name" placeholder="LAST NAME" required>
                <input type="email" name="email" id="email" placeholder="EMAIL ADDRESS" required>
                
                <div class="pass-field-wrapper">
                    <input type="password" name="password" id="password" placeholder="PASSWORD" required>
                    <i class="fas fa-eye toggle-icon" onclick="togglePass('password', this)"></i>
                </div>

                <div class="pass-field-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="CONFIRM PASSWORD" required>
                    <i class="fas fa-eye toggle-icon" onclick="togglePass('confirm_password', this)"></i>
                </div>

                <input type="text" name="contact_no" id="contact_no" placeholder="CONTACT NO." maxlength="11" required>
                
                <button name="register" type="submit">Register</button>
            </form>
            
            <div class="auth-footer" style="margin-top: 25px; font-size: 11px; letter-spacing: 1px; color: #888;">
                ALREADY HAVE AN ACCOUNT? <a href="login.php" style="color: #000; font-weight: bold; text-decoration: none;">LOGIN HERE</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
function togglePass(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    const errorDiv = document.getElementById('js-error');
    const emailInput = document.getElementById('email');
    const contactInput = document.getElementById('contact_no');
    const inputs = this.querySelectorAll('input[required]');
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const contactRegex = /^09\d{9}$/;
    
    let hasEmpty = false;

    errorDiv.style.display = 'none';
    inputs.forEach(i => i.classList.remove('input-error'));

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('input-error');
            hasEmpty = true;
        }
    });

    if (hasEmpty) {
        e.preventDefault();
        errorDiv.innerText = "PLEASE FILL OUT ALL FIELDS";
        errorDiv.style.display = 'block';
        return;
    }

    if (!emailRegex.test(emailInput.value)) {
        e.preventDefault();
        emailInput.classList.add('input-error');
        errorDiv.innerText = "PLEASE ENTER A VALID EMAIL ADDRESS";
        errorDiv.style.display = 'block';
        return;
    }

    if (!contactRegex.test(contactInput.value)) {
        e.preventDefault();
        contactInput.classList.add('input-error');
        errorDiv.innerText = "CONTACT NUMBER MUST BE 11 DIGITS AND START WITH 09";
        errorDiv.style.display = 'block';
        return;
    }
});

document.getElementById('contact_no').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
</body>
</html>