<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

// Instead of static sample data, fetch from DB
require_once '../../models/Member.php';
$memberModel = new Member();
// Get user id from session
$user_id = $_SESSION['user_id'];
// Get member's row using user_id
$user = $memberModel->getMemberByUserId($user_id); // New method we will add
if (!$user) {
    die('Member profile not found.');
}

$updateResult = null; // For modal feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $user['member_id'];
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $notifications = $_POST['notifications'] ?? '';
    $privacy = $_POST['privacy'] ?? '';
    $updateOk = true;
    $passwordChanged = false;

    // Create database connection and pass to User model
    require_once '../../config/database.php';
    require_once '../../models/User.php';
    $database = new Database();
    $userModel = new User($database->getConnection());

    if ($newPassword !== '' && $newPassword === $confirmPassword) {
        $passwordChanged = $userModel->updatePasswordByUserId($user_id, $newPassword);
        if (!$passwordChanged) {
            $updateOk = false;
            $updateResult = ['type' => 'error', 'msg' => 'Failed to update password.'];
        }
    } elseif ($newPassword !== $confirmPassword) {
        $updateOk = false;
        $updateResult = ['type' => 'error', 'msg' => 'Password and Confirm Password do not match.'];
    }
    if ($updateOk) {
        $updateResult = ['type' => 'success', 'msg' => 'Account settings updated successfully!'];
    }
}

// Prepare modal data for the modal component
$modalData = null;
if ($updateResult) {
    $modalData = [
        'id' => 'settings-modal',
        'type' => $updateResult['type'],
        'title' => $updateResult['type'] === 'success' ? 'Success!' : 'Error',
        'message' => $updateResult['msg'],
        'show' => true
    ];
}
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
                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                        </div>
                        <h2 class="profile-name"><?= htmlspecialchars($user['full_name']) ?></h2>
                        <p class="profile-role"><?= htmlspecialchars($user['membership_type']) ?> Member</p>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <h4><?= ucfirst($user['membership_type']); ?></h4>
                            <p>Membership</p>
                        </div>
                        <div class="stat-item">
                            <h4><?= ucfirst($user['status']); ?></h4>
                            <p>Status</p>
                        </div>
                        <div class="stat-item">
                            <h4><?= date('F Y', strtotime($user['start_date'])); ?></h4>
                            <p>Member Since</p>
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
                                <span class="info-value"><?= htmlspecialchars($user['full_name']) ?></span>
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
                                <span class="info-value"><?= date('F j, Y', strtotime($user['start_date'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Fitness Goals -->
                    <!-- Removed Fitness Goals section as per redesign -->

                    <!-- Account Settings -->
                    <div class="profile-section">
                        <h3><i class='bx bx-cog'></i> Account Settings</h3>
                        <form class="profile-form" method="post">
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

    <!-- Include Modern Modal Component -->
    <?php if ($modalData): ?>
        <?php include '../utilities/modal.php'; ?>
    <?php endif; ?>
</body>
</html>
