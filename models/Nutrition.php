<?php
class Nutrition {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }
    public function getMealsByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM nutrition_logs WHERE user_id = :user_id ORDER BY date ASC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMeal($user_id, $food_item, $calories = 0, $protein = 0, $carbs = 0, $fats = 0, $date = null) {
        if (empty($food_item)) { return false; }
        $date = $date ?: date('Y-m-d');
        $stmt = $this->conn->prepare("INSERT INTO nutrition_logs (user_id, food_item, calories, protein, carbs, fats, date)
            VALUES (:user_id, :food_item, :calories, :protein, :carbs, :fats, :date)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':food_item', $food_item);
        $stmt->bindParam(':calories', $calories, PDO::PARAM_INT);
        $stmt->bindParam(':protein', $protein);
        $stmt->bindParam(':carbs', $carbs);
        $stmt->bindParam(':fats', $fats);
        $stmt->bindParam(':date', $date);
        if ($stmt->execute()) {
            return (int)$this->conn->lastInsertId();
        }
        return false;
    }

    public function updateMeal($nutrition_id, $user_id, $food_item, $calories = 0, $protein = 0, $carbs = 0, $fats = 0, $date = null) {
        $date = $date ?: date('Y-m-d');
        $stmt = $this->conn->prepare("UPDATE nutrition_logs SET food_item = :food_item, calories = :calories, protein = :protein, carbs = :carbs, fats = :fats, date = :date
            WHERE nutrition_id = :id AND user_id = :user_id");
        $stmt->bindParam(':food_item', $food_item);
        $stmt->bindParam(':calories', $calories, PDO::PARAM_INT);
        $stmt->bindParam(':protein', $protein);
        $stmt->bindParam(':carbs', $carbs);
        $stmt->bindParam(':fats', $fats);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id', $nutrition_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteMeal($nutrition_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM nutrition_logs WHERE nutrition_id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $nutrition_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Supplement methods
    public function addSupplement($user_id, $supplement_name, $dosage = '', $time_taken = null, $date = null) {
        if (empty($supplement_name)) { return false; }
        $date = $date ?: date('Y-m-d');
        $stmt = $this->conn->prepare("INSERT INTO supplement_logs (user_id, supplement_name, dosage, time_taken, date)
            VALUES (:user_id, :supplement_name, :dosage, :time_taken, :date)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':supplement_name', $supplement_name);
        $stmt->bindParam(':dosage', $dosage);
        $stmt->bindParam(':time_taken', $time_taken);
        $stmt->bindParam(':date', $date);
        if ($stmt->execute()) {
            return (int)$this->conn->lastInsertId();
        }
        return false;
    }

    public function deleteSupplement($supplement_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM supplement_logs WHERE supplement_id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $supplement_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getSupplementsByDate($user_id, $date = null) {
        $date = $date ?: date('Y-m-d');
        $stmt = $this->conn->prepare("SELECT * FROM supplement_logs WHERE user_id = :user_id AND date = :date ORDER BY time_taken ASC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMealsByDate($user_id, $date) {
        $stmt = $this->conn->prepare("SELECT * FROM nutrition_logs WHERE user_id = :user_id AND date = :date ORDER BY nutrition_id DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDailyTotals($user_id, $date) {
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(calories),0) as calories, COALESCE(SUM(protein),0) as protein, COALESCE(SUM(carbs),0) as carbs, COALESCE(SUM(fats),0) as fats
            FROM nutrition_logs WHERE user_id = :user_id AND date = :date");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['calories'=>0,'protein'=>0,'carbs'=>0,'fats'=>0];
    }

    public function getTotalsByRange($user_id, $start_date, $end_date) {
        $stmt = $this->conn->prepare("SELECT date, COALESCE(SUM(calories),0) as calories, COALESCE(SUM(protein),0) as protein, COALESCE(SUM(carbs),0) as carbs, COALESCE(SUM(fats),0) as fats
            FROM nutrition_logs WHERE user_id = :user_id AND date BETWEEN :start AND :end
            GROUP BY date ORDER BY date ASC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':start', $start_date);
        $stmt->bindParam(':end', $end_date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentMeals($user_id, $limit = 20) {
        $stmt = $this->conn->prepare("SELECT * FROM nutrition_logs WHERE user_id = :user_id ORDER BY date DESC, nutrition_id DESC LIMIT :lim");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodayMacros($user_id) {
        $today = date('Y-m-d');
        return $this->getDailyTotals($user_id, $today);
    }

    // Food database helpers (per-100g macros)
    public function searchFoodByName($query, $limit = 10) {
        $q = '%' . strtolower(trim($query)) . '%';
        $stmt = $this->conn->prepare("SELECT food_id, name, kcal_per_100g, protein_per_100g, carbs_per_100g, fats_per_100g FROM food_database WHERE LOWER(name) LIKE :q ORDER BY name ASC LIMIT :lim");
        $stmt->bindParam(':q', $q);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFoodById($food_id) {
        $stmt = $this->conn->prepare("SELECT food_id, name, kcal_per_100g, protein_per_100g, carbs_per_100g, fats_per_100g FROM food_database WHERE food_id = :id LIMIT 1");
        $stmt->bindParam(':id', $food_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>