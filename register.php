<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Donation Platform</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/toggle.css"> <!-- new CSS -->
</head>
<body>

    <!-- Header -->
    <div class="header">
        <a href="index.php"><img src="images/logo.png" alt="Logo"></a>
        <a href="login.php"><button class="signup-btn">Log In</button></a>
    </div>

    <!-- Registration Form -->
    <div class="register-container">
        <h2>Create Your Account</h2>

        <!-- Toggle Switch -->
        <div class="role-toggle">
            <label class="switch">
                <input type="checkbox" id="roleSwitch">
                <span class="slider round"></span>
            </label>
            <span id="roleText">Registering as Donor</span>
        </div>

        <form action="register_handler.php" method="post">
            <input type="hidden" name="role" id="roleInput" value="donor">

            <input type="text" name="name" placeholder="Full Name / Org Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Log in here</a></p>
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

    <script>
        const roleSwitch = document.getElementById('roleSwitch');
        const roleText = document.getElementById('roleText');
        const roleInput = document.getElementById('roleInput');

        roleSwitch.addEventListener('change', () => {
            if (roleSwitch.checked) {
                roleText.textContent = 'Registering as Organization';
                roleInput.value = 'organization';
            } else {
                roleText.textContent = 'Registering as Donor';
                roleInput.value = 'donor';
            }
        });
    </script>

</body>
</html>
