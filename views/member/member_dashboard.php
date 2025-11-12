<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../../models/Member.php';
$member = new Member();
$member_name = $member->getMemberInfo($_SESSION['user_id']);
$data = $member->getDashboardData($_SESSION['user_id']);
$completedToday = $member->hasWorkoutCompletedToday($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FitNexus</title>
    <link rel="stylesheet" type="text/css" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/dashboard_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Dashboard</h1>
                <p>Welcome back! Here's your fitness overview for today.</p>
            </div>

            <!-- Quick Stats Row -->
            <div class="quick-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-dumbbell'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $data['workouts_this_week']; ?></h3>
                        <p>Workouts This Week</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-target-lock'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo round(($data['nutrition_today']['calories'] / 2500) * 100); ?>%</h3>
                        <p>Nutrition Goal</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-timer'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $data['last_workout']; ?></h3>
                        <p>Last Workout</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-dna'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $data['nutrition_today']['protein']; ?>g</h3>
                        <p>Protein Today</p>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Today's Workout -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Today's Workout</h2>
                        <a href="workout.php" class="view-all">View All</a>
                    </div>
                    <div class="workout-card">
                        <div class="workout-info">
                            <h3><?php echo htmlspecialchars($data['todays_workout']); ?></h3>
                            <p class="workout-time">45 minutes • 2:30 PM</p>
                            <div class="workout-exercises">
                                <span class="exercise-tag">Bench Press</span>
                                <span class="exercise-tag">Pull-ups</span>
                                <span class="exercise-tag">Shoulder Press</span>
                            </div>
                        </div>
                        <div class="workout-status">
                            <?php if (!empty($completedToday)): ?>
                                <span class="status-completed">Completed</span>
                            <?php else: ?>
                                <span class="status-in-progress">Pending</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Nutrition Summary -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Nutrition Today</h2>
                        <div class="section-links">
                            <a href="nutrition-food.php" class="view-all">Food</a>
                            <a href="nutrition-supplement.php" class="view-all">Supplements</a>
                        </div>
                    </div>
                    <div class="nutrition-card">
                        <div class="calorie-progress">
                            <div class="progress-info">
                                <span><?php echo $data['nutrition_today']['calories']; ?> / 2,500 calories</span>
                                <span><?php echo round(($data['nutrition_today']['calories'] / 2500) * 100); ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo round(($data['nutrition_today']['calories'] / 2500) * 100); ?>%"></div>
                            </div>
                        </div>
                        <div class="nutrition-macros">
                            <div class="macro-item">
                                <span class="macro-label">Protein</span>
                                <span class="macro-value"><?php echo $data['nutrition_today']['protein']; ?>g</span>
                            </div>
                            <div class="macro-item">
                                <span class="macro-label">Carbs</span>
                                <span class="macro-value"><?php echo $data['nutrition_today']['carbs']; ?>g</span>
                            </div>
                            <div class="macro-item">
                                <span class="macro-label">Fat</span>
                                <span class="macro-value"><?php echo $data['nutrition_today']['fats']; ?>g</span>
                            </div>
                        </div>
                    </div>
                </div>

                

                <!-- Recent Activity -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Recent Activity</h2>
                        <a href="#" class="view-all">View All</a>
                    </div>
                    <div class="activity-card">
                        <?php foreach ($data['recent_activity'] as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class='bx bx-<?php echo $activity['type'] == 'workout' ? 'dumbbell' : ($activity['type'] == 'nutrition' ? 'bowl-rice' : 'trending-up'); ?>'></i>
                            </div>
                            <div class="activity-content">
                                <h4><?php echo htmlspecialchars($activity['description']); ?></h4>
                                <p><?php echo htmlspecialchars($activity['time']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                

                <!-- Quick Actions -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Quick Actions</h2>
                    </div>
                    <div class="quick-actions">
                        <a href="workout.php" class="action-btn">
                            <i class='bx bx-plus'></i>
                            <span>Start Workout</span>
                        </a>
                        <a href="nutrition-food.php" class="action-btn">
                            <i class='bx bx-bowl-rice'></i>
                            <span>Log Meal</span>
                        </a>
                        <a href="coaches.php" class="action-btn">
                            <i class='bx bx-calendar'></i>
                            <span>Book Session</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic sidebar JavaScript is already included in the sidebar component -->
</body>
</html>
</html>
