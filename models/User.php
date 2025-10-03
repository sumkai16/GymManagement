<?php
// models/User.php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password, $role = "member", $status="inactive") {
        $query = "INSERT INTO " . $this->table . " (username, password, role, status) VALUES (:username, :password, :role, :status)";
        $stmt = $this->conn->prepare($query);

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hash);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":status", $status);
        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
