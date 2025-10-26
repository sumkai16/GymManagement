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
        case 'add_exercise':
            $result = $adminController->addExercise(
                $_POST['name'] ?? '',
                $_POST['description'] ?? '',
                $_POST['muscle_group'] ?? '',
                $_POST['equipment'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: exercises.php");
            exit;

        case 'update_exercise':
            $result = $adminController->updateExercise(
                $_POST['exercise_id'] ?? 0,
                $_POST['name'] ?? '',
                $_POST['description'] ?? '',
                $_POST['muscle_group'] ?? '',
                $_POST['equipment'] ?? ''
            );
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: exercises.php");
            exit;

        case 'delete_exercise':
            $result = $adminController->deleteExercise($_POST['exercise_id'] ?? 0);
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            header("Location: exercises.php");
            exit;
    }
}

// Handle filter and sort parameters
$filter_muscle_group = $_GET['filter_muscle_group'] ?? null;
$filter_equipment = $_GET['filter_equipment'] ?? null;
$search_term = $_GET['search'] ?? null;
$sort_by = $_GET['sort_by'] ?? 'name';
$sort_order = $_GET['sort_order'] ?? 'ASC';

// Get all exercises with filters and sorting
$exercises = $adminController->getAllExercises($filter_muscle_group, $filter_equipment, $search_term, $sort_by, $sort_order);
$username = $_SESSION['username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise Management - FitNexus</title>
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
                <h1>Exercise Management</h1>
                <p>Manage exercise database and workout routines.</p>
            </div>

            <!-- Header Actions: Add Exercise Button and Filters -->
            <div class="header-actions" style="display: flex; flex-direction: row; align-items: center; margin-bottom: 20px; gap: 20px; justify-content: space-around;">
                <!-- Add Exercise Button -->
                <button class="add-user-btn" onclick="openAddExerciseModal()">
                    <i class='bx bx-plus'></i> Add New Exercise
                </button>

                <!-- Filters and Sorting -->
                <div class="filters-section" style="display: flex; flex-direction: column; gap: 10px; align-items: flex-start;">
                    <div class="filters-row" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                        <input type="text" id="search" placeholder="Search exercises..." value="<?php echo htmlspecialchars($search_term); ?>" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 200px;">

                        <select id="filter_muscle_group" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_muscle_group)) ? 'selected' : ''; ?>>Muscle Group</option>
                            <option value="">All Groups</option>
                            <option value="Chest" <?php echo ($filter_muscle_group === 'Chest') ? 'selected' : ''; ?>>Chest</option>
                            <option value="Back" <?php echo ($filter_muscle_group === 'Back') ? 'selected' : ''; ?>>Back</option>
                            <option value="Shoulders" <?php echo ($filter_muscle_group === 'Shoulders') ? 'selected' : ''; ?>>Shoulders</option>
                            <option value="Arms" <?php echo ($filter_muscle_group === 'Arms') ? 'selected' : ''; ?>>Arms</option>
                            <option value="Legs" <?php echo ($filter_muscle_group === 'Legs') ? 'selected' : ''; ?>>Legs</option>
                            <option value="Core" <?php echo ($filter_muscle_group === 'Core') ? 'selected' : ''; ?>>Core</option>
                            <option value="Cardio" <?php echo ($filter_muscle_group === 'Cardio') ? 'selected' : ''; ?>>Cardio</option>
                        </select>

                        <select id="filter_equipment" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($filter_equipment)) ? 'selected' : ''; ?>>Equipment</option>
                            <option value="">All Equipment</option>
                            <option value="Bodyweight" <?php echo ($filter_equipment === 'Bodyweight') ? 'selected' : ''; ?>>Bodyweight</option>
                            <option value="Dumbbells" <?php echo ($filter_equipment === 'Dumbbells') ? 'selected' : ''; ?>>Dumbbells</option>
                            <option value="Barbell" <?php echo ($filter_equipment === 'Barbell') ? 'selected' : ''; ?>>Barbell</option>
                            <option value="Machine" <?php echo ($filter_equipment === 'Machine') ? 'selected' : ''; ?>>Machine</option>
                            <option value="Cable" <?php echo ($filter_equipment === 'Cable') ? 'selected' : ''; ?>>Cable</option>
                            <option value="Kettlebell" <?php echo ($filter_equipment === 'Kettlebell') ? 'selected' : ''; ?>>Kettlebell</option>
                        </select>

                        <select id="sort_by" onchange="applyFilters()" style="padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; font-size: 14px; background: #fff; min-width: 140px;">
                            <option value="" disabled <?php echo (empty($sort_by) || $sort_by === 'name') ? '' : 'selected'; ?>>Sort by</option>
                            <option value="name" <?php echo ($sort_by === 'name') ? 'selected' : ''; ?>>Name</option>
                            <option value="muscle_group" <?php echo ($sort_by === 'muscle_group') ? 'selected' : ''; ?>>Muscle Group</option>
                            <option value="equipment" <?php echo ($sort_by === 'equipment') ? 'selected' : ''; ?>>Equipment</option>
                            <option value="exercise_id" <?php echo ($sort_by === 'exercise_id') ? 'selected' : ''; ?>>ID</option>
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

            <!-- Exercises Table -->
            <div class="card">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Muscle Group</th>
                            <th>Equipment</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exercises as $exercise): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($exercise['exercise_id']); ?></td>
                            <td><?php echo htmlspecialchars($exercise['name']); ?></td>
                            <td>
                                <span class="role-badge role-member">
                                    <?php echo htmlspecialchars($exercise['muscle_group'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($exercise['equipment'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(substr($exercise['description'] ?? '', 0, 50)) . (strlen($exercise['description'] ?? '') > 50 ? '...' : ''); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="openEditExerciseModal(<?php echo $exercise['exercise_id']; ?>, '<?php echo htmlspecialchars($exercise['name']); ?>', '<?php echo htmlspecialchars($exercise['description'] ?? ''); ?>', '<?php echo htmlspecialchars($exercise['muscle_group'] ?? ''); ?>', '<?php echo htmlspecialchars($exercise['equipment'] ?? ''); ?>')" title="Edit Exercise">
                                        <i class='bx bx-edit'></i> Edit
                                    </button>
                                    <button class="action-btn btn-delete" onclick="openDeleteExerciseModal(<?php echo $exercise['exercise_id']; ?>, '<?php echo htmlspecialchars($exercise['name']); ?>')" title="Delete Exercise">
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

    <!-- Add Exercise Modal -->
    <div id="addExerciseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-plus'></i>
                <h3>Add New Exercise</h3>
            </div>
            <form method="POST" action="exercises.php">
                <input type="hidden" name="action" value="add_exercise">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name">Exercise Name:</label>
                        <input type="text" id="add_name" name="name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_muscle_group">Muscle Group:</label>
                            <select id="add_muscle_group" name="muscle_group">
                                <option value="">Select Muscle Group</option>
                                <option value="Chest">Chest</option>
                                <option value="Back">Back</option>
                                <option value="Shoulders">Shoulders</option>
                                <option value="Arms">Arms</option>
                                <option value="Legs">Legs</option>
                                <option value="Core">Core</option>
                                <option value="Cardio">Cardio</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_equipment">Equipment:</label>
                            <select id="add_equipment" name="equipment">
                                <option value="">Select Equipment</option>
                                <option value="Bodyweight">Bodyweight</option>
                                <option value="Dumbbells">Dumbbells</option>
                                <option value="Barbell">Barbell</option>
                                <option value="Machine">Machine</option>
                                <option value="Cable">Cable</option>
                                <option value="Kettlebell">Kettlebell</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add_description">Description:</label>
                        <textarea id="add_description" name="description" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('addExerciseModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Exercise</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Exercise Modal -->
    <div id="editExerciseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-edit'></i>
                <h3>Edit Exercise</h3>
            </div>
            <form method="POST" action="exercises.php">
                <input type="hidden" name="action" value="update_exercise">
                <input type="hidden" id="edit_exercise_id" name="exercise_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Exercise Name:</label>
                        <input type="text" id="edit_name" name="name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_muscle_group">Muscle Group:</label>
                            <select id="edit_muscle_group" name="muscle_group">
                                <option value="">Select Muscle Group</option>
                                <option value="Chest">Chest</option>
                                <option value="Back">Back</option>
                                <option value="Shoulders">Shoulders</option>
                                <option value="Arms">Arms</option>
                                <option value="Legs">Legs</option>
                                <option value="Core">Core</option>
                                <option value="Cardio">Cardio</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_equipment">Equipment:</label>
                            <select id="edit_equipment" name="equipment">
                                <option value="">Select Equipment</option>
                                <option value="Bodyweight">Bodyweight</option>
                                <option value="Dumbbells">Dumbbells</option>
                                <option value="Barbell">Barbell</option>
                                <option value="Machine">Machine</option>
                                <option value="Cable">Cable</option>
                                <option value="Kettlebell">Kettlebell</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description:</label>
                        <textarea id="edit_description" name="description" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('editExerciseModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Exercise</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Exercise Modal -->
    <div id="deleteExerciseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-trash'></i>
                <h3>Delete Exercise</h3>
            </div>
            <form method="POST" action="exercises.php">
                <input type="hidden" name="action" value="delete_exercise">
                <input type="hidden" id="delete_exercise_id" name="exercise_id">
                <div class="modal-body">
                    <p>Are you sure you want to delete exercise "<span id="delete_exercise_name"></span>"?</p>
                    <p class="modal-subtext">This action cannot be undone and may affect existing workout routines.</p>
                </div>
                <div class="modal-actions">
                    <button type="button" onclick="closeModal('deleteExerciseModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Delete Exercise</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function applyFilters() {
            const search = document.getElementById('search').value;
            const filterMuscleGroup = document.getElementById('filter_muscle_group').value;
            const filterEquipment = document.getElementById('filter_equipment').value;
            const sortBy = document.getElementById('sort_by').value;
            const sortOrder = document.getElementById('sort_order').value;

            const params = new URLSearchParams(window.location.search);
            params.set('search', search);
            params.set('filter_muscle_group', filterMuscleGroup);
            params.set('filter_equipment', filterEquipment);
            params.set('sort_by', sortBy);
            params.set('sort_order', sortOrder);

            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function clearFilters() {
            window.location.href = window.location.pathname;
        }

        function openAddExerciseModal() {
            document.getElementById('addExerciseModal').style.display = 'block';
        }

        function openEditExerciseModal(exerciseId, name, description, muscleGroup, equipment) {
            document.getElementById('edit_exercise_id').value = exerciseId;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_muscle_group').value = muscleGroup;
            document.getElementById('edit_equipment').value = equipment;
            document.getElementById('editExerciseModal').style.display = 'block';
        }

        function openDeleteExerciseModal(exerciseId, name) {
            document.getElementById('delete_exercise_id').value = exerciseId;
            document.getElementById('delete_exercise_name').textContent = name;
            document.getElementById('deleteExerciseModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = ['addExerciseModal', 'editExerciseModal', 'deleteExerciseModal'];
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
