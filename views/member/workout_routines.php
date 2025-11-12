<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../../controllers/WorkoutController.php';

$workoutController = new WorkoutController();
$data = $workoutController->handleRoutineManagement();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Routines - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_routines_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Clamp routine descriptions to avoid overlap */
        .desc-clamp { 
            min-width: 250px; 
            max-width: 520px; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            display: -webkit-box; 
            -webkit-line-clamp: 2; 
            -webkit-box-orient: vertical; 
            white-space: normal; 
            word-break: break-word; 
        }
    </style>
</head>
<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Workout Routines</h1>
                <p>Create, manage, and organize your workout routines for consistent training.</p>
            </div>
            
            <div class="routines-container">
                <?php $role = 'member'; $routines = $data['routines'] ?? []; include __DIR__ . '/../shared/routines_section.php'; ?>
            </div>
        </div>
    </div>
    
    <!-- Create/Edit Routine Modal -->
    <div id="routineModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="routineModalTitle">Create New Routine</h3>
                <span class="close" onclick="closeModal('routineModal')">&times;</span>
            </div>
            <form id="routineForm">
                <input type="hidden" name="action" id="routineAction" value="create_routine">
                <input type="hidden" name="routine_id" id="routineId">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="routine_name">Routine Name</label>
                        <input type="text" id="routine_name" name="name" required>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('routineModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Routine</button>
                </div>
            </form>
        </div>
    </div>
    <?php 
        $confirmData = [
            'id' => 'delete-routine-confirm',
            'title' => 'Delete Routine',
            'message' => 'Are you sure you want to delete this routine? This action cannot be undone.',
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmButtonClass' => 'danger',
            'show' => true,
        ];
        include __DIR__ . '/../utilities/confirm_modal.php';
    ?>
    
    <!-- Add Exercise to Routine Modal -->
    <div id="addExerciseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Exercise to Routine</h3>
                <span class="close" onclick="closeModal('addExerciseModal')">&times;</span>
            </div>
            <form id="addExerciseForm">
                <input type="hidden" name="action" value="add_exercise_to_routine">
                <input type="hidden" name="routine_id" id="current_routine_id">
                
                <div class="exercise-categories">
                    <?php foreach ($data['categories'] as $category): ?>
                        <div class="category-section">
                            <h4><?= htmlspecialchars($category) ?></h4>
                            <?php 
                            $categoryExercises = array_filter($data['exercises'], function($exercise) use ($category) {
                                return strcasecmp($exercise['muscle_group'], $category) === 0;
                            });
                            ?>
                            <?php foreach ($categoryExercises as $exercise): ?>
                                <div class="exercise-option">
                                    <input type="checkbox" name="selected_exercises[]" value="<?= $exercise['exercise_id'] ?>" id="exercise_<?= $exercise['exercise_id'] ?>">
                                    <label for="exercise_<?= $exercise['exercise_id'] ?>">
                                        <?= htmlspecialchars($exercise['name']) ?>
                                        <span style="font-size: 0.85rem; color: #666;"> - <?= htmlspecialchars($exercise['equipment']) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addExerciseModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Selected Exercises</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function openCreateRoutineModal() {
            document.getElementById('routineModalTitle').textContent = 'Create New Routine';
            document.getElementById('routineAction').value = 'create_routine';
            document.getElementById('routineId').value = '';
            document.getElementById('routineForm').reset();
            openModal('routineModal');
        }
        
        function editRoutine(routineId) {
            // This would populate the form with existing routine data
            document.getElementById('routineModalTitle').textContent = 'Edit Routine';
            document.getElementById('routineAction').value = 'update_routine';
            document.getElementById('routineId').value = routineId;
            openModal('routineModal');
        }
        
        function viewRoutine(routineId) {
            // Redirect to routine detail page
            window.location.href = `workout_routine_detail.php?id=${routineId}`;
        }
        
        let pendingDeleteRoutineId = null;
        document.addEventListener('DOMContentLoaded', function(){
            const m = document.getElementById('delete-routine-confirm');
            if(m){ m.style.display = 'none'; }
        });
        function deleteRoutine(routineId) {
            pendingDeleteRoutineId = routineId;
            const modalId = 'delete-routine-confirm';
            window.confirmModalActions = window.confirmModalActions || {};
            window.confirmModalActions[modalId] = function(){
                const formData = new FormData();
                formData.append('action', 'delete_routine');
                formData.append('routine_id', pendingDeleteRoutineId);
                fetch('workout_routines.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        location.reload();
                    } else {
                        alert(d.message || 'Failed to delete routine');
                    }
                })
                .catch(err => { console.error(err); alert('Network error'); })
                .finally(() => { pendingDeleteRoutineId = null; });
            };
            const modal = document.getElementById(modalId);
            if (modal) { modal.style.display = 'flex'; }
        }
        
        function copyRoutine(routineId) {
            if (confirm('Copy this routine to your routines?')) {
                const formData = new FormData();
                formData.append('action', 'copy_routine');
                formData.append('routine_id', routineId);
                
                fetch('workout_routines.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Check console for details.');
                });
            }
        }

        // Muscle group filtering
        function filterExercises() {
            const filter = document.getElementById('muscle_filter').value;
            const muscleGroups = document.querySelectorAll('.muscle-group');
            
            muscleGroups.forEach(group => {
                if (filter === '' || group.dataset.muscle === filter) {
                    group.style.display = 'block';
                } else {
                    group.style.display = 'none';
                }
            });
        }
        
        // Form submissions
        document.getElementById('routineForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('workout_routines.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to detail view to add exercises
                    if (data.routine_id) {
                        window.location.href = `workout_routine_detail.php?id=${data.routine_id}`;
                    } else {
                        location.reload();
                    }
                } else {
                    alert(data.message || 'Failed to create routine');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            });
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
