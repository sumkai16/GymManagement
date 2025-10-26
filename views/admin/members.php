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
        case 'add_member':
            $user_id = !empty($_POST['existing_user_id']) ? $_POST['existing_user_id'] : null;
            $result = $adminController->addMember(
                $user_id,
                $_POST['username'] ?? null,
                $_POST['password'] ?? null,
                $_POST['full_name'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['address'] ?? '',
                $_POST['membership_type'] ?? 'monthly',
                $_POST['start_date'] ?? '',
                $_POST['end_date'] ?? '',
                $_POST['status'] ?? 'active'
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: members.php");
            exit;

        case 'update_member':
            $result = $adminController->updateMember(
                $_POST['member_id'] ?? 0,
                $_POST['full_name'] ?? '',
                $_POST['email'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['address'] ?? '',
                $_POST['membership_type'] ?? '',
                $_POST['start_date'] ?? '',
                $_POST['end_date'] ?? '',
                $_POST['status'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: members.php");
            exit;

        case 'delete_member':
            $result = $adminController->deleteMember($_POST['member_id'] ?? 0);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: members.php");
            exit;
    }
}

// Handle filter and sort parameters
$filter_status = $_GET['filter_status'] ?? null;
$filter_membership = $_GET['filter_membership'] ?? null;
$sort_by = $_GET['sort_by'] ?? 'start_date';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// Get all members with filters and sorting
$members = $adminController->getAllMembers($filter_status, $filter_membership, $sort_by, $sort_order);
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/modal_styles.css">
    <link rel="stylesheet" href="../../assets/css/admin_users_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="../../assets/js/admin_members.js"></script>
    <script src="../../assets/js/admin_members_validation.js"></script>
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
                <h1>Member Management</h1>
                <p>Manage member profiles, memberships, and information.</p>
            </div>

            <!-- Header Actions: Add Member Button and Filters -->
            <div class="header-actions" style="display: flex; flex-direction: row; align-items: center; margin-bottom: 20px; gap: 20px; justify-content: space-around;">
                <!-- Add Member Button -->
                <button class="add-user-btn" onclick="openAddMemberModal()">
                    <i class='bx bx-plus'></i> Add New Member
                </button>

                <!-- Filters and Sorting -->
                <div class="filters-section" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                    <div class="filters-row" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <select id="filter_status" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_status)) ? 'selected' : ''; ?>>Filter by Status</option>
                            <option value="">All Statuses</option>
                            <option value="active" <?php echo ($filter_status === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($filter_status === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>

                        <select id="filter_membership" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_membership)) ? 'selected' : ''; ?>>Filter by Membership</option>
                            <option value="">All Types</option>
                            <option value="monthly" <?php echo ($filter_membership === 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                            <option value="annual" <?php echo ($filter_membership === 'annual') ? 'selected' : ''; ?>>Annual</option>
                        </select>

                        <select id="sort_by" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($sort_by) || $sort_by === 'start_date') ? '' : 'selected'; ?>>Sort by</option>
                            <option value="start_date" <?php echo ($sort_by === 'start_date') ? 'selected' : ''; ?>>Start Date</option>
                            <option value="end_date" <?php echo ($sort_by === 'end_date') ? 'selected' : ''; ?>>End Date</option>
                            <option value="full_name" <?php echo ($sort_by === 'full_name') ? 'selected' : ''; ?>>Name</option>
                            <option value="membership_type" <?php echo ($sort_by === 'membership_type') ? 'selected' : ''; ?>>Membership</option>
                            <option value="member_id" <?php echo ($sort_by === 'member_id') ? 'selected' : ''; ?>>ID</option>
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

            <!-- Members Table -->
            <div class="card">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Membership</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['member_id']); ?></td>
                            <td><?php echo htmlspecialchars($member['full_name']); ?></td>
                            <td>
                                <span class="role-badge role-member">
                                    <?php echo htmlspecialchars($member['membership_type']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($member['status']); ?>">
                                    <?php echo htmlspecialchars($member['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($member['start_date'] ? date('M d, Y', strtotime($member['start_date'])) : 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($member['end_date'] ? date('M d, Y', strtotime($member['end_date'])) : 'N/A'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="openEditMemberModal(<?php echo $member['member_id']; ?>, '<?php echo htmlspecialchars($member['full_name']); ?>', '<?php echo htmlspecialchars($member['email']); ?>', '<?php echo htmlspecialchars($member['phone'] ?? ''); ?>', '<?php echo htmlspecialchars($member['address'] ?? ''); ?>', '<?php echo htmlspecialchars($member['membership_type']); ?>', '<?php echo htmlspecialchars($member['start_date'] ?? ''); ?>', '<?php echo htmlspecialchars($member['end_date'] ?? ''); ?>', '<?php echo htmlspecialchars($member['status']); ?>')" title="Edit Member">
                                        <i class='bx bx-edit'></i> Edit
                                    </button>
                                    <button class="action-btn btn-delete" onclick="openDeleteMemberModal(<?php echo $member['member_id']; ?>, '<?php echo htmlspecialchars($member['full_name']); ?>')" title="Delete Member">
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

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-user-plus'></i>
                <h3>Add New Member</h3>
            </div>
            <form method="POST" action="members.php">
                <input type="hidden" name="action" value="add_member">
                <div class="modal-body">
                    <!-- User Creation Option -->
                    <div class="form-group form-row-full">
                        <label>User Account:</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="user_option" value="new" checked onchange="toggleUserFields()">
                                Create New User Account
                            </label>
                            <label>
                                <input type="radio" name="user_option" value="existing" onchange="toggleUserFields()">
                                Use Existing User Account
                            </label>
                        </div>
                    </div>

                    <!-- New User Fields -->
                    <div id="newUserFields">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="add_username">Username:</label>
                                <input type="text" id="add_username" name="username">
                            </div>
                            <div class="form-group">
                                <label for="add_password">Password:</label>
                                <input type="password" id="add_password" name="password">
                            </div>
                        </div>
                    </div>

                    <!-- Existing User Selection -->
                    <div id="existingUserFields" style="display: none;">
                        <div class="form-group form-row-full">
                            <label for="existing_user_id">Select Existing User:</label>
                            <select id="existing_user_id" name="existing_user_id">
                                <option value="">Choose a user...</option>
                                <?php
                                $availableUsers = $adminController->getUsersWithoutMembers();
                                foreach ($availableUsers as $user): ?>
                                    <option value="<?php echo $user['user_id']; ?>">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Member Details -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_full_name">Full Name:</label>
                            <input type="text" id="add_full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="add_email">Email:</label>
                            <input type="email" id="add_email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_phone">Phone:</label>
                            <input type="tel" id="add_phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="add_membership_type">Membership Type:</label>
                            <select id="add_membership_type" name="membership_type" required>
                                <option value="monthly">Monthly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_address">Address:</label>
                            <textarea id="add_address" name="address" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="add_status">Status:</label>
                            <select id="add_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_start_date">Start Date:</label>
                            <input type="date" id="add_start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="add_end_date">End Date:</label>
                            <input type="date" id="add_end_date" name="end_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addMemberModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="editMemberModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-edit'></i>
                <h3>Edit Member</h3>
            </div>
            <form method="POST" action="members.php">
                <input type="hidden" name="action" value="update_member">
                <input type="hidden" id="edit_member_id" name="member_id">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_full_name">Full Name:</label>
                            <input type="text" id="edit_full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">Email:</label>
                            <input type="email" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_phone">Phone:</label>
                            <input type="tel" id="edit_phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="edit_membership_type">Membership Type:</label>
                            <select id="edit_membership_type" name="membership_type" required>
                                <option value="monthly">Monthly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_address">Address:</label>
                            <textarea id="edit_address" name="address" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status:</label>
                            <select id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_start_date">Start Date:</label>
                            <input type="date" id="edit_start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_end_date">End Date:</label>
                            <input type="date" id="edit_end_date" name="end_date" required>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('editMemberModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Member Modal -->
    <div id="deleteMemberModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-trash'></i>
                <h3>Delete Member</h3>
            </div>
            <form method="POST" action="members.php">
                <input type="hidden" name="action" value="delete_member">
                <input type="hidden" id="delete_member_id" name="member_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete member "<span id="delete_member_name"></span>"?</p>
                    <p class="modal-subtext">This action cannot be undone and will also remove the associated user account.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('deleteMemberModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Delete Member</button>
                </div>
            </form>
        </div>
    </div>

   <script>
    window.onclick = function(event) {
            const modals = ['addMemberModal', 'editMemberModal', 'deleteMemberModal'];
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
