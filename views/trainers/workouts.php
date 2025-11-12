<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../../controllers/WorkoutController.php';
require_once '../../models/Trainer.php';

$workoutController = new WorkoutController();
$trainerModel = new Trainer();

$trainer_id = $_SESSION['trainer_id'] ?? 0;
$username = $_SESSION['username'] ?? 'Trainer';

// Get trainer's clients/members
$clients = $trainerModel->getTrainerClients($trainer_id);

// Handle workout tracking for trainer view
$data = $workoutController->handleWorkoutTracking();
$routineData = $workoutController->handleRoutineManagement();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Plans - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_routines_styles.css">
    <link rel="stylesheet" href="../../assets/css/trainer_workouts.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../utilities/alert.php'; ?>
    <?php include '../components/dynamic_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Workout Plans</h1>
                <p>Create and manage workout plans for your members and track their progress.</p>
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

            <!-- Tabs Navigation -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('routines')">
                    <i class='bx bx-list-ul'></i> My Routines
                </button>
                
            </div>

            <!-- My Routines Tab -->
            <div id="routines" class="tab-content active">
                <div class="routines-container">
                    <?php $role = 'trainer'; $routines = $routineData['routines'] ?? []; include __DIR__ . '/../shared/routines_section.php'; ?>
                </div>
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
                <input type="hidden" name="member_id" id="routine_client_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="routine_name">Routine Name</label>
                        <input type="text" id="routine_name" name="name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="routine_description">Description (Optional)</label>
                        <textarea id="routine_description" name="description" rows="3" placeholder="Describe this routine..."></textarea>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('routineModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Routine</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Routine Modal -->
    <div id="assignRoutineModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Assign Routine to Client</h3>
                <span class="close" onclick="closeModal('assignRoutineModal')">&times;</span>
            </div>
            <form id="assignRoutineForm">
                <input type="hidden" name="action" value="assign_routine">
                <input type="hidden" name="routine_id" id="assign_routine_id">
                
                <div class="form-group">
                    <label for="client_select">Select Client</label>
                    <select id="client_select" name="member_id" required>
                        <option value="">Choose a client...</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['member_id'] ?>"><?= htmlspecialchars($client['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('assignRoutineModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Routine</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Workout Modal -->
    <div id="assignWorkoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Assign Workout to Client</h3>
                <span class="close" onclick="closeModal('assignWorkoutModal')">&times;</span>
            </div>
            <form id="assignWorkoutForm">
                <input type="hidden" name="action" value="assign_workout">
                <input type="hidden" name="member_id" id="member_id">
                
                <div class="form-group">
                    <label for="workout_select">Select Workout/Routine</label>
                    <select id="workout_select" name="workout_id" required>
                        <option value="">Choose a workout or routine...</option>
                        <?php if (!empty($routineData['routines'])): ?>
                            <optgroup label="My Routines">
                                <?php foreach ($routineData['routines'] as $routine): ?>
                                    <option value="routine_<?= $routine['id'] ?>"><?= htmlspecialchars($routine['name']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="schedule_date">Schedule Date</label>
                    <input type="date" id="schedule_date" name="schedule_date" required>
                </div>
                
                <div class="form-group">
                    <label for="schedule_time">Schedule Time</label>
                    <input type="time" id="schedule_time" name="schedule_time" required>
                </div>
                
                <div class="form-group">
                    <label for="workout_notes">Notes (Optional)</label>
                    <textarea id="workout_notes" name="notes" rows="3" placeholder="Add any special instructions..."></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('assignWorkoutModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Workout</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Workout Confirmation Modal -->
    <div id="delete-workout-confirm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Workout</h3>
                <span class="close" onclick="closeModal('delete-workout-confirm')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this workout?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('delete-workout-confirm')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteWorkout()">Delete</button>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

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
            document.getElementById('routine_client_id').value = '';
            document.getElementById('routineForm').reset();
            openModal('routineModal');
        }

        function viewRoutine(routineId) {
            window.location.href = `workout_routine_detail.php?id=${routineId}`;
        }

        function editRoutine(routineId) {
            document.getElementById('routineModalTitle').textContent = 'Edit Routine';
            document.getElementById('routineAction').value = 'update_routine';
            document.getElementById('routineId').value = routineId;
            openModal('routineModal');
        }

        function assignRoutine(routineId) {
            document.getElementById('assign_routine_id').value = routineId;
            openModal('assignRoutineModal');
        }

        function viewClientWorkout(memberId) {
            window.location.href = `client_workout_history.php?member_id=${memberId}`;
        }

        function assignWorkoutToClient(memberId) {
            // Get selected client info
            const clientSelect = document.getElementById('assign_client_id');
            const memberSelect = document.getElementById('member_id');
            
            // Set the selected client in the assign modal
            if (memberSelect) {
                memberSelect.value = memberId;
            }
            
            // Open the assign workout modal
            openModal('assignWorkoutModal');
        }

        function createRoutineForClient(memberId) {
            // Set the client for whom we're creating the routine
            document.getElementById('routine_client_id').value = memberId;
            
            // Update modal title
            document.getElementById('routineModalTitle').textContent = 'Create Routine for Client';
            
            // Open create routine modal
            openCreateRoutineModal();
        }

        function confirmDeleteWorkout() {
            if (pendingDeleteId) {
                const formData = new FormData();
                formData.append('action', 'delete_workout');
                formData.append('workout_id', pendingDeleteId);
                
                fetch('../../controllers/WorkoutController.php', { 
                    method: 'POST', 
                    body: formData 
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to delete workout');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Network error: ' + error.message);
                })
                .finally(() => { 
                    pendingDeleteId = null; 
                    closeModal('delete-workout-confirm');
                });
            }
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
                fetch('../../controllers/WorkoutController.php', { method: 'POST', body: formData })
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

        function viewWorkout(workoutId) {
            window.location.href = `workout_detail.php?id=${workoutId}`;
        }

        let pendingDeleteId = null;
        document.addEventListener('DOMContentLoaded', function(){
            const m = document.getElementById('delete-workout-confirm');
            if(m){ m.style.display = 'none'; }
        });

        function deleteWorkout(workoutId) {
            pendingDeleteId = workoutId;
            const modalId = 'delete-workout-confirm';
            window.confirmModalActions = window.confirmModalActions || {};
            window.confirmModalActions[modalId] = function(){
                const formData = new FormData();
                formData.append('action', 'delete_workout');
                formData.append('workout_id', pendingDeleteId);
                fetch('../../controllers/WorkoutController.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        location.reload();
                    } else {
                        alert(d.message || 'Failed to delete workout');
                    }
                })
                .catch(err => { console.error(err); alert('Network error'); })
                .finally(() => { pendingDeleteId = null; });
            };
            const modal = document.getElementById(modalId);
            if (modal) { modal.style.display = 'flex'; }
        }

        // Form submissions
        document.getElementById('routineForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../../controllers/WorkoutController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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

        document.getElementById('assignRoutineForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../../controllers/WorkoutController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('assignRoutineModal');
                } else {
                    alert(data.message || 'Failed to assign routine');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            });
        });

        document.getElementById('assignWorkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../../controllers/WorkoutController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Workout assigned successfully!');
                    closeModal('assignWorkoutModal');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to assign workout');
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
