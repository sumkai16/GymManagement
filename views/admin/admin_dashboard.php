<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../../controllers/AdminController.php';
$adminController = new AdminController();

// Handle AJAX requests for dynamic data
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'get_stats':
            $stats = $adminController->getDashboardStats();
            header('Content-Type: application/json');
            echo json_encode($stats);
            exit;

        case 'get_recent_members':
            $members = $adminController->getRecentMembers(5);
            header('Content-Type: application/json');
            echo json_encode($members);
            exit;

        case 'get_recent_trainers':
            $trainers = $adminController->getRecentTrainers(5);
            header('Content-Type: application/json');
            echo json_encode($trainers);
            exit;
    }
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
                        <h3 id="totalMembers">--</h3>
                        <p>Total Members</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-dumbbell'></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="activeTrainers">--</h3>
                        <p>Active Trainers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-credit-card'></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="monthlyRevenue">--</h3>
                        <p>Monthly Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-calendar'></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="sessionsToday">--</h3>
                        <p>Sessions Today</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Members</h2>
                        <a href="members.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <div id="recentMembersContent">
                            <div class="recent-loading">
                                <div class="loading-spinner"></div>
                                <p>Loading recent members...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Trainer Management</h2>
                        <a href="trainers.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <div id="recentTrainersContent">
                            <div class="recent-loading">
                                <div class="loading-spinner"></div>
                                <p>Loading recent trainers...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Financial Reports</h2>
                        <a href="reports.php" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <p>View revenue, payments, and financial analytics.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/admin_dashboard.js"></script>
</body>
</html>
