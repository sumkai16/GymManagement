<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../../controllers/AdminController.php';
$adminController = new AdminController();

// Get real-time statistics
$totalMembers = $adminController->getTotalMembers();
$activeMembers = $adminController->getActiveMembers();
$totalTrainers = $adminController->getTotalTrainers();
$totalRevenue = $adminController->getTotalRevenue();
$monthlyRevenue = $adminController->getMonthlyRevenue();
$todayStats = $adminController->getTodayAttendanceStats();
$recentPayments = $adminController->getRecentPayments(5);
$memberGrowth = $adminController->getMemberGrowth();

$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitNexus</title>
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
                <h1>Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($username); ?>! Manage your gym operations.</p>
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalMembers; ?></h3>
                        <p>Total Members</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-dumbbell'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalTrainers; ?></h3>
                        <p>Active Trainers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-credit-card'></i>
                    </div>
                    <div class="stat-content">
                        <h3>₱<?php echo number_format($monthlyRevenue); ?></h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-calendar'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $todayStats['total_check_ins']; ?></h3>
                        <p>Check-ins Today</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Payments</h2>
                        <a href="payments.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['member_name']); ?></td>
                                    <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td>
                                        <span class="role-badge role-member">
                                            <?php echo htmlspecialchars($payment['payment_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('M d', strtotime($payment['payment_date']))); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Today's Attendance</h2>
                        <a href="attendance.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <div class="attendance-stats">
                            <div class="attendance-item">
                                <h4><?php echo $todayStats['currently_in_gym']; ?></h4>
                                <p>Currently in Gym</p>
                            </div>
                            <div class="attendance-item">
                                <h4><?php echo $todayStats['peak_hour']; ?></h4>
                                <p>Peak Hour</p>
                            </div>
                            <div class="attendance-item">
                                <h4><?php echo $todayStats['avg_duration']; ?> min</h4>
                                <p>Avg Duration</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div class="card">
                        <div class="quick-actions">
                            <a href="members.php" class="action-link">
                                <i class='bx bx-user-plus'></i>
                                <span>Add Member</span>
                            </a>
                            <a href="trainers.php" class="action-link">
                                <i class='bx bx-dumbbell'></i>
                                <span>Add Trainer</span>
                            </a>
                            <a href="payments.php" class="action-link">
                                <i class='bx bx-credit-card'></i>
                                <span>Record Payment</span>
                            </a>
                            <a href="attendance.php" class="action-link">
                                <i class='bx bx-log-in'></i>
                                <span>Check-in Member</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .attendance-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .attendance-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .attendance-item h4 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .attendance-item p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            padding: 20px;
        }

        .action-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            text-decoration: none;
            color: #2c3e50;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .action-link:hover {
            background: #e9ecef;
            border-color: #007bff;
            transform: translateY(-2px);
        }

        .action-link i {
            font-size: 24px;
            margin-bottom: 8px;
            color: #007bff;
        }

        .action-link span {
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</body>
</html>
