
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}
// Fetch user's meals from the database
require_once '../../config/database.php';
require_once '../../models/Nutrition.php';

$nutritionModel = new Nutrition((new Database())->getConnection());
$user_id = $_SESSION['user_id'] ?? 0;
$meals = $nutritionModel->getMealsByUser($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition - Food - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/nutrition_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Nutrition - Food</h1>
                <p>Track your meals, plan your nutrition, and monitor your calorie intake.</p>
            </div>
            <div class="nutrition-container">
                <div class="nutrition-section">
                    <h3><i class='bx bx-bowl-rice'></i> Today's Meals</h3>
                    <?php foreach ($meals as $meal): ?>
                        <div class="meal-item">
                            <div class="meal-time">
                                <span class="time"><?=htmlspecialchars($meal['time'])?></span>
                                <span class="meal-type"><?=htmlspecialchars($meal['type'])?></span>
                            </div>
                            <div class="meal-details">
                                <h4><?=htmlspecialchars($meal['name'])?></h4>
                                <p><?=htmlspecialchars($meal['calories'])?> kcal</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Add more sections for calorie tracker, macros, etc. -->
            </div>
        </div>
    </div>
</body>
</html>