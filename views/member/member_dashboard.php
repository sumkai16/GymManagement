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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gym Management</title>
    <link rel="stylesheet" type="text/css" href="../../assets/css/member_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <nav class="sidebar close">
        <header>
            <div class="logo">
                <img src="../../assets/images/logo.png" alt="Logo" width="150">
                <div class="text">
                    <span class="welcome">Welcome,</span>
                    <span class="member-name"><?php echo htmlspecialchars($member_name); ?></span>
                </div>
            </div>
            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link active">
                        <a href="member.php">
                            <i class='bx bx-home-alt icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="workout.php">
                            <i class='bx bx-dumbbell icon'></i>
                            <span class="text nav-text">Workout</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <button class="dropdown-btn">
                            <i class='bx bx-bowl-rice icon'></i>
                            <span class="text nav-text">Nutrition</span>
                            <i class='bx bx-chevron-down dropdown-arrow'></i>
                        </button>
                    </li>
                    <div class="dropdown-container">
                        <a href="nutrition-food.php">
                            <span class="text nav-text">Food</span>
                        </a>
                        <a href="nutrition-supplement.php">
                            <span class="text nav-text">Supplement</span>
                        </a>
                    </div>

                    <li class="nav-link">
                        <a href="coaches.php">
                            <i class='bx bx-group icon'></i>
                            <span class="text nav-text">Coaches</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="profile.php">
                            <i class='bx bxs-user icon'></i>
                            <span class="text nav-text">Profile</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="bottom-cont">
                <li>
                    <a href="../../controllers/AuthController.php?action=logout" id="logoutBtn">
                        <i class='bx bx-log-out-circle icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>

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
                        <i class='bx bx-bowl-rice'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $data['calories_today']; ?></h3>
                        <p>Calories Today</p>
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
                        <i class='bx bx-trending-up'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $data['strength_gain']; ?></h3>
                        <p>Strength Gain</p>
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
                            <span class="status-completed">Completed</span>
                        </div>
                    </div>
                </div>

                <!-- Nutrition Summary -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Nutrition Today</h2>
                        <a href="nutrition-food.php" class="view-all">View All</a>
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

                <!-- Weekly Progress -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Weekly Progress</h2>
                        <a href="workout.php" class="view-all">View All</a>
                    </div>
                    <div class="progress-card">
                        <div class="progress-item">
                            <div class="progress-label">
                                <span>Workouts</span>
                                <span><?php echo $data['weekly_progress']['workouts']; ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo (explode('/', $data['weekly_progress']['workouts'])[0] / 5) * 100; ?>%"></div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-label">
                                <span>Cardio</span>
                                <span><?php echo $data['weekly_progress']['cardio']; ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo (explode('/', $data['weekly_progress']['cardio'])[0] / 3) * 100; ?>%"></div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <div class="progress-label">
                                <span>Nutrition Goals</span>
                                <span><?php echo $data['weekly_progress']['nutrition']; ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo (explode('/', $data['weekly_progress']['nutrition'])[0] / 7) * 100; ?>%"></div>
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

                <!-- Upcoming Sessions -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Upcoming Sessions</h2>
                        <a href="coaches.php" class="view-all">View All</a>
                    </div>
                    <div class="sessions-card">
                        <?php if (empty($data['upcoming_sessions'])): ?>
                        <p>No upcoming sessions.</p>
                        <?php else: ?>
                        <?php foreach ($data['upcoming_sessions'] as $session): ?>
                        <div class="session-item">
                            <div class="session-time">
                                <span class="time"><?php echo htmlspecialchars($session['time']); ?></span>
                                <span class="date"><?php echo htmlspecialchars($session['date']); ?></span>
                            </div>
                            <div class="session-details">
                                <h4><?php echo htmlspecialchars($session['title']); ?></h4>
                                <p><?php echo htmlspecialchars($session['coach']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
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

    <script src="../../assets/js/dashboard_member.js"></script>
</body>
</html>
</html>
