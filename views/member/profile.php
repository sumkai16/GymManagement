<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

// Instead of static sample data, fetch from DB
require_once '../../config/database.php';
require_once '../../models/Member.php';
require_once '../../controllers/BookingController.php';

try {
    $memberModel = new Member();
    $bookingController = new BookingController();
    // Get user id from session
    $user_id = $_SESSION['user_id'];
    
    // Debug: Check if user_id exists
    if (!$user_id) {
        die('User ID not found in session.');
    }
    
    // Get member's row using user_id
    $user = $memberModel->getMemberByUserId($user_id);
    
    // If no member record exists, create one from user data
    if (!$user) {
        require_once '../../models/User.php';
        $database = new Database();
        $userModel = new User($database->getConnection());
        $userData = $userModel->getUserById($user_id);
        
        if ($userData) {
            // Debug: Log user data
            error_log("User data found: " . print_r($userData, true));
            
            // Ensure we have valid data for required fields
            $fullName = trim(!empty($userData['username']) ? $userData['username'] : 'Member ' . $user_id);
            $email = trim(!empty($userData['email']) ? $userData['email'] : 'member' . $user_id . '@gmail.com');
            $phone = '09123456789'; // Default phone
            $address = 'Address to be updated'; // Default address
            $membershipType = 'monthly';
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+1 month'));
            $status = 'active';
            
            // Debug: Check each field
            error_log("Creating member with - Full Name: '$fullName', Email: '$email', Phone: '$phone', Start: '$startDate', End: '$endDate'");
            error_log("Field emptiness checks - fullName empty: " . (empty($fullName) ? 'true' : 'false') . 
                     ", email empty: " . (empty($email) ? 'true' : 'false') . 
                     ", membershipType empty: " . (empty($membershipType) ? 'true' : 'false') . 
                     ", startDate empty: " . (empty($startDate) ? 'true' : 'false') . 
                     ", endDate empty: " . (empty($endDate) ? 'true' : 'false'));
            
            // Create member record using addMember method
            $result = $memberModel->addMember(
                $user_id, 
                null, // username (not needed for existing user)
                null, // password (not needed for existing user)
                $fullName, // full_name
                $email, // email
                $phone, // phone
                $address, // address
                $membershipType, // membership_type
                $startDate, // start_date
                $endDate, // end_date
                $status // status
            );
            
            error_log("Add member result: " . print_r($result, true));
            
            if ($result['success']) {
                // Try to get the newly created member record
                $user = $memberModel->getMemberByUserId($user_id);
                if (!$user) {
                    error_log("Failed to retrieve newly created member for user_id: $user_id");
                    die('Member profile creation failed. Please contact support.');
                }
            } else {
                error_log("Failed to create member record for user_id: $user_id - " . $result['message']);
                die('Failed to create member profile: ' . $result['message']);
            }
        } else {
            error_log("User data not found for user_id: $user_id");
            die('User account not found. Please log in again.');
        }
    }
} catch (Exception $e) {
    error_log('Profile page error: ' . $e->getMessage());
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

// Get member bookings
$bookings = $bookingController->getMemberBookings($_SESSION['user_id']);
$upcomingBookings = array_filter($bookings, function($booking) {
    $bookingDateTime = $booking['booking_date'] . ' ' . $booking['booking_time'];
    $now = date('Y-m-d H:i:s');
    return ($booking['status'] === 'pending' || $booking['status'] === 'confirmed') && $bookingDateTime >= $now;
});

// Handle booking success message
if (isset($_GET['booking_success']) && $_GET['booking_success'] == 1 && !$updateResult) {
    $updateResult = [
        'type' => 'success',
        'msg' => 'Booking created successfully! Awaiting trainer confirmation.'
    ];
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['cancel_booking'])) {
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

                    <!-- My Bookings -->
                    <div class="profile-section">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h3><i class='bx bx-calendar-check'></i> My Bookings</h3>
                            <a href="coaches.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                <i class='bx bx-plus'></i>
                                Book Trainer
                            </a>
                        </div>
                        <?php if (empty($upcomingBookings)): ?>
                            <div style="text-align: center; padding: 2rem; color: #999;">
                                <i class='bx bx-calendar-x' style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                                <p>No upcoming bookings</p>
                                <p style="font-size: 0.9rem; margin-top: 0.5rem;">Book a session with one of our trainers to get started!</p>
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
                                                    <?= htmlspecialchars($booking['trainer_name']) ?>
                                                </h4>
                                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                                    <?= htmlspecialchars($booking['specialty'] ?? 'Fitness Training') ?>
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
                                        <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                            <?php
                                            $bookingDateTime = $bookingDate->format('F j, Y') . ' at ' . $bookingTime->format('g:i A');
                                            ?>
                                            <form id="cancel-booking-form-<?= $booking['booking_id'] ?>" method="POST" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                <input type="hidden" name="cancel_booking" value="1">
                                                <button type="button" onclick="showCancelBookingModal(<?= $booking['booking_id'] ?>, '<?= htmlspecialchars($booking['trainer_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($bookingDateTime, ENT_QUOTES) ?>')" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                                                    <i class='bx bx-x'></i> Cancel Booking
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

    <!-- Confirmation Modal Container (will be populated dynamically) -->
    <div id="cancel-booking-modal-container"></div>

    <script>
    // Function to show cancellation confirmation modal
    function showCancelBookingModal(bookingId, trainerName, bookingDateTime) {
        const modalId = 'cancel-booking-modal-' + bookingId;
        
        // Check if modal already exists and remove it
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }

        // Create modal HTML
        const modalHTML = `
            <div id="${modalId}" class="confirm-modal-overlay" role="dialog" aria-modal="true" style="display: flex;">
                <div class="confirm-modal-container">
                    <div class="confirm-modal">
                        <div class="confirm-modal-header">
                            <div class="confirm-modal-icon-wrapper">
                                <i class='bx bx-error-circle'></i>
                            </div>
                            <h3 class="confirm-modal-title">Cancel Booking</h3>
                            <button class="confirm-modal-close-btn" onclick="closeConfirmModal('${modalId}')" aria-label="Close modal">
                                <i class='bx bx-x'></i>
                            </button>
                        </div>
                        <div class="confirm-modal-body">
                            <p class="confirm-modal-message">
                                Are you sure you want to cancel your session with <strong>${trainerName}</strong> on <strong>${bookingDateTime}</strong>?
                                <br><br>
                                <span style="color: #dc3545; font-size: 0.9em;">This action cannot be undone.</span>
                            </p>
                        </div>
                        <div class="confirm-modal-footer">
                            <button class="confirm-modal-btn confirm-modal-btn-cancel" onclick="closeConfirmModal('${modalId}')">
                                <i class='bx bx-x'></i>
                                Keep Booking
                            </button>
                            <button class="confirm-modal-btn confirm-modal-btn-danger" onclick="confirmCancelBooking(${bookingId})">
                                <i class='bx bx-check'></i>
                                Cancel Booking
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to container
        const container = document.getElementById('cancel-booking-modal-container');
        container.innerHTML = modalHTML;

        // Add click handler for overlay
        const modal = document.getElementById(modalId);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeConfirmModal(modalId);
            }
        });

        // Add escape key handler
        const escapeHandler = function(e) {
            if (e.key === 'Escape') {
                closeConfirmModal(modalId);
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);
    }

    // Function to confirm cancellation and submit form
    function confirmCancelBooking(bookingId) {
        const formId = 'cancel-booking-form-' + bookingId;
        const form = document.getElementById(formId);
        if (form) {
            form.submit();
        }
    }

    // Function to close confirmation modal
    function closeConfirmModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    }
    </script>

    <!-- Include Confirmation Modal Styles (if not already included) -->
    <?php if (!isset($confirmModalStylesIncluded)): ?>
        <style>
        .confirm-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 20px;
            box-sizing: border-box;
        }

        .confirm-modal-container {
            animation: slideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 450px;
            width: 100%;
        }

        .confirm-modal {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.25),
                0 8px 30px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            overflow: hidden;
            border: 1px solid rgba(220, 53, 69, 0.2);
            position: relative;
        }

        .confirm-modal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #dc3545 0%, #f87171 100%);
        }

        .confirm-modal-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 24px 28px 20px;
            border-bottom: 1px solid rgba(220, 53, 69, 0.15);
            position: relative;
        }

        .confirm-modal-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #dc3545 0%, #f87171 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .confirm-modal-title {
            margin: 0;
            flex: 1;
            font-size: 20px;
            font-weight: 700;
            color: #1a202c;
            letter-spacing: -0.5px;
        }

        .confirm-modal-close-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }

        .confirm-modal-close-btn:hover {
            background: rgba(220, 53, 69, 0.15);
            transform: rotate(90deg) scale(1.1);
            color: #dc3545;
        }

        .confirm-modal-body {
            padding: 24px 28px;
        }

        .confirm-modal-message {
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
            color: #4a5568;
            font-weight: 500;
            text-align: center;
        }

        .confirm-modal-footer {
            padding: 16px 28px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            border-top: 1px solid rgba(220, 53, 69, 0.15);
        }

        .confirm-modal-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .confirm-modal-btn-cancel {
            background: #6c757d;
            color: white;
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
        }

        .confirm-modal-btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
            background: #5a6268;
        }

        .confirm-modal-btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #f87171 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .confirm-modal-btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        .confirm-modal-btn:active {
            transform: translateY(0);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @media (max-width: 640px) {
            .confirm-modal-container {
                max-width: 100%;
            }
            
            .confirm-modal-header {
                padding: 20px 20px 16px;
            }
            
            .confirm-modal-body {
                padding: 20px;
            }
            
            .confirm-modal-footer {
                padding: 16px 20px 20px;
                flex-direction: column-reverse;
            }
            
            .confirm-modal-btn {
                width: 100%;
                justify-content: center;
            }
            
            .confirm-modal-icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 24px;
            }
        }
        </style>
        <?php $confirmModalStylesIncluded = true; ?>
    <?php endif; ?>
</body>
</html>
