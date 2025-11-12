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
                    $id = $this->model->addMeal($user_id,
                        trim($_POST['food_item'] ?? ''),
                        (int)($_POST['calories'] ?? 0),
                        (float)($_POST['protein'] ?? 0),
                        (float)($_POST['carbs'] ?? 0),
                        (float)($_POST['fats'] ?? 0),
                        $_POST['date'] ?? null
                    );
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
        // GET helpers (AJAX): today totals
        if (isset($_GET['action']) && $_GET['action'] === 'today_totals') {
            header('Content-Type: application/json');
            echo json_encode($this->model->getTodayMacros($user_id));
            return;
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
