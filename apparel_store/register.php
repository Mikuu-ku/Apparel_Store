<?php
include "config/database.php";

$success = false; 
$error = "";

if (isset($_POST['register'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pass  = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $contact = mysqli_real_escape_string($conn, $_POST['contact_no']);

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
        $error = "CONTACT NUMBER MUST BE 11 DIGITS STARTING WITH 09";
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
        .pass-field-wrapper { position: relative; width: 100%; margin-bottom: 5px; }
        .toggle-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888; font-size: 14px; }
        
        /* Updated warning styling */
        .field-error {
            color: #ff0000;
            font-size: 9px;
            margin-top: 5px;
            margin-bottom: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
            display: none; /* Hidden by default */
            align-items: center;
            gap: 5px;
        }
        .input-group { margin-bottom: 2px; width: 100%; }
        
        .auth-form input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            outline: none;
            font-size: 12px;
            letter-spacing: 1px;
            box-sizing: border-box;
        }

        .auth-form button {
            width: 100%;
            padding: 14px;
            background: #000;
            color: #fff;
            border: none;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        
        <?php if($success): ?>
            <div class="login-success-overlay" style="text-align: center; padding: 40px 20px;">
                <div class="checkmark-circle" style="width: 80px; height: 80px; border-radius: 50%; background: #000; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px;"><i class="fas fa-check"></i></div>
                <h3 style="letter-spacing: 1px; text-transform: uppercase;">ACCOUNT CREATED</h3>
                <p style="font-size: 12px; color: #888;">Welcome to the Apparel family.</p>
                <p style="font-size: 11px; margin-top: 15px; color: #aaa; text-transform: uppercase;">Redirecting to login...</p>
            </div>
        <?php else: ?>
            <div class="auth-logo">
                <a href="index.php">
                    <img src="assets/images/new_logo.jpg" alt="Apparel's Logo">
                </a>
            </div>

            <h2 style="text-transform: uppercase; letter-spacing: 2px;">Create Account</h2>
            <p class="auth-subtext" style="letter-spacing: 1px; margin-bottom: 25px;">Join our clothing line today</p>
            
            <form method="POST" class="auth-form" id="registerForm" novalidate>
                <div class="input-group">
                    <input type="text" name="first_name" id="first_name" placeholder="FIRST NAME" required>
                    <div id="first_name-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT FIRST NAME</div>
                </div>

                <div class="input-group">
                    <input type="text" name="last_name" id="last_name" placeholder="LAST NAME" required>
                    <div id="last_name-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT LAST NAME</div>
                </div>

                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="EMAIL ADDRESS" required>
                    <div id="email-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT EMAIL</div>
                </div>
                
                <div class="input-group">
                    <div class="pass-field-wrapper">
                        <input type="password" name="password" id="password" placeholder="PASSWORD" required>
                        <i class="fas fa-eye toggle-icon" onclick="togglePass('password', this)"></i>
                    </div>
                    <div id="password-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT PASSWORD</div>
                </div>

                <div class="input-group">
                    <div class="pass-field-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="CONFIRM PASSWORD" required>
                        <i class="fas fa-eye toggle-icon" onclick="togglePass('confirm_password', this)"></i>
                    </div>
                    <div id="confirm_password-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE CONFIRM PASSWORD</div>
                </div>

                <div class="input-group">
                    <input type="text" name="contact_no" id="contact_no" placeholder="CONTACT NO." maxlength="11" required>
                    <div id="contact_no-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT CONTACT NO.</div>
                </div>

                <?php if(!empty($error)): ?>
                    <div class="field-error" style="display: flex; justify-content: center; margin-bottom: 15px;">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <button name="register" type="submit" style="margin-top: 10px;">Register</button>
            </form>
            
            <div class="auth-footer" style="margin-top: 25px; font-size: 11px; letter-spacing: 1px; color: #888; text-align: center;">
                ALREADY HAVE AN ACCOUNT? <a href="login.php" style="color: #000; font-weight: bold; text-decoration: none; border-bottom: 1px solid #000;">LOGIN HERE</a>
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
    const fields = [
        { id: 'first_name', name: 'FIRST NAME' },
        { id: 'last_name', name: 'LAST NAME' },
        { id: 'email', name: 'EMAIL' },
        { id: 'password', name: 'PASSWORD' },
        { id: 'confirm_password', name: 'CONFIRM PASSWORD' },
        { id: 'contact_no', name: 'CONTACT NO' }
    ];
    
    let isValid = true;
    let firstErrorElement = null;

    // Reset all errors
    fields.forEach(field => {
        const input = document.getElementById(field.id);
        const errorDiv = document.getElementById(field.id + '-error');
        input.style.borderColor = "#ddd";
        errorDiv.style.display = 'none';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> PLEASE INPUT ${field.name}`;
    });

    // Check for empty fields
    for (let field of fields) {
        const input = document.getElementById(field.id);
        const errorDiv = document.getElementById(field.id + '-error');
        
        if (!input.value.trim()) {
            input.style.borderColor = "#ff0000";
            errorDiv.style.display = 'flex';
            if(!firstErrorElement) firstErrorElement = input;
            isValid = false;
        }
    }

    // Only check formats if all fields have values
    if (isValid) {
        const emailInput = document.getElementById('email');
        const emailErr = document.getElementById('email-error');
        const contactInput = document.getElementById('contact_no');
        const contactErr = document.getElementById('contact_no-error');
        const pass = document.getElementById('password');
        const conf = document.getElementById('confirm_password');
        const confErr = document.getElementById('confirm_password-error');

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const contactRegex = /^09\d{9}$/;

        if (!emailRegex.test(emailInput.value)) {
            emailInput.style.borderColor = "#ff0000";
            emailErr.innerHTML = `<i class="fas fa-exclamation-triangle"></i> INVALID EMAIL FORMAT`;
            emailErr.style.display = 'flex';
            if(!firstErrorElement) firstErrorElement = emailInput;
            isValid = false;
        } 
        
        if (pass.value !== conf.value) {
            conf.style.borderColor = "#ff0000";
            confErr.innerHTML = `<i class="fas fa-exclamation-triangle"></i> PASSWORDS DO NOT MATCH`;
            confErr.style.display = 'flex';
            if(!firstErrorElement) firstErrorElement = conf;
            isValid = false;
        } 
        
        if (!contactRegex.test(contactInput.value)) {
            contactInput.style.borderColor = "#ff0000";
            contactErr.innerHTML = `<i class="fas fa-exclamation-triangle"></i> MUST BE 11 DIGITS (09XXXXXXXXX)`;
            contactErr.style.display = 'flex';
            if(!firstErrorElement) firstErrorElement = contactInput;
            isValid = false;
        }
    }

    if (!isValid) {
        e.preventDefault();
        if(firstErrorElement) firstErrorElement.focus();
    }
});

document.getElementById('contact_no').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
</body>
</html>