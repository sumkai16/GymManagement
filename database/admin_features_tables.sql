-- Additional tables for admin features
-- Run this script to add the missing tables for the admin functionality

-- Payments table
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` enum('membership','personal_training','class','other') NOT NULL,
  `payment_method` enum('cash','credit_card','debit_card','bank_transfer','gcash','paymaya') NOT NULL,
  `payment_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('completed','pending','cancelled') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Gym settings table
CREATE TABLE IF NOT EXISTS `gym_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_name` varchar(100) NOT NULL DEFAULT 'FitNexus Gym',
  `gym_address` text DEFAULT NULL,
  `gym_phone` varchar(20) DEFAULT NULL,
  `gym_email` varchar(100) DEFAULT NULL,
  `gym_website` varchar(100) DEFAULT NULL,
  `monthly_fee` decimal(10,2) DEFAULT 1500.00,
  `annual_fee` decimal(10,2) DEFAULT 15000.00,
  `operating_hours` varchar(50) DEFAULT '6:00 AM - 10:00 PM',
  `max_capacity` int(11) DEFAULT 200,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- System settings table
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `maintenance_mode` enum('on','off') DEFAULT 'off',
  `registration_enabled` enum('on','off') DEFAULT 'on',
  `email_notifications` enum('on','off') DEFAULT 'on',
  `backup_frequency` enum('daily','weekly','monthly') DEFAULT 'daily',
  `session_timeout` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default gym settings
INSERT IGNORE INTO `gym_settings` (`id`, `gym_name`, `gym_address`, `gym_phone`, `gym_email`, `gym_website`, `monthly_fee`, `annual_fee`, `operating_hours`, `max_capacity`) VALUES
(1, 'FitNexus Gym', '', '', '', '', 1500.00, 15000.00, '6:00 AM - 10:00 PM', 200);

-- Insert default system settings
INSERT IGNORE INTO `system_settings` (`id`, `maintenance_mode`, `registration_enabled`, `email_notifications`, `backup_frequency`, `session_timeout`) VALUES
(1, 'off', 'on', 'on', 'daily', 30);

-- Add some sample exercises
INSERT IGNORE INTO `exercises` (`exercise_id`, `name`, `description`, `muscle_group`, `equipment`) VALUES
(1, 'Push-ups', 'Classic bodyweight exercise for chest, shoulders, and triceps', 'Chest', 'Bodyweight'),
(2, 'Squats', 'Fundamental lower body exercise targeting quadriceps and glutes', 'Legs', 'Bodyweight'),
(3, 'Bench Press', 'Compound exercise for chest, shoulders, and triceps', 'Chest', 'Barbell'),
(4, 'Deadlift', 'Full-body compound exercise targeting back, glutes, and hamstrings', 'Back', 'Barbell'),
(5, 'Pull-ups', 'Upper body pulling exercise for back and biceps', 'Back', 'Bodyweight'),
(6, 'Overhead Press', 'Shoulder exercise using barbell or dumbbells', 'Shoulders', 'Barbell'),
(7, 'Bicep Curls', 'Isolation exercise for biceps', 'Arms', 'Dumbbells'),
(8, 'Plank', 'Core strengthening exercise', 'Core', 'Bodyweight'),
(9, 'Running', 'Cardiovascular exercise', 'Cardio', 'Bodyweight'),
(10, 'Lunges', 'Single-leg exercise for quadriceps and glutes', 'Legs', 'Bodyweight');

-- Add some sample payments (optional - for testing)
INSERT IGNORE INTO `payments` (`payment_id`, `member_id`, `amount`, `payment_type`, `payment_method`, `payment_date`, `notes`, `status`) VALUES
(1, 1, 1500.00, 'membership', 'cash', CURDATE(), 'Monthly membership fee', 'completed'),
(2, 2, 15000.00, 'membership', 'credit_card', CURDATE(), 'Annual membership fee', 'completed'),
(3, 3, 500.00, 'personal_training', 'gcash', CURDATE(), 'Personal training session', 'completed');
