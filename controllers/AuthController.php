<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

// Initialize DB with error handling
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Database connection failed");
    }
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    $_SESSION['error'] = "System error. Please try again later.";
    header("Location: ../views/auth/login.php");
    exit;
}

// Initialize User model
$userModel = new User($db);

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Clear all session data
    $_SESSION = array();
    
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    header("Location: ../views/auth/login.php");
    exit;
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) ?? 'login';
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate action
    if (!in_array($action, ['login', 'register'])) {
        $_SESSION['error'] = "Invalid action.";
        header("Location: ../views/auth/login.php");
        exit;
    }
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username and password are required.";
        header("Location: ../views/auth/{$action}.php");
        exit;
    }
    
    // Additional validation
    if (strlen($username) < 3 || strlen($username) > 50) {
        $_SESSION['error'] = "Username must be between 3 and 50 characters.";
        header("Location: ../views/auth/{$action}.php");
        exit;
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters long.";
        header("Location: ../views/auth/{$action}.php");
        exit;
    }

    if ($action === 'register') {
        // Check if username already exists
        try {
            $existingUser = $userModel->login($username, 'dummy'); // Check if user exists
            if ($existingUser) {
                $_SESSION['error'] = "Username already exists. Please choose a different username.";
                header("Location: ../views/auth/register.php");
                exit;
            }
        } catch (Exception $e) {
            // If login fails, user doesn't exist (which is what we want)
        }
        
        $role = "guest";    // default role
        $status = "inactive"; // default status

        try {
            if ($userModel->register($username, $password, $role, $status)) {
                $_SESSION['success'] = "Registration successful. User still needs to be activated by admin.";
                header("Location: ../views/auth/login.php");
                exit;
            } else {
                $_SESSION['error'] = "Registration failed. Please try again.";
                header("Location: ../views/auth/register.php");
                exit;
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $_SESSION['error'] = "Registration failed. Please try again.";
            header("Location: ../views/auth/register.php");
            exit;
        }
    }

    if ($action === 'login') {
        try {
            $user = $userModel->login($username, $password);

            if (!$user) {
                $_SESSION['error'] = "Invalid username or password.";
                header("Location: ../views/auth/login.php");
                exit;
            }

            // Validate user data structure
            if (!isset($user['user_id'], $user['username'], $user['status'], $user['role'])) {
                error_log("Incomplete user data for user: " . $username);
                $_SESSION['error'] = "Account data incomplete. Please contact admin.";
                header("Location: ../views/auth/login.php");
                exit;
            }

            // Check account status
            if ($user['status'] !== "active") {
                $statusMessage = "Your account is inactive. Please contact admin.";
                if ($user['status'] === "inactive") {
                    $statusMessage = "Your account is pending activation. Please contact admin.";
                }
                $_SESSION['error'] = $statusMessage;
                header("Location: ../views/auth/login.php");
                exit;
            }

            // Secure session handling
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
            $_SESSION['role'] = htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8');
            $_SESSION['status'] = htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8');
            $_SESSION['login_time'] = time();
            $_SESSION['success'] = ucfirst($user['role']) . "Login successful!";

            // Role-based redirect with validation
            $redirect = null;
            switch ($user['role']) {
                case 'admin':
                    $redirect = '../views/admin/admin_dashboard.php';
                    break;
                case 'trainer':
                    $redirect = '../views/trainers/trainers_dashboard.php';
                    break;
                case 'member':
                    $redirect = '../views/member/member_dashboard.php';
                    break;
                case 'guest':
                    $redirect = '../views/guest.php';
                    break;
                default:
                    error_log("Invalid user role: " . $user['role']);
                    $_SESSION['error'] = "Invalid user role.";
                    header("Location: ../views/auth/login.php");
                    exit;
            }
            
            // Validate redirect path
            if (!$redirect || !file_exists(__DIR__ . '/' . $redirect)) {
                error_log("Invalid redirect path: " . $redirect);
                $_SESSION['error'] = "System error. Please contact admin.";
                header("Location: ../views/auth/login.php");
                exit;
            }

            // Remove sleep() for better user experience
            header("Location: $redirect");
            exit;
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['error'] = "Login failed. Please try again.";
            header("Location: ../views/auth/login.php");
            exit;
        }
    }

    // Unknown action
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../views/auth/login.php");
    exit;
}

// Handle GET requests (redirect to login if not logout)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($_GET['action']) || $_GET['action'] !== 'logout')) {
    header("Location: ../views/auth/login.php");
    exit;
}
?>