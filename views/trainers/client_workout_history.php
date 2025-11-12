<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . '/../../controllers/TrainerController.php';
require_once __DIR__ . '/../../models/Trainer.php';

$controller = new TrainerController();
$trainer = $controller->getTrainerByUser($_SESSION['user_id']);
if (!$trainer) {
    $_SESSION['error'] = 'Trainer profile not found';
    header('Location: trainers_dashboard.php');
    exit;
}

$memberId = isset($_GET['member_id']) ? (int)$_GET['member_id'] : 0;
if ($memberId <= 0) {
    $_SESSION['error'] = 'No member selected.';
    header('Location: my_members.php');
    exit;
}

// Verify the member belongs to this trainer via bookings history (simple guard)
require_once __DIR__ . '/../../models/Booking.php';
$bookingModel = new Booking();
$bookingsWithMember = $bookingModel->getTrainerBookings($trainer['trainer_id']);
$allowed = false;
foreach ($bookingsWithMember as $b) {
    if ((int)$b['member_id'] === $memberId) { $allowed = true; break; }
}
if (!$allowed) {
    $_SESSION['error'] = 'Access denied for this member.';
    header('Location: my_members.php');
    exit;
}

// Fetch workout history and simple stats using Trainer model helpers
$trainerModel = new Trainer();
$history = $trainerModel->getClientWorkoutHistory($memberId, 100, 0);
$stats = $trainerModel->getClientWorkoutStats($memberId, 30);
$username = $_SESSION['username'] ?? 'Trainer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Workout History - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .stats { display:flex; gap:16px; flex-wrap:wrap; }
        .stats .stat-card { min-width: 180px; }
    </style>
</head>
<body>
    <?php include '../utilities/alert.php'; ?>
    <?php include '../components/dynamic_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Client Workout History</h1>
                <p>Review logs and stats for the selected member.</p>
            </div>

            <div class="quick-stats stats">
                <div class="stat-card"><div class="stat-icon"><i class='bx bx-calendar'></i></div><div class="stat-content"><h3><?php echo (int)($stats['workout_days'] ?? 0); ?></h3><p>Workout Days (30d)</p></div></div>
                <div class="stat-card"><div class="stat-icon"><i class='bx bx-list-ul'></i></div><div class="stat-content"><h3><?php echo (int)($stats['total_exercises'] ?? 0); ?></h3><p>Total Exercises</p></div></div>
                <div class="stat-card"><div class="stat-icon"><i class='bx bx-time'></i></div><div class="stat-content"><h3><?php echo (int)($stats['total_duration'] ?? 0); ?></h3><p>Total Minutes</p></div></div>
                <div class="stat-card"><div class="stat-icon"><i class='bx bx-dumbbell'></i></div><div class="stat-content"><h3><?php echo (int)($stats['max_weight'] ?? 0); ?></h3><p>Max Weight</p></div></div>
            </div>

            <div class="card">
                <table class="users-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Workout</th>
                            <th>Exercise</th>
                            <th>Sets</th>
                            <th>Reps</th>
                            <th>Weight</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr><td colspan="7" style="text-align:center; padding:16px;">No workout logs yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($history as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['log_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['workout_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['exercise_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['sets'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['reps'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['weight'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['duration'] ?? ''); ?></td>
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
