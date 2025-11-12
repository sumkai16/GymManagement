<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Trainer';

// Load models to compute live stats
require_once __DIR__ . '/../../models/Trainer.php';
require_once __DIR__ . '/../../models/Booking.php';

$trainerModel = new Trainer();
$bookingModel = new Booking();

$trainer = $trainerModel->getTrainerByUserId($_SESSION['user_id']);
$stats_members = 0;
$stats_today = 0;
$stats_upcoming = 0;
if ($trainer) {
    // Members who have booked with this trainer (distinct)
    $counts = $bookingModel->getCountsForTrainer($trainer['trainer_id']);
    $stats_members = (int)($counts['members'] ?? 0);
    $stats_upcoming = (int)($counts['upcoming'] ?? 0);
    // Sessions today (pending or confirmed)
    try {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) as c FROM trainer_bookings WHERE trainer_id = :tid AND booking_date = CURDATE() AND status IN ('pending','confirmed')");
        $stmt->bindParam(':tid', $trainer['trainer_id'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats_today = (int)($row['c'] ?? 0);
    } catch (Exception $e) {
        $stats_today = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Alert System -->
    <?php include '../utilities/alert.php'; ?>
    
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Trainer Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($username); ?>! Manage your training sessions.</p>
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo (int)$stats_members; ?></h3>
                        <p>My Members</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-calendar'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo (int)$stats_today; ?></h3>
                        <p>Sessions Today</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-dumbbell'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo (int)$stats_upcoming; ?></h3>
                        <p>Upcoming Sessions</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-message'></i>
                    </div>
                    <div class="stat-content">
                        <h3>0</h3>
                        <p>New Messages</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Today's Schedule</h2>
                        <a href="schedule.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <p>View and manage your training sessions for today.</p>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>My Members</h2>
                        <a href="my_members.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <p>Track progress and communicate with your members.</p>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Client Workout History</h2>
                        <a href="client_workout_history.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <p>Monitor your clients' workout progress and performance.</p>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Workout Plans</h2>
                        <a href="workouts.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <p>Create and manage workout plans for your members.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
