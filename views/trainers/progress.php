<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . '/../../controllers/TrainerController.php';
$controller = new TrainerController();
$username = $_SESSION['username'] ?? 'Trainer';

// Optional: future filters for a specific member and time range
$selectedMemberId = isset($_GET['member_id']) ? (int)$_GET['member_id'] : null;
$rangeDays = isset($_GET['days']) ? max(7, (int)$_GET['days']) : 30;

$clients = $controller->getMyClients($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../utilities/alert.php'; ?>
    <?php include '../components/dynamic_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Client Progress</h1>
                <p>View high-level progress for your members. (Coming soon)</p>
            </div>

            <div class="card">
                <p>Select a member from your list on <a href="my_members.php">My Members</a> to view more detailed analytics in the future.</p>
            </div>
        </div>
    </div>
</body>
</html>
