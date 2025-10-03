<?php
require_once __DIR__ . '/../config/database.php';

class Member {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getDashboardData($user_id) {
        $data = [];

        // Workouts this week
        $data['workouts_this_week'] = $this->getWorkoutsThisWeek($user_id);

        // Calories today
        $data['calories_today'] = $this->getCaloriesToday($user_id);

        // Last workout duration (assuming we store duration, but schema has log_date, not duration. Perhaps calculate from logs)
        // For simplicity, last workout date
        $data['last_workout'] = $this->getLastWorkout($user_id);

        // Strength gain: perhaps total weight lifted increase, but complex. For now, placeholder
        $data['strength_gain'] = '+2.5kg'; // Placeholder

        // Today's workout: assume latest routine
        $data['todays_workout'] = $this->getTodaysWorkout($user_id);

        // Nutrition today
        $data['nutrition_today'] = $this->getNutritionToday($user_id);

        // Weekly progress
        $data['weekly_progress'] = $this->getWeeklyProgress($user_id);

        // Recent activity
        $data['recent_activity'] = $this->getRecentActivity($user_id);

        // Upcoming sessions: placeholder
        $data['upcoming_sessions'] = []; // No sessions table, so empty

        return $data;
    }

    public function getMemberInfo($user_id) {
        $query = "SELECT full_name FROM members WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['full_name'] : $_SESSION['username'];
    }

    private function getWorkoutsThisWeek($user_id) {
        $query = "SELECT COUNT(DISTINCT log_date) as count FROM workout_logs WHERE member_id = (SELECT member_id FROM members WHERE user_id = :user_id) AND YEARWEEK(log_date, 1) = YEARWEEK(CURDATE(), 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    private function getCaloriesToday($user_id) {
        $query = "SELECT SUM(calories) as total FROM nutrition_logs WHERE user_id = :user_id AND date = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getLastWorkout($user_id) {
        $query = "SELECT log_date FROM workout_logs WHERE member_id = (SELECT member_id FROM members WHERE user_id = :user_id) ORDER BY log_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $date = new DateTime($result['log_date']);
            $now = new DateTime();
            $diff = $now->diff($date);
            if ($diff->days == 0) return 'Today';
            elseif ($diff->days == 1) return 'Yesterday';
            else return $diff->days . ' days ago';
        }
        return 'No recent workout';
    }

    private function getTodaysWorkout($user_id) {
        // Assume latest routine
        $query = "SELECT routine_name FROM workout_routines WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['routine_name'] : 'No workout planned';
    }

    private function getNutritionToday($user_id) {
        $query = "SELECT SUM(calories) as calories, SUM(protein) as protein, SUM(carbs) as carbs, SUM(fats) as fats FROM nutrition_logs WHERE user_id = :user_id AND date = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'calories' => $result['calories'] ?? 0,
            'protein' => $result['protein'] ?? 0,
            'carbs' => $result['carbs'] ?? 0,
            'fats' => $result['fats'] ?? 0
        ];
    }

    private function getWeeklyProgress($user_id) {
        // Workouts: count distinct days this week
        $workouts = $this->getWorkoutsThisWeek($user_id);
        // Cardio: assume if workout_logs have cardio exercises, but for simplicity, placeholder
        $cardio = 2; // Placeholder
        // Nutrition goals: days with logs this week
        $query = "SELECT COUNT(DISTINCT date) as count FROM nutrition_logs WHERE user_id = :user_id AND YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $nutrition = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

        return [
            'workouts' => $workouts . '/5',
            'cardio' => $cardio . '/3',
            'nutrition' => $nutrition . '/7'
        ];
    }

    private function getRecentActivity($user_id) {
        // Latest 3 activities: workouts and nutrition
        $activities = [];

        // Latest workout
        $query = "SELECT 'workout' as type, log_date as date FROM workout_logs WHERE member_id = (SELECT member_id FROM members WHERE user_id = :user_id) ORDER BY log_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $workout = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($workout) {
            $activities[] = ['type' => 'workout', 'description' => 'Completed Workout', 'time' => $this->timeAgo($workout['date'])];
        }

        // Latest nutrition
        $query = "SELECT 'nutrition' as type, date FROM nutrition_logs WHERE user_id = :user_id ORDER BY date DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $nutrition = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($nutrition) {
            $activities[] = ['type' => 'nutrition', 'description' => 'Logged Meal', 'time' => $this->timeAgo($nutrition['date'])];
        }

        // Placeholder for PR
        $activities[] = ['type' => 'pr', 'description' => 'New Personal Record', 'time' => 'Yesterday'];

        return array_slice($activities, 0, 3);
    }

    private function timeAgo($date) {
        $datetime = new DateTime($date);
        $now = new DateTime();
        $interval = $now->diff($datetime);
        if ($interval->days == 0) return 'Today';
        elseif ($interval->days == 1) return 'Yesterday';
        else return $interval->days . ' days ago';
    }
}
