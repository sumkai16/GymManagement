<?php
class Workout {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Get all workouts for a user (scoped via routine owner)
    public function getWorkoutsByUser($user_id) {
        $stmt = $this->conn->prepare("
            SELECT w.*, wr.routine_name 
            FROM workouts w 
            JOIN workout_routines wr ON w.routine_id = wr.routine_id 
            WHERE wr.user_id = :user_id
            ORDER BY w.created_at DESC
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Get workout by ID
    public function getWorkoutById($workout_id, $user_id) {
        $stmt = $this->conn->prepare("
            SELECT w.*, wr.routine_name 
            FROM workouts w 
            JOIN workout_routines wr ON w.routine_id = wr.routine_id 
            WHERE w.workout_id = :workout_id AND wr.user_id = :user_id
        ");
        $stmt->bindParam(':workout_id', $workout_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Create new workout
    public function createWorkout($user_id, $routine_id, $name, $notes = '') {
        // Ensure routine belongs to user for scoping
        $own = $this->conn->prepare("SELECT routine_id FROM workout_routines WHERE routine_id = :rid AND user_id = :uid");
        $own->bindParam(':rid', $routine_id, PDO::PARAM_INT);
        $own->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $own->execute();
        if (!$own->fetch(PDO::FETCH_ASSOC)) {
            return false;
        }
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
                COUNT(DISTINCT DATE(w.created_at)) as active_days,
                0 as avg_duration
            FROM workouts w
            JOIN workout_routines wr ON w.routine_id = wr.routine_id
            WHERE wr.user_id = :user_id AND w.created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
        ");
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
