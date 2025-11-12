<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . '/../../controllers/TrainerController.php';

$controller = new TrainerController();
$clients = $controller->getMyClients($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'Trainer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Members - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../utilities/alert.php'; ?>
    <?php include '../components/dynamic_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>My Members</h1>
                <p>Here are the members who booked with you, <?php echo htmlspecialchars($username); ?>.</p>
            </div>

            <div class="card" style="margin-top: 2rem; padding: 2rem;">
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin: 0; color: var(--primary-color); font-size: 1.3rem;">Client List</h3>
                    <p style="margin: 0.5rem 0 0 0; color: var(--text-muted); font-size: 0.9rem;">Manage your training clients</p>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="users-table" style="width:100%; margin: 0;">
                        <thead>
                            <tr>
                                <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Name</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Email</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Phone</th>
                                <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clients)): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; padding: 3rem 1rem; color: var(--text-muted); font-size: 1rem;">
                                        <i class='bx bx-user-x' style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                                        No clients yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clients as $c): ?>
                                    <tr style="border-bottom: 1px solid var(--border-light); transition: var(--tran-02);">
                                        <td style="padding: 1rem; font-weight: 500;"><?php echo htmlspecialchars($c['full_name']); ?></td>
                                        <td style="padding: 1rem; color: var(--text-muted);"><?php echo htmlspecialchars($c['email']); ?></td>
                                        <td style="padding: 1rem; color: var(--text-muted);"><?php echo htmlspecialchars($c['phone'] ?? 'N/A'); ?></td>
                                        <td style="padding: 1rem;">
                                            <span style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; background: <?php echo ($c['status'] ?? '') === 'active' ? 'var(--success-color)' : 'var(--warning-color)'; ?>; color: white;">
                                                <?php echo htmlspecialchars(ucfirst($c['status'] ?? 'Inactive')); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
