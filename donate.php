<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'donor') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $org_id = intval($_POST['organization_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($org_id > 0 && $amount > 0) {
        $stmt = $conn->prepare("INSERT INTO donations (donor_id, organization_id, amount, message, payment_method) VALUES (?, ?, ?, ?, 'Manual')");
        $stmt->bind_param("iids", $user_id, $org_id, $amount, $message);
        if ($stmt->execute()) {
            $success = "Thank you for your donation!";
        } else {
            $error = "Failed to record donation. Please try again.";
        }
    } else {
        $error = "Please select an organization and enter a valid amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make a Donation - Donation Platform</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <a href="index.php"><img src="images/logo.png" alt="Logo"></a>
        <a href="dashboard.php"><button class="signup-btn">Dashboard</button></a>
    </div>

    <main class="main-content">
        <div class="register-container">
            <h2>Make a Donation</h2>
            <?php if ($success): ?>
                <div class="error-message" style="color:#2ecc71;background:#eafaf1;border:1px solid #b6f0d2;"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="donate.php" autocomplete="off">
                <label for="orgSelect" style="font-weight:600;">Select Organization</label>
                <select name="organization_id" id="orgSelect" required style="width:100%;padding:10px 8px;margin-bottom:16px;border-radius:6px;border:1px solid #e0e0e0;">
                    <option value="">-- Choose Organization --</option>
                    <?php
                    $orgs = $conn->query("SELECT id, org_name FROM organizations WHERE status='approved' ORDER BY org_name ASC");
                    while ($org = $orgs->fetch_assoc()):
                    ?>
                        <option value="<?= $org['id'] ?>" <?= (isset($_POST['organization_id']) && $_POST['organization_id'] == $org['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($org['org_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <label for="donateAmount" style="font-weight:600;">Amount (PKR)</label>
                <input type="number" min="1" step="0.01" name="amount" id="donateAmount" required value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>">
                <label for="donateMessage" style="font-weight:600;">Message (optional)</label>
                <input type="text" name="message" id="donateMessage" maxlength="255" value="<?= htmlspecialchars($_POST['message'] ?? '') ?>">
                <button type="submit" class="cta-btn-large" style="margin-top:18px;">Donate</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
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