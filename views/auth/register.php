<?php
session_start();

// If user already logged in, skip login/register
if (isset($_SESSION['user_id'])) {
     $role = $_SESSION['role'] ?? 'member';
    if ($role === 'admin') {
        header("Location: ../views/admin/admin_dashboard.php");
    } elseif ($role === 'trainer') {
        header("Location: ../views/trainer/trainer_dashboard.php");
    } elseif ($role === 'member') {
        header("Location: ../views/member/member_dashboard.php");
    }else {
        header("Location: ../views/guest.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/auth.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="auth-container">
        <div class="logo-section">
            <h1><i class='bx bx-dumbbell'></i> FITNEXUS</h1>
            <p>JOIN OUR FITNESS COMMUNITY</p>
        </div>
        
        <?php include __DIR__ . '/../utilities/alert.php'; ?>
        
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="flash"><?=$_SESSION['flash']?></div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <form method="POST" action="../../controllers/AuthController.php">
            <input type="hidden" name="action" value="register">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="submit-btn">
                CREATE ACCOUNT
            </button>
        </form>
        
        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Sign in here</a></p>
        </div>
    </div>
</body>
</html>
