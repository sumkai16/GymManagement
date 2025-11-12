<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../../controllers/WorkoutController.php';

$workoutController = new WorkoutController();
$data = $workoutController->handleWorkoutTracking();
$role = 'trainer';
include __DIR__ . '/../shared/workout_history.php';
