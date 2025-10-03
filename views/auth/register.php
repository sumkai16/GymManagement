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
        header("Location: ../dashboard/member.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <h2>Register</h2>
    <div class="form">
        <?php if (!empty($_SESSION['flash'])): ?>
            <p class="flash"><?=$_SESSION['flash']?></p>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="POST" action="../../controllers/AuthController.php">
            <input type="hidden" name="action" value="register">
            <label>Username:</label>
            <input type="text" name="username" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Register</button>
        </form>
        <p id="register">Already registered? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
