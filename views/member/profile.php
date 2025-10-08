<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

// Sample user data - in a real app, this would come from a database
$user = [
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '+1 (555) 123-4567',
    'join_date' => '2023-01-15',
    'membership_type' => 'Premium',
    'workouts_completed' => 45,
    'total_hours' => 180,
    'current_streak' => 7,
    'goals' => 'Build muscle and improve strength',
    'fitness_level' => 'Intermediate',
    'preferred_workout_time' => 'Evening'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/profile_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>My Profile</h1>
                <p>Manage your account settings and view your fitness progress.</p>
            </div>

            <div class="profile-container">
                <!-- Profile Sidebar -->
                <div class="profile-sidebar">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h2 class="profile-name"><?= htmlspecialchars($user['name']) ?></h2>
                        <p class="profile-role"><?= htmlspecialchars($user['membership_type']) ?> Member</p>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <h4><?= $user['workouts_completed'] ?></h4>
                            <p>Workouts Completed</p>
                        </div>
                        <div class="stat-item">
                            <h4><?= $user['total_hours'] ?></h4>
                            <p>Total Hours</p>
                        </div>
                        <div class="stat-item">
                            <h4><?= $user['current_streak'] ?></h4>
                            <p>Day Streak</p>
                        </div>
                    </div>
                    
                    <div class="profile-actions">
                        <button class="btn btn-primary">
                            <i class='bx bx-edit'></i>
                            Edit Profile
                        </button>
                        <button class="btn btn-secondary">
                            <i class='bx bx-cog'></i>
                            Settings
                        </button>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="profile-content">
                    <!-- Personal Information -->
                    <div class="profile-section">
                        <h3><i class='bx bx-user'></i> Personal Information</h3>
                        <div class="profile-info">
                            <div class="info-item">
                                <span class="info-label">Full Name</span>
                                <span class="info-value"><?= htmlspecialchars($user['name']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?= htmlspecialchars($user['phone']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Member Since</span>
                                <span class="info-value"><?= date('F j, Y', strtotime($user['join_date'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Fitness Goals -->
                    <div class="profile-section">
                        <h3><i class='bx bx-target-lock'></i> Fitness Goals</h3>
                        <div class="profile-info">
                            <div class="info-item">
                                <span class="info-label">Current Goals</span>
                                <span class="info-value"><?= htmlspecialchars($user['goals']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Fitness Level</span>
                                <span class="info-value"><?= htmlspecialchars($user['fitness_level']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Preferred Workout Time</span>
                                <span class="info-value"><?= htmlspecialchars($user['preferred_workout_time']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="profile-section">
                        <h3><i class='bx bx-cog'></i> Account Settings</h3>
                        <form class="profile-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" id="password" name="password" placeholder="Enter new password">
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="notifications">Email Notifications</label>
                                <select id="notifications" name="notifications">
                                    <option value="all">All Notifications</option>
                                    <option value="important">Important Only</option>
                                    <option value="none">No Notifications</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="privacy">Privacy Settings</label>
                                <select id="privacy" name="privacy">
                                    <option value="public">Public Profile</option>
                                    <option value="friends">Friends Only</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-save'></i>
                                    Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary">
                                    <i class='bx bx-x'></i>
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Danger Zone -->
                    <div class="profile-section">
                        <h3><i class='bx bx-shield-x'></i> Danger Zone</h3>
                        <div class="danger-actions">
                            <button class="btn btn-danger">
                                <i class='bx bx-trash'></i>
                                Delete Account
                            </button>
                            <p class="danger-warning">This action cannot be undone. All your data will be permanently deleted.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
