<?php
class Exercise {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Mark/unmark exercise as done within a workout
    public function setExerciseDone($workout_exercise_id, $is_done) {
        $stmt = $this->conn->prepare("UPDATE workout_exercises SET is_done = :done WHERE we_id = :id");
        $stmt->bindParam(':id', $workout_exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':done', $is_done, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Get all exercises
    public function getAllExercises() {
        $stmt = $this->conn->prepare("SELECT * FROM exercises ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===== Per-set tracking (workout_exercise_sets) =====
    public function getSetsForExercise($we_id) {
        $stmt = $this->conn->prepare("SELECT wes_id, we_id, set_number, reps, weight, is_done FROM workout_exercise_sets WHERE we_id = :we_id ORDER BY set_number ASC");
        $stmt->bindParam(':we_id', $we_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSetToExercise($we_id, $set_number, $reps = null, $weight = null) {
        $stmt = $this->conn->prepare("INSERT INTO workout_exercise_sets (we_id, set_number, reps, weight) VALUES (:we_id, :set_number, :reps, :weight)");
        $stmt->bindParam(':we_id', $we_id, PDO::PARAM_INT);
        $stmt->bindParam(':set_number', $set_number, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps);
        $stmt->bindParam(':weight', $weight);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function updateSet($wes_id, $reps = null, $weight = null) {
        $stmt = $this->conn->prepare("UPDATE workout_exercise_sets SET reps = :reps, weight = :weight WHERE wes_id = :wes_id");
        $stmt->bindParam(':wes_id', $wes_id, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps);
        $stmt->bindParam(':weight', $weight);
        return $stmt->execute();
    }

    public function removeSet($wes_id) {
        $stmt = $this->conn->prepare("DELETE FROM workout_exercise_sets WHERE wes_id = :wes_id");
        $stmt->bindParam(':wes_id', $wes_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function setSetDone($wes_id, $is_done) {
        $stmt = $this->conn->prepare("UPDATE workout_exercise_sets SET is_done = :done WHERE wes_id = :wes_id");
        $stmt->bindParam(':wes_id', $wes_id, PDO::PARAM_INT);
        $stmt->bindParam(':done', $is_done, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Get exercise by ID
    public function getExerciseById($exercise_id) {
        $stmt = $this->conn->prepare("SELECT * FROM exercises WHERE exercise_id = :exercise_id");
        $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get exercises by category
    public function getExercisesByCategory($category) {
        $stmt = $this->conn->prepare("SELECT * FROM exercises WHERE muscle_group = :category ORDER BY name ASC");
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get exercise categories
    public function getExerciseCategories() {
        $stmt = $this->conn->prepare("SELECT DISTINCT muscle_group FROM exercises ORDER BY muscle_group ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Add exercise to workout (store weight for logging)
    public function addExerciseToWorkout($workout_id, $exercise_id, $sets, $reps, $weight = null, $duration = null, $notes = '') {
        $stmt = $this->conn->prepare("
            INSERT INTO workout_exercises (workout_id, exercise_id, sets, reps, weight) 
            VALUES (:workout_id, :exercise_id, :sets, :reps, :weight)
        ");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
        $stmt->bindParam(':weight', $weight);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Get exercises for a workout
    public function getExercisesForWorkout($workout_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                we.we_id as id,
                we.workout_id,
                we.exercise_id,
                we.sets,
                we.reps,
                we.weight,
                we.is_done,
                e.name as exercise_name,
                e.muscle_group,
                e.description
            FROM workout_exercises we 
            JOIN exercises e ON we.exercise_id = e.exercise_id 
            WHERE we.workout_id = :workout_id 
            ORDER BY we.we_id ASC
        ");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update exercise in workout
    public function updateExerciseInWorkout($workout_exercise_id, $sets, $reps, $weight = null, $duration = null, $notes = '') {
        $stmt = $this->conn->prepare("
            UPDATE workout_exercises 
            SET sets = :sets, reps = :reps, weight = :weight
            WHERE we_id = :workout_exercise_id
        ");
        $stmt->bindParam(':workout_exercise_id', $workout_exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
        $stmt->bindParam(':weight', $weight);
        return $stmt->execute();
    }
    
    // Remove exercise from workout
    public function removeExerciseFromWorkout($workout_exercise_id) {
        $stmt = $this->conn->prepare("DELETE FROM workout_exercises WHERE we_id = :workout_exercise_id");
        $stmt->bindParam(':workout_exercise_id', $workout_exercise_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Get exercise progress for user
    public function getExerciseProgress($user_id, $exercise_id, $days = 30) {
        // Use workout_logs for historical progress
        $stmt = $this->conn->prepare("
            SELECT 
                wl.log_date as date,
                wl.sets,
                wl.reps,
                wl.weight
            FROM workout_logs wl
            WHERE wl.member_id = :user_id 
            AND wl.exercise_id = :exercise_id
            AND wl.log_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            ORDER BY wl.log_date DESC
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
