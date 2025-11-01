<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

require_once '../../controllers/AdminController.php';
$adminController = new AdminController();

// Get report data
$totalMembers = $adminController->getTotalMembers();
$activeMembers = $adminController->getActiveMembers();
$totalTrainers = $adminController->getTotalTrainers();
$totalRevenue = $adminController->getTotalRevenue();
$monthlyRevenue = $adminController->getMonthlyRevenue();
$revenueByMonth = $adminController->getRevenueByMonth();
$membershipStats = $adminController->getMembershipStats();
$paymentMethodStats = $adminController->getPaymentMethodStats();
$recentPayments = $adminController->getRecentPayments(10);
$memberGrowth = $adminController->getMemberGrowth();

$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/modal_styles.css">
    <link rel="stylesheet" href="../../assets/css/admin_users_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h1>Reports & Analytics</h1>
                <p>Comprehensive business insights and performance metrics.</p>
            </div>

            <!-- Key Metrics Overview -->
            <div class="quick-stats" style="margin-bottom: 30px;">
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
                        <i class='bx bx-check-circle'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $activeMembers; ?></h3>
                        <p>Active Members</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-dumbbell'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalTrainers; ?></h3>
                        <p>Total Trainers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-credit-card'></i>
                    </div>
                    <div class="stat-content">
                        <h3>₱<?php echo number_format($totalRevenue); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="dashboard-grid">
                <!-- Revenue Chart -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Revenue Trends</h2>
                        <p>Monthly revenue over the last 12 months</p>
                    </div>
                    <div class="card">
                        <canvas id="revenueChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Membership Distribution -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Membership Distribution</h2>
                        <p>Breakdown by membership type</p>
                    </div>
                    <div class="card">
                        <canvas id="membershipChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Payment Methods</h2>
                        <p>Preferred payment methods</p>
                    </div>
                    <div class="card">
                        <canvas id="paymentMethodChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Member Growth -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Member Growth</h2>
                        <p>New members over time</p>
                    </div>
                    <div class="card">
                        <canvas id="memberGrowthChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Reports -->
            <div class="dashboard-grid" style="margin-top: 30px;">
                <!-- Recent Payments -->
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
                                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime($payment['payment_date']))); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Monthly Summary -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>This Month Summary</h2>
                    </div>
                    <div class="card">
                        <div class="summary-stats">
                            <div class="summary-item">
                                <h4>₱<?php echo number_format($monthlyRevenue); ?></h4>
                                <p>Monthly Revenue</p>
                            </div>
                            <div class="summary-item">
                                <h4><?php echo $adminController->getNewMembersThisMonth(); ?></h4>
                                <p>New Members</p>
                            </div>
                            <div class="summary-item">
                                <h4><?php echo $adminController->getAveragePayment(); ?></h4>
                                <p>Avg Payment</p>
                            </div>
                            <div class="summary-item">
                                <h4><?php echo $adminController->getRetentionRate(); ?>%</h4>
                                <p>Retention Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = <?php echo json_encode($revenueByMonth); ?>;
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => item.month),
                datasets: [{
                    label: 'Revenue (₱)',
                    data: revenueData.map(item => item.revenue),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Membership Chart
        const membershipCtx = document.getElementById('membershipChart').getContext('2d');
        const membershipData = <?php echo json_encode($membershipStats); ?>;
        new Chart(membershipCtx, {
            type: 'doughnut',
            data: {
                labels: membershipData.map(item => item.membership_type),
                datasets: [{
                    data: membershipData.map(item => item.count),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });

        // Payment Method Chart
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const paymentMethodData = <?php echo json_encode($paymentMethodStats); ?>;
        new Chart(paymentMethodCtx, {
            type: 'bar',
            data: {
                labels: paymentMethodData.map(item => item.payment_method),
                datasets: [{
                    label: 'Count',
                    data: paymentMethodData.map(item => item.count),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Member Growth Chart
        const memberGrowthCtx = document.getElementById('memberGrowthChart').getContext('2d');
        const memberGrowthData = <?php echo json_encode($memberGrowth); ?>;
        new Chart(memberGrowthCtx, {
            type: 'bar',
            data: {
                labels: memberGrowthData.map(item => item.month),
                datasets: [{
                    label: 'New Members',
                    data: memberGrowthData.map(item => item.new_members),
                    backgroundColor: 'rgba(75, 192, 192, 0.8)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <style>
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .summary-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .summary-item h4 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .summary-item p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</body>
</html>
