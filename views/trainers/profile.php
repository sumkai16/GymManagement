<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../../config/database.php';
require_once '../../models/Trainer.php';
require_once '../../controllers/BookingController.php';

try {
    $trainerModel = new Trainer();
    $bookingController = new BookingController();
    // Get user id from session
    $user_id = $_SESSION['user_id'];
    
    // Debug: Check if user_id exists
    if (!$user_id) {
        die('User ID not found in session.');
    }
    
    // Get trainer's row using user_id
    $trainer = $trainerModel->getTrainerByUserId($user_id);
    
    // If no trainer record exists, create one from user data
    if (!$trainer) {
        require_once '../../models/User.php';
        $database = new Database();
        $userModel = new User($database->getConnection());
        $userData = $userModel->getUserById($user_id);
        
        if ($userData) {
            // Create trainer record
            $trainerData = [
                'user_id' => $user_id,
                'first_name' => $userData['first_name'] ?? 'First',
                'last_name' => $userData['last_name'] ?? 'Name',
                'email' => $userData['email'] ?? 'trainer@fitnexus.local',
                'phone' => '0000000000',
                'specialty' => 'Fitness Training',
                'experience_years' => 1,
                'certification' => 'Certified Trainer',
                'hourly_rate' => 500,
                'availability' => 'Available',
                'status' => 'active'
            ];
            
            $trainerId = $trainerModel->addTrainer($trainerData);
            if ($trainerId) {
                // Try to get the newly created trainer record
                $trainer = $trainerModel->getTrainerByUserId($user_id);
                if (!$trainer) {
                    error_log("Failed to retrieve newly created trainer for user_id: $user_id");
                    die('Trainer profile creation failed. Please contact support.');
                }
            } else {
                error_log("Failed to create trainer record for user_id: $user_id");
                die('Failed to create trainer profile. Please try again later.');
            }
        } else {
            error_log("User data not found for user_id: $user_id");
            die('User account not found. Please log in again.');
        }
    }
} catch (Exception $e) {
    error_log('Trainer profile page error: ' . $e->getMessage());
    die('An error occurred while loading your profile. Please try again later.');
}

$updateResult = null; // For modal feedback

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'] ?? null;
    if ($booking_id) {
        $cancelResult = $bookingController->cancelBooking($booking_id, $_SESSION['user_id']);
        $updateResult = [
            'type' => $cancelResult['success'] ? 'success' : 'error',
            'msg' => $cancelResult['message']
        ];
    }
}

// Get trainer bookings
$bookings = $bookingController->getTrainerBookings($_SESSION['user_id']);
$upcomingBookings = array_filter($bookings, function($booking) {
    $bookingDateTime = $booking['booking_date'] . ' ' . $booking['booking_time'];
    $now = date('Y-m-d H:i:s');
    return ($booking['status'] === 'pending' || $booking['status'] === 'confirmed') && $bookingDateTime >= $now;
});

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['cancel_booking'])) {
    $trainer_id = $trainer['trainer_id'];
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $updateOk = true;
    $passwordChanged = false;

    // Create database connection and pass to User model
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
    <title>Trainer Profile - FitNexus</title>
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
                <h1>My Trainer Profile</h1>
                <p>Manage your trainer profile and view your client bookings.</p>
            </div>

            <div class="profile-container">
                <!-- Profile Sidebar -->
                <div class="profile-sidebar">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($trainer['first_name'], 0, 1) . substr($trainer['last_name'], 0, 1)) ?>
                        </div>
                        <h2 class="profile-name"><?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?></h2>
                        <p class="profile-role"><?= htmlspecialchars($trainer['specialty']) ?></p>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <h4><?= $trainer['experience_years'] ?> Years</h4>
                            <p>Experience</p>
                        </div>
                        <div class="stat-item">
                            <h4>₱<?= number_format($trainer['hourly_rate']) ?></h4>
                            <p>Hourly Rate</p>
                        </div>
                        <div class="stat-item">
                            <h4><?= ucfirst($trainer['status']); ?></h4>
                            <p>Status</p>
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
                                <span class="info-value"><?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?= htmlspecialchars($trainer['email']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?= htmlspecialchars($trainer['phone']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Specialty</span>
                                <span class="info-value"><?= htmlspecialchars($trainer['specialty']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Certification</span>
                                <span class="info-value"><?= htmlspecialchars($trainer['certification']) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- My Bookings -->
                    <div class="profile-section">
                        <h3><i class='bx bx-calendar-check'></i> My Bookings</h3>
                        <?php if (empty($upcomingBookings)): ?>
                            <div style="text-align: center; padding: 2rem; color: #999;">
                                <i class='bx bx-calendar-x' style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                <p>No upcoming bookings</p>
                                <p style="font-size: 0.9rem; margin-top: 0.5rem;">Your client bookings will appear here.</p>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <?php foreach ($upcomingBookings as $booking): ?>
                                    <?php
                                    $bookingDate = new DateTime($booking['booking_date']);
                                    $bookingTime = new DateTime($booking['booking_time']);
                                    $isToday = $booking['booking_date'] === date('Y-m-d');
                                    $statusColors = [
                                        'pending' => '#ffc107',
                                        'confirmed' => '#28a745',
                                        'cancelled' => '#dc3545'
                                    ];
                                    ?>
                                    <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 1.5rem; background: #f8f9fa;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                            <div>
                                                <h4 style="margin: 0 0 0.5rem 0; color: #333;">
                                                    <?= htmlspecialchars($booking['member_name']) ?>
                                                </h4>
                                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                                    Client Session
                                                </p>
                                            </div>
                                            <span style="background: <?= $statusColors[$booking['status']] ?? '#6c757d' ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                                <?= htmlspecialchars($booking['status']) ?>
                                            </span>
                                        </div>
                                        <div style="display: flex; gap: 2rem; margin-bottom: 1rem; color: #666;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class='bx bx-calendar'></i>
                                                <span>
                                                    <?= $isToday ? 'Today' : $bookingDate->format('F j, Y') ?>
                                                </span>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class='bx bx-time'></i>
                                                <span><?= $bookingTime->format('g:i A') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

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
