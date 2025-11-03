<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../../controllers/WorkoutController.php';

$routine_id = $_GET['id'] ?? 0;
$workoutController = new WorkoutController();
$data = $workoutController->handleRoutineDetail($routine_id);

if (!$data['routine']) {
    header('Location: workout_routines.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['routine']['name']) ?> - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_routines_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .exercise-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .exercise-info h4 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .exercise-info p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        .exercise-details {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .detail-badge {
            background: #f0f0f0;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .exercise-actions {
            display: flex;
            gap: 0.5rem;
        }
        /* Ensure action buttons look consistent */
        .exercise-actions .btn{display:inline-flex;align-items:center;justify-content:center;height:42px;padding:.5rem 1rem}
        .modal-exercises {
            max-height: 400px;
            overflow-y: auto;
        }
        .exercise-select-item {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .exercise-select-item:hover {
            background: #f5f5f5;
            border-color: #007bff;
        }
        .exercise-select-item.selected {
            background: #e3f2fd;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <?php include '../components/dynamic_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <div>
                    <h1><?= htmlspecialchars($data['routine']['name']) ?></h1>
                    <p><?= htmlspecialchars($data['routine']['description'] ?? 'No description') ?></p>
                </div>
                <div style="display:flex; gap: .5rem;">
                    <button class="btn btn-primary" onclick="startRoutine(<?= (int)$data['routine']['id'] ?>)">
                        <i class='bx bx-play'></i> Start Routine
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='workout_routines.php'">
                        <i class='bx bx-arrow-back'></i> Back to Routines
                    </button>
                </div>
            </div>
            
            <?php if (!empty($data['can_edit'])): ?>
                <div style="margin-bottom: 1rem;">
                    <button class="btn btn-primary" onclick="openAddExerciseModal()">
                        <i class='bx bx-plus'></i> Add Exercise
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="exercises-list">
                <?php if (!empty($data['routine_exercises'])): ?>
                    <?php foreach ($data['routine_exercises'] as $exercise): ?>
                        <div class="exercise-card">
                            <div class="exercise-info">
                                <h4><?= htmlspecialchars($exercise['exercise_name']) ?></h4>
                                <div class="exercise-details"></div>
                                <p style="margin-top: 0.5rem;">
                                    <span style="background: #e3f2fd; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                                        <?= htmlspecialchars($exercise['muscle_group']) ?>
                                    </span>
                                </p>
                                <?php if ($exercise['notes']): ?>
                                    <p style="margin-top: 0.5rem; font-style: italic;"><?= htmlspecialchars($exercise['notes']) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($data['can_edit'])): ?>
                                <div class="exercise-actions">
                                    <button class="btn btn-sm btn-secondary" onclick="editExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['notes'] ?? '', ENT_QUOTES) ?>')">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="removeExercise(<?= $exercise['id'] ?>)">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data">
                        <i class='bx bx-dumbbell'></i>
                        <p>No exercises added yet. Click "Add Exercise" to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php 
        // Reusable confirmation modal for removing an exercise
        $confirmData = [
            'id' => 'remove-exercise-confirm',
            'title' => 'Remove Exercise',
            'message' => 'Are you sure you want to remove this exercise from the routine?',
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmButtonClass' => 'danger',
            'show' => true,
        ];
        include __DIR__ . '/../utilities/confirm_modal.php';
    ?>
    <?php 
        // Reusable modal for in-progress workout notice when starting a new routine
        $confirmData = [
            'id' => 'start-blocked-modal',
            'title' => 'Cannot Start Workout',
            'message' => 'You already have an in-progress workout. Please end it before starting a new one.',
            'confirmText' => 'OK',
            'cancelText' => 'Close',
            'confirmButtonClass' => 'primary',
            'show' => true,
        ];
        include __DIR__ . '/../utilities/confirm_modal.php';
    ?>
    
    <!-- Info Modal -->
    <div id="infoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Notice</h3>
                <span class="close" onclick="closeModal('infoModal')">&times;</span>
            </div>
            <div id="infoModalBody" style="padding: .5rem 0 1rem; color:#333;">
                <!-- Message injected by JS -->
            </div>
            <div style="display:flex; justify-content:flex-end; gap:.5rem;">
                <button class="btn btn-primary" onclick="closeModal('infoModal')">OK</button>
            </div>
        </div>
    </div>
    
    <!-- Add Exercise Modal -->
    <div id="addExerciseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Exercise to Routine</h3>
                <span class="close" onclick="closeModal('addExerciseModal')">&times;</span>
            </div>
            <div class="form-row" style="gap: 1rem; padding: 0 1rem 0.5rem;">
                <div class="form-group" style="flex:1;">
                    <label for="exercise_search">Search</label>
                    <input type="text" id="exercise_search" placeholder="Search exercises by name or equipment..." oninput="filterExerciseList()">
                </div>
                <div class="form-group" style="width: 240px;">
                    <label for="exercise_muscle_filter">Muscle Group</label>
                    <select id="exercise_muscle_filter" onchange="filterExerciseList()">
                        <option value="">All</option>
                        <?php foreach ($data['categories'] as $category): ?>
                            <option value="<?= strtolower($category) ?>"><?= htmlspecialchars($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-exercises">
                <?php foreach ($data['categories'] as $category): ?>
                    <h4 style="margin-top: 1rem;"><?= htmlspecialchars($category) ?></h4>
                    <?php 
                    $categoryExercises = array_filter($data['all_exercises'], function($ex) use ($category) {
                        return strcasecmp($ex['muscle_group'], $category) === 0;
                    });
                    ?>
                    <?php foreach ($categoryExercises as $exercise): ?>
                        <div class="exercise-select-item" 
                             data-muscle="<?= strtolower($exercise['muscle_group']) ?>" 
                             data-name="<?= htmlspecialchars(strtolower($exercise['name']), ENT_QUOTES) ?>"
                             data-equipment="<?= htmlspecialchars(strtolower($exercise['equipment']), ENT_QUOTES) ?>"
                             onclick="selectExercise(<?= $exercise['exercise_id'] ?>, '<?= htmlspecialchars($exercise['name'], ENT_QUOTES) ?>')">
                            <strong><?= htmlspecialchars($exercise['name']) ?></strong>
                            <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: #666;">
                                <?= htmlspecialchars($exercise['equipment']) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Exercise Details Modal -->
    <div id="exerciseDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="exerciseModalTitle">Exercise Details</h3>
                <span class="close" onclick="closeModal('exerciseDetailsModal')">&times;</span>
            </div>
            <form id="exerciseDetailsForm">
                <input type="hidden" name="action" value="add_exercise">
                <input type="hidden" name="routine_id" value="<?= $routine_id ?>">
                <input type="hidden" name="exercise_id" id="selected_exercise_id">
                <input type="hidden" name="routine_exercise_id" id="routine_exercise_id">
                
                <div class="form-group">
                    <label>Exercise</label>
                    <p id="selected_exercise_name" style="font-weight: bold; margin: 0.5rem 0;"></p>
                </div>
                
                <div class="form-group">
                    <label for="exercise_notes">Notes</label>
                    <textarea id="exercise_notes" name="notes" rows="3" placeholder="Add any notes..."></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('exerciseDetailsModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Exercise</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function openAddExerciseModal() {
            openModal('addExerciseModal');
        }

        function startRoutine(routineId) {
            const fd = new FormData();
            fd.append('action', 'start_routine');
            fd.append('routine_id', routineId);
            fetch('', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(d => {
                if (d.success && d.workout_id) {
                    window.location.href = `workout_detail.php?id=${d.workout_id}`;
                } else {
                    const modalId = 'start-blocked-modal';
                    // Inject server message into the reusable confirm modal
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        const msgEl = modal.querySelector('.confirm-modal-message');
                        if (msgEl) msgEl.textContent = d.message || 'Failed to start routine';
                        // Confirm just closes the modal
                        window.confirmModalActions = window.confirmModalActions || {};
                        window.confirmModalActions[modalId] = function(){};
                        modal.style.display = 'flex';
                    } else {
                        // Fallback
                        showInfoModal(d.message || 'Failed to start routine');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                const modalId = 'start-blocked-modal';
                const modal = document.getElementById(modalId);
                if (modal) {
                    const msgEl = modal.querySelector('.confirm-modal-message');
                    if (msgEl) msgEl.textContent = 'Network error';
                    window.confirmModalActions = window.confirmModalActions || {};
                    window.confirmModalActions[modalId] = function(){};
                    modal.style.display = 'flex';
                } else {
                    showInfoModal('Network error');
                }
            });
        }
        
        function selectExercise(exerciseId, exerciseName) {
            document.getElementById('selected_exercise_id').value = exerciseId;
            document.getElementById('selected_exercise_name').textContent = exerciseName;
            document.getElementById('exerciseModalTitle').textContent = 'Add Exercise';
            document.getElementById('exerciseDetailsForm').querySelector('[name="action"]').value = 'add_exercise';
            document.getElementById('routine_exercise_id').value = '';
            document.getElementById('exercise_notes').value = '';
            closeModal('addExerciseModal');
            openModal('exerciseDetailsModal');
        }
        
        function editExercise(routineExerciseId, notes) {
            document.getElementById('exerciseModalTitle').textContent = 'Edit Exercise';
            document.getElementById('exerciseDetailsForm').querySelector('[name="action"]').value = 'update_exercise';
            document.getElementById('routine_exercise_id').value = routineExerciseId;
            document.getElementById('exercise_notes').value = notes || '';
            
            // Hide exercise selection fields for edit
            document.getElementById('selected_exercise_id').disabled = true;
            openModal('exerciseDetailsModal');
        }
        
        let pendingRemovalId = null;
        // Hide the confirm modal initially (component renders visible by default)
        document.addEventListener('DOMContentLoaded', function(){
            const m = document.getElementById('remove-exercise-confirm');
            if(m){ m.style.display = 'none'; }
            const sb = document.getElementById('start-blocked-modal');
            if(sb){ sb.style.display = 'none'; }
        });
        function removeExercise(routineExerciseId) {
            pendingRemovalId = routineExerciseId;
            const modalId = 'remove-exercise-confirm';
            // Assign confirm action using the reusable modal API
            window.confirmModalActions = window.confirmModalActions || {};
            window.confirmModalActions[modalId] = function(){
                if(!pendingRemovalId) return;
                const formData = new FormData();
                formData.append('action', 'remove_exercise');
                formData.append('routine_exercise_id', pendingRemovalId);
                fetch('', { method: 'POST', body: formData })
                .then(r=>r.json())
                .then(d=>{
                    if(d.success){ location.reload(); }
                    else { showInfoModal(d.message || 'Failed to remove exercise'); }
                })
                .catch(err=>{ console.error(err); showInfoModal('Network error'); })
                .finally(()=>{ pendingRemovalId = null; });
            };
            const modal = document.getElementById(modalId);
            if(modal){ modal.style.display = 'flex'; }
        }
        
        document.getElementById('exerciseDetailsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = formData.get('action');
            
            if (action === 'update_exercise') {
                formData.set('routine_exercise_id', document.getElementById('routine_exercise_id').value);
            }
            // Since sets/reps/weight are configured during workout, remove if present
            formData.delete('sets');
            formData.delete('reps');
            formData.delete('weight');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
        
        function filterExerciseList() {
            const q = (document.getElementById('exercise_search').value || '').toLowerCase();
            const mus = (document.getElementById('exercise_muscle_filter').value || '').toLowerCase();
            const items = document.querySelectorAll('#addExerciseModal .exercise-select-item');
            items.forEach(it => {
                const name = it.getAttribute('data-name') || '';
                const equip = it.getAttribute('data-equipment') || '';
                const muscle = it.getAttribute('data-muscle') || '';
                const matchesText = !q || name.includes(q) || equip.includes(q);
                const matchesMuscle = !mus || muscle === mus;
                it.style.display = (matchesText && matchesMuscle) ? 'block' : 'none';
            });
        }

        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        function showInfoModal(message){
            const modal = document.getElementById('infoModal');
            const body = document.getElementById('infoModalBody');
            if(body){ body.textContent = message || ''; }
            if(modal){ modal.style.display = 'block'; }
        }
    </script>
</body>
</html>
