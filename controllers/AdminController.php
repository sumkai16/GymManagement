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

    public function getAllTrainers($filter_specialty = null, $sort_by = 'trainer_id', $sort_order = 'ASC') {
        try {
            $where_clauses = [];
            $params = [];

            if ($filter_specialty) {
                $where_clauses[] = "t.specialty = :specialty";
                $params[':specialty'] = $filter_specialty;
            }

            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

            $valid_sort_columns = ['trainer_id', 'full_name', 'email', 'specialty'];
            if (!in_array($sort_by, $valid_sort_columns)) {
                $sort_by = 'trainer_id';
            }

            $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT t.*, u.username FROM trainers t JOIN users u ON t.user_id = u.user_id $where_sql ORDER BY t.$sort_by $sort_order";
            $stmt = $this->db->prepare($sql);

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

    public function addTrainer($user_id = null, $username = null, $password = null, $full_name, $email, $phone, $specialty) {
        // Validate input
        if (empty($full_name)) {
            return ['success' => false, 'message' => 'Full name is required'];
        }
        if (empty($email)) {
            return ['success' => false, 'message' => 'Email is required'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            if ($user_id) {
                // Use existing user
                // Check if user exists and is not already a trainer
                $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :user_id AND user_id NOT IN (SELECT user_id FROM trainers)");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                if (!$stmt->fetch()) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'User not found or already a trainer'];
                }
                // Update role to 'trainer'
                $stmt = $this->db->prepare("UPDATE users SET role = 'trainer' WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
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
                $user_result = $this->userModel->register($username, $password, 'trainer', 'active');
                if (!$user_result) {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to create user account'];
                }

                // Get the new user ID
                $final_user_id = $this->db->lastInsertId();
            }

            // Add trainer details
            $stmt = $this->db->prepare("INSERT INTO trainers (user_id, full_name, email, phone, specialty) VALUES (:user_id, :full_name, :email, :phone, :specialty)");
            $stmt->bindParam(':user_id', $final_user_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':specialty', $specialty);

            if ($stmt->execute()) {
                $this->db->commit();
                return ['success' => true, 'message' => 'Trainer added successfully'];
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to add trainer details'];
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Add trainer error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function updateTrainer($trainer_id, $full_name, $email, $phone, $specialty) {
        // Validate input
        if (empty($trainer_id) || empty($full_name)) {
            return ['success' => false, 'message' => 'Trainer ID and full name are required'];
        }

        // Validate email format if provided
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        try {
            // Update trainer
            $stmt = $this->db->prepare("UPDATE trainers SET full_name = :full_name, email = :email, phone = :phone, specialty = :specialty WHERE trainer_id = :trainer_id");
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':specialty', $specialty);
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

    public function deleteTrainer($trainer_id) {
        try {
            // Get user_id first
            $stmt = $this->db->prepare("SELECT user_id FROM trainers WHERE trainer_id = :trainer_id");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->execute();
            $trainer = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$trainer) {
                return ['success' => false, 'message' => 'Trainer not found'];
            }

            $user_id = $trainer['user_id'];

            // Start transaction
            $this->db->beginTransaction();

            // Delete trainer (this will cascade to related tables)
            $stmt = $this->db->prepare("DELETE FROM trainers WHERE trainer_id = :trainer_id");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->execute();

            // Delete user
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return ['success' => true, 'message' => 'Trainer deleted successfully'];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Delete trainer error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getTrainerById($trainer_id) {
        try {
            $stmt = $this->db->prepare("SELECT t.*, u.username FROM trainers t JOIN users u ON t.user_id = u.user_id WHERE t.trainer_id = :trainer_id");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get trainer by ID error: " . $e->getMessage());
            return false;
        }
    }

    public function getUsersWithoutTrainers() {
        try {
            $stmt = $this->db->prepare("SELECT user_id, username FROM users WHERE user_id NOT IN (SELECT user_id FROM trainers)");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get users without trainers error: " . $e->getMessage());
            return [];
        }
    }

    public function getAllPayments($filter_member = null, $filter_payment_type = null, $filter_date_from = null, $filter_date_to = null, $sort_by = 'payment_date', $sort_order = 'DESC') {
        try {
            $where_clauses = [];
            $params = [];

            if ($filter_member) {
                $where_clauses[] = "p.member_id = :member_id";
                $params[':member_id'] = $filter_member;
            }

            if ($filter_payment_type) {
                $where_clauses[] = "p.payment_type = :payment_type";
                $params[':payment_type'] = $filter_payment_type;
            }

            if ($filter_date_from) {
                $where_clauses[] = "p.payment_date >= :date_from";
                $params[':date_from'] = $filter_date_from;
            }

            if ($filter_date_to) {
                $where_clauses[] = "p.payment_date <= :date_to";
                $params[':date_to'] = $filter_date_to;
            }

            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

            $valid_sort_columns = ['payment_id', 'amount', 'payment_type', 'payment_date', 'member_name'];
            if (!in_array($sort_by, $valid_sort_columns)) {
                $sort_by = 'payment_date';
            }

            $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT p.*, m.full_name as member_name FROM payments p JOIN members m ON p.member_id = m.member_id $where_sql ORDER BY p.$sort_by $sort_order";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all payments error: " . $e->getMessage());
            return [];
        }
    }

    public function addPayment($member_id, $amount, $payment_type, $payment_method, $payment_date, $notes = '') {
        // Validate input
        if (empty($member_id) || empty($amount) || empty($payment_type) || empty($payment_method) || empty($payment_date)) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }

        // Validate amount
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Amount must be greater than 0'];
        }

        // Validate payment type
        $validTypes = ['membership', 'personal_training', 'class', 'other'];
        if (!in_array($payment_type, $validTypes)) {
            return ['success' => false, 'message' => 'Invalid payment type'];
        }

        // Validate payment method
        $validMethods = ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'gcash', 'paymaya'];
        if (!in_array($payment_method, $validMethods)) {
            return ['success' => false, 'message' => 'Invalid payment method'];
        }

        try {
            // Check if member exists
            $stmt = $this->db->prepare("SELECT member_id FROM members WHERE member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();
            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Member not found'];
            }

            // Add payment
            $stmt = $this->db->prepare("INSERT INTO payments (member_id, amount, payment_type, payment_method, payment_date, notes, status) VALUES (:member_id, :amount, :payment_type, :payment_method, :payment_date, :notes, 'completed')");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_type', $payment_type);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':payment_date', $payment_date);
            $stmt->bindParam(':notes', $notes);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Payment added successfully'];
            }

            return ['success' => false, 'message' => 'Failed to add payment'];
        } catch (PDOException $e) {
            error_log("Add payment error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function updatePayment($payment_id, $amount, $payment_type, $payment_method, $payment_date, $notes = '') {
        // Validate input
        if (empty($payment_id) || empty($amount) || empty($payment_type) || empty($payment_method) || empty($payment_date)) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }

        // Validate amount
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Amount must be greater than 0'];
        }

        // Validate payment type
        $validTypes = ['membership', 'personal_training', 'class', 'other'];
        if (!in_array($payment_type, $validTypes)) {
            return ['success' => false, 'message' => 'Invalid payment type'];
        }

        // Validate payment method
        $validMethods = ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'gcash', 'paymaya'];
        if (!in_array($payment_method, $validMethods)) {
            return ['success' => false, 'message' => 'Invalid payment method'];
        }

        try {
            // Update payment
            $stmt = $this->db->prepare("UPDATE payments SET amount = :amount, payment_type = :payment_type, payment_method = :payment_method, payment_date = :payment_date, notes = :notes WHERE payment_id = :payment_id");
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':payment_type', $payment_type);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':payment_date', $payment_date);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Payment updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update payment'];
        } catch (PDOException $e) {
            error_log("Update payment error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function deletePayment($payment_id) {
        try {
            // Check if payment exists
            $stmt = $this->db->prepare("SELECT payment_id FROM payments WHERE payment_id = :payment_id");
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Payment not found'];
            }

            // Delete payment
            $stmt = $this->db->prepare("DELETE FROM payments WHERE payment_id = :payment_id");
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Payment deleted successfully'];
            }

            return ['success' => false, 'message' => 'Failed to delete payment'];
        } catch (PDOException $e) {
            error_log("Delete payment error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getTotalRevenue() {
        try {
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get total revenue error: " . $e->getMessage());
            return 0;
        }
    }

    public function getMonthlyRevenue() {
        try {
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get monthly revenue error: " . $e->getMessage());
            return 0;
        }
    }

    public function getPaymentCount() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM payments");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get payment count error: " . $e->getMessage());
            return 0;
        }
    }

    public function getPendingPayments() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM payments WHERE status = 'pending'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get pending payments error: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalMembers() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM members");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get total members error: " . $e->getMessage());
            return 0;
        }
    }

    public function getActiveMembers() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM members WHERE status = 'active'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get active members error: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalTrainers() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM trainers");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get total trainers error: " . $e->getMessage());
            return 0;
        }
    }

    public function getRevenueByMonth() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(payment_date, '%Y-%m') as month,
                    SUM(amount) as revenue
                FROM payments 
                WHERE status = 'completed' 
                AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                ORDER BY month
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get revenue by month error: " . $e->getMessage());
            return [];
        }
    }

    public function getMembershipStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    membership_type,
                    COUNT(*) as count
                FROM members 
                GROUP BY membership_type
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get membership stats error: " . $e->getMessage());
            return [];
        }
    }

    public function getPaymentMethodStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    payment_method,
                    COUNT(*) as count
                FROM payments 
                WHERE status = 'completed'
                GROUP BY payment_method
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get payment method stats error: " . $e->getMessage());
            return [];
        }
    }

    public function getRecentPayments($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    m.full_name as member_name
                FROM payments p 
                JOIN members m ON p.member_id = m.member_id 
                WHERE p.status = 'completed'
                ORDER BY p.payment_date DESC 
                LIMIT :limit
            ");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get recent payments error: " . $e->getMessage());
            return [];
        }
    }

    public function getMemberGrowth() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(start_date, '%Y-%m') as month,
                    COUNT(*) as new_members
                FROM members 
                WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(start_date, '%Y-%m')
                ORDER BY month
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get member growth error: " . $e->getMessage());
            return [];
        }
    }

    public function getNewMembersThisMonth() {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM members 
                WHERE MONTH(start_date) = MONTH(CURRENT_DATE()) 
                AND YEAR(start_date) = YEAR(CURRENT_DATE())
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get new members this month error: " . $e->getMessage());
            return 0;
        }
    }

    public function getAveragePayment() {
        try {
            $stmt = $this->db->prepare("
                SELECT COALESCE(AVG(amount), 0) as average 
                FROM payments 
                WHERE status = 'completed'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return number_format($result['average'] ?? 0, 2);
        } catch (PDOException $e) {
            error_log("Get average payment error: " . $e->getMessage());
            return 0;
        }
    }

    public function getRetentionRate() {
        try {
            // Calculate retention rate as percentage of active members vs total members
            $totalMembers = $this->getTotalMembers();
            $activeMembers = $this->getActiveMembers();
            
            if ($totalMembers == 0) return 0;
            
            return round(($activeMembers / $totalMembers) * 100, 1);
        } catch (PDOException $e) {
            error_log("Get retention rate error: " . $e->getMessage());
            return 0;
        }
    }

    public function getGymSettings() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM gym_settings LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                // Return default settings if none exist
                return [
                    'gym_name' => 'FitNexus Gym',
                    'gym_address' => '',
                    'gym_phone' => '',
                    'gym_email' => '',
                    'gym_website' => '',
                    'monthly_fee' => 1500,
                    'annual_fee' => 15000,
                    'operating_hours' => '6:00 AM - 10:00 PM',
                    'max_capacity' => 200
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Get gym settings error: " . $e->getMessage());
            return [];
        }
    }

    public function updateGymSettings($gym_name, $gym_address, $gym_phone, $gym_email, $gym_website, $monthly_fee, $annual_fee, $operating_hours, $max_capacity) {
        try {
            // Check if settings exist
            $stmt = $this->db->prepare("SELECT id FROM gym_settings LIMIT 1");
            $stmt->execute();
            $exists = $stmt->fetch();

            if ($exists) {
                // Update existing settings
                $stmt = $this->db->prepare("
                    UPDATE gym_settings SET 
                        gym_name = :gym_name,
                        gym_address = :gym_address,
                        gym_phone = :gym_phone,
                        gym_email = :gym_email,
                        gym_website = :gym_website,
                        monthly_fee = :monthly_fee,
                        annual_fee = :annual_fee,
                        operating_hours = :operating_hours,
                        max_capacity = :max_capacity,
                        updated_at = CURRENT_TIMESTAMP
                ");
            } else {
                // Insert new settings
                $stmt = $this->db->prepare("
                    INSERT INTO gym_settings (
                        gym_name, gym_address, gym_phone, gym_email, gym_website,
                        monthly_fee, annual_fee, operating_hours, max_capacity
                    ) VALUES (
                        :gym_name, :gym_address, :gym_phone, :gym_email, :gym_website,
                        :monthly_fee, :annual_fee, :operating_hours, :max_capacity
                    )
                ");
            }

            $stmt->bindParam(':gym_name', $gym_name);
            $stmt->bindParam(':gym_address', $gym_address);
            $stmt->bindParam(':gym_phone', $gym_phone);
            $stmt->bindParam(':gym_email', $gym_email);
            $stmt->bindParam(':gym_website', $gym_website);
            $stmt->bindParam(':monthly_fee', $monthly_fee);
            $stmt->bindParam(':annual_fee', $annual_fee);
            $stmt->bindParam(':operating_hours', $operating_hours);
            $stmt->bindParam(':max_capacity', $max_capacity);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Gym settings updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update gym settings'];
        } catch (PDOException $e) {
            error_log("Update gym settings error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getSystemSettings() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM system_settings LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                // Return default settings if none exist
                return [
                    'maintenance_mode' => 'off',
                    'registration_enabled' => 'on',
                    'email_notifications' => 'on',
                    'backup_frequency' => 'daily',
                    'session_timeout' => 30
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Get system settings error: " . $e->getMessage());
            return [];
        }
    }

    public function updateSystemSettings($maintenance_mode, $registration_enabled, $email_notifications, $backup_frequency, $session_timeout) {
        try {
            // Check if settings exist
            $stmt = $this->db->prepare("SELECT id FROM system_settings LIMIT 1");
            $stmt->execute();
            $exists = $stmt->fetch();

            if ($exists) {
                // Update existing settings
                $stmt = $this->db->prepare("
                    UPDATE system_settings SET 
                        maintenance_mode = :maintenance_mode,
                        registration_enabled = :registration_enabled,
                        email_notifications = :email_notifications,
                        backup_frequency = :backup_frequency,
                        session_timeout = :session_timeout,
                        updated_at = CURRENT_TIMESTAMP
                ");
            } else {
                // Insert new settings
                $stmt = $this->db->prepare("
                    INSERT INTO system_settings (
                        maintenance_mode, registration_enabled, email_notifications,
                        backup_frequency, session_timeout
                    ) VALUES (
                        :maintenance_mode, :registration_enabled, :email_notifications,
                        :backup_frequency, :session_timeout
                    )
                ");
            }

            $stmt->bindParam(':maintenance_mode', $maintenance_mode);
            $stmt->bindParam(':registration_enabled', $registration_enabled);
            $stmt->bindParam(':email_notifications', $email_notifications);
            $stmt->bindParam(':backup_frequency', $backup_frequency);
            $stmt->bindParam(':session_timeout', $session_timeout);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'System settings updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update system settings'];
        } catch (PDOException $e) {
            error_log("Update system settings error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function backupDatabase() {
        try {
            // Create backup directory if it doesn't exist
            $backupDir = __DIR__ . '/../backups/';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Generate backup filename
            $backupFile = $backupDir . 'gym_backup_' . date('Y-m-d_H-i-s') . '.sql';

            // Get database connection details
            $host = 'localhost';
            $dbname = 'gymmanagement';
            $username = 'root';
            $password = '';

            // Create mysqldump command
            $command = "mysqldump -h $host -u $username -p$password $dbname > $backupFile";

            // Execute backup command
            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                return ['success' => true, 'message' => 'Database backup created successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to create database backup'];
            }
        } catch (Exception $e) {
            error_log("Database backup error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Backup error: ' . $e->getMessage()];
        }
    }

    public function getAttendanceRecords($filter_date = null, $filter_member = null, $filter_status = null, $sort_by = 'check_in', $sort_order = 'DESC') {
        try {
            $where_clauses = [];
            $params = [];
            if ($filter_date) {
                $where_clauses[] = "DATE(a.check_in) = :filter_date";
                $params[':filter_date'] = $filter_date;
            }
            if ($filter_member) {
                if ($filter_member === 'guest') {
                    $where_clauses[] = "(a.user_id IS NULL OR a.user_id = 0)";
                } else if ($filter_member === 'member') {
                    $where_clauses[] = "(a.user_id IS NOT NULL AND a.user_id > 0)";
                } else {
                    $where_clauses[] = "a.user_id = :filter_member";
                    $params[':filter_member'] = $filter_member;
                }
            }
            if ($filter_status) {
                if ($filter_status === 'checked_in') {
                    $where_clauses[] = "a.check_out IS NULL";
                } elseif ($filter_status === 'checked_out') {
                    $where_clauses[] = "a.check_out IS NOT NULL";
                }
            }
            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
            $valid_sort_columns = ['attendance_id', 'check_in', 'check_out', 'full_name', 'duration'];
            if (!in_array($sort_by, $valid_sort_columns)) {
                $sort_by = 'check_in';
            }
            $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';
            $sql = "SELECT a.attendance_id, a.user_id, COALESCE(m.full_name, a.full_name) AS full_name, a.check_in, a.check_out FROM attendance a LEFT JOIN members m ON a.user_id = m.user_id $where_sql ORDER BY a.$sort_by $sort_order";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get attendance records error: " . $e->getMessage());
            return [];
        }
    }

    public function checkInMember($member_id) {
        try {
            // Check if member exists
            $stmt = $this->db->prepare("SELECT user_id FROM members WHERE member_id = :member_id");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();
            $member = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$member) {
                return ['success' => false, 'message' => 'Member not found'];
            }

            $user_id = $member['user_id'];

            // Check if member is already checked in today
            $stmt = $this->db->prepare("SELECT attendance_id FROM attendance WHERE user_id = :user_id AND DATE(check_in) = CURDATE() AND check_out IS NULL");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Member is already checked in today'];
            }

            // Check in member
            $stmt = $this->db->prepare("INSERT INTO attendance (user_id, full_name, check_in) VALUES (:user_id, (SELECT full_name FROM members WHERE user_id = :user_id), NOW())");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Member checked in successfully'];
            }

            return ['success' => false, 'message' => 'Failed to check in member'];
        } catch (PDOException $e) {
            error_log("Check in member error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function checkOutMember($attendance_id) {
        try {
            // Check if attendance record exists and is not already checked out
            $stmt = $this->db->prepare("SELECT attendance_id FROM attendance WHERE attendance_id = :attendance_id AND check_out IS NULL");
            $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Attendance record not found or already checked out'];
            }

            // Check out member
            $stmt = $this->db->prepare("UPDATE attendance SET check_out = NOW() WHERE attendance_id = :attendance_id");
            $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Member checked out successfully'];
            }

            return ['success' => false, 'message' => 'Failed to check out member'];
        } catch (PDOException $e) {
            error_log("Check out member error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function deleteAttendance($attendance_id) {
        try {
            // Check if attendance record exists
            $stmt = $this->db->prepare("SELECT attendance_id FROM attendance WHERE attendance_id = :attendance_id");
            $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Attendance record not found'];
            }

            // Delete attendance record
            $stmt = $this->db->prepare("DELETE FROM attendance WHERE attendance_id = :attendance_id");
            $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Attendance record deleted successfully'];
            }

            return ['success' => false, 'message' => 'Failed to delete attendance record'];
        } catch (PDOException $e) {
            error_log("Delete attendance error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getTodayAttendanceStats() {
        try {
            // Get total check-ins today (all, not just members)
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM attendance WHERE DATE(check_in) = CURDATE()");
            $stmt->execute();
            $totalCheckIns = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            // Get currently in gym (all, not just members)
            $stmt = $this->db->prepare("SELECT COUNT(*) as current FROM attendance WHERE DATE(check_in) = CURDATE() AND check_out IS NULL");
            $stmt->execute();
            $currentlyInGym = $stmt->fetch(PDO::FETCH_ASSOC)['current'] ?? 0;
            // Get peak hour
            $stmt = $this->db->prepare("
                SELECT HOUR(check_in) as hour, COUNT(*) as count 
                FROM attendance 
                WHERE DATE(check_in) = CURDATE() 
                GROUP BY HOUR(check_in) 
                ORDER BY count DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $peakHour = $stmt->fetch(PDO::FETCH_ASSOC);
            $peakHourText = $peakHour ? $peakHour['hour'] . ':00' : 'N/A';
            // Get average duration
            $stmt = $this->db->prepare("
                SELECT AVG(TIMESTAMPDIFF(MINUTE, check_in, check_out)) as avg_duration 
                FROM attendance 
                WHERE DATE(check_in) = CURDATE() AND check_out IS NOT NULL
            ");
            $stmt->execute();
            $avgDuration = $stmt->fetch(PDO::FETCH_ASSOC)['avg_duration'] ?? 0;
            return [
                'total_check_ins' => $totalCheckIns,
                'currently_in_gym' => $currentlyInGym,
                'peak_hour' => $peakHourText,
                'avg_duration' => round($avgDuration)
            ];
        } catch (PDOException $e) {
            error_log("Get today attendance stats error: " . $e->getMessage());
            return [
                'total_check_ins' => 0,
                'currently_in_gym' => 0,
                'peak_hour' => 'N/A',
                'avg_duration' => 0
            ];
        }
    }

    public function getAllExercises($filter_muscle_group = null, $filter_equipment = null, $search_term = null, $sort_by = 'name', $sort_order = 'ASC') {
        try {
            $where_clauses = [];
            $params = [];

            if ($filter_muscle_group) {
                $where_clauses[] = "muscle_group = :muscle_group";
                $params[':muscle_group'] = $filter_muscle_group;
            }

            if ($filter_equipment) {
                $where_clauses[] = "equipment = :equipment";
                $params[':equipment'] = $filter_equipment;
            }

            if ($search_term) {
                $where_clauses[] = "(name LIKE :search OR description LIKE :search)";
                $params[':search'] = '%' . $search_term . '%';
            }

            $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

            $valid_sort_columns = ['exercise_id', 'name', 'muscle_group', 'equipment'];
            if (!in_array($sort_by, $valid_sort_columns)) {
                $sort_by = 'name';
            }

            $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT * FROM exercises $where_sql ORDER BY $sort_by $sort_order";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get all exercises error: " . $e->getMessage());
            return [];
        }
    }

    public function addExercise($name, $description, $muscle_group, $equipment) {
        // Validate input
        if (empty($name)) {
            return ['success' => false, 'message' => 'Exercise name is required'];
        }

        try {
            // Check if exercise name already exists
            $stmt = $this->db->prepare("SELECT exercise_id FROM exercises WHERE name = :name");
            $stmt->bindParam(':name', $name);
            $stmt->execute();

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Exercise with this name already exists'];
            }

            // Add exercise
            $stmt = $this->db->prepare("INSERT INTO exercises (name, description, muscle_group, equipment) VALUES (:name, :description, :muscle_group, :equipment)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':muscle_group', $muscle_group);
            $stmt->bindParam(':equipment', $equipment);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Exercise added successfully'];
            }

            return ['success' => false, 'message' => 'Failed to add exercise'];
        } catch (PDOException $e) {
            error_log("Add exercise error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function updateExercise($exercise_id, $name, $description, $muscle_group, $equipment) {
        // Validate input
        if (empty($exercise_id) || empty($name)) {
            return ['success' => false, 'message' => 'Exercise ID and name are required'];
        }

        try {
            // Check if exercise name is taken by another exercise
            $stmt = $this->db->prepare("SELECT exercise_id FROM exercises WHERE name = :name AND exercise_id != :exercise_id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Exercise with this name already exists'];
            }

            // Update exercise
            $stmt = $this->db->prepare("UPDATE exercises SET name = :name, description = :description, muscle_group = :muscle_group, equipment = :equipment WHERE exercise_id = :exercise_id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':muscle_group', $muscle_group);
            $stmt->bindParam(':equipment', $equipment);
            $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Exercise updated successfully'];
            }

            return ['success' => false, 'message' => 'Failed to update exercise'];
        } catch (PDOException $e) {
            error_log("Update exercise error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function deleteExercise($exercise_id) {
        try {
            // Check if exercise exists
            $stmt = $this->db->prepare("SELECT exercise_id FROM exercises WHERE exercise_id = :exercise_id");
            $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'message' => 'Exercise not found'];
            }

            // Check if exercise is used in any workout routines
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM workout_exercises WHERE exercise_id = :exercise_id");
            $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);
            $stmt->execute();
            $usage = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usage['count'] > 0) {
                return ['success' => false, 'message' => 'Cannot delete exercise. It is being used in workout routines.'];
            }

            // Delete exercise
            $stmt = $this->db->prepare("DELETE FROM exercises WHERE exercise_id = :exercise_id");
            $stmt->bindParam(':exercise_id', $exercise_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Exercise deleted successfully'];
            }

            return ['success' => false, 'message' => 'Failed to delete exercise'];
        } catch (PDOException $e) {
            error_log("Delete exercise error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function checkInGuest($guest_name, $guest_contact = null) {
        try {
            if (!$guest_name) {
                return ['success' => false, 'message' => 'Guest name is required'];
            }
            $stmt = $this->db->prepare("INSERT INTO attendance (user_id, full_name, check_in) VALUES (NULL, :full_name, NOW())");
            $stmt->bindParam(':full_name', $guest_name);
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Guest checked in successfully'];
            }
            return ['success' => false, 'message' => 'Failed to check in guest'];
        } catch (PDOException $e) {
            error_log("Check in guest error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function updateCurrentAdmin($user_id, $username, $new_password = null) {
        // Validate input
        if (empty($user_id) || empty($username)) {
            return ['success' => false, 'message' => 'Username is required'];
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
            // Build SQL
            if ($new_password) {
                $hash = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $this->db->prepare("UPDATE users SET username = :username, password = :password WHERE user_id = :user_id");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hash);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            } else {
                $stmt = $this->db->prepare("UPDATE users SET username = :username WHERE user_id = :user_id");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            }
            if ($stmt->execute()) {
                // Update session username if change was successful
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['username'] = $username;
                return ['success' => true, 'message' => 'Account updated successfully'];
            }
            return ['success' => false, 'message' => 'Failed to update account'];
        } catch (PDOException $e) {
            error_log("Update admin error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
}
?>
