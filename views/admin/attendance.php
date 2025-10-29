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
        case 'check_in':
            $result = $adminController->checkInMember(
                $_POST['member_id'] ?? 0
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: attendance.php");
            exit;

        case 'check_out':
            $result = $adminController->checkOutMember(
                $_POST['attendance_id'] ?? 0
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: attendance.php");
            exit;

        case 'delete_attendance':
            $result = $adminController->deleteAttendance($_POST['attendance_id'] ?? 0);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: attendance.php");
            exit;

        case 'check_in_guest':
            $result = $adminController->checkInGuest(
                trim($_POST['guest_name'] ?? ''),
                trim($_POST['guest_contact'] ?? '')
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: attendance.php");
            exit;
    }
}

// Handle filter and sort parameters
$filter_date = $_GET['filter_date'] ?? date('Y-m-d');
$filter_member = $_GET['filter_member'] ?? null;
$filter_status = $_GET['filter_status'] ?? null;
$sort_by = $_GET['sort_by'] ?? 'check_in';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// Get attendance records with filters and sorting
$attendance = $adminController->getAttendanceRecords($filter_date, $filter_member, $filter_status, $sort_by, $sort_order);
$members = $adminController->getAllMembers();
$todayStats = $adminController->getTodayAttendanceStats();
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - FitNexus</title>
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
                <h1>Attendance Management</h1>
                <p>Track member check-ins and gym usage patterns.</p>
            </div>

            <!-- Today's Stats -->
            <div class="quick-stats" style="margin-bottom: 30px;">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-user-check'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $todayStats['total_check_ins']; ?></h3>
                        <p>Check-ins Today</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-time'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $todayStats['currently_in_gym']; ?></h3>
                        <p>Currently in Gym</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-trending-up'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $todayStats['peak_hour']; ?></h3>
                        <p>Peak Hour</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class='bx bx-calendar'></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $todayStats['avg_duration']; ?> min</h3>
                        <p>Avg Duration</p>
                    </div>
                </div>
            </div>

            <!-- Header Actions: Check-in Button and Filters -->
            <div class="header-actions" style="display: flex; flex-direction: row; align-items: center; margin-bottom: 20px; gap: 20px; justify-content: space-around;">
                <!-- Check-in Button -->
                <button class="add-user-btn" onclick="openCheckInModal()">
                    <i class='bx bx-plus'></i> Check-in Member
                </button>

                <!-- Add button for guest check-in next to member check-in -->
                <button class="add-user-btn" onclick="openGuestCheckInModal()">
                    <i class='bx bx-user'></i> Check-in Guest
                </button>

                <!-- Filters and Sorting -->
                <div class="filters-section" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                    <div class="filters-row" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <input type="date" id="filter_date" onchange="applyFilters()" value="<?php echo $filter_date; ?>" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff;">

                        <select id="filter_member" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_member)) ? 'selected' : ''; ?>>Filter by</option>
                            <option value="">All (Members and Guests)</option>
                            <option value="member" <?php echo ($filter_member === 'member') ? 'selected' : ''; ?>>Members Only</option>
                            <option value="guest" <?php echo ($filter_member === 'guest') ? 'selected' : ''; ?>>Guests Only</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['member_id']; ?>" <?php echo ($filter_member == $member['member_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select id="filter_status" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_status)) ? 'selected' : ''; ?>>Filter by Status</option>
                            <option value="">All Statuses</option>
                            <option value="checked_in" <?php echo ($filter_status === 'checked_in') ? 'selected' : ''; ?>>Checked In</option>
                            <option value="checked_out" <?php echo ($filter_status === 'checked_out') ? 'selected' : ''; ?>>Checked Out</option>
                        </select>

                        <select id="sort_by" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($sort_by) || $sort_by === 'check_in') ? '' : 'selected'; ?>>Sort by</option>
                            <option value="check_in" <?php echo ($sort_by === 'check_in') ? 'selected' : ''; ?>>Check-in Time</option>
                            <option value="check_out" <?php echo ($sort_by === 'check_out') ? 'selected' : ''; ?>>Check-out Time</option>
                            <option value="full_name" <?php echo ($sort_by === 'full_name') ? 'selected' : ''; ?>>Member Name</option>
                            <option value="duration" <?php echo ($sort_by === 'duration') ? 'selected' : ''; ?>>Duration</option>
                        </select>

                        <select id="sort_order" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled>Order</option>
                            <option value="DESC" <?php echo ($sort_order === 'DESC') ? 'selected' : ''; ?>>Descending</option>
                            <option value="ASC" <?php echo ($sort_order === 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                        </select>

                        <button onclick="clearFilters()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer;">Clear Filters</button>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="card">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['attendance_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                            <td><?php echo htmlspecialchars(date('H:i', strtotime($record['check_in']))); ?></td>
                            <td><?php echo $record['check_out'] ? htmlspecialchars(date('H:i', strtotime($record['check_out']))) : 'N/A'; ?></td>
                            <td>
                                <?php 
                                if ($record['check_out']) {
                                    $duration = strtotime($record['check_out']) - strtotime($record['check_in']);
                                    echo round($duration / 60) . ' min';
                                } else {
                                    echo 'In Progress';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $record['check_out'] ? 'checked_out' : 'checked_in'; ?>">
                                    <?php echo $record['check_out'] ? 'Checked Out' : 'Checked In'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if (!$record['check_out']): ?>
                                        <button class="action-btn btn-edit" onclick="openCheckOutModal(<?php echo $record['attendance_id']; ?>, '<?php echo htmlspecialchars($record['full_name']); ?>')" title="Check Out">
                                            <i class='bx bx-log-out'></i> Check Out
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Check-in Modal -->
    <div id="checkInModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-log-in'></i>
                <h3>Check-in Member</h3>
            </div>
            <form method="POST" action="attendance.php">
                <input type="hidden" name="action" value="check_in">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="check_in_member_id">Select Member:</label>
                        <select id="check_in_member_id" name="member_id" required>
                            <option value="">Choose a member...</option>
                            <?php foreach ($members as $member): ?>
                                <option value="<?php echo $member['member_id']; ?>">
                                    <?php echo htmlspecialchars($member['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('checkInModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Check In</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Check-out Modal -->
    <div id="checkOutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-log-out'></i>
                <h3>Check-out Member</h3>
            </div>
            <form method="POST" action="attendance.php">
                <input type="hidden" name="action" value="check_out">
                <input type="hidden" id="check_out_attendance_id" name="attendance_id">
                <div class="modal-body">
                    <p>Are you sure you want to check out "<span id="check_out_member_name"></span>"?</p>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('checkOutModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Check Out</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Attendance Modal -->
    <div id="deleteAttendanceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-trash'></i>
                <h3>Delete Attendance Record</h3>
            </div>
            <form method="POST" action="attendance.php">
                <input type="hidden" name="action" value="delete_attendance">
                <input type="hidden" id="delete_attendance_id" name="attendance_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete attendance record for "<span id="delete_attendance_member"></span>"?</p>
                    <p class="modal-subtext">This action cannot be undone.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('deleteAttendanceModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Delete Record</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Guest Check-in Modal -->
    <div id="guestCheckInModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <i class='bx bx-user'></i>
          <h3>Check-in Guest</h3>
        </div>
        <form method="POST" action="attendance.php">
          <input type="hidden" name="action" value="check_in_guest">
          <div class="modal-body">
            <div class="form-group">
              <label for="guest_name">Name (required):</label>
              <input type="text" id="guest_name" name="guest_name" required>
            </div>
            <div class="form-group">
              <label for="guest_contact">Contact (optional):</label>
              <input type="text" id="guest_contact" name="guest_contact">
            </div>
          </div>
          <div class="modal-actions">
            <button type="button" onclick="closeModal('guestCheckInModal')" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Check In</button>
          </div>
        </form>
      </div>
    </div>

    <script>
        function applyFilters() {
            const filterDate = document.getElementById('filter_date').value;
            const filterMember = document.getElementById('filter_member').value;
            const filterStatus = document.getElementById('filter_status').value;
            const sortBy = document.getElementById('sort_by').value;
            const sortOrder = document.getElementById('sort_order').value;

            const params = new URLSearchParams(window.location.search);
            params.set('filter_date', filterDate);
            params.set('filter_member', filterMember);
            params.set('filter_status', filterStatus);
            params.set('sort_by', sortBy);
            params.set('sort_order', sortOrder);

            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function clearFilters() {
            window.location.href = window.location.pathname;
        }

        function openCheckInModal() {
            document.getElementById('checkInModal').style.display = 'block';
        }

        function openCheckOutModal(attendanceId, memberName) {
            document.getElementById('check_out_attendance_id').value = attendanceId;
            document.getElementById('check_out_member_name').textContent = memberName;
            document.getElementById('checkOutModal').style.display = 'block';
        }

        function openDeleteAttendanceModal(attendanceId, memberName) {
            document.getElementById('delete_attendance_id').value = attendanceId;
            document.getElementById('delete_attendance_member').textContent = memberName;
            document.getElementById('deleteAttendanceModal').style.display = 'block';
        }

        function openGuestCheckInModal(){
          document.getElementById('guestCheckInModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['checkInModal', 'checkOutModal', 'deleteAttendanceModal', 'guestCheckInModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    closeModal(modalId);
                }
            });
        }
    </script>
</body>
</html>
