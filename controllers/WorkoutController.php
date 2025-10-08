<?php
require_once __DIR__ . '/../models/Workout.php';
require_once __DIR__ . '/../models/Exercise.php';
require_once __DIR__ . '/../models/WorkoutRoutine.php';
require_once __DIR__ . '/../config/database.php';

class WorkoutController {
    private $workoutModel;
    private $exerciseModel;
    private $routineModel;
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->workoutModel = new Workout($this->db);
        $this->exerciseModel = new Exercise($this->db);
        $this->routineModel = new WorkoutRoutine($this->db);
    }
    
    // Handle workout tracking
    public function handleWorkoutTracking() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user_id = $_SESSION['user_id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'start_workout':
                    return $this->startWorkout($user_id);
                case 'end_workout':
                    return $this->endWorkout($user_id);
                case 'add_exercise':
                    return $this->addExerciseToWorkout($user_id);
                case 'update_exercise':
                    return $this->updateExerciseInWorkout($user_id);
                case 'remove_exercise':
                    return $this->removeExerciseFromWorkout($user_id);
                case 'delete_workout':
                    return $this->deleteWorkout($user_id);
            }
        }
        
        return $this->getWorkoutData($user_id);
    }
    
    // Handle routine management
    public function handleRoutineManagement() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user_id = $_SESSION['user_id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create_routine':
                    return $this->createRoutine($user_id);
                case 'update_routine':
                    return $this->updateRoutine($user_id);
                case 'delete_routine':
                    return $this->deleteRoutine($user_id);
                case 'add_exercise_to_routine':
                    return $this->addExerciseToRoutine($user_id);
                case 'update_exercise_in_routine':
                    return $this->updateExerciseInRoutine($user_id);
                case 'remove_exercise_from_routine':
                    return $this->removeExerciseFromRoutine($user_id);
                case 'copy_routine':
                    return $this->copyRoutine($user_id);
            }
        }
        
        return $this->getRoutineData($user_id);
    }
    
    // Start new workout
    private function startWorkout($user_id) {
        $routine_id = $_POST['routine_id'] ?? null;
        $name = $_POST['name'] ?? 'Workout ' . date('Y-m-d H:i');
        $notes = $_POST['notes'] ?? '';
        
        $workout_id = $this->workoutModel->createWorkout($user_id, $routine_id, $name, $notes);
        
        if ($workout_id) {
            return ['success' => true, 'workout_id' => $workout_id, 'message' => 'Workout started successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to start workout'];
    }
    
    // End workout
    private function endWorkout($user_id) {
        $workout_id = $_POST['workout_id'] ?? 0;
        $end_time = $_POST['end_time'] ?? date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("UPDATE workouts SET end_time = :end_time WHERE id = :workout_id AND user_id = :user_id");
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Workout completed successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to end workout'];
    }
    
    // Add exercise to workout
    private function addExerciseToWorkout($user_id) {
        $workout_id = $_POST['workout_id'] ?? 0;
        $exercise_id = $_POST['exercise_id'] ?? 0;
        $sets = $_POST['sets'] ?? 0;
        $reps = $_POST['reps'] ?? 0;
        $weight = $_POST['weight'] ?? '';
        $duration = $_POST['duration'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        // Verify workout belongs to user
        $workout = $this->workoutModel->getWorkoutById($workout_id, $user_id);
        if (!$workout) {
            return ['success' => false, 'message' => 'Workout not found'];
        }
        
        $result = $this->exerciseModel->addExerciseToWorkout($workout_id, $exercise_id, $sets, $reps, $weight, $duration, $notes);
        
        if ($result) {
            return ['success' => true, 'message' => 'Exercise added successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to add exercise'];
    }
    
    // Update exercise in workout
    private function updateExerciseInWorkout($user_id) {
        $workout_exercise_id = $_POST['workout_exercise_id'] ?? 0;
        $sets = $_POST['sets'] ?? 0;
        $reps = $_POST['reps'] ?? 0;
        $weight = $_POST['weight'] ?? '';
        $duration = $_POST['duration'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        $result = $this->exerciseModel->updateExerciseInWorkout($workout_exercise_id, $sets, $reps, $weight, $duration, $notes);
        
        if ($result) {
            return ['success' => true, 'message' => 'Exercise updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update exercise'];
    }
    
    // Remove exercise from workout
    private function removeExerciseFromWorkout($user_id) {
        $workout_exercise_id = $_POST['workout_exercise_id'] ?? 0;
        
        $result = $this->exerciseModel->removeExerciseFromWorkout($workout_exercise_id);
        
        if ($result) {
            return ['success' => true, 'message' => 'Exercise removed successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to remove exercise'];
    }
    
    // Delete workout
    private function deleteWorkout($user_id) {
        $workout_id = $_POST['workout_id'] ?? 0;
        
        $result = $this->workoutModel->deleteWorkout($workout_id, $user_id);
        
        if ($result) {
            return ['success' => true, 'message' => 'Workout deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete workout'];
    }
    
    // Create routine
    private function createRoutine($user_id) {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $is_public = $_POST['is_public'] ?? 0;
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Routine name is required'];
        }
        
        $routine_id = $this->routineModel->createRoutine($user_id, $name, $description, $is_public);
        
        if ($routine_id) {
            return ['success' => true, 'routine_id' => $routine_id, 'message' => 'Routine created successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to create routine'];
    }
    
    // Update routine
    private function updateRoutine($user_id) {
        $routine_id = $_POST['routine_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $is_public = $_POST['is_public'] ?? 0;
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Routine name is required'];
        }
        
        $result = $this->routineModel->updateRoutine($routine_id, $user_id, $name, $description, $is_public);
        
        if ($result) {
            return ['success' => true, 'message' => 'Routine updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update routine'];
    }
    
    // Delete routine
    private function deleteRoutine($user_id) {
        $routine_id = $_POST['routine_id'] ?? 0;
        
        $result = $this->routineModel->deleteRoutine($routine_id, $user_id);
        
        if ($result) {
            return ['success' => true, 'message' => 'Routine deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete routine'];
    }
    
    // Add exercise to routine
    private function addExerciseToRoutine($user_id) {
        $routine_id = $_POST['routine_id'] ?? 0;
        $exercise_id = $_POST['exercise_id'] ?? 0;
        $sets = $_POST['sets'] ?? 0;
        $reps = $_POST['reps'] ?? 0;
        $weight = $_POST['weight'] ?? '';
        $duration = $_POST['duration'] ?? null;
        $notes = $_POST['notes'] ?? '';
        $order_index = $_POST['order_index'] ?? 0;
        
        // Verify routine belongs to user
        $routine = $this->routineModel->getRoutineById($routine_id, $user_id);
        if (!$routine) {
            return ['success' => false, 'message' => 'Routine not found'];
        }
        
        $result = $this->routineModel->addExerciseToRoutine($routine_id, $exercise_id, $sets, $reps, $weight, $duration, $notes, $order_index);
        
        if ($result) {
            return ['success' => true, 'message' => 'Exercise added to routine successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to add exercise to routine'];
    }
    
    // Update exercise in routine
    private function updateExerciseInRoutine($user_id) {
        $routine_exercise_id = $_POST['routine_exercise_id'] ?? 0;
        $sets = $_POST['sets'] ?? 0;
        $reps = $_POST['reps'] ?? 0;
        $weight = $_POST['weight'] ?? '';
        $duration = $_POST['duration'] ?? null;
        $notes = $_POST['notes'] ?? '';
        $order_index = $_POST['order_index'] ?? 0;
        
        $result = $this->routineModel->updateExerciseInRoutine($routine_exercise_id, $sets, $reps, $weight, $duration, $notes, $order_index);
        
        if ($result) {
            return ['success' => true, 'message' => 'Exercise updated in routine successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update exercise in routine'];
    }
    
    // Remove exercise from routine
    private function removeExerciseFromRoutine($user_id) {
        $routine_exercise_id = $_POST['routine_exercise_id'] ?? 0;
        
        $result = $this->routineModel->removeExerciseFromRoutine($routine_exercise_id);
        
        if ($result) {
            return ['success' => true, 'message' => 'Exercise removed from routine successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to remove exercise from routine'];
    }
    
    // Copy routine
    private function copyRoutine($user_id) {
        $routine_id = $_POST['routine_id'] ?? 0;
        
        $result = $this->routineModel->copyRoutineToUser($routine_id, $user_id);
        
        if ($result) {
            return ['success' => true, 'message' => 'Routine copied successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to copy routine'];
    }
    
    // Get workout data
    private function getWorkoutData($user_id) {
        $workouts = $this->workoutModel->getWorkoutsByUser($user_id);
        $exercises = $this->exerciseModel->getAllExercises();
        $routines = $this->routineModel->getRoutinesByUser($user_id);
        $stats = $this->workoutModel->getWorkoutStats($user_id);
        
        return [
            'workouts' => $workouts,
            'exercises' => $exercises,
            'routines' => $routines,
            'stats' => $stats
        ];
    }
    
    // Get routine data
    private function getRoutineData($user_id) {
        $routines = $this->routineModel->getRoutinesByUser($user_id);
        $public_routines = $this->routineModel->getPublicRoutines();
        $exercises = $this->exerciseModel->getAllExercises();
        $categories = $this->exerciseModel->getExerciseCategories();
        
        return [
            'routines' => $routines,
            'public_routines' => $public_routines,
            'exercises' => $exercises,
            'categories' => $categories
        ];
    }
}
?>
