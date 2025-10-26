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
            $stmt = $this->db->prepare($sql);

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
            $stmt = $this->db->prepare("SELECT member_id FROM members WHERE email = :email");
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
            $this->db->beginTransaction();

            if ($user_id) {
                // Use existing user
                // Check if user exists and is not already a member
                $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :user_id AND user_id NOT IN (SELECT user_id FROM members)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                if (!$stmt->fetch()) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'User not found or already a member'];
                }
                $final_user_id = $user_id;
            } else {
                // Create new user
                if (empty($username) || empty($password)) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Username and password are required for new users'];
                }

                // Check if username exists
                if ($this->userModel->userExists($username)) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Username already exists'];
                }

                // Add user
                $user_result = $this->userModel->register($username, $password, 'member', $status);
                if (!$user_result) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to create user account'];
                }

                // Get the new user ID
                $final_user_id = $this->db->lastInsertId();
            }

            // Add member details
            $stmt = $this->db->prepare("INSERT INTO members (user_id, full_name, email, phone, address, membership_type, start_date, end_date, status) VALUES (:user_id, :full_name, :email, :phone, :address, :membership_type, :start_date, :end_date, :status)");
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
                $this->db->commit();
                return ['success' => true, 'message' => 'Member added successfully'];
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to add member details'];
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
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
            $stmt = $this->db->prepare("SELECT member_id FROM members WHERE email = :email AND member_id != :member_id");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Update member
            $stmt = $this->db->prepare("UPDATE members SET full_name = :full_name, email = :email, phone = :phone, address = :address, membership_type = :membership_type, start_date = :start_date, end_date = :end_date, status = :status WHERE member_id = :member_id");
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
            // Get user_id first
            $stmt = $this->db->prepare("SELECT user_id FROM members WHERE member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();
            $member = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$member) {
                return ['success' => false, 'message' => 'Member not found'];
            }

            $user_id = $member['user_id'];

            // Start transaction
            $this->db->beginTransaction();

            // Delete member (this will cascade to related tables)
            $stmt = $this->db->prepare("DELETE FROM members WHERE member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();

            // Delete user
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return ['success' => true, 'message' => 'Member deleted successfully'];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Delete member error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getMemberById($member_id) {
        try {
            $stmt = $this->db->prepare("SELECT m.*, u.username FROM members m JOIN users u ON m.user_id = u.user_id WHERE m.member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get member by ID error: " . $e->getMessage());
            return false;
        }
    }

    public function getUsersWithoutMembers() {
        try {
            $stmt = $this->db->prepare("SELECT user_id, username FROM users WHERE user_id NOT IN (SELECT user_id FROM members) AND role = 'guest'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get users without members error: " . $e->getMessage());
            return [];
        }
    }
}
?>
