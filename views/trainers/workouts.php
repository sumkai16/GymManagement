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

            <!-- Tabs Navigation -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('routines')">
                    <i class='bx bx-list-ul'></i> My Routines
                </button>
                <button class="tab" onclick="switchTab('clients')">
                    <i class='bx bx-group'></i> Client Workouts
                </button>
                <button class="tab" onclick="switchTab('history')">
                    <i class='bx bx-history'></i> Workout History
                </button>
            </div>

            <!-- My Routines Tab -->
            <div id="routines" class="tab-content active">
                <div class="routines-container">
                    <div class="routines-section">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h3><i class='bx bx-list-ul'></i> My Workout Routines</h3>
                            <button class="btn btn-primary" onclick="openCreateRoutineModal()">
                                <i class='bx bx-plus'></i> New Routine
                            </button>
                        </div>
                        <div class="routine-list">
                            <?php if (!empty($routineData['routines'])): ?>
                                <?php foreach ($routineData['routines'] as $routine): ?>
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
                                                <button class="btn btn-sm btn-success" onclick="assignRoutine(<?= $routine['id'] ?>)">
                                                    <i class='bx bx-user-plus'></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteRoutine(<?= $routine['id'] ?>)">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
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
                </div>
            </div>

            <!-- Client Workouts Tab -->
            <div id="clients" class="tab-content">
                <h3><i class='bx bx-group'></i> Client Workout Progress</h3>
                <div class="client-grid">
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <div class="client-card">
                                <div class="client-header">
                                    <div class="client-info">
                                        <h4><?= htmlspecialchars($client['full_name']) ?></h4>
                                        <p><?= htmlspecialchars($client['email']) ?></p>
                                        <p><span class="role-badge role-<?= $client['status'] ?>"><?= ucfirst($client['status']) ?></span></p>
                                    </div>
                                    <div class="client-actions">
                                        <button class="btn btn-sm btn-primary" onclick="viewClientWorkout(<?= $client['member_id'] ?>)">
                                            <i class='bx bx-show'></i> View
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="assignWorkoutToClient(<?= $client['member_id'] ?>)">
                                            <i class='bx bx-plus'></i> Assign
                                        </button>
                                    </div>
                                </div>
                                <div class="client-stats">
                                    <div class="stat-item">
                                        <div class="value"><?= $client['membership_type'] ?></div>
                                        <div class="label">Plan</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="value"><?= date('M j', strtotime($client['start_date'])) ?></div>
                                        <div class="label">Started</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">
                            <i class='bx bx-user-x'></i>
                            <p>No clients assigned yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Workout History Tab -->
            <div id="history" class="tab-content">
                <div class="workout-container">
                    <h3><i class='bx bx-history'></i> Workout History</h3>
                    
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
            // This could open a modal to assign a workout or routine
            alert('Assign workout functionality coming soon!');
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
                    alert('An error occurred. Check console for details.');
                });
            }
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
