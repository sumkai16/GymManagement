<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}
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
                <h1>Workout History</h1>
                <p>Review past workouts. Click View to see performed sets, reps, and weights.</p>
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
            <h3><i class='bx bx-history'></i> All Workouts</h3>
            <div class="workout-container">
                
                    <?php if (!empty($data['workouts'])): ?>
                        <div class="history-wrap">
                            <div class="history-grid">
                            <?php foreach ($data['workouts'] as $workout): ?>
                                <?php 
                                    $created = $workout['created_at'] ?? null;
                                    $ended = $workout['end_time'] ?? null;
                                    $dur = 0;
                                    if ($created && $ended) {
                                        $dur = max(0, (int)ceil((strtotime($ended) - strtotime($created))/60));
                                    }
                                    $isInProgress = empty($ended);
                                    $statusLabel = $isInProgress ? 'In Progress' : 'Completed';
                                    $statusStyle = $isInProgress
                                        ? "background:#fde68a;color:#92400e;border-color:#fcd34d;"
                                        : "background:#dcfce7;color:#166534;border-color:#86efac;";
                                ?>
                                <div class="workout-card">
                                    <div class="card-title">
                                        <h4 style="margin:0; font-size:1.05rem; color:#223;"><?= htmlspecialchars($workout['workout_name'] ?? 'Workout') ?></h4>
                                        <?php if (!empty($workout['routine_name'])): ?>
                                            <a class="chip" href="workout_routine_detail.php?id=<?= (int)($workout['routine_id'] ?? 0) ?>">
                                                <i class='bx bx-list-ul'></i><?= htmlspecialchars($workout['routine_name']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="meta">
                                        <span title="Date"><i class='bx bx-calendar'></i> <?= $created ? date('M j, Y · g:i A', strtotime($created)) : '' ?></span>
                                        <?php if ($dur > 0): ?>
                                            <span title="Duration"><i class='bx bx-time'></i> <?= $dur ?>m</span>
                                        <?php endif; ?>
                                        <span class="chip" style="<?= $statusStyle ?>">
                                            <i class='bx bx-flag'></i> <?= $statusLabel ?>
                                        </span>
                                    </div>
                                    <div class="card-actions">
                                        <button class="btn btn-primary" onclick="viewWorkout(<?= (int)$workout['workout_id'] ?>)">
                                            <i class='bx bx-show'></i> View
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteWorkout(<?= (int)$workout['workout_id'] ?>)">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <i class='bx bx-dumbbell'></i>
                            <p>No workouts yet.</p>
                        </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php 
        $confirmData = [
            'id' => 'delete-workout-confirm',
            'title' => 'Delete Workout',
            'message' => 'Are you sure you want to delete this workout?',
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmButtonClass' => 'danger',
            'show' => true,
        ];
        include __DIR__ . '/../utilities/confirm_modal.php';
    ?>
    <script>
        // Modal functions
        function viewWorkout(workoutId) {
            // Redirect to workout detail page
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
    </script>
</body>
</html>
