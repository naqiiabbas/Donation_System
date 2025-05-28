<?php
session_start();
require_once 'db.php'; 

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $hashed_password, $role);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = $role;
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    } else {
        $error = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Donation Platform</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/toggle.css">
</head>
<body>

    <!-- Header -->
    <div class="header">
        <a href="index.php"><img src="images/logo.png" alt="Logo"></a>
        <a href="register.php"><button class="signup-btn">Sign Up</button></a>
    </div>

    <div class="login-container">

    <!-- Login Form -->
    <div class="register-container">
        <h2>Log In to Your Account</h2>
        <?php if ($error): ?>
            <div class="error-message" style="color: #e74c3c; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    </div>
    

    <!-- Optional Footer -->
    <div class="footer">
        <div class="footer-container">
            <div class="footer-left">
                <img src="images/logo.png" alt="Footer Logo">
            </div>
            <div class="footer-center">
                <p>&copy; <?= date("Y") ?> Donation Platform. All rights reserved.</p>
            </div>
            <div class="footer-right">
                <a href="#"><img src="images/socials/fb.png" alt="Facebook"></a>
                <a href="#"><img src="images/socials/x.png" alt="Twitter"></a>
                <a href="#"><img src="images/socials/insta.png" alt="Instagram"></a>
            </div>
        </div>
    </div>

</body>
</html>