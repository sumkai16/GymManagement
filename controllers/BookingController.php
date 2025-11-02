<?php
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Member.php';

class BookingController {
    private $bookingModel;
    private $memberModel;

    public function __construct() {
        $this->bookingModel = new Booking();
        $this->memberModel = new Member();
    }

    public function createBooking($userId, $trainerId, $date, $time) {
        $member = $this->memberModel->getMemberByUserId($userId);
        if (!$member) {
            return ['success' => false, 'message' => 'Member profile not found.'];
        }
        return $this->bookingModel->createBooking($member['member_id'], $trainerId, $date, $time);
    }

    public function cancelBooking($bookingId, $userId) {
        $member = $this->memberModel->getMemberByUserId($userId);
        if (!$member) {
            return ['success' => false, 'message' => 'Member profile not found.'];
        }
        return $this->bookingModel->cancelBooking($bookingId, $member['member_id']);
    }

    public function getUpcomingBookings($userId) {
        $member = $this->memberModel->getMemberByUserId($userId);
        if (!$member) {
            return [];
        }
        return $this->bookingModel->getUpcomingBookings($member['member_id']);
    }

    public function getMemberBookings($userId) {
        $member = $this->memberModel->getMemberByUserId($userId);
        if (!$member) {
            return [];
        }
        return $this->bookingModel->getMemberBookings($member['member_id']);
    }
}
