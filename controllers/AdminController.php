<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AdminController {
    private $userModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
    }

    public function getAllUsers($filter_role = null, $filter_status = null, $sort_by = 'created_at', $sort_order = 'DESC') {
        try {
            $where_clauses = [];
            $params = [];

            if ($filter_role) {
                $where_clauses[] = "role = :role";
                $params[':role'] = $filter_role;
            }

            if ($filter_status) {
                $where_clauses[] = "status = :status";
                $params[':status'] = $filter_status;
            }

            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

            $valid_sort_columns = ['user_id', 'username', 'role', 'status', 'created_at'];
            if (!in_array($sort_by, $valid_sort_columns)) {
                $sort_by = 'created_at';
            }

            $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT user_id, username, role, status, created_at FROM users $where_sql ORDER BY $sort_by $sort_order";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    public function addUser($username, $password, $role, $status = 'inactive') {
        // Validate input
        if (empty($username) || empty($password) || empty($role)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        // Validate role
        $validRoles = ['admin', 'trainer', 'member', 'guest'];
        if (!in_array($role, $validRoles)) {
            return ['success' => false, 'message' => 'Invalid role'];
        }

        // Validate status
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        // Check if username exists
        if ($this->userModel->userExists($username)) {
            return ['success' => false, 'message' => 'Username already exists'];
        }

        // Add user
        $result = $this->userModel->register($username, $password, $role, $status);
        if ($result) {
            return ['success' => true, 'message' => 'User added successfully'];
        }

        return ['success' => false, 'message' => 'Failed to add user'];
    }

    public function updateUser($user_id, $username, $role, $status) {
        // Validate input
        if (empty($user_id) || empty($username) || empty($role) || !isset($status)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        // Validate role
        $validRoles = ['admin', 'trainer', 'member', 'guest'];
        if (!in_array($role, $validRoles)) {
            return ['success' => false, 'message' => 'Invalid role'];
        }

        // Validate status
        $validStatuses = ['active', 'inactive'];
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        try {
            // Check if username is taken by another user
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE username = :username AND user_id != :user_id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Username already exists'];
            }

            // Update user
            $stmt = $this->db->prepare("UPDATE users SET username = :username, role = :role, status = :status WHERE user_id = :user_id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'User updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update user'];
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function deleteUser($user_id) {
        // Prevent deleting own account
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
            return ['success' => false, 'message' => 'Cannot delete your own account'];
        }

        try {
            // Check if user exists
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'User not found'];
            }

            // Delete user (cascade will handle related records)
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'User deleted successfully'];
            }

            return ['success' => false, 'message' => 'Failed to delete user'];
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getUserById($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT user_id, username, role, status, created_at FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return false;
        }
    }
}
?>
