<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$user_name = $_SESSION['user_name'];

// Helper: fetch single value
function fetch_value($conn, $query, $types = '', $params = []) {
    $stmt = $conn->prepare($query);
    if ($types && $params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->bind_result($val);
    $stmt->fetch();
    $stmt->close();
    return $val;
}

if ($user_role === 'donor' && isset($_POST['make_donation'])) {
    $org_id = intval($_POST['organization_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if ($org_id > 0 && $amount > 0) {
        $stmt = $conn->prepare("INSERT INTO donations (donor_id, organization_id, amount, message, payment_method) VALUES (?, ?, ?, ?, 'Manual')");
        $stmt->bind_param("iids", $user_id, $org_id, $amount, $message);
        if ($stmt->execute()) {
            exit('success-donation');
        } else {
            exit('Failed to record donation.');
        }
    } else {
        exit('Please select an organization and enter a valid amount.');
    }
}

// Handle admin approval POST
if ($user_role === 'admin' && isset($_POST['approve_org_id'])) {
    $org_id = intval($_POST['approve_org_id']);
    $stmt = $conn->prepare("UPDATE organizations SET status='approved' WHERE id=?");
    $stmt->bind_param("i", $org_id);
    $stmt->execute();
    exit('approved');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Donation Platform</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/toggle.css">
    <style>
        .dashboard-container { max-width: 1100px; margin: 40px 20%; background: #fff; border-radius: 12px; padding: 32px 40px; box-shadow: 0 2px 16px #eee; }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
        .dashboard-header h2 { margin: 0; }
        .dashboard-stats { display: flex; gap: 32px; margin-bottom: 32px; }
        .stat-box { background: #ff007a;; border-radius: 10px; padding: 24px 32px; flex: 1; text-align: center; }
        .stat-box h3 { margin: 0 0 8px 0; color: #ffffff; }
        .pending-list, .recent-list, .donation-list, .donor-list { margin-bottom: 32px; }
        .search-bar { margin-bottom: 24px; }
        .search-bar input { padding: 8px 14px; border-radius: 6px; border: 1px solid #ccc; width: 250px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #eee; }
        th { background: #f0f0f0; }
        .graph-placeholder { background: #f7f7f7; border-radius: 8px; height: 220px; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php"><img src="images/logo.png" alt="Logo"></a>
        <span style="font-weight: bold; font-size: 1.1em;">Welcome, <?= htmlspecialchars($user_name) ?>!</span>
        <a href="logout.php"><button class="signup-btn">Log Out</button></a>
    </div>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>
                <?php
                    if ($user_role === 'admin') echo "Admin Dashboard";
                    elseif ($user_role === 'donor') echo "Donor Dashboard";
                    elseif ($user_role === 'organization') echo "Organization Dashboard";
                ?>
            </h2>
            <?php if ($user_role === 'admin'): ?>
                <button id="approvalsBtn" class="cta-btn-large" style="background:#ff007a; font-size:15px; padding:10px 22px;">Approvals</button>
            <?php endif; ?>
        </div>

        <?php if ($user_role === 'admin'): ?>
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h3>Total Donors</h3>
                    <div><?= fetch_value($conn, "SELECT COUNT(*) FROM users WHERE role='donor'") ?></div>
                </div>
                <div class="stat-box">
                    <h3>Total Organizations</h3>
                    <div><?= fetch_value($conn, "SELECT COUNT(*) FROM users WHERE role='organization'") ?></div>
                </div>
                <div class="stat-box">
                    <h3>Total Donations</h3>
                    <div><?= fetch_value($conn, "SELECT COUNT(*) FROM donations") ?></div>
                </div>
            </div>

            <div class="pending-list">
                <h3>Pending NGOs for Approval</h3>
                <table>
                    <tr><th>Org Name</th><th>Email</th><th>Status</th><th>Registered</th></tr>
                    <?php
                    $result = $conn->query("SELECT o.org_name, u.email, o.status, o.created_at FROM organizations o JOIN users u ON o.user_id=u.id WHERE o.status='pending' LIMIT 5");
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['org_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <div class="recent-list">
                <h3>Recent Donations</h3>
                <table>
                    <tr><th>Donor</th><th>Organization</th><th>Amount</th><th>Date</th></tr>
                    <?php
                    $sql = "SELECT d.amount, d.donated_at, u.name as donor, o.org_name 
                            FROM donations d 
                            LEFT JOIN users u ON d.donor_id=u.id 
                            LEFT JOIN organizations o ON d.organization_id=o.id 
                            ORDER BY d.donated_at DESC LIMIT 5";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['donor']) ?></td>
                        <td><?= htmlspecialchars($row['org_name']) ?></td>
                        <td>PKR<?= number_format($row['amount'],2) ?></td>
                        <td><?= htmlspecialchars($row['donated_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <div class="search-bar">
                <form method="get">
                    <input type="text" name="search" placeholder="Search by donor/org/date..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            <?php if (!empty($_GET['search'])): 
                $search = "%{$_GET['search']}%";
                $stmt = $conn->prepare(
                    "SELECT d.amount, d.donated_at, u.name as donor, o.org_name 
                     FROM donations d 
                     LEFT JOIN users u ON d.donor_id=u.id 
                     LEFT JOIN organizations o ON d.organization_id=o.id 
                     WHERE u.name LIKE ? OR o.org_name LIKE ? OR d.donated_at LIKE ?
                     ORDER BY d.donated_at DESC"
                );
                $stmt->bind_param("sss", $search, $search, $search);
                $stmt->execute();
                $result = $stmt->get_result();
            ?>
                <table>
                    <tr><th>Donor</th><th>Organization</th><th>Amount</th><th>Date</th></tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['donor']) ?></td>
                        <td><?= htmlspecialchars($row['org_name']) ?></td>
                        <td>PKR<?= number_format($row['amount'],2) ?></td>
                        <td><?= htmlspecialchars($row['donated_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php endif; ?>

        <?php elseif ($user_role === 'donor'): ?>
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h3>Total Donated</h3>
                    <div>
                        PKR <?= number_format(fetch_value($conn, "SELECT IFNULL(SUM(amount),0) FROM donations WHERE donor_id=?", "i", [$user_id]), 2) ?>
                    </div>
                </div>
            </div>
            <!-- Donate Now Button -->
            <div style="text-align:center; margin-bottom:20px;">
                <a href="donate.php" class="cta-btn-large" style="background:#ff007a; display:inline-block; text-decoration:none;">Donate Now</a>
            </div>
            <div class="donation-list">
                <h3>Your Donation History</h3>
                <table>
                    <tr><th>Organization</th><th>Amount</th><th>Message</th><th>Date</th></tr>
                    <?php
                    $sql = "SELECT o.org_name, d.amount, d.message, d.donated_at 
                            FROM donations d 
                            LEFT JOIN organizations o ON d.organization_id=o.id 
                            WHERE d.donor_id=? ORDER BY d.donated_at DESC LIMIT 10";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['org_name']) ?></td>
                        <td>PKR<?= number_format($row['amount'],2) ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= htmlspecialchars($row['donated_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div class="donor-list">
                <h3>Top Donated Organizations</h3>
                <table>
                    <tr><th>Organization</th><th>Total Donated</th></tr>
                    <?php
                    $sql = "SELECT o.org_name, SUM(d.amount) as total 
                            FROM donations d 
                            LEFT JOIN organizations o ON d.organization_id=o.id 
                            WHERE d.donor_id=? GROUP BY d.organization_id 
                            ORDER BY total DESC LIMIT 5";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['org_name']) ?></td>
                        <td>PKR<?= number_format($row['total'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php elseif ($user_role === 'organization'): ?>
            <div class="dashboard-stats">
                <div class="stat-box">
                    <h3>Donations Received</h3>
                    <div>
                        PKR <?= number_format(fetch_value($conn, "SELECT IFNULL(SUM(amount),0) FROM donations WHERE organization_id=(SELECT id FROM organizations WHERE user_id=?)", "i", [$user_id]), 2) ?>
                    </div>
                </div>
            </div>
            <div class="donor-list">
                <h3>Donors List</h3>
                <table>
                    <tr><th>Donor Name</th><th>Total Donated</th></tr>
                    <?php
                    $sql = "SELECT u.name, SUM(d.amount) as total 
                            FROM donations d 
                            LEFT JOIN users u ON d.donor_id=u.id 
                            WHERE d.organization_id=(SELECT id FROM organizations WHERE user_id=?) 
                            GROUP BY d.donor_id ORDER BY total DESC LIMIT 10";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>PKR<?= number_format($row['total'],2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div class="donation-list">
                <h3>Recent Donations</h3>
                <table>
                    <tr><th>Donor</th><th>Amount</th><th>Message</th><th>Date</th></tr>
                    <?php
                    $sql = "SELECT u.name, d.amount, d.message, d.donated_at 
                            FROM donations d 
                            LEFT JOIN users u ON d.donor_id=u.id 
                            WHERE d.organization_id=(SELECT id FROM organizations WHERE user_id=?) 
                            ORDER BY d.donated_at DESC LIMIT 10";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>PKR<?= number_format($row['amount'],2) ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= htmlspecialchars($row['donated_at']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            <div class="graph-placeholder" style="margin-bottom:32px;">
                <!-- For a real graph, use Chart.js or similar. This is a placeholder. -->
                <canvas id="monthlyDonationsChart" width="600" height="220"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            // Fetch monthly donations data via PHP
            <?php
            $monthly = [];
            $orgIdRes = $conn->prepare("SELECT id FROM organizations WHERE user_id=?");
            $orgIdRes->bind_param("i", $user_id);
            $orgIdRes->execute();
            $orgIdRes->bind_result($org_id);
            $orgIdRes->fetch();
            $orgIdRes->close();
            if ($org_id) {
                $stmt = $conn->prepare("
                    SELECT DATE_FORMAT(donated_at, '%Y-%m') as month, SUM(amount) as total
                    FROM donations
                    WHERE organization_id=?
                    GROUP BY month
                    ORDER BY month ASC
                ");
                $stmt->bind_param("i", $org_id);
                $stmt->execute();
                $stmt->bind_result($month, $total);
                while ($stmt->fetch()) {
                    $monthly[$month] = $total;
                }
                $stmt->close();
            }
            $labels = json_encode(array_keys($monthly));
            $data = json_encode(array_values($monthly));
            ?>
            const ctx = document.getElementById('monthlyDonationsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= $labels ?>,
                    datasets: [{
                        label: 'Monthly Donations (PKR)',
                        data: <?= $data ?>,
                        backgroundColor: '#ff007a'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
            </script>
        <?php endif; ?>
    </div>

    <!-- Approvals Modal -->
    <div id="approvalsModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(11,27,77,0.7); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:#fff; color:#0b1b4d; border-radius:12px; padding:32px 28px 24px 28px; min-width:320px; max-width:90vw; box-shadow:0 8px 32px rgba(11,27,77,0.18); position:relative;">
            <button id="closeApprovalsModal" style="position:absolute; top:12px; right:16px; background:none; border:none; font-size:22px; color:#ff007a; cursor:pointer;">&times;</button>
            <h3 style="margin-top:0; margin-bottom:18px;">Pending Organization Approvals</h3>
            <div id="approvalsList">
                <table>
                    <tr>
                        <th>Org Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $result = $conn->query("SELECT o.id, o.org_name, u.email, o.created_at FROM organizations o JOIN users u ON o.user_id=u.id WHERE o.status='pending'");
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr id="org-row-<?= $row['id'] ?>">
                        <td><?= htmlspecialchars($row['org_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <button class="approve-org-btn" data-org="<?= $row['id'] ?>" style="background:#2ecc71;color:#fff;border:none;padding:7px 16px;border-radius:5px;cursor:pointer;">Approve</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
    <script>
    // Approvals Modal open/close
    const approvalsBtn = document.getElementById('approvalsBtn');
    const approvalsModal = document.getElementById('approvalsModal');
    const closeApprovalsModal = document.getElementById('closeApprovalsModal');
    approvalsBtn.onclick = () => approvalsModal.style.display = 'flex';
    closeApprovalsModal.onclick = () => approvalsModal.style.display = 'none';

    // Approve organization AJAX
    document.querySelectorAll('.approve-org-btn').forEach(btn => {
        btn.onclick = function() {
            const orgId = this.getAttribute('data-org');
            this.disabled = true;
            fetch('', {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                body: new URLSearchParams({approve_org_id: orgId})
            })
            .then(r => r.text())
            .then(txt => {
                if (txt.trim() === 'approved') {
                    document.getElementById('org-row-' + orgId).remove();
                } else {
                    this.disabled = false;
                    alert('Approval failed.');
                }
            });
        };
    });
    </script>
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