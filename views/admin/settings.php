<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {

    header("Location: ../../index.php");

    exit;

}

require_once '../../controllers/AdminController.php';

$adminController = new AdminController();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_user_settings':
            $user_id = $_SESSION['user_id'] ?? 0;
            $username = trim($_POST['username'] ?? '');
            $new_password = trim($_POST['new_password'] ?? '');
            $result = $adminController->updateCurrentAdmin($user_id, $username, $new_password);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: settings.php');
            exit;

        case 'update_gym_settings':
            $gym_name = trim($_POST['gym_name'] ?? '');
            $gym_address = trim($_POST['gym_address'] ?? '');
            $gym_phone = trim($_POST['gym_phone'] ?? '');
            $gym_email = trim($_POST['gym_email'] ?? '');
            $gym_website = trim($_POST['gym_website'] ?? '');
            $monthly_fee = floatval($_POST['monthly_fee'] ?? 0);
            $annual_fee = floatval($_POST['annual_fee'] ?? 0);
            $operating_hours = trim($_POST['operating_hours'] ?? '');
            $max_capacity = intval($_POST['max_capacity'] ?? 0);
            $result = $adminController->updateGymSettings($gym_name, $gym_address, $gym_phone, $gym_email, $gym_website, $monthly_fee, $annual_fee, $operating_hours, $max_capacity);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: settings.php');
            exit;

        case 'update_system_settings':
            $maintenance_mode = $_POST['maintenance_mode'] ?? 'off';
            $registration_enabled = $_POST['registration_enabled'] ?? 'on';
            $email_notifications = $_POST['email_notifications'] ?? 'on';
            $backup_frequency = $_POST['backup_frequency'] ?? 'daily';
            $session_timeout = intval($_POST['session_timeout'] ?? 30);
            $result = $adminController->updateSystemSettings($maintenance_mode, $registration_enabled, $email_notifications, $backup_frequency, $session_timeout);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: settings.php');
            exit;

        case 'backup_database':
            $result = $adminController->backupDatabase();
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header('Location: settings.php');
            exit;
    }
}

// Get current settings

$gymSettings = $adminController->getGymSettings();

$systemSettings = $adminController->getSystemSettings();

$username = $_SESSION['username'] ?? 'Admin';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Settings - FitNexus</title>

    <link rel="stylesheet" href="../../assets/css/member_styles.css">

    <link rel="stylesheet" href="../../assets/css/modal_styles.css">

    <link rel="stylesheet" href="../../assets/css/admin_users_styles.css">

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

            <!-- Settings Tabs -->
            <div class="settings-tabs">
                <button class="tab-button active" onclick="showTab('user-settings')">
                    <i class='bx bx-user'></i>
                    User Settings
                </button>
                <button class="tab-button" onclick="showTab('gym-settings')">
                    <i class='bx bx-building'></i>
                    Gym Settings
                </button>
                <button class="tab-button" onclick="showTab('system-settings')">
                    <i class='bx bx-cog'></i>
                    System Settings
                </button>
                <button class="tab-button" onclick="showTab('maintenance')">
                    <i class='bx bx-wrench'></i>
                    Maintenance
                </button>
            </div>

            <!-- User Settings Tab -->
            <div id="user-settings" class="tab-content active">
                <div class="card" style="max-width: 440px; margin: 0 auto;">
                    <form method="POST" action="settings.php" autocomplete="off">
                        <input type="hidden" name="action" value="update_user_settings">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" autocomplete="new-password" placeholder="Leave blank to keep current password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" placeholder="Retype password">
                        </div>
                        <div class="form-actions" style="margin-top: 18px;">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Account</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Gym Settings Tab -->
            <div id="gym-settings" class="tab-content">
                <div class="card">
                    <form method="POST" action="settings.php">
                        <input type="hidden" name="action" value="update_gym_settings">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gym_name">Gym Name:</label>
                                <input type="text" id="gym_name" name="gym_name" value="<?php echo htmlspecialchars($gymSettings['gym_name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="gym_address">Address:</label>
                                <input type="text" id="gym_address" name="gym_address" value="<?php echo htmlspecialchars($gymSettings['gym_address'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gym_phone">Phone:</label>
                                <input type="tel" id="gym_phone" name="gym_phone" value="<?php echo htmlspecialchars($gymSettings['gym_phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gym_email">Email:</label>
                                <input type="email" id="gym_email" name="gym_email" value="<?php echo htmlspecialchars($gymSettings['gym_email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gym_website">Website:</label>
                                <input type="url" id="gym_website" name="gym_website" value="<?php echo htmlspecialchars($gymSettings['gym_website'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="operating_hours">Operating Hours:</label>
                                <input type="text" id="operating_hours" name="operating_hours" value="<?php echo htmlspecialchars($gymSettings['operating_hours'] ?? ''); ?>" placeholder="e.g., 6:00 AM - 10:00 PM">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="monthly_fee">Monthly Fee (₱):</label>
                                <input type="number" id="monthly_fee" name="monthly_fee" step="0.01" value="<?php echo htmlspecialchars($gymSettings['monthly_fee'] ?? 0); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="annual_fee">Annual Fee (₱):</label>
                                <input type="number" id="annual_fee" name="annual_fee" step="0.01" value="<?php echo htmlspecialchars($gymSettings['annual_fee'] ?? 0); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="max_capacity">Max Capacity:</label>
                                <input type="number" id="max_capacity" name="max_capacity" value="<?php echo htmlspecialchars($gymSettings['max_capacity'] ?? 0); ?>" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Gym Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Settings Tab -->
            <div id="system-settings" class="tab-content">
                <div class="card">
                    <form method="POST" action="settings.php">
                        <input type="hidden" name="action" value="update_system_settings">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="maintenance_mode">Maintenance Mode:</label>
                                <select id="maintenance_mode" name="maintenance_mode">
                                    <option value="off" <?php echo ($systemSettings['maintenance_mode'] ?? 'off') === 'off' ? 'selected' : ''; ?>>Off</option>
                                    <option value="on" <?php echo ($systemSettings['maintenance_mode'] ?? 'off') === 'on' ? 'selected' : ''; ?>>On</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="registration_enabled">Registration Enabled:</label>
                                <select id="registration_enabled" name="registration_enabled">
                                    <option value="on" <?php echo ($systemSettings['registration_enabled'] ?? 'on') === 'on' ? 'selected' : ''; ?>>On</option>
                                    <option value="off" <?php echo ($systemSettings['registration_enabled'] ?? 'on') === 'off' ? 'selected' : ''; ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email_notifications">Email Notifications:</label>
                                <select id="email_notifications" name="email_notifications">
                                    <option value="on" <?php echo ($systemSettings['email_notifications'] ?? 'on') === 'on' ? 'selected' : ''; ?>>On</option>
                                    <option value="off" <?php echo ($systemSettings['email_notifications'] ?? 'on') === 'off' ? 'selected' : ''; ?>>Off</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="backup_frequency">Backup Frequency:</label>
                                <select id="backup_frequency" name="backup_frequency">
                                    <option value="daily" <?php echo ($systemSettings['backup_frequency'] ?? 'daily') === 'daily' ? 'selected' : ''; ?>>Daily</option>
                                    <option value="weekly" <?php echo ($systemSettings['backup_frequency'] ?? 'daily') === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                    <option value="monthly" <?php echo ($systemSettings['backup_frequency'] ?? 'daily') === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="session_timeout">Session Timeout (minutes):</label>
                                <input type="number" id="session_timeout" name="session_timeout" min="5" max="480" value="<?php echo htmlspecialchars($systemSettings['session_timeout'] ?? 30); ?>" required>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save System Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Maintenance Tab -->
            <div id="maintenance" class="tab-content">
                <div class="maintenance-actions">
                    <div class="maintenance-item">
                        <div class="maintenance-info">
                            <h3>Database Backup</h3>
                            <p>Create a backup of the entire database for safety.</p>
                        </div>
                        <form method="POST" action="settings.php" style="display: inline;">
                            <input type="hidden" name="action" value="backup_database">
                            <button type="submit" class="btn btn-warning">Backup Now</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>

        function showTab(tabId) {

            // Hide all tab contents

            const tabContents = document.querySelectorAll('.tab-content');

            tabContents.forEach(content => content.classList.remove('active'));



            // Remove active class from all tab buttons

            const tabButtons = document.querySelectorAll('.tab-button');

            tabButtons.forEach(button => button.classList.remove('active'));



            // Show the selected tab content

            document.getElementById(tabId).classList.add('active');



            // Add active class to the clicked button

            event.target.classList.add('active');

        }



        document.querySelector('form').onsubmit = function(e) {

            const pw = document.getElementById('new_password').value;

            const conf = document.getElementById('confirm_password').value;

            if (pw && pw !== conf) {

                alert('Passwords do not match.');

                e.preventDefault();

                return false;

            }

        };

    </script>

    <style>

        .settings-tabs {

            display: flex;

            margin-bottom: 20px;

            border-bottom: 2px solid #e9ecef;

        }

        .tab-button {

            padding: 12px 24px;

            border: none;

            background: none;

            cursor: pointer;

            border-bottom: 3px solid transparent;

            transition: all 0.3s ease;

            display: flex;

            align-items: center;

            gap: 8px;

        }

        .tab-button:hover {

            background: #f8f9fa;

        }

        .tab-button.active {

            border-bottom-color: #007bff;

            background: #f8f9fa;

        }

        .tab-content {

            display: none;

        }

        .tab-content.active {

            display: block;

        }

        .form-row {

            display: flex;

            gap: 20px;

            margin-bottom: 15px;

        }

        .form-row .form-group {

            flex: 1;

        }

        .form-actions {

            margin-top: 20px;

            padding-top: 20px;

            border-top: 1px solid #e9ecef;

        }

        .maintenance-actions {

            display: flex;

            flex-direction: column;

            gap: 20px;

        }

        .maintenance-item {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 20px;

            border: 1px solid #e9ecef;

            border-radius: 8px;

            background: #f8f9fa;

        }

        .maintenance-info h3 {

            margin: 0 0 5px 0;

            color: #2c3e50;

        }

        .maintenance-info p {

            margin: 0;

            color: #6c757d;

            font-size: 14px;

        }

        .btn-warning {

            background: #ffc107;

            color: #212529;

            border: none;

            padding: 8px 16px;

            border-radius: 4px;

            cursor: pointer;

        }

        .btn-warning:hover {

            background: #e0a800;

        }

    </style>

</body>

</html>
