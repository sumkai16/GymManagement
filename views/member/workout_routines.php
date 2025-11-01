<?php
session_start();
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
                <!-- My Routines -->
                <div class="routines-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3><i class='bx bx-list-ul'></i> My Routines</h3>
                        <button class="btn btn-primary" onclick="openCreateRoutineModal()">
                            <i class='bx bx-plus'></i> New Routine
                        </button>
                    </div>
                    <div class="routine-list">
                        <?php if (!empty($data['routines'])): ?>
                            <?php foreach ($data['routines'] as $routine): ?>
                                <div class="routine-item">
                                    <div class="routine-header">
                                        <div class="routine-info">
                                            <h4><?= htmlspecialchars($routine['name']) ?></h4>
                                            <p>
                                                <?= date('M j, Y', strtotime($routine['created_at'])) ?>
                                                <?php if ($routine['is_public']): ?>
                                                    • <span style="color: #10b981;">Public</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="routine-actions">
                                            <button class="btn btn-sm btn-primary" onclick="viewRoutine(<?= $routine['id'] ?>)">
                                                <i class='bx bx-show'></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary" onclick="editRoutine(<?= $routine['id'] ?>)">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteRoutine(<?= $routine['id'] ?>)">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="routine-exercises">
                                        <?php 
                                        $routineExercises = $workoutController->routineModel->getExercisesForRoutine($routine['id']);
                                        if (!empty($routineExercises)): 
                                        ?>
                                            <?php foreach (array_slice($routineExercises, 0, 3) as $exercise): ?>
                                                <div class="exercise-item">
                                                    <div class="exercise-info">
                                                        <h5><?= htmlspecialchars($exercise['exercise_name']) ?></h5>
                                                        <p><?= $exercise['sets'] ?> sets × <?= $exercise['reps'] ?> reps
                                                        <?php if ($exercise['weight']): ?>
                                                            @ <?= $exercise['weight'] ?>kg
                                                        <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if (count($routineExercises) > 3): ?>
                                                <p style="text-align: center; color: #666; font-size: 0.8rem; margin: 0.5rem 0 0 0;">
                                                    +<?= count($routineExercises) - 3 ?> more exercises
                                                </p>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <p style="color: #666; font-size: 0.9rem; text-align: center; margin: 0;">No exercises added yet</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data">
                                <i class='bx bx-list-ul'></i>
                                <p>No routines created yet. Create your first routine!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Public Routines -->
                <div class="routines-section">
                    <h3><i class='bx bx-globe'></i> Public Routines</h3>
                    <div class="routine-list">
                        <?php if (!empty($data['public_routines'])): ?>
                            <?php foreach ($data['public_routines'] as $routine): ?>
                                <div class="routine-item">
                                    <div class="routine-header">
                                        <div class="routine-info">
                                            <h4><?= htmlspecialchars($routine['name']) ?></h4>
                                            <p>
                                                by <?= htmlspecialchars($routine['first_name'] . ' ' . $routine['last_name']) ?>
                                                • <?= date('M j, Y', strtotime($routine['created_at'])) ?>
                                            </p>
                                        </div>
                                        <div class="routine-actions">
                                            <button class="btn btn-sm btn-primary" onclick="viewRoutine(<?= $routine['id'] ?>)">
                                                <i class='bx bx-show'></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="copyRoutine(<?= $routine['id'] ?>)">
                                                <i class='bx bx-copy'></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="routine-exercises">
                                        <?php if ($routine['description']): ?>
                                            <p class="desc-clamp" style="color: #666; font-size: 0.9rem; margin: 0;"><?= htmlspecialchars($routine['description']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-data">
                                <i class='bx bx-globe'></i>
                                <p>No public routines available</p>
                            </div>
                        <?php endif; ?>
                    </div>
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
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="routine_name">Routine Name</label>
                        <input type="text" id="routine_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="routine_public" name="is_public" value="1">
                            <label for="routine_public">Make public</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="routine_description">Description</label>
                    <textarea id="routine_description" name="description" placeholder="Describe this routine..."></textarea>
                </div>

                <!-- Quick Exercise Selection -->
                <div class="exercise-selection">
                    <h4><i class='bx bx-dumbbell'></i> Add Exercises</h4>
                    
                    <!-- Muscle Group Filter -->
                    <div class="form-group">
                        <label for="muscle_filter">Filter by Muscle Group:</label>
                        <select id="muscle_filter" onchange="filterExercises()">
                            <option value="">All</option>
                            <option value="chest">Chest</option>
                            <option value="back">Back</option>
                            <option value="shoulders">Shoulders</option>
                            <option value="arms">Arms</option>
                            <option value="legs">Legs</option>
                            <option value="core">Core</option>
                        </select>
                    </div>

                    <!-- Exercise Grid -->
                    <div class="exercise-grid">
                        <!-- Chest -->
                        <div class="muscle-group" data-muscle="chest">
                            <h5>Chest</h5>
                            <div class="exercise-options">
                                <label><input type="checkbox" name="exercises[]" value="bench_press"> Bench Press</label>
                                <label><input type="checkbox" name="exercises[]" value="push_ups"> Push-ups</label>
                                <label><input type="checkbox" name="exercises[]" value="incline_bench"> Incline Bench</label>
                                <label><input type="checkbox" name="exercises[]" value="dips"> Dips</label>
                            </div>
                        </div>

                        <!-- Back -->
                        <div class="muscle-group" data-muscle="back">
                            <h5>Back</h5>
                            <div class="exercise-options">
                                <label><input type="checkbox" name="exercises[]" value="pull_ups"> Pull-ups</label>
                                <label><input type="checkbox" name="exercises[]" value="deadlift"> Deadlift</label>
                                <label><input type="checkbox" name="exercises[]" value="bent_row"> Bent Row</label>
                                <label><input type="checkbox" name="exercises[]" value="lat_pulldown"> Lat Pulldown</label>
                            </div>
                        </div>

                        <!-- Shoulders -->
                        <div class="muscle-group" data-muscle="shoulders">
                            <h5>Shoulders</h5>
                            <div class="exercise-options">
                                <label><input type="checkbox" name="exercises[]" value="overhead_press"> Overhead Press</label>
                                <label><input type="checkbox" name="exercises[]" value="lateral_raises"> Lateral Raises</label>
                                <label><input type="checkbox" name="exercises[]" value="front_raises"> Front Raises</label>
                                <label><input type="checkbox" name="exercises[]" value="rear_fly"> Rear Fly</label>
                            </div>
                        </div>

                        <!-- Arms -->
                        <div class="muscle-group" data-muscle="arms">
                            <h5>Arms</h5>
                            <div class="exercise-options">
                                <label><input type="checkbox" name="exercises[]" value="bicep_curls"> Bicep Curls</label>
                                <label><input type="checkbox" name="exercises[]" value="tricep_dips"> Tricep Dips</label>
                                <label><input type="checkbox" name="exercises[]" value="hammer_curls"> Hammer Curls</label>
                                <label><input type="checkbox" name="exercises[]" value="tricep_extensions"> Tricep Extensions</label>
                            </div>
                        </div>

                        <!-- Legs -->
                        <div class="muscle-group" data-muscle="legs">
                            <h5>Legs</h5>
                            <div class="exercise-options">
                                <label><input type="checkbox" name="exercises[]" value="squats"> Squats</label>
                                <label><input type="checkbox" name="exercises[]" value="lunges"> Lunges</label>
                                <label><input type="checkbox" name="exercises[]" value="leg_press"> Leg Press</label>
                                <label><input type="checkbox" name="exercises[]" value="calf_raises"> Calf Raises</label>
                            </div>
                        </div>

                        <!-- Core -->
                        <div class="muscle-group" data-muscle="core">
                            <h5>Core</h5>
                            <div class="exercise-options">
                                <label><input type="checkbox" name="exercises[]" value="plank"> Plank</label>
                                <label><input type="checkbox" name="exercises[]" value="crunches"> Crunches</label>
                                <label><input type="checkbox" name="exercises[]" value="russian_twists"> Russian Twists</label>
                                <label><input type="checkbox" name="exercises[]" value="leg_raises"> Leg Raises</label>
                            </div>
                        </div>
                    </div>

                    <!-- Sets & Reps Template -->
                    <div class="sets-reps-template" style="display: none;">
                        <h4>Exercise Details</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Sets</label>
                                <input type="number" name="sets[]" value="3" min="1" max="10">
                            </div>
                            <div class="form-group">
                                <label>Reps</label>
                                <input type="number" name="reps[]" value="10" min="1" max="50">
                            </div>
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <input type="number" name="weight[]" value="0" min="0" step="0.5">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('routineModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Routine</button>
                </div>
            </form>
        </div>
    </div>
    
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
                                return $exercise['category'] === $category;
                            });
                            ?>
                            <?php foreach ($categoryExercises as $exercise): ?>
                                <div class="exercise-option">
                                    <input type="checkbox" name="selected_exercises[]" value="<?= $exercise['id'] ?>" id="exercise_<?= $exercise['id'] ?>">
                                    <label for="exercise_<?= $exercise['id'] ?>"><?= htmlspecialchars($exercise['name']) ?></label>
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
        
        function deleteRoutine(routineId) {
            if (confirm('Are you sure you want to delete this routine?')) {
                const formData = new FormData();
                formData.append('action', 'delete_routine');
                formData.append('routine_id', routineId);
                
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
        
        function copyRoutine(routineId) {
            if (confirm('Copy this routine to your routines?')) {
                const formData = new FormData();
                formData.append('action', 'copy_routine');
                formData.append('routine_id', routineId);
                
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
