<?php
class Workout {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all workouts for a user
    public function getWorkoutsByUser($user_id) {
        $stmt = $this->conn->prepare("
            SELECT w.*, wr.routine_name 
            FROM workouts w 
            LEFT JOIN workout_routines wr ON w.routine_id = wr.routine_id 
            ORDER BY w.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get workout by ID
    public function getWorkoutById($workout_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT w.*, wr.routine_name 
            FROM workouts w 
            LEFT JOIN workout_routines wr ON w.routine_id = wr.routine_id 
            WHERE w.workout_id = :workout_id
        ");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Create new workout
    public function createWorkout($user_id, $routine_id, $name, $notes = '') {
        $stmt = $this->conn->prepare("
            INSERT INTO workouts (routine_id, workout_name, created_at) 
            VALUES (:routine_id, :name, NOW())
        ");
        $stmt->bindParam(':routine_id', $routine_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    // Update workout
    public function updateWorkout($workout_id, $user_id, $name, $notes = '') {
        $stmt = $this->conn->prepare("
            UPDATE workouts 
            SET workout_name = :name 
            WHERE workout_id = :workout_id
        ");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }
    
    // Delete workout
    public function deleteWorkout($workout_id, $user_id) {
        // First delete all exercises for this workout
        $stmt = $this->conn->prepare("DELETE FROM workout_exercises WHERE workout_id = :workout_id");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Then delete the workout
        $stmt = $this->conn->prepare("DELETE FROM workouts WHERE workout_id = :workout_id");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Get workout statistics
    public function getWorkoutStats($user_id, $days = 30) {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_workouts,
                COUNT(DISTINCT DATE(created_at)) as active_days,
                0 as avg_duration
            FROM workouts 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
        ");
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
