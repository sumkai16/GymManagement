<?php
require_once __DIR__ . '/workout_helpers.php';
// Unified Routine Detail View for both Trainer and Member
// Required variables: $role, $routine_id, $data
// $role must be 'trainer' or 'member'
// $data is as provided by WorkoutController->handleRoutineDetail
// $routine_id = routine id (int)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['routine']['name']) ?> - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_routines_styles.css">
    <link rel="stylesheet" href="../../assets/css/routine_detail.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php if ($role === 'trainer') {
        include '../utilities/alert.php';
        include '../components/dynamic_sidebar.php';
    } elseif ($role === 'member') {
        include '../components/dynamic_sidebar.php';
    } ?>
    <div class="main-content">
        <div class="content-wrapper">
            <!-- Modern Breadcrumb Bar -->
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?= $role === 'trainer' ? 'workouts.php' : 'workout_routines.php' ?>" class="breadcrumb-link">
                    <i class='bx bx-arrow-back'></i> <?= $role === 'trainer' ? 'Workouts' : 'Routines' ?>
                </a>
                <span class="breadcrumb-separator">❯</span>
                <span class="breadcrumb-current">Routine Details</span>
            </nav>
            <!-- Routine Header Card -->
            <section class="card" style="margin-bottom: 2rem; display:flex; align-items:center;justify-content:space-between;gap:2rem;">
                <div>
                    <h1 class="page-title">
                        <i class='bx bx-dumbbell' style="font-size:2.4rem;"></i> <?= htmlspecialchars($data['routine']['name']) ?>
                    </h1>
                    <p class="page-subtitle">
                        <?= htmlspecialchars($data['routine']['description'] ?? 'No description provided') ?>
                    </p>
                </div>
            </section>
            <!-- Meta Info (Stats) -->
            <section class="card" style="padding:0; margin-bottom:2rem;background:#fffefb;">
                <div class="routine-meta">
                    <div class="meta-item"><i class='bx bx-calendar'></i> <span><?= date('M j, Y', strtotime($data['routine']['created_at'])) ?></span></div>
                    <div class="meta-item"><i class='bx bx-dumbbell'></i> <span><?= count($data['routine_exercises']) ?> Exercises</span></div>
                    <?php if (!empty($data['routine']['is_public'])): ?><div class="meta-item"><i class='bx bx-globe'></i> <span>Public</span></div><?php endif; ?>
                    <div class="meta-item"><i class='bx bx-time'></i> <span>~<?php echo estimateWorkoutTime($data['routine_exercises']) ?> min</span></div>
                </div>
            </section>
            <!-- Quick Stats Cards -->
            <section class="card" style="margin-bottom:2.5rem;">
                <div class="quick-stats stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-target-lock'></i></div>
                        <div class="stat-content">
                            <h3><?= calculateTotalSets($data['routine_exercises']) ?></h3>
                            <p>Total Sets</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-repeat'></i></div>
                        <div class="stat-content">
                            <h3><?= calculateTotalReps($data['routine_exercises']) ?></h3>
                            <p>Total Reps</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-trending-up'></i></div>
                        <div class="stat-content">
                            <h3><?= calculateDifficulty($data['routine_exercises']) ?></h3>
                            <p>Difficulty</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class='bx bx-fire'></i></div>
                        <div class="stat-content">
                            <h3><?= estimateCalories($data['routine_exercises']) ?></h3>
                            <p>Est. Calories</p>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Action Toolbar / Button Bar -->
            <section class="card" style="margin-bottom:1.9rem;box-shadow:0 4px 22px rgba(0,0,0,.03);border:1.5px solid #e7eaf3;">
                <div class="action-buttons">
                    <div style="display:flex;gap:0.8rem;align-items:center;">
                        <button class="btn btn-primary" onclick="startRoutine(<?= (int)$data['routine']['id'] ?>)"><i class='bx bx-play'></i> Start <?= ($role === 'trainer') ? 'Workout' : 'Routine' ?></button>
                        <?php if (($role === 'trainer') || (!empty($data['can_edit']))): ?>
                            <button class="btn btn-success" onclick="openAddExerciseModal()"><i class='bx bx-plus-circle'></i> Add Exercise</button>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <!-- Exercises Section -->
            <section class="card" style="margin-bottom:2.5rem;">
                <div class="section-header">
                    <h2 style="color:var(--primary-color); font-size:1.4rem;font-weight:600;margin-bottom:0;">Exercises</h2>
                    <div class="section-controls" style="display:flex;align-items:center;gap:1rem;">
                        <select class="sort-select" onchange="sortExercises(this.value)"><option value="order">Default Order</option><option value="name">By Name</option></select>
                        <?php if ($role === 'trainer'): ?>
                        <button class="btn btn-sm btn-secondary" onclick="reorderExercises()"><i class='bx bx-drag-vertical'></i><span style="margin-left:2px;">Reorder</span></button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="exercises-container grid" id="exercisesContainer">
                    <?php if (!empty($data['routine_exercises'])): ?>
                        <?php foreach ($data['routine_exercises'] as $index => $exercise): ?>
                        <div class="exercise-card card" data-exercise-id="<?= ($exercise['exercise_id'] ?? $exercise['id']) ?>" data-order="<?= $index ?>">
                            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.75rem;">
                                <div class="exercise-number">
                                    <?= $index + 1 ?>
                                </div>
                                <div style="flex:1">
                                    <div style="display:flex;align-items:center;gap:0.46rem;margin-bottom:4px;">
                                        <h3 class="exercise-name"> <?= htmlspecialchars($exercise['name'] ?? $exercise['exercise_name']) ?> </h3>
                                        <span class="detail-badge" style="background:#e3f2fd;color:#1976d2;"> <?= htmlspecialchars($exercise['muscle_group'] ?? 'General') ?> </span>
                                        <span class="detail-badge" style="background:#fff5eb;color:#f59e0b;"> <?= htmlspecialchars($exercise['equipment'] ?? 'Bodyweight') ?> </span>
                                    </div>
                                    <?php if (!empty($exercise['notes'])): ?>
                                        <div class="exercise-notes"> <?= htmlspecialchars($exercise['notes']) ?> </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (($role === 'trainer') || (!empty($data['can_edit']))): ?>
                                <div style="display:flex;gap:0.5rem;justify-content:flex-end;margin-top:1rem;">
                                    <button class="btn btn-sm btn-edit" title="Edit" onclick="editExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['notes'] ?? '', ENT_QUOTES) ?>')"><i class='bx bx-edit'></i></button>
                                    <?php if ($role === 'trainer'): ?>
                                        <button class="btn btn-sm btn-info" title="Move" onclick="moveExercise(<?= $exercise['exercise_id'] ?? $exercise['id'] ?>)"><i class='bx bx-move'></i></button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-danger" title="Delete" onclick="removeExercise(<?= $exercise['id'] ?>)"><i class='bx bx-trash'></i></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class='bx bx-dumbbell'></i>
                            <h3>No exercises yet</h3>
                            <p>Start building this routine by adding exercises.</p>
                            <?php if (($role === 'trainer') || (!empty($data['can_edit']))): ?>
                            <button class="btn btn-primary" onclick="openAddExerciseModal()"><i class='bx bx-plus'></i> Add Your First Exercise</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <!-- TODO: Place notes, modals, and any remaining sections in matching card format for UI consistency. -->
        </div>
    </div>
    <?php 
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
    <div id="feedback-modal" class="feedback-modal-overlay" style="display:none;">
        <div class="feedback-modal">
            <div class="feedback-modal-header">
                <div class="feedback-modal-icon" id="feedback-modal-icon"></div>
                <h3 class="feedback-modal-title" id="feedback-modal-title">Notice</h3>
                <button class="feedback-modal-close" onclick="closeFeedbackModal()"><i class='bx bx-x'></i></button>
            </div>
            <div class="feedback-modal-body">
                <p id="feedback-modal-message"></p>
            </div>
            <div class="feedback-modal-footer">
                <button class="feedback-modal-btn" onclick="closeFeedbackModal()">OK</button>
            </div>
        </div>
    </div>
<!-- Add Exercise Modal -->
<div id="addExerciseModal" class="modal" tabindex="-1">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add Exercise to Routine</h3>
            <span class="close" onclick="closeModal('addExerciseModal')">&times;</span>
        </div>
        <div class="form-row" style="gap: 1rem; padding: 1rem 1.5rem 0.5rem;">
            <div class="form-group" style="flex:1;">
                <label for="exercise_search">Search</label>
                <input type="text" id="exercise_search" placeholder="Search exercises by name or equipment..." oninput="filterExerciseList()" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>
            <div class="form-group" style="width: 240px;">
                <label for="exercise_muscle_filter">Muscle Group</label>
                <select id="exercise_muscle_filter" onchange="filterExerciseList()" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px;">
                    <option value="">All</option>
                    <?php foreach ($data['categories'] as $category): ?>
                        <option value="<?= strtolower($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-exercises" style="max-height: 400px; overflow-y: auto; padding: 0 1.5rem;">
            <?php foreach ($data['categories'] as $category): ?>
                <h4 style="margin-top: 1rem; font-size: 1rem; font-weight: 600; color: #374151;"> <?= htmlspecialchars($category) ?> </h4>
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
                         onclick="selectExercise(<?= $exercise['exercise_id'] ?>, '<?= htmlspecialchars($exercise['name'], ENT_QUOTES) ?>')"
                         style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; margin-bottom: 0.5rem; cursor: pointer; transition: all 0.3s;">
                        <strong style="display: block; margin-bottom: 0.25rem;"> <?= htmlspecialchars($exercise['name']) ?> </strong>
                        <p style="margin: 0; font-size: 0.85rem; color: #6b7280;"> <?= htmlspecialchars($exercise['equipment']) ?> </p>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Exercise Details Modal -->
<div id="exerciseDetailsModal" class="modal" tabindex="-1">
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
                <textarea id="exercise_notes" name="notes" rows="4" placeholder="Add any notes..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('exerciseDetailsModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Exercise</button>
            </div>
        </form>
    </div>
</div>

<script>
const ROUTINE_ID = <?= (int)$routine_id ?>;
const USER_ROLE = '<?= $role ?>';
const EXISTING_EXERCISE_IDS = new Set(<?= json_encode(array_map(function($ex){
   return (int)($ex['exercise_id'] ?? $ex['id']);
}, $data['routine_exercises'])) ?>);
let pendingRemoveId = null;
document.addEventListener('DOMContentLoaded', function(){
    const removeModal = document.getElementById('remove-exercise-confirm');
    if(removeModal){ removeModal.style.display = 'none'; }
});
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
function openAddExerciseModal() {
    openModal('addExerciseModal');
}
function showFeedbackModal(message, type = 'info', title = 'Notice') {
    const overlay = document.getElementById('feedback-modal');
    if (!overlay) { alert(message); return; }
    overlay.classList.remove('success','error','warning','info');
    overlay.classList.add(type);
    const icon = document.getElementById('feedback-modal-icon');
    if (icon) {
        icon.innerHTML = type === 'success' ? "<i class='bx bx-check-circle'></i>" :
                         type === 'error' ? "<i class='bx bx-error-circle'></i>" :
                         type === 'warning' ? "<i class='bx bx-error'></i>" :
                         "<i class='bx bx-info-circle'></i>";
    }
    const titleEl = document.getElementById('feedback-modal-title');
    if (titleEl) titleEl.textContent = title;
    const msgEl = document.getElementById('feedback-modal-message');
    if (msgEl) msgEl.textContent = message;
    overlay.style.display = 'flex';
}
function closeFeedbackModal() {
    const overlay = document.getElementById('feedback-modal');
    if (overlay) overlay.style.display = 'none';
}
function selectExercise(exerciseId, exerciseName) {
    if (EXISTING_EXERCISE_IDS.has(Number(exerciseId))) {
        showFeedbackModal('This exercise is already part of the routine.', 'warning', 'Duplicate Exercise');
        return;
    }
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
    openModal('exerciseDetailsModal');
}
function removeExercise(routineExerciseId) {
    pendingRemoveId = routineExerciseId;
    const modalId = 'remove-exercise-confirm';
    window.confirmModalActions = window.confirmModalActions || {};
    window.confirmModalActions[modalId] = function(){
        const fd = new FormData();
        fd.append('action', 'remove_exercise');
        fd.append('routine_id', ROUTINE_ID);
        fd.append('routine_exercise_id', pendingRemoveId);
        fetch('../../controllers/WorkoutController.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                location.reload();
            } else {
                showFeedbackModal(d.message || 'Failed to remove exercise', 'error', 'Error');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showFeedbackModal('Network error. Please try again.', 'error', 'Error');
        })
        .finally(() => { pendingRemoveId = null; });
    };
    const modal = document.getElementById(modalId);
    if (modal) { modal.style.display = 'flex'; }
}
// START WORKOUT (AJAX)
function startRoutine(routineId) {
    // Client-side guard: ensure at least one exercise exists
    try {
        const exCount = document.querySelectorAll('#exercisesContainer .exercise-card').length;
        if (!exCount || exCount === 0) {
            showFeedbackModal('Please add at least one exercise before starting this routine.', 'warning', 'Cannot Start');
            return;
        }
    } catch (e) { /* ignore and fall back to server check */ }
    const fd = new FormData();
    fd.append('action', 'start_routine');
    fd.append('routine_id', routineId);
    fetch('../../controllers/WorkoutController.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && d.workout_id) {
            if (USER_ROLE === 'trainer') {
                window.location.href = `workout_detail.php?id=${d.workout_id}`;
            } else {
                window.location.href = `workout_detail.php?id=${d.workout_id}`;
            }
        } else {
            showFeedbackModal(d.message || 'Failed to start routine', 'error', 'Error');
        }
    })
    .catch(err => {
        console.error(err);
        showFeedbackModal('Network error. Please try again.', 'error', 'Error');
    });
}
// Modal - Add/Edit Exercise AJAX submit
const form = document.getElementById('exerciseDetailsForm');
if (form) {
form.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../../controllers/WorkoutController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('exerciseDetailsModal');
            location.reload();
        } else {
            showFeedbackModal(data.message || 'Failed to save exercise', 'error', 'Error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showFeedbackModal('An error occurred. Please try again.', 'error', 'Error');
    });
});
}
// Filtering in add exercise modal
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
        if (matchesText && matchesMuscle) {
            it.style.background = '#f8fafc';
            it.style.borderColor = '#d1d5db';
        }
    });
}
// Close any open modal if background is clicked
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    if (event.target && event.target.id === 'feedback-modal') {
        closeFeedbackModal();
    }
}
</script>
