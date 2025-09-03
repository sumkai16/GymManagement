<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function register($username, $password) {
        return $this->user->register($username, $password);
    }

    public function login($username, $password) {
        return $this->user->login($username, $password);
    }
}
?>
