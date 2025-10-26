<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Trainer.php';

class AdminController {
    private $userModel;
    private $memberModel;
    private $trainerModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
        $this->memberModel = new Member();
        $this->trainerModel = new Trainer();
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

    public function getAllMembers($filter_status = null, $filter_membership = null, $sort_by = 'start_date', $sort_order = 'DESC') {
        return $this->memberModel->getAllMembers($filter_status, $filter_membership, $sort_by, $sort_order);
    }

    public function addMember($user_id = null, $username = null, $password = null, $full_name, $email, $phone, $address, $membership_type, $start_date, $end_date, $status = 'active') {
        return $this->memberModel->addMember($user_id, $username, $password, $full_name, $email, $phone, $address, $membership_type, $start_date, $end_date, $status);
    }

    public function updateMember($member_id, $full_name, $email, $phone, $address, $membership_type, $start_date, $end_date, $status) {
        return $this->memberModel->updateMember($member_id, $full_name, $email, $phone, $address, $membership_type, $start_date, $end_date, $status);
    }

    public function deleteMember($member_id) {
        return $this->memberModel->deleteMember($member_id);
    }

    public function getMemberById($member_id) {
        return $this->memberModel->getMemberById($member_id);
    }

    public function getUsersWithoutMembers() {
        return $this->memberModel->getUsersWithoutMembers();
    }

    public function getAllTrainers($filter_specialty = null, $sort_by = 'full_name', $sort_order = 'ASC') {
        return $this->trainerModel->getAllTrainers($filter_specialty, $sort_by, $sort_order);
    }

    public function addTrainer($user_id = null, $username = null, $password = null, $full_name, $specialty, $phone, $email, $image = null) {
        return $this->trainerModel->addTrainer($user_id, $username, $password, $full_name, $specialty, $phone, $email, $image);
    }

    public function updateTrainer($trainer_id, $full_name, $specialty, $phone, $email, $image = null) {
        return $this->trainerModel->updateTrainer($trainer_id, $full_name, $specialty, $phone, $email, $image);
    }

    public function deleteTrainer($trainer_id) {
        return $this->trainerModel->deleteTrainer($trainer_id);
    }

    public function getTrainerById($trainer_id) {
        return $this->trainerModel->getTrainerById($trainer_id);
    }

    public function getUsersWithoutTrainers() {
        return $this->trainerModel->getUsersWithoutTrainers();
    }

    public function getDashboardStats() {
        try {
            $stats = [];

            // Total Members
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM members WHERE status = 'active'");
            $stmt->execute();
            $stats['total_members'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Active Trainers
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM trainers t JOIN users u ON t.user_id = u.user_id WHERE u.status = 'active'");
            $stmt->execute();
            $stats['active_trainers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Monthly Revenue (assuming payments table exists)
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) as revenue FROM payments WHERE DATE_FORMAT(payment_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')");
            $stmt->execute();
            $stats['monthly_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];

            // Sessions Today (assuming sessions table exists)
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM sessions WHERE DATE(session_date) = CURDATE()");
            $stmt->execute();
            $stats['sessions_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return $stats;
        } catch (PDOException $e) {
            error_log("Get dashboard stats error: " . $e->getMessage());
            return [
                'total_members' => 0,
                'active_trainers' => 0,
                'monthly_revenue' => 0,
                'sessions_today' => 0
            ];
        }
    }

    public function getRecentMembers($limit = 5) {
        try {
            $stmt = $this->db->prepare("SELECT m.member_id, m.full_name, m.email, m.membership_type, m.start_date, m.image FROM members m ORDER BY m.created_at DESC LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent members error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentTrainers($limit = 5) {
        try {
            $stmt = $this->db->prepare("SELECT t.trainer_id, t.full_name, t.email, t.specialty, t.image FROM trainers t ORDER BY t.trainer_id DESC LIMIT :limit");
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
