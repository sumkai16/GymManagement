
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
}
?>