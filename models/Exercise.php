<?php
class Exercise {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all exercises
    public function getAllExercises() {
        $stmt = $this->conn->prepare("SELECT * FROM exercises ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get exercise by ID
    public function getExerciseById($exercise_id) {
        $stmt = $this->conn->prepare("SELECT * FROM exercises WHERE id = :exercise_id");
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
    
    // Add exercise to workout
    public function addExerciseToWorkout($workout_id, $exercise_id, $sets, $reps, $weight, $duration = null, $notes = '') {
        $stmt = $this->conn->prepare("
            INSERT INTO workout_exercises (workout_id, exercise_id, sets, reps, weight, duration, notes, created_at) 
            VALUES (:workout_id, :exercise_id, :sets, :reps, :weight, :duration, :notes, NOW())
        ");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
        $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':notes', $notes);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Get exercises for a workout
    public function getExercisesForWorkout($workout_id) {
        $stmt = $this->conn->prepare("
            SELECT we.*, e.name as exercise_name, e.muscle_group, e.description 
            FROM workout_exercises we 
            JOIN exercises e ON we.exercise_id = e.exercise_id 
            WHERE we.workout_id = :workout_id 
            ORDER BY we.created_at ASC
        ");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Update exercise in workout
    public function updateExerciseInWorkout($workout_exercise_id, $sets, $reps, $weight, $duration = null, $notes = '') {
        $stmt = $this->conn->prepare("
            UPDATE workout_exercises 
            SET sets = :sets, reps = :reps, weight = :weight, duration = :duration, notes = :notes, updated_at = NOW() 
            WHERE id = :workout_exercise_id
        ");
        $stmt->bindParam(':workout_exercise_id', $workout_exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':sets', $sets, PDO::PARAM_INT);
        $stmt->bindParam(':reps', $reps, PDO::PARAM_INT);
        $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':notes', $notes);
        return $stmt->execute();
    }
    
    // Remove exercise from workout
    public function removeExerciseFromWorkout($workout_exercise_id) {
        $stmt = $this->conn->prepare("DELETE FROM workout_exercises WHERE id = :workout_exercise_id");
        $stmt->bindParam(':workout_exercise_id', $workout_exercise_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Get exercise progress for user
    public function getExerciseProgress($user_id, $exercise_id, $days = 30) {
        $stmt = $this->conn->prepare("
            SELECT 
                DATE(w.workout_date) as date,
                we.sets,
                we.reps,
                we.weight,
                we.duration
            FROM workout_exercises we
            JOIN workouts w ON we.workout_id = w.id
            WHERE w.user_id = :user_id 
            AND we.exercise_id = :exercise_id
            AND w.workout_date >= DATE_SUB(NOW(), INTERVAL :days DAY)
            ORDER BY w.workout_date DESC
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
