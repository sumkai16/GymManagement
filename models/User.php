<?php
// models/User.php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password, $role = "guest", $status = "inactive") {
        try {
            // Check if username already exists
            if ($this->userExists($username)) {
                return false;
            }
            
            $query = "INSERT INTO " . $this->table . " (username, password, role, status) VALUES (:username, :password, :role, :status)";
            $stmt = $this->conn->prepare($query);

            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $hash);
            $stmt->bindParam(":role", $role);
            $stmt->bindParam(":status", $status);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT user_id, username, password, role, status FROM " . $this->table . " WHERE username = :username LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                // Remove password from returned data for security
                unset($user['password']);
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function userExists($username) {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("User exists check error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserById($user_id) {
        try {
            $query = "SELECT user_id, username, role, status FROM " . $this->table . " WHERE user_id = :user_id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateUserStatus($user_id, $status) {
        try {
            $query = "UPDATE " . $this->table . " SET status = :status WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update user status error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePasswordByUserId($user_id, $newPassword) {
        try {
            $query = "UPDATE users SET password = :password WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $hash);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update password error: " . $e->getMessage());
            return false;
        }
    }
}
