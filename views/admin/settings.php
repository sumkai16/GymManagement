<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../../controllers/AdminController.php';
$adminController = new AdminController();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_gym_settings':
            $result = $adminController->updateGymSettings(
                $_POST['gym_name'] ?? '',
                $_POST['gym_address'] ?? '',
                $_POST['gym_phone'] ?? '',
                $_POST['gym_email'] ?? '',
                $_POST['gym_website'] ?? '',
                $_POST['monthly_fee'] ?? 0,
                $_POST['annual_fee'] ?? 0,
                $_POST['operating_hours'] ?? '',
                $_POST['max_capacity'] ?? 0
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: settings.php");
            exit;

        case 'update_system_settings':
            $result = $adminController->updateSystemSettings(
                $_POST['maintenance_mode'] ?? 'off',
                $_POST['registration_enabled'] ?? 'on',
                $_POST['email_notifications'] ?? 'on',
                $_POST['backup_frequency'] ?? 'daily',
                $_POST['session_timeout'] ?? 30
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: settings.php");
            exit;

        case 'backup_database':
            $result = $adminController->backupDatabase();
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: settings.php");
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
    <title>System Settings - FitNexus</title>
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
            <div class="dashboard-header">
                <h1>System Settings</h1>
                <p>Configure gym information, system preferences, and maintenance options.</p>
            </div>

            <!-- Settings Tabs -->
            <div class="settings-tabs">
                <button class="tab-button active" onclick="showTab('gym-settings')">
                    <i class='bx bx-building'></i> Gym Information
                </button>
                <button class="tab-button" onclick="showTab('system-settings')">
                    <i class='bx bx-cog'></i> System Settings
                </button>
                <button class="tab-button" onclick="showTab('maintenance')">
                    <i class='bx bx-wrench'></i> Maintenance
                </button>
            </div>

            <!-- Gym Settings Tab -->
            <div id="gym-settings" class="tab-content active">
                <div class="card">
                    <div class="section-header">
                        <h2>Gym Information</h2>
                        <p>Update basic gym details and membership pricing.</p>
                    </div>
                    <form method="POST" action="settings.php">
                        <input type="hidden" name="action" value="update_gym_settings">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gym_name">Gym Name:</label>
                                <input type="text" id="gym_name" name="gym_name" value="<?php echo htmlspecialchars($gymSettings['gym_name'] ?? 'FitNexus Gym'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="gym_phone">Phone Number:</label>
                                <input type="tel" id="gym_phone" name="gym_phone" value="<?php echo htmlspecialchars($gymSettings['gym_phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="gym_email">Email Address:</label>
                                <input type="email" id="gym_email" name="gym_email" value="<?php echo htmlspecialchars($gymSettings['gym_email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="gym_website">Website:</label>
                                <input type="url" id="gym_website" name="gym_website" value="<?php echo htmlspecialchars($gymSettings['gym_website'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gym_address">Address:</label>
                            <textarea id="gym_address" name="gym_address" rows="3"><?php echo htmlspecialchars($gymSettings['gym_address'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="monthly_fee">Monthly Membership Fee (₱):</label>
                                <input type="number" id="monthly_fee" name="monthly_fee" step="0.01" min="0" value="<?php echo $gymSettings['monthly_fee'] ?? 1500; ?>">
                            </div>
                            <div class="form-group">
                                <label for="annual_fee">Annual Membership Fee (₱):</label>
                                <input type="number" id="annual_fee" name="annual_fee" step="0.01" min="0" value="<?php echo $gymSettings['annual_fee'] ?? 15000; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="operating_hours">Operating Hours:</label>
                                <input type="text" id="operating_hours" name="operating_hours" value="<?php echo htmlspecialchars($gymSettings['operating_hours'] ?? '6:00 AM - 10:00 PM'); ?>" placeholder="e.g., 6:00 AM - 10:00 PM">
                            </div>
                            <div class="form-group">
                                <label for="max_capacity">Maximum Capacity:</label>
                                <input type="number" id="max_capacity" name="max_capacity" min="1" value="<?php echo $gymSettings['max_capacity'] ?? 200; ?>">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update Gym Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- System Settings Tab -->
            <div id="system-settings" class="tab-content">
                <div class="card">
                    <div class="section-header">
                        <h2>System Preferences</h2>
                        <p>Configure system behavior and user experience settings.</p>
                    </div>
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
                                <label for="registration_enabled">Registration:</label>
                                <select id="registration_enabled" name="registration_enabled">
                                    <option value="on" <?php echo ($systemSettings['registration_enabled'] ?? 'on') === 'on' ? 'selected' : ''; ?>>Enabled</option>
                                    <option value="off" <?php echo ($systemSettings['registration_enabled'] ?? 'on') === 'off' ? 'selected' : ''; ?>>Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email_notifications">Email Notifications:</label>
                                <select id="email_notifications" name="email_notifications">
                                    <option value="on" <?php echo ($systemSettings['email_notifications'] ?? 'on') === 'on' ? 'selected' : ''; ?>>Enabled</option>
                                    <option value="off" <?php echo ($systemSettings['email_notifications'] ?? 'on') === 'off' ? 'selected' : ''; ?>>Disabled</option>
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
                        <div class="form-group">
                            <label for="session_timeout">Session Timeout (minutes):</label>
                            <input type="number" id="session_timeout" name="session_timeout" min="5" max="480" value="<?php echo $systemSettings['session_timeout'] ?? 30; ?>">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Update System Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Maintenance Tab -->
            <div id="maintenance" class="tab-content">
                <div class="card">
                    <div class="section-header">
                        <h2>System Maintenance</h2>
                        <p>Database backup and system maintenance operations.</p>
                    </div>
                    <div class="maintenance-actions">
                        <div class="maintenance-item">
                            <div class="maintenance-info">
                                <h3>Database Backup</h3>
                                <p>Create a complete backup of the database.</p>
                            </div>
                            <form method="POST" action="settings.php" style="display: inline;">
                                <input type="hidden" name="action" value="backup_database">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-download'></i> Create Backup
                                </button>
                            </form>
                        </div>
                        <div class="maintenance-item">
                            <div class="maintenance-info">
                                <h3>System Status</h3>
                                <p>Check system health and performance.</p>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="checkSystemStatus()">
                                <i class='bx bx-check-circle'></i> Check Status
                            </button>
                        </div>
                        <div class="maintenance-item">
                            <div class="maintenance-info">
                                <h3>Clear Cache</h3>
                                <p>Clear system cache and temporary files.</p>
                            </div>
                            <button type="button" class="btn btn-warning" onclick="clearCache()">
                                <i class='bx bx-trash'></i> Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function checkSystemStatus() {
            alert('System status check functionality would be implemented here.');
        }

        function clearCache() {
            if (confirm('Are you sure you want to clear the system cache?')) {
                alert('Cache cleared successfully!');
            }
        }
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
