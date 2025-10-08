<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Member';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition - Supplements - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/nutrition_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Nutrition - Supplements</h1>
                <p>Track your supplement intake and monitor your nutritional supplements.</p>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Today's Supplements</h2>
                        <button class="btn-primary">Add Supplement</button>
                    </div>
                    <div class="card">
                        <p>Track your daily supplement intake including vitamins, protein powders, and other nutritional supplements.</p>
                        <div class="supplement-list">
                            <div class="supplement-item">
                                <div class="supplement-info">
                                    <h4>Whey Protein</h4>
                                    <p>25g protein • 120 calories</p>
                                </div>
                                <div class="supplement-time">
                                    <span>Post-Workout</span>
                                </div>
                            </div>
                            <div class="supplement-item">
                                <div class="supplement-info">
                                    <h4>Multivitamin</h4>
                                    <p>Daily vitamin complex</p>
                                </div>
                                <div class="supplement-time">
                                    <span>Morning</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Supplement Schedule</h2>
                    </div>
                    <div class="card">
                        <p>Plan and schedule your supplement intake throughout the day.</p>
                        <div class="schedule-grid">
                            <div class="schedule-item">
                                <h4>Morning</h4>
                                <ul>
                                    <li>Multivitamin</li>
                                    <li>Omega-3</li>
                                </ul>
                            </div>
                            <div class="schedule-item">
                                <h4>Pre-Workout</h4>
                                <ul>
                                    <li>Creatine</li>
                                    <li>Pre-workout</li>
                                </ul>
                            </div>
                            <div class="schedule-item">
                                <h4>Post-Workout</h4>
                                <ul>
                                    <li>Whey Protein</li>
                                    <li>BCAA</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Supplement History</h2>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="card">
                        <p>View your supplement intake history and track your consistency.</p>
                        <div class="history-chart">
                            <p>📊 Supplement tracking chart would go here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
