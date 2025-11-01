<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/User.php';

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

    // Admin methods
    public function getAllMembers($filter_status = null, $filter_membership = null, $sort_by = 'start_date', $sort_order = 'DESC') {
        try {
            $where_clauses = [];
            $params = [];

            if ($filter_status) {
                $where_clauses[] = "m.status = :status";
                $params[':status'] = $filter_status;
            }

            if ($filter_membership) {
                $where_clauses[] = "m.membership_type = :membership_type";
                $params[':membership_type'] = $filter_membership;
            }

            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

            $valid_sort_columns = ['member_id', 'full_name', 'email', 'membership_type', 'status', 'start_date', 'end_date'];
            if (!in_array($sort_by, $valid_sort_columns)) {
                $sort_by = 'start_date';
            }

            $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT m.*, u.username FROM members m JOIN users u ON m.user_id = u.user_id $where_sql ORDER BY m.$sort_by $sort_order";
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all members error: " . $e->getMessage());
            return [];
        }
    }

    public function addMember($user_id = null, $username = null, $password = null, $full_name, $email, $phone, $address, $membership_type, $start_date, $end_date, $status = 'active') {
        // Validate input
        if (empty($full_name) || empty($email) || empty($membership_type) || empty($start_date) || empty($end_date)) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Validate membership type
        $validTypes = ['monthly', 'annual'];
        if (!in_array($membership_type, $validTypes)) {
            return ['success' => false, 'message' => 'Invalid membership type'];
        }

        // Validate status
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        // Check if email exists
        try {
            $stmt = $this->conn->prepare("SELECT member_id FROM members WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error'];
        }

        try {
            // Start transaction
            $this->conn->beginTransaction();

            if ($user_id) {
                // Use existing user
                // Check if user exists and is not already a member
                $stmt = $this->conn->prepare("SELECT user_id, role FROM users WHERE user_id = :user_id AND user_id NOT IN (SELECT user_id FROM members)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    $this->conn->rollBack();
                    return ['success' => false, 'message' => 'User not found or already a member'];
                }

                // Update user role to member if it's currently guest
                if ($user['role'] === 'guest') {
                    $stmt = $this->conn->prepare("UPDATE users SET role = 'member' WHERE user_id = :user_id");
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    if (!$stmt->execute()) {
                        $this->conn->rollBack();
                        return ['success' => false, 'message' => 'Failed to update user role'];
                    }
                }

                $final_user_id = $user_id;
            } else {
                // Create new user
                if (empty($username) || empty($password)) {
                    $this->conn->rollBack();
                    return ['success' => false, 'message' => 'Username and password are required for new users'];
                }

                // Check if username exists
                $userModel = new User($this->conn);
                if ($userModel->userExists($username)) {
                    $this->conn->rollBack();
                    return ['success' => false, 'message' => 'Username already exists'];
                }

                // Add user
                $user_result = $userModel->register($username, $password, 'member', $status);
                if (!$user_result) {
                    $this->conn->rollBack();
                    return ['success' => false, 'message' => 'Failed to create user account'];
                }

                // Get the new user ID
                $final_user_id = $this->conn->lastInsertId();
            }

            // Add member details
            $stmt = $this->conn->prepare("INSERT INTO members (user_id, full_name, email, phone, address, membership_type, start_date, end_date, status) VALUES (:user_id, :full_name, :email, :phone, :address, :membership_type, :start_date, :end_date, :status)");
            $stmt->bindParam(':user_id', $final_user_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':membership_type', $membership_type);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                $this->conn->commit();
                return ['success' => true, 'message' => 'Member added successfully'];
            } else {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Failed to add member details'];
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Add member error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function updateMember($member_id, $full_name, $email, $phone, $address, $membership_type, $start_date, $end_date, $status) {
        // Validate input
        if (empty($member_id) || empty($full_name) || empty($email) || empty($membership_type) || empty($start_date) || empty($end_date) || !isset($status)) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Validate membership type
        $validTypes = ['monthly', 'annual'];
        if (!in_array($membership_type, $validTypes)) {
            return ['success' => false, 'message' => 'Invalid membership type'];
        }

        // Validate status
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        try {
            // Check if email is taken by another member
            $stmt = $this->conn->prepare("SELECT member_id FROM members WHERE email = :email AND member_id != :member_id");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Update member
            $stmt = $this->conn->prepare("UPDATE members SET full_name = :full_name, email = :email, phone = :phone, address = :address, membership_type = :membership_type, start_date = :start_date, end_date = :end_date, status = :status WHERE member_id = :member_id");
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':membership_type', $membership_type);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Member updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update member'];
        } catch (PDOException $e) {
            error_log("Update member error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function deleteMember($member_id) {
        try {
            // Check if member exists
            $stmt = $this->conn->prepare("SELECT member_id FROM members WHERE member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Member not found'];
            }

            // Delete member (this will cascade to related tables)
            $stmt = $this->conn->prepare("DELETE FROM members WHERE member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Member deleted successfully'];
            }

            return ['success' => false, 'message' => 'Failed to delete member'];
        } catch (PDOException $e) {
            error_log("Delete member error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getMemberById($member_id) {
        try {
            $stmt = $this->conn->prepare("SELECT m.*, u.username FROM members m JOIN users u ON m.user_id = u.user_id WHERE m.member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get member by ID error: " . $e->getMessage());
            return false;
        }
    }

    public function getMemberByUserId($user_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM members WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get member by user_id error: " . $e->getMessage());
            return false;
        }
    }

    public function getUsersWithoutMembers() {
        try {
            $stmt = $this->conn->prepare("SELECT user_id, username FROM users WHERE user_id NOT IN (SELECT user_id FROM members) AND role = 'guest'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get users without members error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentMembers($limit = 5) {
        try {
            $stmt = $this->conn->prepare("SELECT m.member_id, m.full_name, m.email, m.membership_type, m.start_date, m.image FROM members m ORDER BY m.created_at DESC LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent members error: " . $e->getMessage());
            return [];
        }
    }
}
