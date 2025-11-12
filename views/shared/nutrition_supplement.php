<?php
// Shared Supplement Tracker view for both member and trainer
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Nutrition.php';

$db = (new Database())->getConnection();
$nutrition = new Nutrition($db);
$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$supplements = $nutrition->getSupplementsByDate($user_id, $today);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition - Supplements - FitNexus</title>
    <?php $ms = __DIR__.'/../../assets/css/member_styles.css'; $ns = __DIR__.'/../../assets/css/nutrition_styles.css'; ?>
    <link rel="stylesheet" href="../../assets/css/member_styles.css?v=<?= @filemtime($ms) ?: time() ?>">
    <link rel="stylesheet" href="../../assets/css/nutrition_styles.css?v=<?= @filemtime($ns) ?: time() ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="page-nutrition">
    <?php include __DIR__ . '/../components/dynamic_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-wrapper">
            <div class="nutrition-header">
                <h1>Supplement Tracker</h1>
                <p>Log your supplements and track your daily intake.</p>
            </div>
            <div class="nutrition-grid">
                <div class="nutrition-card">
                    <h3><i class='bx bx-plus-circle'></i> Add Supplement</h3>
                    <form method="POST" action="../../controllers/NutritionController.php" class="quick-add-form">
                        <input type="hidden" name="action" value="add_supplement">
                        <div class="form-field">
                            <label>Supplement</label>
                            <input type="text" name="supplement_name" required placeholder="e.g. Whey Protein">
                        </div>
                        <div class="form-field">
                            <label>Dosage</label>
                            <input type="text" name="dosage" placeholder="e.g. 25g, 1 capsule">
                        </div>
                        <div class="form-field">
                            <label>Time</label>
                            <input type="time" name="time_taken">
                        </div>
                        <div class="form-field">
                            <label>Date</label>
                            <input type="date" name="date" value="<?= htmlspecialchars($today) ?>">
                        </div>
                        <button type="submit" class="btn-add"><i class='bx bx-plus'></i> Add</button>
                    </form>
                </div>
                <div class="nutrition-card">
                    <h3><i class='bx bx-pills'></i> Today's Supplements</h3>
                    <div class="supplement-list">
                        <?php if (empty($supplements)): ?>
                            <div class="empty-state">
                                <i class='bx bx-capsule'></i>
                                <p>No supplements logged today.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($supplements as $supplement): ?>
                                <div class="meal-row">
                                    <div class="food-name"><?= htmlspecialchars($supplement['supplement_name']) ?></div>
                                    <div class="macro"><?= htmlspecialchars($supplement['dosage'] ?: 'N/A') ?></div>
                                    <div class="macro"><?= htmlspecialchars($supplement['time_taken'] ?: 'N/A') ?></div>
                                    <div class="date"><?= htmlspecialchars($supplement['date']) ?></div>
                                    <form method="POST" action="../../controllers/NutritionController.php" onsubmit="return confirm('Delete this supplement?');" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_supplement">
                                        <input type="hidden" name="supplement_id" value="<?= (int)$supplement['supplement_id'] ?>">
                                        <button type="submit" class="btn-delete"><i class='bx bx-trash'></i> Delete</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="nutrition-card">
                    <h3><i class='bx bx-calendar-check'></i> Common Schedule</h3>
                    <div class="schedule-grid">
                        <div class="schedule-item">
                            <h4>Morning</h4>
                            <ul>
                                <li>Multivitamin</li>
                                <li>Omega-3</li>
                                <li>Vitamin D</li>
                            </ul>
                        </div>
                        <div class="schedule-item">
                            <h4>Pre-Workout</h4>
                            <ul>
                                <li>Creatine</li>
                                <li>Pre-workout</li>
                                <li>BCAA</li>
                            </ul>
                        </div>
                        <div class="schedule-item">
                            <h4>Post-Workout</h4>
                            <ul>
                                <li>Whey Protein</li>
                                <li>Glutamine</li>
                                <li>Electrolytes</li>
                            </ul>
                        </div>
                        <div class="schedule-item">
                            <h4>Evening</h4>
                            <ul>
                                <li>Magnesium</li>
                                <li>Zinc</li>
                                <li>Casein Protein</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
