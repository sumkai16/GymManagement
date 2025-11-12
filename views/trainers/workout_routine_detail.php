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
$role = 'trainer';
include __DIR__ . '/../shared/workout_routine_detail.php';
