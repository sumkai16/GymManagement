<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/User.php';

class Trainer {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all trainers
    public function getAllTrainers($filter_specialty = null, $sort_by = 'full_name', $sort_order = 'ASC') {
        try {
            $where_clauses = [];
            $params = [];

            if ($filter_specialty) {
                $where_clauses[] = "t.specialty = :specialty";
                $params[':specialty'] = $filter_specialty;
            }

            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

            $valid_sort_columns = ['trainer_id', 'full_name', 'specialty', 'phone', 'email', 'image'];
            if (!in_array($sort_by, $valid_sort_columns)) {
                $sort_by = 'full_name';
            }

            $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT t.*, u.username FROM trainers t JOIN users u ON t.user_id = u.user_id $where_sql ORDER BY t.$sort_by $sort_order";
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all trainers error: " . $e->getMessage());
            return [];
        }
    }

    // Get trainer by ID
    public function getTrainerById($trainer_id) {
        try {
            $stmt = $this->conn->prepare("SELECT t.*, u.username FROM trainers t JOIN users u ON t.user_id = u.user_id WHERE t.trainer_id = :trainer_id");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get trainer by ID error: " . $e->getMessage());
            return false;
        }
    }

    // Add trainer
    public function addTrainer($user_id = null, $username = null, $password = null, $full_name, $specialty, $phone, $email, $image = null) {
        // Validate input
        if (empty($full_name) || empty($email)) {
            return ['success' => false, 'message' => 'Full name and email are required'];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Check if email exists
        try {
            $stmt = $this->conn->prepare("SELECT trainer_id FROM trainers WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error'];
        }

        // Handle image upload - store only filename in DB
        $image_filename = 'default_trainer.png';
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            // Validate image
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($image['type'], $allowed_types)) {
                return ['success' => false, 'message' => 'Invalid image type. Only JPG, PNG, and GIF are allowed'];
            }

            if ($image['size'] > $max_size) {
                return ['success' => false, 'message' => 'Image size too large. Maximum 5MB allowed'];
            }

            // Generate unique filename
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $filename = uniqid('trainer_') . '.' . $extension;
            $upload_dir = __DIR__ . '/../assets/images/trainers/';

            // Ensure directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($image['tmp_name'], $target_path)) {
                $image_filename = $filename;
            } else {
                return ['success' => false, 'message' => 'Failed to upload image'];
            }
        }

        try {
            // Start transaction
            $this->conn->beginTransaction();

            if ($user_id) {
                // Use existing user
                // Check if user exists and is not already a trainer
                $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE user_id = :user_id AND user_id NOT IN (SELECT user_id FROM trainers)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                if (!$stmt->fetch()) {
                    $this->conn->rollBack();
                    return ['success' => false, 'message' => 'User not found or already a trainer'];
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

                // Split full name into first and last name
                $name_parts = explode(' ', $full_name, 2);
                $first_name = $name_parts[0];
                $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
                
                // Add user
                $user_result = $userModel->register($username, $password, $email, $first_name, $last_name, '', 'trainer', 'active');
                if (!$user_result) {
                    $this->conn->rollBack();
                    return ['success' => false, 'message' => 'Failed to create user account'];
                }

                // Get the new user ID
                $final_user_id = $this->conn->lastInsertId();
            }

            // Add trainer details
            $stmt = $this->conn->prepare("INSERT INTO trainers (user_id, full_name, specialty, phone, email, image) VALUES (:user_id, :full_name, :specialty, :phone, :email, :image)");
            $stmt->bindParam(':user_id', $final_user_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':specialty', $specialty);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':image', $image_filename);

            if ($stmt->execute()) {
                $this->conn->commit();
                return ['success' => true, 'message' => 'Trainer added successfully'];
            } else {
                $this->conn->rollBack();
                return ['success' => false, 'message' => 'Failed to add trainer details'];
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Add trainer error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    // Update trainer
    public function updateTrainer($trainer_id, $full_name, $specialty, $phone, $email, $image = null) {
        // Validate input
        if (empty($trainer_id) || empty($full_name) || empty($email)) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Handle image upload - only change image if a new one is uploaded
        $image_filename = null;
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            // Validate image
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($image['type'], $allowed_types)) {
                return ['success' => false, 'message' => 'Invalid image type. Only JPG, PNG, and GIF are allowed'];
            }

            if ($image['size'] > $max_size) {
                return ['success' => false, 'message' => 'Image size too large. Maximum 5MB allowed'];
            }

            // Generate unique filename
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $filename = uniqid('trainer_') . '.' . $extension;
            $upload_dir = __DIR__ . '/../assets/images/trainers/';

            // Ensure directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($image['tmp_name'], $target_path)) {
                $image_filename = $filename;
            } else {
                return ['success' => false, 'message' => 'Failed to upload image'];
            }
        }

        try {
            // Check if email is taken by another trainer
            $stmt = $this->conn->prepare("SELECT trainer_id FROM trainers WHERE email = :email AND trainer_id != :trainer_id");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Update trainer
            if ($image_filename !== null) {
                $stmt = $this->conn->prepare("UPDATE trainers SET full_name = :full_name, specialty = :specialty, phone = :phone, email = :email, image = :image WHERE trainer_id = :trainer_id");
                $stmt->bindParam(':image', $image_filename);
            } else {
                $stmt = $this->conn->prepare("UPDATE trainers SET full_name = :full_name, specialty = :specialty, phone = :phone, email = :email WHERE trainer_id = :trainer_id");
            }
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':specialty', $specialty);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Trainer updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update trainer'];
        } catch (PDOException $e) {
            error_log("Update trainer error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    // Delete trainer
    public function deleteTrainer($trainer_id) {
        try {
            // Check if trainer exists
            $stmt = $this->conn->prepare("SELECT trainer_id FROM trainers WHERE trainer_id = :trainer_id");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Trainer not found'];
            }

            // Delete trainer (this will cascade to related tables)
            $stmt = $this->conn->prepare("DELETE FROM trainers WHERE trainer_id = :trainer_id");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Trainer deleted successfully'];
            }

            return ['success' => false, 'message' => 'Failed to delete trainer'];
        } catch (PDOException $e) {
            error_log("Delete trainer error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    // Get users without trainers
    public function getUsersWithoutTrainers() {
        try {
            $stmt = $this->conn->prepare("SELECT user_id, username FROM users WHERE user_id NOT IN (SELECT user_id FROM trainers) AND role = 'guest'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get users without trainers error: " . $e->getMessage());
            return [];
        }
    }

    // Get recent trainers
    public function getRecentTrainers($limit = 5) {
        try {
            $stmt = $this->conn->prepare("SELECT t.trainer_id, t.full_name, t.email, t.specialty, t.image FROM trainers t ORDER BY t.trainer_id DESC LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent trainers error: " . $e->getMessage());
            return [];
        }
    }

    // Get trainer by user ID
    public function getTrainerByUserId($user_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM trainers WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get trainer by user ID error: " . $e->getMessage());
            return false;
        }
    }

    // Get trainer's clients (members)
    public function getTrainerClients($trainer_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT m.member_id, m.full_name, m.email, m.phone, m.membership_type, m.start_date, m.end_date, m.status
                FROM members m
                WHERE m.member_id IN (
                    SELECT DISTINCT tb.member_id 
                    FROM trainer_bookings tb 
                    WHERE tb.trainer_id = :trainer_id
                )
                ORDER BY m.full_name
            ");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get trainer clients error: " . $e->getMessage());
            return [];
        }
    }

    // Get client workout history
    public function getClientWorkoutHistory($member_id, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    wl.log_id,
                    wl.log_date,
                    wl.sets,
                    wl.reps,
                    wl.weight,
                    wl.duration,
                    e.name as exercise_name,
                    e.muscle_group,
                    e.equipment,
                    w.workout_name,
                    w.created_at as workout_date
                FROM workout_logs wl
                LEFT JOIN exercises e ON wl.exercise_id = e.exercise_id
                LEFT JOIN workouts w ON wl.workout_id = w.workout_id
                WHERE wl.member_id = :member_id
                ORDER BY wl.log_date DESC, w.created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get client workout history error: " . $e->getMessage());
            return [];
        }
    }

    // Get client workout statistics
    public function getClientWorkoutStats($member_id, $days = 30) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(DISTINCT wl.log_date) as workout_days,
                    COUNT(wl.log_id) as total_exercises,
                    SUM(wl.sets) as total_sets,
                    SUM(wl.reps) as total_reps,
                    MAX(wl.weight) as max_weight,
                    AVG(wl.weight) as avg_weight,
                    SUM(wl.duration) as total_duration
                FROM workout_logs wl
                WHERE wl.member_id = :member_id 
                AND wl.log_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            ");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get client workout stats error: " . $e->getMessage());
            return [];
        }
    }

    // Get client workout history by date range
    public function getClientWorkoutHistoryByDateRange($member_id, $start_date, $end_date) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    wl.log_date,
                    COUNT(wl.log_id) as exercises_completed,
                    SUM(wl.sets) as total_sets,
                    SUM(wl.reps) as total_reps,
                    SUM(wl.duration) as total_duration,
                    GROUP_CONCAT(DISTINCT e.name ORDER BY e.name) as exercises
                FROM workout_logs wl
                LEFT JOIN exercises e ON wl.exercise_id = e.exercise_id
                WHERE wl.member_id = :member_id
                AND wl.log_date BETWEEN :start_date AND :end_date
                GROUP BY wl.log_date
                ORDER BY wl.log_date DESC
            ");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get client workout history by date range error: " . $e->getMessage());
            return [];
        }
    }

    // Get client muscle group breakdown
    public function getClientMuscleGroupBreakdown($member_id, $days = 30) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    e.muscle_group,
                    COUNT(wl.log_id) as exercise_count,
                    SUM(wl.sets) as total_sets,
                    SUM(wl.reps) as total_reps,
                    MAX(wl.weight) as max_weight
                FROM workout_logs wl
                LEFT JOIN exercises e ON wl.exercise_id = e.exercise_id
                WHERE wl.member_id = :member_id 
                AND wl.log_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY e.muscle_group
                ORDER BY exercise_count DESC
            ");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get client muscle group breakdown error: " . $e->getMessage());
            return [];
        }
    }
}
?>
