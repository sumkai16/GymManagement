<?php
require_once __DIR__ . '/../config/database.php';

class Booking {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Create a new trainer booking
     */
    public function createBooking($member_id, $trainer_id, $booking_date, $booking_time) {
        try {
            // Normalize and validate date (Y-m-d)
            $dateObj = DateTime::createFromFormat('Y-m-d', $booking_date);
            $dateValid = $dateObj && $dateObj->format('Y-m-d') === $booking_date;
            if (!$dateValid) {
                return ['success' => false, 'message' => 'Invalid date format.'];
            }

            // Normalize and validate time (H:i or H:i:s), store as H:i:s
            $timeStr = $booking_time;
            $timeObj = DateTime::createFromFormat('H:i:s', $timeStr);
            if (!$timeObj) {
                $timeObj = DateTime::createFromFormat('H:i', $timeStr);
                if ($timeObj) {
                    $timeStr = $timeObj->format('H:i:s');
                }
            }
            if (!$timeObj) {
                return ['success' => false, 'message' => 'Invalid time format.'];
            }

            // Ensure combined datetime is in the future
            $combinedStr = $dateObj->format('Y-m-d') . ' ' . $timeStr;
            $combinedObj = DateTime::createFromFormat('Y-m-d H:i:s', $combinedStr);
            if (!$combinedObj) {
                return ['success' => false, 'message' => 'Invalid date/time selection.'];
            }
            $now = new DateTime();
            if ($combinedObj <= $now) {
                return ['success' => false, 'message' => 'Cannot book sessions in the past.'];
            }

            // Reassign normalized values
            $booking_date = $dateObj->format('Y-m-d');
            $booking_time = $timeStr; // normalized H:i:s

            // Check if trainer is already booked at this time
            if ($this->isTrainerBooked($trainer_id, $booking_date, $booking_time)) {
                return ['success' => false, 'message' => 'Trainer is not available at this time slot.'];
            }

            // Check if member already has a booking at this time
            if ($this->isMemberBooked($member_id, $booking_date, $booking_time)) {
                return ['success' => false, 'message' => 'You already have a booking at this time.'];
            }

            // Insert booking
            $stmt = $this->conn->prepare("INSERT INTO trainer_bookings (member_id, trainer_id, booking_date, booking_time, status) VALUES (:member_id, :trainer_id, :booking_date, :booking_time, 'pending')");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->bindParam(':booking_date', $booking_date);
            $stmt->bindParam(':booking_time', $booking_time);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Booking created successfully! Awaiting trainer confirmation.', 'booking_id' => $this->conn->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Failed to create booking.'];
            }
        } catch (PDOException $e) {
            error_log("Create booking error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred.'];
        }
    }

    /**
     * Check if trainer is already booked at a specific date and time
     */
    public function isTrainerBooked($trainer_id, $booking_date, $booking_time) {
        try {
            $stmt = $this->conn->prepare("SELECT booking_id FROM trainer_bookings WHERE trainer_id = :trainer_id AND booking_date = :booking_date AND booking_time = :booking_time AND status IN ('pending', 'confirmed')");
            $stmt->bindParam(':trainer_id', $trainer_id, PDO::PARAM_INT);
            $stmt->bindParam(':booking_date', $booking_date);
            $stmt->bindParam(':booking_time', $booking_time);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Check trainer booking error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if member already has a booking at a specific date and time
     */
    public function isMemberBooked($member_id, $booking_date, $booking_time) {
        try {
            $stmt = $this->conn->prepare("SELECT booking_id FROM trainer_bookings WHERE member_id = :member_id AND booking_date = :booking_date AND booking_time = :booking_time AND status IN ('pending', 'confirmed')");
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->bindParam(':booking_date', $booking_date);
            $stmt->bindParam(':booking_time', $booking_time);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Check member booking error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all bookings for a member
     */
    public function getMemberBookings($member_id, $status = null) {
        try {
            $sql = "SELECT tb.*, t.full_name as trainer_name, t.specialty, t.email as trainer_email, t.phone as trainer_phone 
                    FROM trainer_bookings tb 
                    JOIN trainers t ON tb.trainer_id = t.trainer_id 
                    WHERE tb.member_id = :member_id";
            
            if ($status) {
                $sql .= " AND tb.status = :status";
            }
            
            $sql .= " ORDER BY tb.booking_date DESC, tb.booking_time DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            
            if ($status) {
                $stmt->bindParam(':status', $status);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get member bookings error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get upcoming bookings for a member
     */
    public function getUpcomingBookings($member_id) {
        try {
            $sql = "SELECT tb.*, t.full_name as trainer_name, t.specialty 
                    FROM trainer_bookings tb 
                    JOIN trainers t ON tb.trainer_id = t.trainer_id 
                    WHERE tb.member_id = :member_id 
                    AND (tb.booking_date > CURDATE() OR (tb.booking_date = CURDATE() AND tb.booking_time > CURTIME()))
                    AND tb.status IN ('pending', 'confirmed')
                    ORDER BY tb.booking_date ASC, tb.booking_time ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get upcoming bookings error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking($booking_id, $member_id) {
        try {
            // Verify booking belongs to member
            $stmt = $this->conn->prepare("SELECT booking_id FROM trainer_bookings WHERE booking_id = :booking_id AND member_id = :member_id");
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            $stmt->bindParam(':member_id', $member_id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Booking not found or access denied.'];
            }

            // Update status to cancelled
            $stmt = $this->conn->prepare("UPDATE trainer_bookings SET status = 'cancelled' WHERE booking_id = :booking_id");
            $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Booking cancelled successfully.'];
            } else {
                return ['success' => false, 'message' => 'Failed to cancel booking.'];
            }
        } catch (PDOException $e) {
            error_log("Cancel booking error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred.'];
        }
    }
}
?>

