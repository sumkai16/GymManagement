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
        case 'add_trainer':
            $user_id = !empty($_POST['existing_user_id']) ? $_POST['existing_user_id'] : null;
            $result = $adminController->addTrainer(
                $user_id,
                $_POST['username'] ?? null,
                $_POST['password'] ?? null,
                $_POST['full_name'] ?? '',
                $_POST['specialty'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                $_FILES['image'] ?? null
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: trainers.php");
            exit;

        case 'update_trainer':
            $result = $adminController->updateTrainer(
                $_POST['trainer_id'] ?? 0,
                $_POST['full_name'] ?? '',
                $_POST['specialty'] ?? '',
                $_POST['phone'] ?? '',
                $_POST['email'] ?? '',
                $_FILES['image'] ?? null
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: trainers.php");
            exit;

        case 'delete_trainer':
            $result = $adminController->deleteTrainer($_POST['trainer_id'] ?? 0);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: trainers.php");
            exit;
    }
}

// Handle filter and sort parameters
$filter_specialty = $_GET['filter_specialty'] ?? null;
$sort_by = $_GET['sort_by'] ?? 'full_name';
$sort_order = $_GET['sort_order'] ?? 'ASC';

// Get all trainers with filters and sorting
$trainers = $adminController->getAllTrainers($filter_specialty, $sort_by, $sort_order);
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Management - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/modal_styles.css">
    <link rel="stylesheet" href="../../assets/css/admin_users_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="../../assets/js/admin_trainers.js"></script>
    <script src="../../assets/js/admin_trainers_validation.js"></script>
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
                <h1>Trainer Management</h1>
                <p>Manage trainer profiles, specializations, and information.</p>
            </div>

            <!-- Header Actions: Add Trainer Button and Filters -->
            <div class="header-actions" style="display: flex; flex-direction: row; align-items: center; margin-bottom: 20px; gap: 20px; justify-content: space-around;">
                <!-- Add Trainer Button -->
                <button class="add-user-btn" onclick="openAddTrainerModal()">
                    <i class='bx bx-plus'></i> Add New Trainer
                </button>

                <!-- Filters and Sorting -->
                <div class="filters-section" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                    <div class="filters-row" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <select id="filter_specialty" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_specialty)) ? 'selected' : ''; ?>>Filter by Specialty</option>
                            <option value="">All Specialties</option>
                            <option value="Strength Training" <?php echo ($filter_specialty === 'Strength Training') ? 'selected' : ''; ?>>Strength Training</option>
                            <option value="Cardio" <?php echo ($filter_specialty === 'Cardio') ? 'selected' : ''; ?>>Cardio</option>
                            <option value="Yoga" <?php echo ($filter_specialty === 'Yoga') ? 'selected' : ''; ?>>Yoga</option>
                            <option value="Pilates" <?php echo ($filter_specialty === 'Pilates') ? 'selected' : ''; ?>>Pilates</option>
                            <option value="CrossFit" <?php echo ($filter_specialty === 'CrossFit') ? 'selected' : ''; ?>>CrossFit</option>
                        </select>

                        <select id="sort_by" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($sort_by) || $sort_by === 'full_name') ? '' : 'selected'; ?>>Sort by</option>
                            <option value="full_name" <?php echo ($sort_by === 'full_name') ? 'selected' : ''; ?>>Name</option>
                            <option value="specialty" <?php echo ($sort_by === 'specialty') ? 'selected' : ''; ?>>Specialty</option>
                            <option value="email" <?php echo ($sort_by === 'email') ? 'selected' : ''; ?>>Email</option>
                            <option value="trainer_id" <?php echo ($sort_by === 'trainer_id') ? 'selected' : ''; ?>>ID</option>
                        </select>

                        <select id="sort_order" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled>Order</option>
                            <option value="ASC" <?php echo ($sort_order === 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                            <option value="DESC" <?php echo ($sort_order === 'DESC') ? 'selected' : ''; ?>>Descending</option>
                        </select>

                        <button onclick="clearFilters()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer;">Clear Filters</button>
                    </div>
                </div>
            </div>

            <!-- Trainers Table -->
            <div class="card">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Specialty</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trainers as $trainer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($trainer['trainer_id']); ?></td>
                            <td><?php echo htmlspecialchars($trainer['full_name']); ?></td>
                            <td>
                                <span class="role-badge role-trainer">
                                    <?php echo htmlspecialchars($trainer['specialty'] ?? 'General'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($trainer['email']); ?></td>
                            <td><?php echo htmlspecialchars($trainer['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="openEditTrainerModal(<?php echo $trainer['trainer_id']; ?>, '<?php echo htmlspecialchars($trainer['full_name']); ?>', '<?php echo htmlspecialchars($trainer['specialty'] ?? ''); ?>', '<?php echo htmlspecialchars($trainer['phone'] ?? ''); ?>', '<?php echo htmlspecialchars($trainer['email']); ?>')" title="Edit Trainer">
                                        <i class='bx bx-edit'></i> Edit
                                    </button>
                                    <button class="action-btn btn-delete" onclick="openDeleteTrainerModal(<?php echo $trainer['trainer_id']; ?>, '<?php echo htmlspecialchars($trainer['full_name']); ?>')" title="Delete Trainer">
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

    <!-- Add Trainer Modal -->
    <div id="addTrainerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-user-plus'></i>
                <h3>Add New Trainer</h3>
            </div>
            <form method="POST" action="trainers.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_trainer">
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
                                $availableUsers = $adminController->getUsersWithoutTrainers();
                                foreach ($availableUsers as $user): ?>
                                    <option value="<?php echo $user['user_id']; ?>">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Trainer Details -->
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
                            <label for="add_specialty">Specialty:</label>
                            <select id="add_specialty" name="specialty">
                                <option value="">General</option>
                                <option value="Strength Training">Strength Training</option>
                                <option value="Cardio">Cardio</option>
                                <option value="Yoga">Yoga</option>
                                <option value="Pilates">Pilates</option>
                                <option value="CrossFit">CrossFit</option>
                                <option value="Boxing">Boxing</option>
                                <option value="Martial Arts">Martial Arts</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-row-full">
                            <label for="add_image">Profile Image:</label>
                            <input type="file" id="add_image" name="image" accept="image/*">
                            <small style="color: #6c757d; font-size: 0.875rem;">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addTrainerModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Trainer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Trainer Modal -->
    <div id="editTrainerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-edit'></i>
                <h3>Edit Trainer</h3>
            </div>
            <form method="POST" action="trainers.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_trainer">
                <input type="hidden" id="edit_trainer_id" name="trainer_id">
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
                            <label for="edit_specialty">Specialty:</label>
                            <select id="edit_specialty" name="specialty">
                                <option value="">General</option>
                                <option value="Strength Training">Strength Training</option>
                                <option value="Cardio">Cardio</option>
                                <option value="Yoga">Yoga</option>
                                <option value="Pilates">Pilates</option>
                                <option value="CrossFit">CrossFit</option>
                                <option value="Boxing">Boxing</option>
                                <option value="Martial Arts">Martial Arts</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-row-full">
                            <label for="edit_image">Profile Image:</label>
                            <input type="file" id="edit_image" name="image" accept="image/*">
                            <small style="color: #6c757d; font-size: 0.875rem;">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('editTrainerModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Trainer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Trainer Modal -->
    <div id="deleteTrainerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-trash'></i>
                <h3>Delete Trainer</h3>
            </div>
            <form method="POST" action="trainers.php">
                <input type="hidden" name="action" value="delete_trainer">
                <input type="hidden" id="delete_trainer_id" name="trainer_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete trainer "<span id="delete_trainer_name"></span>"?</p>
                    <p class="modal-subtext">This action cannot be undone and will also remove the associated user account.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('deleteTrainerModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Delete Trainer</button>
                </div>
            </form>
        </div>
    </div>
</div>


   <script>
    window.onclick = function(event) {
            const modals = ['addTrainerModal', 'editTrainerModal', 'deleteTrainerModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    closeModal(modalId);
                }
            });
        }

   </script>
