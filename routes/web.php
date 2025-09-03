<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

$db = (new Database())->connect();
$auth = new AuthController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'register') {
        $auth->register($_POST['username'], $_POST['password']);
        header("Location: /login");
    } elseif ($_POST['action'] === 'login') {
        $user = $auth->login($_POST['username'], $_POST['password']);
        if ($user) {
            session_start();
            $_SESSION['user'] = $user;
            header("Location: /dashboard");
        } else {
            echo "Invalid credentials";
        }
    }
}
?>

