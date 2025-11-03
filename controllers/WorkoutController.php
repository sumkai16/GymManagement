<?php
require_once __DIR__ . '/../models/Workout.php';
require_once __DIR__ . '/../models/Exercise.php';
require_once __DIR__ . '/../models/WorkoutRoutine.php';
require_once __DIR__ . '/../config/database.php';

class WorkoutController {
    private $workoutModel;
    private $exerciseModel;
    public $routineModel;  // Made public for view access
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
                case 'set_exercise_done':
                    $we_id = (int)($_POST['workout_exercise_id'] ?? 0);
                    $done = (int)($_POST['is_done'] ?? 0);
                    if ($we_id <= 0) {
                        return ['success' => false, 'message' => 'Invalid exercise'];
                    }
                    $ok = $this->exerciseModel->setExerciseDone($we_id, $done);
                    return $ok ? ['success' => true] : ['success' => false, 'message' => 'Failed to update status'];
                // Per-set tracking actions
                case 'add_set':
                    $we_id = (int)($_POST['workout_exercise_id'] ?? 0);
                    $set_number = (int)($_POST['set_number'] ?? 1);
                    $reps = $_POST['reps'] ?? null;
                    $weight = $_POST['weight'] ?? null;
                    if ($we_id <= 0) return ['success' => false, 'message' => 'Invalid exercise'];
                    $id = $this->exerciseModel->addSetToExercise($we_id, $set_number, $reps, $weight);
                    return $id ? ['success' => true, 'wes_id' => $id] : ['success' => false, 'message' => 'Failed to add set'];
                case 'update_set':
                    $wes_id = (int)($_POST['wes_id'] ?? 0);
                    $reps = $_POST['reps'] ?? null;
                    $weight = $_POST['weight'] ?? null;
                    if ($wes_id <= 0) return ['success' => false, 'message' => 'Invalid set'];
                    $ok = $this->exerciseModel->updateSet($wes_id, $reps, $weight);
                    return $ok ? ['success' => true] : ['success' => false, 'message' => 'Failed to update set'];
                case 'remove_set':
                    $wes_id = (int)($_POST['wes_id'] ?? 0);
                    if ($wes_id <= 0) return ['success' => false, 'message' => 'Invalid set'];
                    $ok = $this->exerciseModel->removeSet($wes_id);
                    return $ok ? ['success' => true] : ['success' => false, 'message' => 'Failed to remove set'];
                case 'set_set_done':
                    $wes_id = (int)($_POST['wes_id'] ?? 0);
                    $done = (int)($_POST['is_done'] ?? 0);
                    if ($wes_id <= 0) return ['success' => false, 'message' => 'Invalid set'];
                    $ok = $this->exerciseModel->setSetDone($wes_id, $done);
                    return $ok ? ['success' => true] : ['success' => false, 'message' => 'Failed to update set'];
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
                    $response = $this->createRoutine($user_id);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                case 'update_routine':
                    $response = $this->updateRoutine($user_id);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                case 'delete_routine':
                    $response = $this->deleteRoutine($user_id);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                case 'add_exercise_to_routine':
                    $response = $this->addExerciseToRoutine($user_id);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                case 'update_exercise_in_routine':
                    $response = $this->updateExerciseInRoutine($user_id);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                case 'remove_exercise_from_routine':
                    $response = $this->removeExerciseFromRoutine($user_id);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
                case 'copy_routine':
                    $response = $this->copyRoutine($user_id);
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit;
            }
        }
        
        return $this->getRoutineData($user_id);
    }
    
    // Start new workout
    private function startWorkout($user_id) {
        $routine_id = $_POST['routine_id'] ?? null;
        // Use date-only for default name (no time)
        $name = $_POST['name'] ?? ('Workout ' . date('Y-m-d'));
        $notes = $_POST['notes'] ?? '';
        
        // Prevent starting if there is an in-progress workout (no end_time)
        $check = $this->db->query("SELECT workout_id FROM workouts WHERE end_time IS NULL LIMIT 1");
        if ($check && $check->fetch(PDO::FETCH_ASSOC)) {
            return ['success' => false, 'message' => 'You already have an in-progress workout. Please end it before starting a new one.'];
        }
        
        $workout_id = $this->workoutModel->createWorkout($user_id, $routine_id, $name, $notes);
        
        if ($workout_id) {
            return ['success' => true, 'workout_id' => $workout_id, 'message' => 'Workout started successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to start workout'];
    }
    
    // End workout
    private function endWorkout($user_id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        try {
            $workout_id = (int)($_POST['workout_id'] ?? 0);
            if ($workout_id <= 0) {
                return ['success' => false, 'message' => 'Invalid workout'];
            }
            // Mark workout end time using DB server time to ensure consistency with created_at (current_timestamp)
            $stmt = $this->db->prepare("UPDATE workouts SET end_time = NOW() WHERE workout_id = :workout_id");
            $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
            if (!$stmt->execute()) {
                return ['success' => false, 'message' => 'Failed to end workout'];
            }
            
            // Compute duration in minutes (created_at to end_time)
            $createdAt = null;
            $wq = $this->db->prepare("SELECT created_at FROM workouts WHERE workout_id = :wid");
            $wq->bindParam(':wid', $workout_id, PDO::PARAM_INT);
            if ($wq->execute()) {
                $row = $wq->fetch(PDO::FETCH_ASSOC);
                if ($row && !empty($row['created_at'])) {
                    $createdAt = $row['created_at'];
                }
            }
            $durationMinutes = 0;
            if ($createdAt) {
                // Re-fetch the stored end_time (set by DB NOW()) to be precise
                $eq = $this->db->prepare("SELECT end_time FROM workouts WHERE workout_id = :wid");
                $eq->bindParam(':wid', $workout_id, PDO::PARAM_INT);
                $endTimeVal = null;
                if ($eq->execute()) {
                    $erow = $eq->fetch(PDO::FETCH_ASSOC);
                    if ($erow && !empty($erow['end_time'])) { $endTimeVal = $erow['end_time']; }
                }
                $endTs = $endTimeVal ? strtotime($endTimeVal) : time();
                $durationMinutes = (int)ceil(($endTs - strtotime($createdAt)) / 60);
                if ($durationMinutes < 0) { $durationMinutes = 0; }
            }

            // Insert logs per set if available; fallback to per exercise
            $member_id = isset($_SESSION['member_id']) ? (int)$_SESSION['member_id'] : (int)($_SESSION['user_id'] ?? 0);
            // Try per-set
            $perSet = $this->db->prepare("INSERT INTO workout_logs (member_id, workout_id, exercise_id, sets, reps, weight, duration, log_date)
                SELECT :member_id, we.workout_id, we.exercise_id, 1, wes.reps, wes.weight, :duration, CURDATE()
                FROM workout_exercises we
                JOIN workout_exercise_sets wes ON wes.we_id = we.we_id
                WHERE we.workout_id = :workout_id");
            $perSet->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $perSet->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
            $perSet->bindParam(':duration', $durationMinutes, PDO::PARAM_INT);
            $perSet->execute();
            if ($perSet->rowCount() === 0) {
                // Fallback per-exercise
                $perEx = $this->db->prepare("INSERT INTO workout_logs (member_id, workout_id, exercise_id, sets, reps, weight, duration, log_date)
                    SELECT :member_id, we.workout_id, we.exercise_id, we.sets, we.reps, we.weight, :duration, CURDATE()
                    FROM workout_exercises we
                    WHERE we.workout_id = :workout_id");
                $perEx->bindParam(':member_id', $member_id, PDO::PARAM_INT);
                $perEx->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
                $perEx->bindParam(':duration', $durationMinutes, PDO::PARAM_INT);
                $perEx->execute();
            }
            
            return ['success' => true, 'message' => 'Workout completed successfully'];
        } catch (Exception $e) {
            // Return a JSON-safe error without leaking HTML
            return ['success' => false, 'message' => 'Database error: '.$e->getMessage()];
        }
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
        try {
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
        } catch (Exception $e) {
            error_log("Create routine error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
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
        $sets = $_POST['sets'] ?? 3;
        $reps = $_POST['reps'] ?? 10;
        $weight = $_POST['weight'] ?? null;
        $notes = $_POST['notes'] ?? '';
        $order_index = $_POST['order_index'] ?? 0;
        
        // Verify routine belongs to user
        $routine = $this->routineModel->getRoutineById($routine_id, $user_id);
        if (!$routine) {
            return ['success' => false, 'message' => 'Routine not found'];
        }
        
        $result = $this->routineModel->addExerciseToRoutine($routine_id, $exercise_id, $sets, $reps, $weight, $notes, $order_index);
        
        if ($result) {
            return ['success' => true, 'message' => 'Exercise added to routine successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to add exercise to routine'];
    }
    
    // Update exercise in routine
    private function updateExerciseInRoutine($user_id) {
        $routine_exercise_id = $_POST['routine_exercise_id'] ?? 0;
        $sets = $_POST['sets'] ?? 3;
        $reps = $_POST['reps'] ?? 10;
        $weight = $_POST['weight'] ?? null;
        $notes = $_POST['notes'] ?? '';
        $order_index = $_POST['order_index'] ?? 0;
        
        $result = $this->routineModel->updateExerciseInRoutine($routine_exercise_id, $sets, $reps, $weight, $notes, $order_index);
        
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

    // Handle routine detail view and actions
    public function handleRoutineDetail($routine_id) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user_id = $_SESSION['user_id'] ?? 0;

        // Determine routine ownership / visibility
        $routine = $this->routineModel->getRoutineById($routine_id, $user_id);
        $can_edit = true;
        if (!$routine) {
            // Allow viewing public routines not owned by the user
            $stmt = $this->db->prepare("SELECT routine_id as id, routine_name as name, description, is_public, created_at, user_id FROM workout_routines WHERE routine_id = :id AND is_public = 1");
            $stmt->bindParam(':id', $routine_id, PDO::PARAM_INT);
            $stmt->execute();
            $routine = $stmt->fetch(PDO::FETCH_ASSOC);
            $can_edit = false;
        }

        // Handle AJAX actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $action = $_POST['action'] ?? '';

            // Block modifications if user cannot edit
            $modActions = ['add_exercise', 'update_exercise', 'remove_exercise'];
            if (in_array($action, $modActions, true) && (!$routine || !$can_edit)) {
                echo json_encode(['success' => false, 'message' => 'Not allowed']);
                exit;
            }

            switch ($action) {
                case 'add_exercise':
                    echo json_encode($this->addExerciseToRoutine($user_id));
                    exit;
                case 'update_exercise':
                    echo json_encode($this->updateExerciseInRoutine($user_id));
                    exit;
                case 'remove_exercise':
                    echo json_encode($this->removeExerciseFromRoutine($user_id));
                    exit;
                case 'start_routine':
                    // Only owners can start directly from their routine; for public, allow start as well (creates a new workout tied to routine)
                    $routine_id_post = (int)($_POST['routine_id'] ?? 0);
                    if (!$routine || (int)$routine['id'] !== $routine_id_post) {
                        echo json_encode(['success' => false, 'message' => 'Invalid routine']);
                        exit;
                    }
                    $resp = $this->startWorkoutFromRoutine($user_id, $routine_id_post);
                    echo json_encode($resp);
                    exit;
                default:
                    echo json_encode(['success' => false, 'message' => 'Unknown action']);
                    exit;
            }
        }

        $routine_exercises = $routine ? $this->routineModel->getExercisesForRoutine($routine_id) : [];
        $all_exercises = $this->exerciseModel->getAllExercises();
        $categories = $this->exerciseModel->getExerciseCategories();

        return [
            'routine' => $routine,
            'routine_exercises' => $routine_exercises,
            'all_exercises' => $all_exercises,
            'categories' => $categories,
            'can_edit' => $can_edit,
        ];
    }

    // Create a workout from a routine and seed exercises
    private function startWorkoutFromRoutine($user_id, $routine_id) {
        // Prevent starting if there is an in-progress workout (no end_time)
        $check = $this->db->query("SELECT workout_id FROM workouts WHERE end_time IS NULL LIMIT 1");
        if ($check && $check->fetch(PDO::FETCH_ASSOC)) {
            return ['success' => false, 'message' => 'You already have an in-progress workout. Please end it before starting a new one.'];
        }

        // Create workout record with date-only (no time)
        $name = 'Workout ' . date('M j, Y');
        $notes = '';
        $workout_id = $this->workoutModel->createWorkout($user_id, $routine_id, $name, $notes);
        if (!$workout_id) {
            return ['success' => false, 'message' => 'Failed to create workout'];
        }
        // Seed exercises from routine_exercises into workout_exercises (sets, reps only)
        $stmt = $this->db->prepare("INSERT INTO workout_exercises (workout_id, exercise_id, sets, reps)
                                     SELECT :workout_id, re.exercise_id, re.sets, re.reps
                                     FROM routine_exercises re
                                     WHERE re.routine_id = :routine_id");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Failed to seed exercises'];
        }
        return ['success' => true, 'workout_id' => $workout_id, 'message' => 'Routine started'];
    }
}
?>
