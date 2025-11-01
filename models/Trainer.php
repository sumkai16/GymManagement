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

                // Add user
                $user_result = $userModel->register($username, $password, 'trainer', 'active');
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
}
?>
