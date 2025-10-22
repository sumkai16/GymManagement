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
        case 'add_user':
            $result = $adminController->addUser(
                $_POST['username'] ?? '',
                $_POST['password'] ?? '',
                $_POST['role'] ?? '',
                $_POST['status'] ?? 'inactive'
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: users.php");
            exit;

        case 'update_user':
            $result = $adminController->updateUser(
                $_POST['user_id'] ?? 0,
                $_POST['username'] ?? '',
                $_POST['role'] ?? '',
                $_POST['status'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: users.php");
            exit;

        case 'delete_user':
            $result = $adminController->deleteUser($_POST['user_id'] ?? 0);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: users.php");
            exit;
    }
}

// Handle filter and sort parameters
$filter_role = $_GET['filter_role'] ?? null;
$filter_status = $_GET['filter_status'] ?? null;
$sort_by = $_GET['sort_by'] ?? 'created_at';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// Get all users with filters and sorting
$users = $adminController->getAllUsers($filter_role, $filter_status, $sort_by, $sort_order);
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - FitNexus</title>
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
                <h1>User Management</h1>
                <p>Manage user accounts and permissions.</p>
            </div>

            <!-- Header Actions: Add User Button and Filters -->
            <div class="header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 20px;">
                <!-- Add User Button -->
                <button class="add-user-btn" onclick="openAddUserModal()">
                    <i class='bx bx-plus'></i> Add New User
                </button>

                <!-- Filters and Sorting -->
                <div class="filters-section" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                    <div class="filters-row" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <select id="filter_role" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_role)) ? 'selected' : ''; ?>>Filter by Role</option>
                            <option value="">All Roles</option>
                            <option value="admin" <?php echo ($filter_role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="trainer" <?php echo ($filter_role === 'trainer') ? 'selected' : ''; ?>>Trainer</option>
                            <option value="member" <?php echo ($filter_role === 'member') ? 'selected' : ''; ?>>Member</option>
                            <option value="guest" <?php echo ($filter_role === 'guest') ? 'selected' : ''; ?>>Guest</option>
                        </select>

                        <select id="filter_status" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_status)) ? 'selected' : ''; ?>>Filter by Status</option>
                            <option value="">All Statuses</option>
                            <option value="active" <?php echo ($filter_status === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($filter_status === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>

                        <select id="sort_by" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($sort_by) || $sort_by === 'created_at') ? '' : 'selected'; ?>>Sort by</option>
                            <option value="created_at" <?php echo ($sort_by === 'created_at') ? 'selected' : ''; ?>>Created Date</option>
                            <option value="username" <?php echo ($sort_by === 'username') ? 'selected' : ''; ?>>Username</option>
                            <option value="role" <?php echo ($sort_by === 'role') ? 'selected' : ''; ?>>Role</option>
                            <option value="status" <?php echo ($sort_by === 'status') ? 'selected' : ''; ?>>Status</option>
                            <option value="user_id" <?php echo ($sort_by === 'user_id') ? 'selected' : ''; ?>>ID</option>
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

            <!-- Users Table -->
            <div class="card">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($user['status']); ?>">
                                    <?php echo htmlspecialchars($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('M d, Y H:i', strtotime($user['created_at']))); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="openEditUserModal(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['role']); ?>', '<?php echo htmlspecialchars($user['status']); ?>')" title="Edit User">
                                        <i class='bx bx-edit'></i> Edit
                                    </button>
                                    <button class="action-btn btn-delete" onclick="openDeleteUserModal(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" title="Delete User">
                                        <i class='bx bx-trash'></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-user-plus'></i>
                <h3>Add New User</h3>
            </div>
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="add_user">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_username">Username:</label>
                        <input type="text" id="add_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="add_password">Password:</label>
                        <input type="password" id="add_password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="add_role">Role:</label>
                        <select id="add_role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="trainer">Trainer</option>
                            <option value="member">Member</option>
                            <option value="guest">Guest</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_status">Status:</label>
                        <select id="add_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addUserModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-edit'></i>
                <h3>Edit User</h3>
            </div>
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_username">Username:</label>
                        <input type="text" id="edit_username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role:</label>
                        <select id="edit_role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="trainer">Trainer</option>
                            <option value="member">Member</option>
                            <option value="guest">Guest</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status:</label>
                        <select id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('editUserModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div id="deleteUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-trash'></i>
                <h3>Delete User</h3>
            </div>
            <form method="POST" action="users.php">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" id="delete_user_id" name="user_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete user "<span id="delete_username"></span>"?</p>
                    <p class="modal-subtext">This action cannot be undone.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('deleteUserModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Delete User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function applyFilters() {
            const filterRole = document.getElementById('filter_role').value;
            const filterStatus = document.getElementById('filter_status').value;
            const sortBy = document.getElementById('sort_by').value;
            const sortOrder = document.getElementById('sort_order').value;

            const params = new URLSearchParams(window.location.search);
            params.set('filter_role', filterRole);
            params.set('filter_status', filterStatus);
            params.set('sort_by', sortBy);
            params.set('sort_order', sortOrder);

            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function clearFilters() {
            window.location.href = window.location.pathname;
        }

        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'block';
        }

        function openEditUserModal(userId, username, role, status) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_status').value = status;
            document.getElementById('editUserModal').style.display = 'block';
        }

        function openDeleteUserModal(userId, username) {
            document.getElementById('delete_user_id').value = userId;
            document.getElementById('delete_username').textContent = username;
            document.getElementById('deleteUserModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['addUserModal', 'editUserModal', 'deleteUserModal'];
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
