<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle AJAX actions first to keep JSON clean
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ini_set('display_errors', '0');
    error_reporting(0);
    while (ob_get_level()) { ob_end_clean(); }
    require_once '../../controllers/WorkoutController.php';
    $controller = new WorkoutController();
    $resp = $controller->handleWorkoutTracking();
    header('Content-Type: application/json');
    echo json_encode($resp);
    exit;
}

require_once '../../config/database.php';
require_once '../../models/Workout.php';
require_once '../../models/Exercise.php';

$db = (new Database())->getConnection();
$workoutModel = new Workout($db);
$exerciseModel = new Exercise($db);

$user_id = $_SESSION['user_id'] ?? 0;
$workout_id = $_GET['id'] ?? 0;

$workout = $workoutModel->getWorkoutById($workout_id, $user_id);
$exercises = $exerciseModel->getExercisesForWorkout($workout_id);
$isEnded = !empty($workout['end_time'] ?? '');

if (!$workout) {
    header('Location: workouts.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($workout['workout_name'] ?? 'Workout') ?> - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_detail_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include '../components/dynamic_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="content-wrapper">
            <div class="workout-detail">
                <!-- Workout Header -->
                <div class="workout-header">
                    <div class="workout-title" style="align-items:center; gap:.5rem;">
                        <h1><?= htmlspecialchars($workout['workout_name'] ?? 'Workout') ?></h1>
                        <div style="display:flex; gap:.5rem;">
                            <?php if (empty($workout['end_time'] ?? '')): ?>
                                <button class="btn btn-primary" onclick="endWorkout(<?= (int)$workout_id ?>)">
                                    <i class='bx bx-check-circle'></i> End Workout
                                </button>
                            <?php endif; ?>
                            <a href="workouts.php" class="btn btn-secondary">
                                <i class='bx bx-arrow-back'></i> Back
                            </a>
                        </div>
                    </div>
                    
                    <div class="workout-meta">
                        <div class="meta-item">
                            <h4><?= isset($workout['created_at']) ? date('M j, Y', strtotime($workout['created_at'])) : '' ?></h4>
                            <p>Date</p>
                        </div>
                        <div class="meta-item">
                            <h4><?= count($exercises) ?></h4>
                            <p>Exercises</p>
                        </div>
                        <div class="meta-item">
                            <h4><?= array_sum(array_column($exercises, 'sets')) ?></h4>
                            <p>Total Sets</p>
                        </div>
                        <div class="meta-item">
                            <?php 
                                $createdTs = isset($workout['created_at']) ? strtotime($workout['created_at']) : null;
                                $endTs = !empty($workout['end_time'] ?? '') ? strtotime($workout['end_time']) : null;
                                $nowTs = time();
                                $durationSec = 0;
                                if ($createdTs) {
                                    $baseEnd = $endTs ?: $nowTs;
                                    $durationSec = max(0, $baseEnd - $createdTs);
                                }
                                $h = intdiv($durationSec, 3600);
                                $m = intdiv($durationSec % 3600, 60);
                                $s = $durationSec % 60;
                                $durationLabel = $h > 0 ? sprintf('%d:%02d:%02d', $h, $m, $s) : sprintf('%d:%02d', $m, $s);
                            ?>
                            <h4 id="duration_value" data-created="<?= (int)($createdTs ?? 0) ?>" data-end="<?= (int)($endTs ?? 0) ?>" data-initial="<?= (int)$durationSec ?>"><?= $durationLabel ?></h4>
                            <p>Duration</p>
                        </div>
                        <div class="meta-item">
                            <h4><?= !empty($workout['routine_name']) ? htmlspecialchars($workout['routine_name']) : 'Custom' ?></h4>
                            <p>Routine</p>
                        </div>
                    </div>
                    
                    <?php if (!empty($workout['notes'] ?? '')): ?>
                        <div class="workout-notes">
                            <h4>Notes</h4>
                            <p><?= htmlspecialchars($workout['notes'] ?? '') ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Exercises Section -->
                <div class="exercises-section">
                    <h2>Exercises</h2>
                    
                    <?php if (!empty($exercises)): ?>
                        <?php foreach ($exercises as $exercise): ?>
                            <div class="exercise-item" data-we-id="<?= (int)$exercise['id'] ?>">
                                <div class="exercise-header" style="display:flex; justify-content: space-between; align-items:center; gap:.5rem;">
                                    <div class="exercise-info">
                                        <h3><?= htmlspecialchars($exercise['exercise_name']) ?></h3>
                                        <p><?= htmlspecialchars($exercise['muscle_group'] ?? '') ?></p>
                                    </div>
                                    <div>
                                        <?php if (!$isEnded): ?>
                                            <button class="btn <?= !empty($exercise['is_done']) ? 'btn-secondary' : 'btn-primary' ?>" onclick="toggleDone(<?= (int)$exercise['id'] ?>, <?= !empty($exercise['is_done']) ? 0 : 1 ?>)">
                                                <i class='bx <?= !empty($exercise['is_done']) ? 'bx-undo' : 'bx-check' ?>'></i> <?= !empty($exercise['is_done']) ? 'Undo' : 'Done' ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="exercise-details">
                                    <?php $sets = $exerciseModel->getSetsForExercise($exercise['id']); ?>
                                    <div class="exercise-sets" id="sets_container_<?= (int)$exercise['id'] ?>" style="margin-top: .25rem;">
                                        <h4 style="margin: .25rem 0;">Sets</h4>
                                        <?php if (!empty($sets)): ?>
                                            <?php foreach ($sets as $set): ?>
                                                <div class="set-row" data-set-row="we-<?= (int)$exercise['id'] ?>" style="display:flex; gap:.5rem; align-items:center; margin:.35rem 0;">
                                                    <span style="width:2rem;">#<?= (int)$set['set_number'] ?></span>
                                                    <label style="font-size:.8rem;color:#666;width:3rem;">Reps</label>
                                                    <input type="number" id="reps_wes_<?= (int)$set['wes_id'] ?>" value="<?= htmlspecialchars((string)($set['reps'] ?? '')) ?>" min="0" style="width:80px;" <?= $isEnded ? 'disabled' : '' ?>>
                                                    <label style="font-size:.8rem;color:#666;width:4rem;">Weight</label>
                                                    <input type="number" step="0.1" min="0" id="weight_wes_<?= (int)$set['wes_id'] ?>" value="<?= htmlspecialchars((string)($set['weight'] ?? '')) ?>" style="width:100px;" <?= $isEnded ? 'disabled' : '' ?>>
                                                    <?php if (!$isEnded): ?>
                                                        <button class="btn btn-primary" onclick="saveSet(<?= (int)$set['wes_id'] ?>)"><i class='bx bx-save'></i></button>
                                                        <button class="btn btn-danger" onclick="removeSet(<?= (int)$set['wes_id'] ?>)"><i class='bx bx-trash'></i></button>
                                                        <button class="btn <?= !empty($set['is_done']) ? 'btn-secondary' : 'btn-outline' ?>" onclick="toggleSetDone(<?= (int)$set['wes_id'] ?>, <?= !empty($set['is_done']) ? 0 : 1 ?>)">
                                                            <i class='bx <?= !empty($set['is_done']) ? 'bx-undo' : 'bx-check' ?>'></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <span id="save_ind_<?= (int)$set['wes_id'] ?>" style="display:none; color:#10b981; font-weight:600; font-size:.85rem; margin-left:.25rem;">Saved</span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="no-sets" style="color:#666; font-size:.9rem;">No sets yet.</div>
                                        <?php endif; ?>
                                        <?php if (!$isEnded): ?>
                                            <div style="margin-top:.5rem;">
                                                <button class="btn btn-secondary" onclick="addSet(<?= (int)$exercise['id'] ?>)"><i class='bx bx-plus'></i> Add Set</button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-exercises">
                            <i class='bx bx-dumbbell'></i>
                            <h3>No exercises recorded</h3>
                            <p>This workout doesn't have any exercises yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php 
        $confirmData = [
            'id' => 'remove-set-confirm',
            'title' => 'Remove Set',
            'message' => 'Are you sure you want to remove this set?',
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmButtonClass' => 'danger',
            'show' => true,
        ];
        include __DIR__ . '/../utilities/confirm_modal.php';
    ?>
    <script>
        const WORKOUT_ENDED = <?= $isEnded ? 'true' : 'false' ?>;
        // Live duration
        (function(){
            if (!WORKOUT_ENDED) {
                const el = document.getElementById('duration_value');
                if (el) {
                    let sec = parseInt(el.getAttribute('data-initial') || '0', 10);
                    const created = parseInt(el.getAttribute('data-created') || '0', 10);
                    const useCreated = !(sec >= 0);
                    const tick = () => {
                        let currentSec = sec;
                        if (useCreated && created > 0) {
                            const now = Math.floor(Date.now() / 1000);
                            currentSec = Math.max(0, now - created);
                        }
                        const h = Math.floor(currentSec / 3600);
                        const m = Math.floor((currentSec % 3600) / 60);
                        const s = currentSec % 60;
                        el.textContent = h > 0 ? `${h}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}` : `${m}:${String(s).padStart(2,'0')}`;
                        if (!useCreated) { sec++; }
                    };
                    tick();
                    setInterval(tick, 1000);
                }
            }
        })();

        function endWorkout(workoutId){
            const fd = new FormData();
            fd.append('action','end_workout');
            fd.append('workout_id', workoutId);
            fetch('', {method:'POST', body: fd})
            .then(r=>r.json())
            .then(d=>{
                if(d.success){
                    location.reload();
                } else {
                    alert(d.message || 'Failed to end workout');
                }
            })
            .catch(e=>{console.error(e); alert('Network error');});
        }

        function saveSet(wesId){
            if (WORKOUT_ENDED) { alert('Workout has ended. Edits are disabled.'); return; }
            const reps = document.getElementById(`reps_wes_${wesId}`).value;
            const weight = document.getElementById(`weight_wes_${wesId}`).value;
            const fd = new FormData();
            fd.append('action','update_set');
            fd.append('wes_id', wesId);
            fd.append('reps', reps);
            fd.append('weight', weight);
            fetch('', {method:'POST', body: fd})
            .then(r=>r.json())
            .then(d=>{
                const ind = document.getElementById(`save_ind_${wesId}`);
                if(d.success){
                    if(ind){ ind.style.display = 'inline'; setTimeout(()=>{ ind.style.display = 'none'; }, 1500); }
                } else {
                    alert(d.message || 'Failed to save set');
                }
            })
            .catch(e=>{console.error(e); alert('Network error');});
        }

        let pendingRemoveSetId = null;
        document.addEventListener('DOMContentLoaded', function(){
            const m = document.getElementById('remove-set-confirm');
            if(m){ m.style.display = 'none'; }
        });
        function removeSet(wesId){
            if (WORKOUT_ENDED) { alert('Workout has ended. Edits are disabled.'); return; }
            pendingRemoveSetId = wesId;
            const modalId = 'remove-set-confirm';
            window.confirmModalActions = window.confirmModalActions || {};
            window.confirmModalActions[modalId] = function(){
                const fd = new FormData();
                fd.append('action','remove_set');
                fd.append('wes_id', pendingRemoveSetId);
                fetch('', {method:'POST', body: fd})
                .then(r=>r.json())
                .then(d=>{
                    if(d.success){
                        location.reload();
                    }else{
                        alert(d.message || 'Failed to remove set');
                    }
                })
                .catch(e=>{console.error(e); alert('Network error');})
                .finally(()=>{ pendingRemoveSetId = null; });
            };
            const modal = document.getElementById(modalId);
            if(modal){ modal.style.display = 'flex'; }
        }

        function toggleDone(weId, isDone){
            const fd = new FormData();
            fd.append('action','set_exercise_done');
            fd.append('workout_exercise_id', weId);
            fd.append('is_done', isDone);
            fetch('', {method:'POST', body: fd})
            .then(r=>r.text())
            .then(txt=>{
                try{
                    const d = JSON.parse(txt);
                    if(d.success){
                        location.reload();
                    }else{
                        alert(d.message || 'Failed to update');
                    }
                }catch(err){
                    console.error('Non-JSON response:', txt);
                    alert('Server error (non-JSON). Check console');
                }
            })
            .catch(e=>{console.error(e); alert('Network error');});
        }

        function toggleSetDone(wesId, isDone){
            if (WORKOUT_ENDED) { alert('Workout has ended. Edits are disabled.'); return; }
            const fd = new FormData();
            fd.append('action','set_set_done');
            fd.append('wes_id', wesId);
            fd.append('is_done', isDone);
            fetch('', {method:'POST', body: fd})
            .then(r=>r.text())
            .then(txt=>{
                try{
                    const d = JSON.parse(txt);
                    if(d.success){
                        location.reload();
                    }else{
                        alert(d.message || 'Failed to update');
                    }
                }catch(err){
                    console.error('Non-JSON response:', txt);
                    alert('Server error (non-JSON). Check console');
                }
            })
            .catch(e=>{console.error(e); alert('Network error');});
        }

        function addSet(weId){
            if (WORKOUT_ENDED) { alert('Workout has ended. Edits are disabled.'); return; }
            const container = document.getElementById(`sets_container_${weId}`);
            const current = container ? container.querySelectorAll('[data-set-row]') : [];
            const nextNum = (current ? current.length : 0) + 1;
            const fd = new FormData();
            fd.append('action','add_set');
            fd.append('workout_exercise_id', weId);
            fd.append('set_number', nextNum);
            fetch('', {method:'POST', body: fd})
            .then(r=>r.text())
            .then(txt=>{
                try{
                    const d = JSON.parse(txt);
                    if(d.success){
                        location.reload();
                    }else{
                        alert(d.message || 'Failed to add set');
                    }
                }catch(err){
                    console.error('Non-JSON response:', txt);
                    alert('Server error (non-JSON). Check console');
                }
            })
            .catch(e=>{console.error(e); alert('Network error');});
        }
    </script>
</body>
</html>
