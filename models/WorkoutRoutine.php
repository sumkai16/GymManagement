<?php
class WorkoutRoutine {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all routines for a user
    public function getRoutinesByUser($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM workout_routines 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get routine by ID
    public function getRoutineById($routine_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM workout_routines 
            WHERE routine_id = :routine_id
        ");
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Create new routine
    public function createRoutine($user_id, $name, $description = '', $is_public = 0) {
        $stmt = $this->conn->prepare("
            INSERT INTO workout_routines (user_id, routine_name) 
            VALUES (:user_id, :name)
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Update routine
    public function updateRoutine($routine_id, $user_id, $name, $description = '', $is_public = 0) {
        $stmt = $this->conn->prepare("
            UPDATE workout_routines 
            SET routine_name = :name 
            WHERE routine_id = :routine_id
        ");
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }
    
    // Delete routine
    public function deleteRoutine($routine_id, $user_id) {
        // First delete all exercises in this routine
        $stmt = $this->conn->prepare("DELETE FROM routine_exercises WHERE routine_id = :routine_id");
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Then delete the routine
        $stmt = $this->conn->prepare("DELETE FROM workout_routines WHERE routine_id = :routine_id");
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Add exercise to routine
    public function addExerciseToRoutine($routine_id, $exercise_id, $sets, $reps, $weight, $duration = null, $notes = '', $order_index = 0) {
        $stmt = $this->conn->prepare("
            INSERT INTO routine_exercises (routine_id, exercise_id, sets, reps, weight, duration, notes, order_index, created_at) 
            VALUES (:routine_id, :exercise_id, :sets, :reps, :weight, :duration, :notes, :order_index, NOW())
        ");
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
        $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':order_index', $order_index, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Get exercises for a routine
    public function getExercisesForRoutine($routine_id) {
        $stmt = $this->conn->prepare("
            SELECT re.*, e.name as exercise_name, e.muscle_group, e.description 
            FROM routine_exercises re 
            JOIN exercises e ON re.exercise_id = e.exercise_id 
            WHERE re.routine_id = :routine_id 
            ORDER BY re.order_index ASC, re.created_at ASC
        ");
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update exercise in routine
    public function updateExerciseInRoutine($routine_exercise_id, $sets, $reps, $weight, $duration = null, $notes = '', $order_index = 0) {
        $stmt = $this->conn->prepare("
            UPDATE routine_exercises 
            SET sets = :sets, reps = :reps, weight = :weight, duration = :duration, notes = :notes, order_index = :order_index, updated_at = NOW() 
            WHERE id = :routine_exercise_id
        ");
        $stmt->bindParam(':routine_exercise_id', $routine_exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
        $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':order_index', $order_index, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Remove exercise from routine
    public function removeExerciseFromRoutine($routine_exercise_id) {
        $stmt = $this->conn->prepare("DELETE FROM routine_exercises WHERE id = :routine_exercise_id");
        $stmt->bindParam(':routine_exercise_id', $routine_exercise_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Get public routines
    public function getPublicRoutines() {
        $stmt = $this->conn->prepare("
            SELECT wr.*, u.username 
            FROM workout_routines wr 
            JOIN users u ON wr.user_id = u.user_id 
            ORDER BY wr.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Copy routine to user
    public function copyRoutineToUser($routine_id, $user_id) {
        // Get the original routine
        $original = $this->getRoutineById($routine_id, null);
        if (!$original) {
            return false;
        }
        
        // Create new routine for user
        $new_routine_id = $this->createRoutine($user_id, $original['routine_name'] . ' (Copy)', '', 0);
        if (!$new_routine_id) {
            return false;
        }
        
        // Copy exercises
        $stmt = $this->conn->prepare("
            INSERT INTO routine_exercises (routine_id, exercise_id, sets, reps, weight, duration, notes, order_index, created_at)
            SELECT :new_routine_id, exercise_id, sets, reps, weight, duration, notes, order_index, NOW()
            FROM routine_exercises 
            WHERE routine_id = :original_routine_id
        ");
        $stmt->bindParam(':new_routine_id', $new_routine_id, PDO::PARAM_INT);
        $stmt->bindParam(':original_routine_id', $routine_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>
