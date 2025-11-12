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
$role = 'member';
include __DIR__ . '/../shared/workout_routine_detail.php';
