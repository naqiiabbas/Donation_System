<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="refresh" content="2;url=index.php">
    <meta charset="UTF-8">
    <title>Logging Out...</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <div class="logout-center-container">
        <div class="logout-message-box">
            <div>You have been logged out.</div>
            <p>Redirecting to homepage...</p>
        </div>
    </div>
</body>
</html>