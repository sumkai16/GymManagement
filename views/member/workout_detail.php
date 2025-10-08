<?php
session_start();
require_once '../../models/Workout.php';
require_once '../../models/Exercise.php';
require_once '../../config/database.php';

$db = (new Database())->getConnection();
$workoutModel = new Workout($db);
$exerciseModel = new Exercise($db);

$user_id = $_SESSION['user_id'] ?? 0;
$workout_id = $_GET['id'] ?? 0;

$workout = $workoutModel->getWorkoutById($workout_id, $user_id);
$exercises = $exerciseModel->getExercisesForWorkout($workout_id);

if (!$workout) {
    header('Location: workout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($workout['name']) ?> - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/workout_detail_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="content-wrapper">
            <div class="workout-detail">
                <!-- Workout Header -->
                <div class="workout-header">
                    <div class="workout-title">
                        <h1><?= htmlspecialchars($workout['name']) ?></h1>
                        <div>
                            <a href="workout.php" class="btn btn-secondary">
                                <i class='bx bx-arrow-back'></i> Back
                            </a>
                        </div>
                    </div>
                    
                    <div class="workout-meta">
                        <div class="meta-item">
                            <h4><?= date('M j, Y', strtotime($workout['workout_date'])) ?></h4>
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
                            <h4><?= $workout['routine_name'] ?: 'Custom' ?></h4>
                            <p>Routine</p>
                        </div>
                    </div>
                    
                    <?php if ($workout['notes']): ?>
                        <div class="workout-notes">
                            <h4>Notes</h4>
                            <p><?= htmlspecialchars($workout['notes']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Exercises Section -->
                <div class="exercises-section">
                    <h2>Exercises</h2>
                    
                    <?php if (!empty($exercises)): ?>
                        <?php foreach ($exercises as $exercise): ?>
                            <div class="exercise-item">
                                <div class="exercise-header">
                                    <div class="exercise-info">
                                        <h3><?= htmlspecialchars($exercise['exercise_name']) ?></h3>
                                        <p><?= htmlspecialchars($exercise['category']) ?></p>
                                    </div>
                                </div>
                                
                                <div class="exercise-details">
                                    <div class="exercise-stats">
                                        <div class="stat-item">
                                            <h5><?= $exercise['sets'] ?></h5>
                                            <p>Sets</p>
                                        </div>
                                        <div class="stat-item">
                                            <h5><?= $exercise['reps'] ?></h5>
                                            <p>Reps</p>
                                        </div>
                                        <?php if ($exercise['weight']): ?>
                                            <div class="stat-item">
                                                <h5><?= $exercise['weight'] ?>kg</h5>
                                                <p>Weight</p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($exercise['duration']): ?>
                                            <div class="stat-item">
                                                <h5><?= $exercise['duration'] ?>min</h5>
                                                <p>Duration</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($exercise['notes']): ?>
                                        <div style="background: #f9fafb; padding: 0.75rem; border-radius: 6px; margin-top: 0.5rem;">
                                            <strong>Notes:</strong> <?= htmlspecialchars($exercise['notes']) ?>
                                        </div>
                                    <?php endif; ?>
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
</body>
</html>
