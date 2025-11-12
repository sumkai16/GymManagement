<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Nutrition.php';

class NutritionController {
    private $db;
    private $model;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->model = new Nutrition($this->db);
    }

    public function handle() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $user_id = $_SESSION['user_id'] ?? 0;
        if (!$user_id) { $this->redirectBack('Unauthorized'); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            switch ($action) {
                case 'add_meal':
                    // Support auto-calc via food_id/name + weight_g; fallback to manual fields
                    $food_item = trim($_POST['food_item'] ?? '');
                    $food_id   = (int)($_POST['food_id'] ?? 0);
                    $weight_g  = (float)($_POST['weight_g'] ?? 0);
                    $date      = $_POST['date'] ?? null;
                    $cal = (float)($_POST['calories'] ?? 0);
                    $pro = (float)($_POST['protein'] ?? 0);
                    $car = (float)($_POST['carbs'] ?? 0);
                    $fat = (float)($_POST['fats'] ?? 0);
                    if (($food_id || $food_item) && $weight_g > 0) {
                        // Compute macros from food database
                        $foodRow = null;
                        if ($food_id) {
                            $foodRow = $this->model->getFoodById($food_id);
                        }
                        if (!$foodRow && $food_item) {
                            $hits = $this->model->searchFoodByName($food_item, 1);
                            if (!empty($hits)) { $foodRow = $hits[0]; }
                        }
                        if ($foodRow) {
                            $food_item = $food_item ?: ($foodRow['name'] ?? 'Food');
                            $cal = round(((float)$foodRow['kcal_per_100g'] * $weight_g) / 100);
                            $pro = round(((float)$foodRow['protein_per_100g'] * $weight_g) / 100, 2);
                            $car = round(((float)$foodRow['carbs_per_100g'] * $weight_g) / 100, 2);
                            $fat = round(((float)$foodRow['fats_per_100g'] * $weight_g) / 100, 2);
                        }
                    }
                    $id = $this->model->addMeal($user_id, $food_item, $cal, $pro, $car, $fat, $date);
                    $msg = $id ? 'Meal added' : 'Failed to add meal';
                    $this->redirectBack($msg, (bool)$id);
                case 'update_meal':
                    $ok = $this->model->updateMeal(
                        (int)($_POST['nutrition_id'] ?? 0), $user_id,
                        trim($_POST['food_item'] ?? ''),
                        (int)($_POST['calories'] ?? 0),
                        (float)($_POST['protein'] ?? 0),
                        (float)($_POST['carbs'] ?? 0),
                        (float)($_POST['fats'] ?? 0),
                        $_POST['date'] ?? null
                    );
                    $this->redirectBack($ok ? 'Meal updated' : 'Failed to update meal', $ok);
                case 'delete_meal':
                    $ok = $this->model->deleteMeal((int)($_POST['nutrition_id'] ?? 0), $user_id);
                    $this->redirectBack($ok ? 'Meal deleted' : 'Failed to delete meal', $ok);
                case 'add_supplement':
                    $id = $this->model->addSupplement($user_id,
                        trim($_POST['supplement_name'] ?? ''),
                        trim($_POST['dosage'] ?? ''),
                        $_POST['time_taken'] ?? null,
                        $_POST['date'] ?? null
                    );
                    $msg = $id ? 'Supplement added' : 'Failed to add supplement';
                    $this->redirectBack($msg, (bool)$id);
                case 'delete_supplement':
                    $ok = $this->model->deleteSupplement((int)($_POST['supplement_id'] ?? 0), $user_id);
                    $this->redirectBack($ok ? 'Supplement deleted' : 'Failed to delete supplement', $ok);
            }
        }
        // GET helpers (AJAX)
        if (isset($_GET['action'])) {
            header('Content-Type: application/json');
            switch ($_GET['action']) {
                case 'today_totals':
                    echo json_encode($this->model->getTodayMacros($user_id));
                    return;
                case 'search_food':
                    $q = $_GET['q'] ?? '';
                    echo json_encode($this->model->searchFoodByName($q, 10));
                    return;
                case 'compute_macros':
                    $weight = (float)($_GET['weight_g'] ?? 0);
                    $fid = (int)($_GET['food_id'] ?? 0);
                    $name = trim($_GET['food_name'] ?? '');
                    $row = null;
                    if ($fid) { $row = $this->model->getFoodById($fid); }
                    if (!$row && $name) {
                        $hits = $this->model->searchFoodByName($name, 1);
                        if (!empty($hits)) { $row = $hits[0]; }
                    }
                    if (!$row || $weight <= 0) {
                        echo json_encode(['success' => false, 'message' => 'Food not found or weight missing']);
                        return;
                    }
                    $resp = [
                        'success' => true,
                        'food' => $row,
                        'computed' => [
                            'calories' => round(((float)$row['kcal_per_100g'] * $weight) / 100),
                            'protein'  => round(((float)$row['protein_per_100g'] * $weight) / 100, 2),
                            'carbs'    => round(((float)$row['carbs_per_100g'] * $weight) / 100, 2),
                            'fats'     => round(((float)$row['fats_per_100g'] * $weight) / 100, 2),
                        ]
                    ];
                    echo json_encode($resp);
                    return;
            }
        }
        // Unknown request
        $this->redirectBack();
    }

    private function redirectBack($message = null, $success = false) {
        if ($message) {
            $_SESSION[$success ? 'success' : 'error'] = $message;
        }
        $ref = $_SERVER['HTTP_REFERER'] ?? '../views/member/nutrition_food.php';
        header('Location: ' . $ref);
        exit;
    }
}

// Entrypoint when accessed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    (new NutritionController())->handle();
}
