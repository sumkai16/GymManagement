<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../../controllers/WorkoutController.php';

$routine_id = $_GET['id'] ?? 0;
$workoutController = new WorkoutController();
$data = $workoutController->handleRoutineDetail($routine_id);

if (!$data['routine']) {
    header('Location: workouts.php');
    exit;
}

// Helper functions for statistics
function calculateTotalSets($exercises) {
    $total = 0;
    foreach ($exercises as $exercise) {
        $total += $exercise['sets'] ?? 0;
    }
    return $total;
}

function calculateTotalReps($exercises) {
    $total = 0;
    foreach ($exercises as $exercise) {
        $total += ($exercise['sets'] ?? 0) * ($exercise['reps'] ?? 0);
    }
    return $total;
}

function calculateDifficulty($exercises) {
    if (empty($exercises)) return 'N/A';
    
    $totalScore = 0;
    foreach ($exercises as $exercise) {
        $score = 0;
        $score += ($exercise['sets'] ?? 0) * 2;
        $score += ($exercise['reps'] ?? 0) * 1;
        $score += ($exercise['weight'] ?? 0) * 0.5;
        $totalScore += $score;
    }
    
    $avgScore = $totalScore / count($exercises);
    
    if ($avgScore < 20) return 'Beginner';
    if ($avgScore < 40) return 'Intermediate';
    if ($avgScore < 60) return 'Advanced';
    return 'Expert';
}

function estimateWorkoutTime($exercises) {
    if (empty($exercises)) return 0;
    
    $totalTime = 0;
    foreach ($exercises as $exercise) {
        $sets = $exercise['sets'] ?? 0;
        $reps = $exercise['reps'] ?? 0;
        $duration = $exercise['duration'] ?? 0;
        
        if ($duration > 0) {
            $totalTime += $duration;
        } else {
            // Estimate time based on sets and reps (3 seconds per rep + 60 seconds rest per set)
            $totalTime += ($sets * $reps * 3) + ($sets * 60);
        }
    }
    
    return round($totalTime / 60); // Convert to minutes
}

function estimateCalories($exercises) {
    if (empty($exercises)) return 0;
    
    $totalCalories = 0;
    foreach ($exercises as $exercise) {
        $sets = $exercise['sets'] ?? 0;
        $reps = $exercise['reps'] ?? 0;
        $weight = $exercise['weight'] ?? 0;
        
        // Simple calorie estimation (rough calculation)
        $calories = ($sets * $reps * 0.1) + ($weight * $sets * 0.05);
        $totalCalories += $calories;
    }
    
    return round($totalCalories);
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
        /* Simple Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .close {
            color: white;
            font-size: 1.5rem;
            font-weight: 300;
            cursor: pointer;
            line-height: 1;
        }
        
        .close:hover {
            opacity: 0.7;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            padding: 1rem 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.95rem;
            background: white;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        /* Simple Button Styles */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        
        .btn-info {
            background: #3b82f6;
            color: white;
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        /* Page Layout */
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .breadcrumb-link {
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .breadcrumb-link:hover {
            text-decoration: underline;
        }
        
        .breadcrumb-separator {
            color: #9ca3af;
        }
        
        .breadcrumb-current {
            color: #6b7280;
            font-weight: 600;
        }
        
        .header-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        /* Routine Header */
        .routine-header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .routine-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .routine-header p {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }
        
        .routine-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .meta-item i {
            font-size: 1.1rem;
        }
        
        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .add-exercise-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .add-exercise-btn:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        }
        
        .view-options {
            display: flex;
            gap: 0.25rem;
        }
        
        .view-btn {
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #6b7280;
        }
        
        .view-btn:hover,
        .view-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        /* Statistics */
        .stats-section {
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .stat-content h3 {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        /* Exercises Section */
        .exercises-section {
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .section-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .section-controls {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        
        .sort-select {
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: white;
            font-size: 0.9rem;
            cursor: pointer;
        }
        
        .exercise-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .exercise-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .exercise-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .exercise-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .exercise-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .exercise-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .exercise-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-edit {
            background: #3b82f6;
            color: white;
        }
        
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        
        .btn-move {
            background: #f59e0b;
            color: white;
        }
        
        .exercise-details {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }
        
        .detail-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .exercise-notes {
            margin: 0.5rem 0 0 0;
            font-style: italic;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .exercise-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .detail-item {
            text-align: center;
            padding: 0.75rem 0.5rem;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .detail-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            font-weight: 500;
        }
        
        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        /* Notes Section */
        .notes-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .notes-content {
            margin-top: 1rem;
        }
        
        .notes-text {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid var(--primary-color);
            color: #4b5563;
            line-height: 1.6;
        }
        
        .no-notes {
            color: #9ca3af;
            font-style: italic;
            margin: 0;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #6b7280;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px dashed #d1d5db;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            display: block;
        }
        
        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                margin-bottom: 1.5rem;
            }
            
            .action-bar {
                flex-direction: column;
                gap: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .exercise-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .routine-header {
                padding: 1.5rem;
            }
            
            .routine-title {
                font-size: 1.3rem;
            }
            
            .routine-meta {
                gap: 1rem;
            }
            
            .exercise-card {
                padding: 1rem;
            }
            
            .exercise-details {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <?php include '../utilities/alert.php'; ?>
    <?php include '../components/dynamic_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <div class="header-content">
                    <div class="breadcrumb">
                        <a href="workouts.php" class="breadcrumb-link">
                            <i class='bx bx-arrow-back'></i> Back to Workouts
                        </a>
                        <span class="breadcrumb-separator">/</span>
                        <span class="breadcrumb-current">Routine Details</span>
                    </div>
                    <h1 class="page-title">Workout Routine Details</h1>
                    <p class="page-subtitle">Manage exercises and customize this training routine</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class='bx bx-printer'></i> Print
                    </button>
                    <button class="btn btn-info" onclick="shareRoutine()">
                        <i class='bx bx-share-alt'></i> Share
                    </button>
                </div>
            </div>

            <!-- Routine Overview Section -->
            <section class="routine-overview">
                <div class="routine-header">
                    <div class="routine-content">
                        <h2 class="routine-title"><?= htmlspecialchars($data['routine']['name']) ?></h2>
                        <p class="routine-description"><?= htmlspecialchars($data['routine']['description'] ?? 'No description provided') ?></p>
                        <div class="routine-meta">
                            <div class="meta-item">
                                <i class='bx bx-calendar'></i>
                                <span>Created: <?= date('M j, Y', strtotime($data['routine']['created_at'])) ?></span>
                            </div>
                            <div class="meta-item">
                                <i class='bx bx-dumbbell'></i>
                                <span><?= count($data['routine_exercises']) ?> Exercises</span>
                            </div>
                            <?php if ($data['routine']['is_public']): ?>
                            <div class="meta-item">
                                <i class='bx bx-globe'></i>
                                <span>Public</span>
                            </div>
                            <?php endif; ?>
                            <div class="meta-item">
                                <i class='bx bx-time'></i>
                                <span>~<?= estimateWorkoutTime($data['routine_exercises']) ?> min</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Action Bar -->
            <section class="action-bar">
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="startRoutine(<?= (int)$data['routine']['id'] ?>)">
                        <i class='bx bx-play'></i> Start Workout
                    </button>
                    <button class="add-exercise-btn" onclick="openAddExerciseModal()">
                        <i class='bx bx-plus-circle'></i> Add Exercise
                    </button>
                    <button class="btn btn-secondary" onclick="editRoutine()">
                        <i class='bx bx-edit'></i> Edit Routine
                    </button>
                    <button class="btn btn-info" onclick="duplicateRoutine()">
                        <i class='bx bx-copy'></i> Duplicate
                    </button>
                    <button class="btn btn-danger" onclick="deleteRoutine()">
                        <i class='bx bx-trash'></i> Delete Routine
                    </button>
                </div>
                <div class="view-options">
                    <button class="view-btn active" onclick="setView('grid')">
                        <i class='bx bx-grid'></i>
                    </button>
                    <button class="view-btn" onclick="setView('list')">
                        <i class='bx bx-list-ul'></i>
                    </button>
                </div>
            </section>

            <!-- Statistics Cards -->
            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class='bx bx-target-lock'></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Sets</h3>
                            <p class="stat-value"><?= calculateTotalSets($data['routine_exercises']) ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class='bx bx-repeat'></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Reps</h3>
                            <p class="stat-value"><?= calculateTotalReps($data['routine_exercises']) ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class='bx bx-trending-up'></i>
                        </div>
                        <div class="stat-content">
                            <h3>Difficulty</h3>
                            <p class="stat-value"><?= calculateDifficulty($data['routine_exercises']) ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class='bx bx-fire'></i>
                        </div>
                        <div class="stat-content">
                            <h3>Est. Calories</h3>
                            <p class="stat-value"><?= estimateCalories($data['routine_exercises']) ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Exercises Section -->
            <section class="exercises-section">
                <div class="section-header">
                    <h2>Exercises</h2>
                    <div class="section-controls">
                        <select class="sort-select" onchange="sortExercises(this.value)">
                            <option value="order">Default Order</option>
                            <option value="name">By Name</option>
                            <option value="sets">By Sets</option>
                            <option value="reps">By Reps</option>
                        </select>
                        <button class="btn btn-sm btn-secondary" onclick="reorderExercises()">
                            <i class='bx bx-drag-vertical'></i> Reorder
                        </button>
                    </div>
                </div>

                <div class="exercises-container" id="exercisesContainer">
                    <?php if (!empty($data['routine_exercises'])): ?>
                        <?php foreach ($data['routine_exercises'] as $index => $exercise): ?>
                            <div class="exercise-card" data-exercise-id="<?= $exercise['exercise_id'] ?>" data-order="<?= $index ?>">
                                <div class="exercise-header">
                                    <div class="exercise-info">
                                        <div class="exercise-number"><?= $index + 1 ?></div>
                                        <div>
                                            <h3 class="exercise-name"><?= htmlspecialchars($exercise['name']) ?></h3>
                                            <div class="exercise-details">
                                                <span class="detail-badge"><?= htmlspecialchars($exercise['muscle_group'] ?? 'General') ?></span>
                                                <span class="detail-badge"><?= htmlspecialchars($exercise['equipment'] ?? 'Bodyweight') ?></span>
                                            </div>
                                            <?php if (!empty($exercise['notes'])): ?>
                                                <p class="exercise-notes"><?= htmlspecialchars($exercise['notes']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="exercise-actions">
                                        <button class="btn-sm btn-edit" onclick="editExercise(<?= $exercise['id'] ?>, '<?= htmlspecialchars($exercise['notes'] ?? '', ENT_QUOTES) ?>')">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button class="btn-sm btn-move" onclick="moveExercise(<?= $exercise['exercise_id'] ?>)">
                                            <i class='bx bx-move'></i>
                                        </button>
                                        <button class="btn-sm btn-delete" onclick="removeExercise(<?= $exercise['id'] ?>)">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="exercise-stats">
                                    <div class="detail-item">
                                        <div class="detail-label">Sets</div>
                                        <div class="detail-value"><?= $exercise['sets'] ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Reps</div>
                                        <div class="detail-value"><?= $exercise['reps'] ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Weight</div>
                                        <div class="detail-value"><?= $exercise['weight'] ?? 'N/A' ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Duration</div>
                                        <div class="detail-value"><?= $exercise['duration'] ?? 'N/A' ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Rest</div>
                                        <div class="detail-value">60s</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class='bx bx-dumbbell'></i>
                            <h3>No exercises yet</h3>
                            <p>Start building this routine by adding exercises.</p>
                            <button class="btn btn-primary" onclick="openAddExerciseModal()">
                                <i class='bx bx-plus'></i> Add Your First Exercise
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Notes Section -->
            <section class="notes-section">
                <div class="section-header">
                    <h2>Trainer Notes</h2>
                    <button class="btn btn-sm btn-secondary" onclick="editNotes()">
                        <i class='bx bx-edit'></i> Edit Notes
                    </button>
                </div>
                <div class="notes-content">
                    <div class="notes-text" id="notesText">
                        <?= $data['routine']['notes'] ?? '<p class="no-notes">No trainer notes added yet. Click edit to add notes for this routine.</p>' ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Add Exercise Modal -->
    <div id="addExerciseModal" class="modal">
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
                    <h4 style="margin-top: 1rem; font-size: 1rem; font-weight: 600; color: #374151;"><?= htmlspecialchars($category) ?></h4>
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
                            <strong style="display: block; margin-bottom: 0.25rem;"><?= htmlspecialchars($exercise['name']) ?></strong>
                            <p style="margin: 0; font-size: 0.85rem; color: #6b7280;">
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
                    <p id="selected_exercise_name" style="font-weight: bold; margin: 0.5rem 0; color: #374151;"></p>
                </div>
                
                <div class="form-group">
                    <label for="exercise_sets">Sets</label>
                    <input type="number" id="exercise_sets" name="sets" min="1" value="3" required>
                </div>
                
                <div class="form-group">
                    <label for="exercise_reps">Reps</label>
                    <input type="number" id="exercise_reps" name="reps" min="1" value="10" required>
                </div>
                
                <div class="form-group">
                    <label for="exercise_weight">Weight (Optional)</label>
                    <input type="number" id="exercise_weight" name="weight" step="0.5" placeholder="0">
                </div>
                
                <div class="form-group">
                    <label for="exercise_duration">Duration (Optional)</label>
                    <input type="number" id="exercise_duration" name="duration" min="1" placeholder="0">
                </div>
                
                <div class="form-group">
                    <label for="exercise_notes">Notes</label>
                    <textarea id="exercise_notes" name="notes" rows="3" placeholder="Add any notes..." style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem; padding: 0 1.5rem 1.5rem;">
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
            fetch('../../controllers/WorkoutController.php', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(d => {
                if (d.success && d.workout_id) {
                    window.location.href = `workout_detail.php?id=${d.workout_id}`;
                } else {
                    alert(d.message || 'Failed to start routine');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error. Please try again.');
            });
        }
        
        function selectExercise(exerciseId, exerciseName) {
            document.getElementById('selected_exercise_id').value = exerciseId;
            document.getElementById('selected_exercise_name').textContent = exerciseName;
            document.getElementById('exerciseModalTitle').textContent = 'Add Exercise';
            document.getElementById('exerciseDetailsForm').querySelector('[name="action"]').value = 'add_exercise';
            document.getElementById('routine_exercise_id').value = '';
            document.getElementById('exercise_notes').value = '';
            document.getElementById('exercise_sets').value = '3';
            document.getElementById('exercise_reps').value = '10';
            document.getElementById('exercise_weight').value = '';
            document.getElementById('exercise_duration').value = '';
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
        
        function removeExercise(routineExerciseId) {
            if (confirm('Are you sure you want to remove this exercise from the routine?')) {
                const formData = new FormData();
                formData.append('action', 'remove_exercise');
                formData.append('routine_exercise_id', routineExerciseId);
                
                fetch('../../controllers/WorkoutController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to remove exercise');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
        
        document.getElementById('exerciseDetailsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = formData.get('action');
            
            if (action === 'update_exercise') {
                formData.set('routine_exercise_id', document.getElementById('routine_exercise_id').value);
            }
            
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
                    alert(data.message || 'Failed to save exercise');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
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
                if (matchesText && matchesMuscle) {
                    it.style.background = '#f8fafc';
                    it.style.borderColor = '#d1d5db';
                }
            });
        }
        
        // Add hover effect for exercise selection
        document.addEventListener('DOMContentLoaded', function() {
            const exerciseItems = document.querySelectorAll('.exercise-select-item');
            exerciseItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.background = '#e3f2fd';
                    this.style.borderColor = '#3b82f6';
                });
                item.addEventListener('mouseleave', function() {
                    if (this.style.display !== 'none') {
                        this.style.background = '#f8fafc';
                        this.style.borderColor = '#d1d5db';
                    }
                });
            });
        });
        
        function editRoutine() {
            // TODO: Implement edit routine functionality
            alert('Edit routine functionality coming soon!');
        }
        
        function duplicateRoutine() {
            if (confirm('Create a duplicate of this routine?')) {
                const formData = new FormData();
                formData.append('action', 'copy_routine');
                formData.append('routine_id', <?= $routine_id ?>);
                
                fetch('../../controllers/WorkoutController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'workout_routine_detail.php?id=' + data.routine_id;
                    } else {
                        alert(data.message || 'Failed to duplicate routine');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }
        
        function shareRoutine() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: '<?= htmlspecialchars($data['routine']['name']) ?>',
                    text: 'Check out this workout routine!',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    alert('Routine link copied to clipboard!');
                });
            }
        }
        
        function setView(viewType) {
            const container = document.getElementById('exercisesContainer');
            const buttons = document.querySelectorAll('.view-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.closest('.view-btn').classList.add('active');
            
            if (viewType === 'list') {
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.gap = '1rem';
            } else {
                container.style.display = 'grid';
                container.style.gridTemplateColumns = 'repeat(auto-fit, minmax(300px, 1fr))';
            }
        }
        
        function sortExercises(sortBy) {
            const container = document.getElementById('exercisesContainer');
            const exercises = Array.from(container.querySelectorAll('.exercise-card'));
            
            exercises.sort((a, b) => {
                switch(sortBy) {
                    case 'name':
                        const nameA = a.querySelector('.exercise-name').textContent;
                        const nameB = b.querySelector('.exercise-name').textContent;
                        return nameA.localeCompare(nameB);
                    case 'sets':
                        const setsA = parseInt(a.querySelector('.detail-value').textContent);
                        const setsB = parseInt(b.querySelector('.detail-value').textContent);
                        return setsB - setsA;
                    case 'reps':
                        const repsA = parseInt(a.querySelectorAll('.detail-value')[1].textContent);
                        const repsB = parseInt(b.querySelectorAll('.detail-value')[1].textContent);
                        return repsB - repsA;
                    default:
                        return 0;
                }
            });
            
            exercises.forEach(exercise => container.appendChild(exercise));
        }
        
        function reorderExercises() {
            alert('Drag and drop reordering coming soon!');
        }
        
        function moveExercise(exerciseId) {
            alert('Move exercise functionality coming soon!');
        }
        
        function editNotes() {
            const notesText = document.getElementById('notesText');
            const currentNotes = notesText.textContent.trim();
            
            const newNotes = prompt('Edit trainer notes:', currentNotes === 'No trainer notes added yet. Click edit to add notes for this routine.' ? '' : currentNotes);
            
            if (newNotes !== null) {
                // TODO: Save notes to database
                if (newNotes.trim() === '') {
                    notesText.innerHTML = '<p class="no-notes">No trainer notes added yet. Click edit to add notes for this routine.</p>';
                } else {
                    notesText.textContent = newNotes;
                }
                alert('Notes updated successfully!');
            }
        }
        
        function deleteRoutine() {
            if (confirm('Are you sure you want to delete this routine? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_routine');
                formData.append('routine_id', <?= $routine_id ?>);
                
                fetch('../../controllers/WorkoutController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'workouts.php';
                    } else {
                        alert(data.message || 'Failed to delete routine');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
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
