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

            <div class="card">
                <table class="users-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clients)): ?>
                            <tr><td colspan="4" style="text-align:center; padding:16px;">No clients yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($clients as $c): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($c['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                                    <td><?php echo htmlspecialchars($c['phone'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($c['status'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
