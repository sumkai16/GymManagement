<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

// Init DB
$database = new Database();
$db = $database->getConnection();

// Init User model
$userModel = new User($db);

// ✅ Handle logout first (works with GET)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    session_destroy();

    header("Location: ../views/auth/login.php");
    exit;
}

// ✅ Handle register/login only on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login'; // default: login
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($action === 'register') {
        $role = "member"; // default role
        $status = "inactive";

        if ($userModel->register($username, $password, $role, $status)) {
            $_SESSION['flash'] = "Registration successful. Please login.";
            header("Location: ../views/auth/login.php");
            exit;
        } else {
            $_SESSION['flash'] = "Registration failed. Try again.";
            header("Location: ../views/auth/register.php");
            exit;
        }
    }

    if ($action === 'login') {
        $user = $userModel->login($username, $password);
        if ($user) {
            session_regenerate_id(true); // prevent session fixation
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            // Redirect by role
            if ($user['role'] === 'admin') {
                header("Location: ../views/dashboard/admin.php");
            } elseif ($user['role'] === 'trainer') {
                header("Location: ../views/dashboard/trainer.php");
            } elseif ($user['role'] === 'member') {
                header("Location: ../views/member/member_dashboard.php");
            } else {
                $_SESSION['flash'] = "Login failed. Try again.";
                header("Location: ../views/auth/login.php");
            }
            exit;
        } else {
            $_SESSION['flash'] = "Invalid username or password.";
            header("Location: ../views/auth/login.php");
            exit;
        }
    }
}
?>
