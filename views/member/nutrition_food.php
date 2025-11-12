
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}
require_once '../../config/database.php';
require_once '../../models/Nutrition.php';

$db = (new Database())->getConnection();
$nutritionModel = new Nutrition($db);
$user_id = $_SESSION['user_id'] ?? 0;
$today = date('Y-m-d');
$totals = $nutritionModel->getDailyTotals($user_id, $today);
$meals = $nutritionModel->getMealsByDate($user_id, $today);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition - Food - FitNexus</title>
    <?php $ms = __DIR__.'/../../assets/css/member_styles.css'; $ns = __DIR__.'/../../assets/css/nutrition_styles.css'; ?>
    <link rel="stylesheet" href="../../assets/css/member_styles.css?v=<?= @filemtime($ms) ?: time() ?>">
    <link rel="stylesheet" href="../../assets/css/nutrition_styles.css?v=<?= @filemtime($ns) ?: time() ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="page-nutrition">
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>
    <div class="main-content">
        <div class="content-wrapper">
            <div class="nutrition-header">
                <h1>Food Tracker</h1>
                <p>Log meals and see your daily nutrition at a glance.</p>
            </div>
            <div class="nutrition-grid">
                <!-- Add Meal Card -->
                <div class="nutrition-card">
                    <h3><i class='bx bx-plus-circle'></i> Add Meal</h3>
                    <form method="POST" action="../../controllers/NutritionController.php" class="quick-add-form">
                        <input type="hidden" name="action" value="add_meal">
                        <div class="form-field">
                            <label>Food</label>
                            <input type="text" name="food_item" required placeholder="e.g. Grilled Chicken">
                        </div>
                        <div class="form-field">
                            <label>Calories</label>
                            <input type="number" name="calories" min="0" step="1" value="0" placeholder="0">
                        </div>
                        <div class="form-field">
                            <label>Protein (g)</label>
                            <input type="number" name="protein" min="0" step="0.1" value="0" placeholder="0">
                        </div>
                        <div class="form-field">
                            <label>Carbs (g)</label>
                            <input type="number" name="carbs" min="0" step="0.1" value="0" placeholder="0">
                        </div>
                        <div class="form-field">
                            <label>Fats (g)</label>
                            <input type="number" name="fats" min="0" step="0.1" value="0" placeholder="0">
                        </div>
                        <div class="form-field">
                            <label>Date</label>
                            <input type="date" name="date" value="<?= htmlspecialchars($today) ?>">
                        </div>
                        <button type="submit" class="btn-add"><i class='bx bx-plus'></i> Add</button>
                    </form>
                </div>
                <!-- Today's Totals Card -->
                <div class="nutrition-card">
                    <h3><i class='bx bx-pie-chart-alt-2'></i> Today's Totals</h3>
                    <div class="totals-grid">
                        <div class="total-card">
                            <i class='bx bx-fire icon'></i>
                            <div class="value"><?= (int)($totals['calories'] ?? 0) ?></div>
                            <div class="label">Calories</div>
                        </div>
                        <div class="total-card">
                            <i class='bx bx-dna icon'></i>
                            <div class="value"><?= (float)($totals['protein'] ?? 0) ?>g</div>
                            <div class="label">Protein</div>
                        </div>
                        <div class="total-card">
                            <i class='bx bx-baguette icon'></i>
                            <div class="value"><?= (float)($totals['carbs'] ?? 0) ?>g</div>
                            <div class="label">Carbs</div>
                        </div>
                        <div class="total-card">
                            <i class='bx bx-droplet icon'></i>
                            <div class="value"><?= (float)($totals['fats'] ?? 0) ?>g</div>
                            <div class="label">Fats</div>
                        </div>
                    </div>
                </div>
                <!-- Today's Meals Card -->
                <div class="nutrition-card">
                    <h3><i class='bx bx-bowl-rice'></i> Today's Meals</h3>
                    <div class="meals-list">
                        <?php if (empty($meals)): ?>
                            <div class="empty-state">
                                <i class='bx bx-restaurant'></i>
                                <p>No meals logged today.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($meals as $meal): ?>
                                <div class="meal-row">
                                    <div class="food-name"><?= htmlspecialchars($meal['food_item']) ?></div>
                                    <div class="macro"><span><?= (int)$meal['calories'] ?></span> kcal</div>
                                    <div class="macro"><span><?= (float)$meal['protein'] ?></span> g</div>
                                    <div class="macro"><span><?= (float)$meal['carbs'] ?></span> g</div>
                                    <div class="macro"><span><?= (float)$meal['fats'] ?></span> g</div>
                                    <div class="date"><?= htmlspecialchars($meal['date']) ?></div>
                                    <form method="POST" action="../../controllers/NutritionController.php" onsubmit="return confirm('Delete this meal?');" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_meal">
                                        <input type="hidden" name="nutrition_id" value="<?= (int)$meal['nutrition_id'] ?>">
                                        <button type="submit" class="btn-delete"><i class='bx bx-trash'></i> Delete</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>