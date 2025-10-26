<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/admin_dashboard.css">
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
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button id="refreshDashboard" class="refresh-btn">
                        <i class='bx bx-refresh'></i> Refresh Data
                    </button>
                    <span class="auto-refresh">Auto-refresh: <span id="autoRefreshStatus">ON</span></span>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="stat-content">
                        <h3>150</h3>
                        <p>Total Members</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-dumbbell'></i>
                    </div>
                    <div class="stat-content">
                        <h3>8</h3>
                        <p>Active Trainers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-credit-card'></i>
                    </div>
                    <div class="stat-content">
                        <h3>₱45,000</h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-calendar'></i>
                    </div>
                    <div class="stat-content">
                        <h3>25</h3>
                        <p>Sessions Today</p>
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
                        <p>Manage member registrations, payments, and profiles.</p>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Today's Attendance</h2>
                        <a href="attendance.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <p>Manage trainer schedules, assignments, and performance.</p>
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
</body>
</html>
