<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Trainer.php';
require_once __DIR__ . '/../models/Booking.php';

class TrainerController {
    private $conn;
    private $trainerModel;
    private $bookingModel;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->trainerModel = new Trainer();
        $this->bookingModel = new Booking();
    }

    public function getTrainerByUser($userId) {
        return $this->trainerModel->getTrainerByUserId($userId);
    }

    public function getDashboardStats($userId) {
        $trainer = $this->getTrainerByUser($userId);
        if (!$trainer) {
            return ['members' => 0, 'upcoming' => 0, 'pending' => 0, 'today' => 0];
        }
        $counts = $this->bookingModel->getCountsForTrainer($trainer['trainer_id']);
        $counts['today'] = $this->getSessionsTodayCount($trainer['trainer_id']);
        return $counts;
    }

    private function getSessionsTodayCount($trainerId) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as c FROM trainer_bookings WHERE trainer_id = :trainer_id AND booking_date = CURDATE() AND status IN ('pending','confirmed')");
            $stmt->bindParam(':trainer_id', $trainerId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['c'] ?? 0);
        } catch (PDOException $e) {
            error_log('Sessions today count error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getMyClients($userId) {
        $trainer = $this->getTrainerByUser($userId);
        if (!$trainer) return [];
        return $this->trainerModel->getTrainerClients($trainer['trainer_id']);
    }

    public function getPendingBookings($userId) {
        $trainer = $this->getTrainerByUser($userId);
        if (!$trainer) return [];
        return $this->bookingModel->getTrainerBookings($trainer['trainer_id'], 'pending', true);
    }

    public function getUpcomingBookings($userId) {
        $trainer = $this->getTrainerByUser($userId);
        if (!$trainer) return [];
        return $this->bookingModel->getTrainerBookings($trainer['trainer_id'], 'confirmed', true);
    }

    public function updateBookingStatus($userId, $bookingId, $status) {
        $trainer = $this->getTrainerByUser($userId);
        if (!$trainer) {
            return ['success' => false, 'message' => 'Trainer profile not found'];
        }
        return $this->bookingModel->updateBookingStatus($bookingId, $trainer['trainer_id'], $status);
    }
}
