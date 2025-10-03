<?php
session_start();

// If user already logged in, skip login/register
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'member';
    if ($role === 'admin') {
        header("Location: ../dashboard/admin.php");
    } elseif ($role === 'trainer') {
        header("Location: ../dashboard/trainer.php");
    } else {
        header("Location: ../views/member_dashboard.php");
    }
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <h2>Login</h2>
    <div class="form">
        <form method="POST" action="../../controllers/AuthController.php">
            <input type="hidden" name="action" value="login">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Login</button>
            <p id="register">Not registered? <a href="register.php">Register here</a></p>
        </form>
    </div>
</body>
</html>
