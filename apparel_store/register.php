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
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        
        <?php if($success): ?>
            <div class="login-success-overlay">
                <div class="checkmark-circle"><i class="fas fa-check"></i></div>
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

            <h2>CREATE ACCOUNT</h2>
            <p class="auth-subtext">JOIN OUR CLOTHING LINE TODAY</p>
            
            <form method="POST" class="auth-form" id="registerForm" novalidate>
    <div class="input-group">
        <input type="text" name="first_name" id="first_name" placeholder="FIRST NAME">
        <div id="first_name-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT FIRST NAME</div>
    </div>

    <div class="input-group">
        <input type="text" name="last_name" id="last_name" placeholder="LAST NAME">
        <div id="last_name-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT LAST NAME</div>
    </div>

    <div class="input-group">
        <input type="email" name="email" id="email" placeholder="EMAIL ADDRESS">
        <div id="email-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT EMAIL</div>
    </div>
    
    <div class="input-group">
        <div class="pass-field-wrapper">
            <input type="password" name="password" id="password" placeholder="PASSWORD">
            <i class="fas fa-eye toggle-icon" onclick="togglePass('password', this)"></i>
        </div>
        <div id="password-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT PASSWORD</div>
    </div>

    <div class="input-group">
        <div class="pass-field-wrapper">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="CONFIRM PASSWORD">
            <i class="fas fa-eye toggle-icon" onclick="togglePass('confirm_password', this)"></i>
        </div>
        <div id="confirm_password-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE CONFIRM PASSWORD</div>
    </div>

    <div class="input-group">
        <input type="text" name="contact_no" id="contact_no" placeholder="CONTACT NO." maxlength="11">
        <div id="contact_no-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT CONTACT NO.</div>
    </div>

    <button name="register" type="submit">Register</button>
</form>
            
            <div class="auth-footer">
                ALREADY HAVE AN ACCOUNT? <a href="login.php">LOGIN HERE</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
function togglePass(inputId, icon) {
    const field = document.getElementById(inputId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    const fields = ['first_name', 'last_name', 'email', 'password', 'confirm_password', 'contact_no'];
    let isValid = true;
    const sErr = document.getElementById('server-error-container');
    if(sErr) sErr.innerHTML = "";

    fields.forEach(id => {
        const input = document.getElementById(id);
        const errorDiv = document.getElementById(id + '-error');
        input.classList.remove('input-error');
        errorDiv.style.display = 'none';

        if (!input.value.trim()) {
            input.classList.add('input-error');
            errorDiv.style.display = 'flex';
            isValid = false;
        }
    });

    if (isValid) {
        const email = document.getElementById('email');
        const contact = document.getElementById('contact_no');
        const pass = document.getElementById('password');
        const conf = document.getElementById('confirm_password');

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            email.classList.add('input-error');
            document.getElementById('email-error').style.display = 'flex';
            isValid = false;
        } 
        
        if (pass.value !== conf.value) {
            conf.classList.add('input-error');
            document.getElementById('confirm_password-error').style.display = 'flex';
            isValid = false;
        } 
        
        if (!/^09\d{9}$/.test(contact.value)) {
            contact.classList.add('input-error');
            document.getElementById('contact_no-error').style.display = 'flex';
            isValid = false;
        }
    }

    if (!isValid) e.preventDefault();
});

document.getElementById('contact_no').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
</body>
</html>