<?php
session_start();
require_once '../../controllers/WorkoutController.php';

$workoutController = new WorkoutController();
$data = $workoutController->handleWorkoutTracking();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Workout Tracker</h1>
                <p>Track your workouts, monitor progress, and achieve your fitness goals.</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card">
                    <h4><?= $data['stats']['total_workouts'] ?? 0 ?></h4>
                    <p>Total Workouts</p>
                </div>
                <div class="stat-card">
                    <h4><?= $data['stats']['active_days'] ?? 0 ?></h4>
                    <p>Active Days</p>
                </div>
                <div class="stat-card">
                    <h4><?= round($data['stats']['avg_duration'] ?? 0) ?>m</h4>
                    <p>Avg Duration</p>
                </div>
            </div>
            
            <div class="workout-container">
                <!-- Recent Workouts -->
                <div class="workout-section">
                    <h3><i class='bx bx-dumbbell'></i> Recent Workouts</h3>
                    <div class="workout-list">
                        <?php if (!empty($data['workouts'])): ?>
                            <?php foreach (array_slice($data['workouts'], 0, 5) as $workout): ?>
                                <div class="workout-item">
                                    <div class="workout-info">
                                        <h4><?= htmlspecialchars($workout['name']) ?></h4>
                                        <p>
                                            <?= date('M j, Y', strtotime($workout['workout_date'])) ?>
                                            <?php if ($workout['routine_name']): ?>
                                                • <?= htmlspecialchars($workout['routine_name']) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="workout-actions">
                                        <button class="btn btn-primary" onclick="viewWorkout(<?= $workout['id'] ?>)">
                                            <i class='bx bx-show'></i> View
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteWorkout(<?= $workout['id'] ?>)">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data">
                                <i class='bx bx-dumbbell'></i>
                                <p>No workouts yet. Start your first workout!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="workout-section">
                    <h3><i class='bx bx-plus-circle'></i> Quick Actions</h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <button class="btn btn-primary" onclick="openStartWorkoutModal()">
                            <i class='bx bx-play'></i> Start New Workout
                        </button>
                        <button class="btn btn-secondary" onclick="openRoutineModal()">
                            <i class='bx bx-list-ul'></i> Manage Routines
                        </button>
                        <button class="btn btn-secondary" onclick="openExerciseModal()">
                            <i class='bx bx-plus'></i> Add Exercise
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Start Workout Modal -->
    <div id="startWorkoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Start New Workout</h3>
                <span class="close" onclick="closeModal('startWorkoutModal')">&times;</span>
            </div>
            <form id="startWorkoutForm">
                <input type="hidden" name="action" value="start_workout">
                <div class="form-group">
                    <label for="workout_name">Workout Name</label>
                    <input type="text" id="workout_name" name="name" value="Workout <?= date('M j, Y H:i') ?>" required>
                </div>
                <div class="form-group">
                    <label for="routine_select">Routine (Optional)</label>
                    <select id="routine_select" name="routine_id">
                        <option value="">No Routine</option>
                        <?php foreach ($data['routines'] as $routine): ?>
                            <option value="<?= $routine['id'] ?>"><?= htmlspecialchars($routine['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="workout_notes">Notes</label>
                    <textarea id="workout_notes" name="notes" placeholder="Add any notes about this workout..."></textarea>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('startWorkoutModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Start Workout</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Exercise Modal -->
    <div id="addExerciseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Exercise</h3>
                <span class="close" onclick="closeModal('addExerciseModal')">&times;</span>
            </div>
            <form id="addExerciseForm">
                <input type="hidden" name="action" value="add_exercise">
                <input type="hidden" name="workout_id" id="current_workout_id">
                <div class="form-group">
                    <label for="exercise_select">Exercise</label>
                    <select id="exercise_select" name="exercise_id" required>
                        <option value="">Select Exercise</option>
                        <?php foreach ($data['exercises'] as $exercise): ?>
                            <option value="<?= $exercise['id'] ?>"><?= htmlspecialchars($exercise['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="exercise_sets">Sets</label>
                        <input type="number" id="exercise_sets" name="sets" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="exercise_reps">Reps</label>
                        <input type="number" id="exercise_reps" name="reps" min="1" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="exercise_weight">Weight (kg)</label>
                        <input type="number" id="exercise_weight" name="weight" step="0.1" min="0">
                    </div>
                    <div class="form-group">
                        <label for="exercise_duration">Duration (minutes)</label>
                        <input type="number" id="exercise_duration" name="duration" min="1">
                    </div>
                </div>
                <div class="form-group">
                    <label for="exercise_notes">Notes</label>
                    <textarea id="exercise_notes" name="notes" placeholder="Add any notes about this exercise..."></textarea>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addExerciseModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Exercise</button>
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
        
        function openStartWorkoutModal() {
            openModal('startWorkoutModal');
        }
        
        function openRoutineModal() {
            // Redirect to routine management page
            window.location.href = 'workout_routines.php';
        }
        
        function openExerciseModal() {
            // This would open a modal to add exercises to current workout
            alert('Select a workout first to add exercises');
        }
        
        // Form submissions
        document.getElementById('startWorkoutForm').addEventListener('submit', function(e) {
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
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        });
        
        function viewWorkout(workoutId) {
            // Redirect to workout detail page
            window.location.href = `workout_detail.php?id=${workoutId}`;
        }
        
        function deleteWorkout(workoutId) {
            if (confirm('Are you sure you want to delete this workout?')) {
                const formData = new FormData();
                formData.append('action', 'delete_workout');
                formData.append('workout_id', workoutId);
                
                fetch('../../controllers/WorkoutController.php', {
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
                    alert('An error occurred');
                });
            }
        }
        
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
