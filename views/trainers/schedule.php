<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . '/../../controllers/TrainerController.php';
$controller = new TrainerController();

// Handle approve/decline actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = (int)($_POST['booking_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if ($bookingId && in_array($status, ['confirmed','declined'])) {
        $res = $controller->updateBookingStatus($_SESSION['user_id'], $bookingId, $status);
        if ($res['success']) {
            $_SESSION['success'] = $res['message'] ?? 'Booking updated';
        } else {
            $_SESSION['error'] = $res['message'] ?? 'Failed to update booking';
        }
        header('Location: schedule.php');
        exit;
    }
}

$pending = $controller->getPendingBookings($_SESSION['user_id']);
$upcoming = $controller->getUpcomingBookings($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'Trainer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .actions form { display:inline-block; margin-right:6px; }
        .badge { padding:2px 8px; border-radius:12px; font-size:12px; }
        .badge-pending { background:#fff3cd; color:#856404; }
        .badge-confirmed { background:#d4edda; color:#155724; }
    </style>
</head>
<body>
    <?php include '../utilities/alert.php'; ?>
    <?php include '../components/dynamic_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>My Schedule</h1>
                <p>Manage your booking requests and upcoming sessions, <?php echo htmlspecialchars($username); ?>.</p>
            </div>

            <div class="dashboard-section" style="margin-bottom: 2rem;">
                    <div class="section-header">
                        <h2>Pending Requests</h2>
                    </div>
                    <div class="card" style="padding: 1.5rem;">
                        <table class="users-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Date</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Time</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Member</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Contact</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Status</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pending)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding: 3rem 1rem; color: var(--text-muted); font-size: 1rem;">
                                            <i class='bx bx-time-five' style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                                            No pending requests.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($pending as $b): ?>
                                        <tr style="border-bottom: 1px solid var(--border-light); transition: var(--tran-02);">
                                            <td style="padding: 1rem; font-weight: 500;"><?php echo htmlspecialchars($b['booking_date']); ?></td>
                                            <td style="padding: 1rem;"><?php echo htmlspecialchars(substr($b['booking_time'],0,5)); ?></td>
                                            <td style="padding: 1rem; font-weight: 500;"><?php echo htmlspecialchars($b['member_name']); ?></td>
                                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;"><?php echo htmlspecialchars($b['member_email']); ?></td>
                                            <td style="padding: 1rem;">
                                                <span class="badge badge-pending" style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; background: var(--warning-color); color: white;">Pending</span>
                                            </td>
                                            <td style="padding: 1rem;" class="actions">
                                                <form method="POST" action="schedule.php" style="display:inline-block; margin-right: 0.5rem;">
                                                    <input type="hidden" name="booking_id" value="<?php echo (int)$b['booking_id']; ?>">
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" class="action-btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;"><i class='bx bx-check'></i> Approve</button>
                                                </form>
                                                <form method="POST" action="schedule.php" style="display:inline-block;">
                                                    <input type="hidden" name="booking_id" value="<?php echo (int)$b['booking_id']; ?>">
                                                    <input type="hidden" name="status" value="declined">
                                                    <button type="submit" class="action-btn btn-delete" style="padding: 0.5rem 1rem; font-size: 0.85rem; background: var(--accent-color);"><i class='bx bx-x'></i> Decline</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="dashboard-section">
                    <div class="section-header">
                        <h2>Upcoming Sessions</h2>
                    </div>
                    <div class="card" style="padding: 1.5rem;">
                        <table class="users-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Date</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Time</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Member</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Contact</th>
                                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border-color);">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($upcoming)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center; padding: 3rem 1rem; color: var(--text-muted); font-size: 1rem;">
                                            <i class='bx bx-calendar' style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                                            No upcoming sessions.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($upcoming as $b): ?>
                                        <tr style="border-bottom: 1px solid var(--border-light); transition: var(--tran-02);">
                                            <td style="padding: 1rem; font-weight: 500;"><?php echo htmlspecialchars($b['booking_date']); ?></td>
                                            <td style="padding: 1rem;"><?php echo htmlspecialchars(substr($b['booking_time'],0,5)); ?></td>
                                            <td style="padding: 1rem; font-weight: 500;"><?php echo htmlspecialchars($b['member_name']); ?></td>
                                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;"><?php echo htmlspecialchars($b['member_email']); ?></td>
                                            <td style="padding: 1rem;">
                                                <span class="badge badge-confirmed" style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; background: var(--success-color); color: white;">Confirmed</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>
</body>
</html>
