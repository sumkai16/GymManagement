-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2025 at 07:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gymmanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `check_in` datetime DEFAULT current_timestamp(),
  `check_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `user_id`, `full_name`, `check_in`, `check_out`) VALUES
(11, NULL, 'Aljon', '2025-10-29 12:05:06', '2025-10-29 12:08:00'),
(12, NULL, 'Joseph', '2025-10-29 12:05:09', NULL),
(13, NULL, 'Markj', '2025-10-29 12:05:15', NULL),
(14, 19, 'Arl Sison', '2025-10-29 12:06:30', NULL),
(15, NULL, 'Markj', '2025-10-30 20:46:35', '2025-10-30 20:53:09'),
(16, 19, 'Arl Sison', '2025-10-30 20:46:39', '2025-10-30 20:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `exercise_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `muscle_group` varchar(50) DEFAULT NULL,
  `equipment` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`exercise_id`, `name`, `description`, `muscle_group`, `equipment`) VALUES
(335, 'Barbell Bench Press', 'Compound chest press lying on flat bench', 'Chest', 'Barbell'),
(336, 'Incline Barbell Bench Press', 'Upper chest focused bench press on incline', 'Chest', 'Barbell'),
(337, 'Dumbbell Bench Press', 'Chest press with dumbbells for greater range of motion', 'Chest', 'Dumbbells'),
(338, 'Incline Dumbbell Press', 'Upper chest press on incline bench', 'Chest', 'Dumbbells'),
(339, 'Dumbbell Flyes', 'Chest isolation with fly motion', 'Chest', 'Dumbbells'),
(340, 'Cable Flyes', 'Chest isolation using cable for constant tension', 'Chest', 'Cable'),
(341, 'Chest Press Machine', 'Machine-based chest press for stability', 'Chest', 'Machine'),
(342, 'Overhead Press', 'Standing or seated barbell press overhead', 'Shoulders', 'Barbell'),
(343, 'Dumbbell Shoulder Press', 'Overhead press with dumbbells', 'Shoulders', 'Dumbbells'),
(344, 'Dumbbell Lateral Raises', 'Side delt isolation raising to sides', 'Shoulders', 'Dumbbells'),
(345, 'Cable Lateral Raises', 'Side raises using cable machine', 'Shoulders', 'Cable'),
(346, 'Rear Delt Flyes', 'Rear deltoid isolation bent over', 'Shoulders', 'Dumbbells'),
(347, 'Face Pulls', 'Cable exercise for rear delts and upper back', 'Shoulders', 'Cable'),
(348, 'Shoulder Press Machine', 'Machine overhead press', 'Shoulders', 'Machine'),
(349, 'Close Grip Bench Press', 'Narrow grip bench for triceps', 'Triceps', 'Barbell'),
(350, 'Tricep Dips', 'Bodyweight dips for triceps', 'Triceps', 'Bodyweight'),
(351, 'Overhead Tricep Extension', 'Tricep extension with weight overhead', 'Triceps', 'Dumbbells'),
(352, 'Tricep Pushdown', 'Cable pushdown for tricep isolation', 'Triceps', 'Cable'),
(353, 'Skull Crushers', 'Lying tricep extension to forehead', 'Triceps', 'Barbell'),
(354, 'Barbell Row', 'Bent over row with barbell', 'Back', 'Barbell'),
(355, 'Dumbbell Row', 'Single arm row with dumbbell support', 'Back', 'Dumbbells'),
(356, 'Deadlift', 'Hip hinge lifting barbell from ground', 'Back', 'Barbell'),
(357, 'Lat Pulldown', 'Vertical pull on cable machine', 'Back', 'Cable'),
(358, 'Seated Cable Row', 'Horizontal rowing with cable', 'Back', 'Cable'),
(359, 'Pull-ups', 'Bodyweight vertical pull', 'Back', 'Bodyweight'),
(360, 'T-Bar Row', 'Rowing with T-bar or landmine setup', 'Back', 'Barbell'),
(361, 'Machine Row', 'Seated row on machine', 'Back', 'Machine'),
(362, 'Barbell Curl', 'Standing bicep curl with barbell', 'Biceps', 'Barbell'),
(363, 'Dumbbell Curl', 'Bicep curls with dumbbells', 'Biceps', 'Dumbbells'),
(364, 'Hammer Curl', 'Neutral grip bicep curl', 'Biceps', 'Dumbbells'),
(365, 'Cable Curl', 'Bicep curl using cable machine', 'Biceps', 'Cable'),
(366, 'Preacher Curl', 'Bicep curl on preacher bench', 'Biceps', 'Barbell'),
(367, 'Barbell Back Squat', 'Squat with barbell on upper back', 'Quads', 'Barbell'),
(368, 'Front Squat', 'Squat with barbell on front delts', 'Quads', 'Barbell'),
(369, 'Leg Press', 'Machine compound leg press', 'Quads', 'Machine'),
(370, 'Leg Extension', 'Quad isolation on machine', 'Quads', 'Machine'),
(371, 'Bulgarian Split Squat', 'Single leg squat with rear foot elevated', 'Quads', 'Dumbbells'),
(372, 'Goblet Squat', 'Squat holding dumbbell at chest', 'Quads', 'Dumbbells'),
(373, 'Dumbbell Lunges', 'Walking or stationary lunges', 'Quads', 'Dumbbells'),
(374, 'Romanian Deadlift', 'Hip hinge targeting hamstrings', 'Hamstrings', 'Barbell'),
(375, 'Leg Curl', 'Hamstring isolation on machine', 'Hamstrings', 'Machine'),
(376, 'Dumbbell RDL', 'Romanian deadlift with dumbbells', 'Hamstrings', 'Dumbbells'),
(377, 'Hip Thrust', 'Barbell glute bridge for hip extension', 'Glutes', 'Barbell'),
(378, 'Glute Bridge', 'Bodyweight hip extension', 'Glutes', 'Bodyweight'),
(379, 'Standing Calf Raise', 'Calf raise on machine', 'Calves', 'Machine'),
(380, 'Seated Calf Raise', 'Seated calf isolation', 'Calves', 'Machine'),
(381, 'Plank', 'Isometric core hold', 'Core', 'Bodyweight'),
(382, 'Cable Crunch', 'Kneeling ab crunch with cable', 'Core', 'Cable'),
(383, 'Hanging Leg Raises', 'Leg raise hanging from bar', 'Core', 'Bodyweight'),
(384, 'Russian Twist', 'Rotational core exercise', 'Core', 'Bodyweight'),
(385, 'Ab Wheel Rollout', 'Core extension with ab wheel', 'Core', 'Bodyweight'),
(386, 'Cable Woodchoppers', 'Rotational cable movement for obliques', 'Core', 'Cable');

-- --------------------------------------------------------

--
-- Table structure for table `food_database`
--

CREATE TABLE `food_database` (
  `food_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `kcal_per_100g` decimal(6,2) NOT NULL DEFAULT 0.00,
  `protein_per_100g` decimal(6,2) NOT NULL DEFAULT 0.00,
  `carbs_per_100g` decimal(6,2) NOT NULL DEFAULT 0.00,
  `fats_per_100g` decimal(6,2) NOT NULL DEFAULT 0.00,
  `source` varchar(32) NOT NULL DEFAULT 'local',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_database`
--

INSERT INTO `food_database` (`food_id`, `name`, `kcal_per_100g`, `protein_per_100g`, `carbs_per_100g`, `fats_per_100g`, `source`, `created_at`) VALUES
(1, 'rice, cooked', 130.00, 2.69, 28.17, 0.28, 'local', '2025-11-12 14:53:46'),
(2, 'chicken breast, cooked, skinless', 165.00, 31.00, 0.00, 3.60, 'local', '2025-11-12 14:53:46'),
(3, 'egg, whole, cooked', 155.00, 13.00, 1.10, 11.00, 'local', '2025-11-12 14:53:46'),
(4, 'banana', 89.00, 1.09, 22.84, 0.33, 'local', '2025-11-12 14:53:46'),
(5, 'oats, dry', 379.00, 13.15, 67.70, 6.52, 'local', '2025-11-12 14:53:46'),
(6, 'sweet potato, cooked', 90.00, 2.00, 20.70, 0.10, 'local', '2025-11-12 14:53:46'),
(7, 'broccoli, cooked', 35.00, 2.40, 7.20, 0.40, 'local', '2025-11-12 14:53:46'),
(8, 'olive oil', 884.00, 0.00, 0.00, 100.00, 'local', '2025-11-12 14:53:46'),
(9, 'beef, lean, cooked', 250.00, 26.10, 0.00, 15.00, 'local', '2025-11-12 14:53:46'),
(10, 'salmon, cooked', 208.00, 20.42, 0.00, 13.42, 'local', '2025-11-12 14:53:46');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `membership_type` enum('monthly','annual') DEFAULT 'monthly',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `user_id`, `full_name`, `email`, `phone`, `address`, `membership_type`, `start_date`, `end_date`, `status`) VALUES
(4, 19, 'Arl Sison', 'arlsison@gmail.com', '9877654325', 'san vicente 4, tunghaan, minglanilla, cebu', 'monthly', '2025-12-25', '2026-12-06', 'active'),
(5, 25, 'arljoshua', 'member25@gmail.com', '09123456789', 'Address to be updated', 'monthly', '2025-11-12', '2025-12-12', 'active'),
(6, 27, 'James Sison', 'james@gmail.com', '9876544325', 'Minglanilla', 'annual', '2025-11-14', '2025-12-14', 'active'),
(7, 28, 'Nico Deiparine', 'nicO@GMAIL.COM', '9832456789', 'Minglanilla', 'monthly', '2025-11-14', '2025-12-14', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_logs`
--

CREATE TABLE `nutrition_logs` (
  `nutrition_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_item` varchar(100) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein` decimal(5,2) DEFAULT NULL,
  `carbs` decimal(5,2) DEFAULT NULL,
  `fats` decimal(5,2) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nutrition_logs`
--

INSERT INTO `nutrition_logs` (`nutrition_id`, `user_id`, `food_item`, `calories`, `protein`, `carbs`, `fats`, `date`) VALUES
(1, 25, 'Chicken Breast', 100, 250.00, 100.00, 50.00, '2025-11-12'),
(2, 23, 'rice, cooked', 130, 2.69, 28.17, 0.28, '2025-11-12'),
(3, 19, 'rice', 130, 2.69, 28.17, 0.28, '2025-11-12'),
(4, 19, 'salmon', 208, 20.42, 0.00, 13.42, '2025-11-12'),
(5, 19, 'chicken breast, cooked, skinless', 165, 31.00, 0.00, 3.60, '2025-11-12'),
(6, 19, 'rice', 143, 2.96, 30.99, 0.31, '2025-11-13');

-- --------------------------------------------------------

--
-- Table structure for table `routine_exercises`
--

CREATE TABLE `routine_exercises` (
  `re_id` int(11) NOT NULL,
  `routine_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sort_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routine_exercises`
--

INSERT INTO `routine_exercises` (`re_id`, `routine_id`, `exercise_id`, `sets`, `reps`, `weight`, `notes`, `order_index`, `created_at`, `sort_order`) VALUES
(1, 1, 3, 3, 10, NULL, '', 0, '2025-11-03 12:26:28', NULL),
(2, 1, 335, 3, 10, NULL, '', 0, '2025-11-03 12:27:37', NULL),
(3, 1, 336, 3, 10, NULL, '', 0, '2025-11-03 12:27:49', NULL),
(4, 1, 352, 3, 10, NULL, '', 0, '2025-11-03 12:27:58', NULL),
(5, 1, 351, 3, 10, NULL, '', 0, '2025-11-03 12:28:08', NULL),
(6, 1, 343, 3, 10, NULL, '', 0, '2025-11-03 12:28:17', NULL),
(7, 1, 344, 3, 10, NULL, '', 0, '2025-11-03 12:28:25', NULL),
(8, 2, 377, 3, 10, NULL, '', 0, '2025-11-12 03:57:20', NULL),
(9, 2, 376, 3, 10, NULL, '', 0, '2025-11-12 03:57:34', NULL),
(10, 4, 362, 3, 10, NULL, 'awe', 0, '2025-11-12 12:27:22', NULL),
(11, 4, 354, 3, 10, NULL, '', 0, '2025-11-12 12:27:36', NULL),
(12, 3, 354, 3, 10, 0.00, '', 0, '2025-11-12 13:16:26', NULL),
(15, 4, 355, 3, 10, NULL, '', 0, '2025-11-12 13:34:31', NULL),
(16, 3, 356, 3, 10, NULL, '', 0, '2025-11-12 13:35:37', NULL),
(17, 3, 355, 3, 10, NULL, '', 0, '2025-11-12 13:35:40', NULL),
(18, 3, 357, 3, 10, NULL, '', 0, '2025-11-12 13:35:44', NULL),
(19, 3, 362, 3, 10, NULL, '', 0, '2025-11-12 13:35:53', NULL),
(20, 7, 341, 3, 10, NULL, '', 0, '2025-11-13 06:19:00', NULL),
(21, 7, 340, 3, 10, NULL, '', 0, '2025-11-13 06:19:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplement_logs`
--

CREATE TABLE `supplement_logs` (
  `supplement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `supplement_name` varchar(100) NOT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `time_taken` time DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplement_logs`
--

INSERT INTO `supplement_logs` (`supplement_id`, `user_id`, `supplement_name`, `dosage`, `time_taken`, `date`) VALUES
(1, 19, 'Creatine', '5g', '12:00:00', '2025-11-13');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `experience` varchar(50) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 4.50,
  `total_clients` int(11) DEFAULT 0,
  `total_sessions` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `image` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `user_id`, `full_name`, `specialty`, `experience`, `rating`, `total_clients`, `total_sessions`, `description`, `phone`, `email`, `image`) VALUES
(6, 21, 'Jerkean Gabrina', 'Strength Training', '2.5 years', 4.50, 0, 0, NULL, '9914082061', 'jerkeangabrina1@gmail.com', 'trainer_69058fb9cf2ea.jpg'),
(7, 22, 'Joseph Anthony Arambala', 'Strength Training', '5 years', 4.50, 0, 0, NULL, '98234762521', 'joseph@gmail.com', 'trainer_69058eefdc87b.jpg'),
(8, 23, 'Jan Klyde Bulagao', 'Boxing', '3 years', 4.50, 0, 0, NULL, '9876543211', 'janklyde@gmail.com', 'trainer_690490b707ef5.jpg'),
(9, 18, 'Axcee Cabusas', 'Martial Arts', '3 years', 4.50, 0, 0, NULL, '9914082061', 'axceelfelis03@gmail.com', 'trainer_69048b8b7b60e.png'),
(10, 26, 'Karl Campoy', '', NULL, 4.50, 0, 0, NULL, '9914082061', 'karl123@gmail.com', 'default_trainer.png');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_bookings`
--

CREATE TABLE `trainer_bookings` (
  `booking_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_bookings`
--

INSERT INTO `trainer_bookings` (`booking_id`, `member_id`, `trainer_id`, `booking_date`, `booking_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 8, '2025-11-04', '22:00:00', 'cancelled', '2025-11-02 04:06:38', '2025-11-02 04:12:56'),
(2, 5, 9, '2025-11-17', '17:00:00', 'pending', '2025-11-12 05:49:02', '2025-11-12 05:49:02'),
(3, 4, 8, '2025-11-15', '19:05:00', 'confirmed', '2025-11-12 11:01:12', '2025-11-12 11:02:08'),
(4, 4, 9, '2025-11-14', '19:23:00', 'pending', '2025-11-13 06:23:37', '2025-11-13 06:23:37'),
(5, 5, 8, '2025-11-14', '14:40:00', 'confirmed', '2025-11-13 06:36:09', '2025-11-13 06:40:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `role` enum('admin','trainer','member','guest') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive','','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`, `address`, `role`, `created_at`, `status`) VALUES
(14, 'member', '$2y$10$IDbNhnZOSBnu9McC6s/TzeAHA8MYwA1ieaysH.pUq/iawcIB.VtLS', '', '', '', '', 'member', '2025-10-07 14:23:59', 'active'),
(15, 'admin1', '$2y$10$fUXk2ECJmDMgLFkrCVnPG.bz5CvF9zCURCZy7zk3YK1qfYBcjG5IC', '', '', '', '', 'admin', '2025-10-08 07:37:53', 'active'),
(18, 'axcee', '$2y$10$M8RjJ/r9lBMEvZAuzvVuU.P.hauTrA8gSUTHG8/IdBGc6xbTtD6FC', '', '', '', '', 'trainer', '2025-10-26 05:40:40', 'active'),
(19, 'arl', '$2y$10$duEoAKgWAMfKy8MbrbEuNeGNh0radDnLP9w3PEGoWmEk/hZ/DRA4m', '', '', '', '', 'member', '2025-10-26 05:41:48', 'active'),
(21, 'Jerkean', '$2y$10$FA4MQoWAVj8Fd9teyUJHi.osq4CV3RhDSDKotrV3tKoLo0dn7WQda', '', '', '', '', 'trainer', '2025-10-30 12:19:00', 'active'),
(22, 'Joseph', '$2y$10$FA4MQoWAVj8Fd9teyUJHi.osq4CV3RhDSDKotrV3tKoLo0dn7WQda', '', '', '', '', 'trainer', '2025-10-30 12:19:00', 'active'),
(23, 'Klyde', '$2y$10$M8RjJ/r9lBMEvZAuzvVuU.P.hauTrA8gSUTHG8/IdBGc6xbTtD6FC', '', '', '', '', 'trainer', '2025-10-30 12:19:00', 'active'),
(24, 'trainer', '$2y$10$29KOP8GpbpsA.rg7UHczUe/DTS8xjpQv/SlJiByYpS8mnqLC9D.qa', '', '', '', '', 'trainer', '2025-11-12 02:49:23', 'active'),
(25, 'arljoshua', '$2y$10$v2VEJizRRgjYofO9gegsy.i9UHp6pJPBKVYtTtdqqSjis/ZutXOLu', '', '', '', '', 'member', '2025-11-12 03:32:43', 'active'),
(26, 'admin123', '$2y$10$9QydKxY/UW18RCOXYbcqVeZeib1d3M.z2atuY5WsCNWw0R9gdZJWK', '', '', '', '', 'admin', '2025-11-12 05:07:48', 'active'),
(27, 'jimjim', '$2y$10$bWPHPxlK5uz.K.us/NnuZ.d2YkK6Yzvr5uuD0D9Z5Z1rfw.sBDBxK', 'james@gmail.com', 'James', 'Sison', 'Minglanilla', 'member', '2025-11-13 06:46:14', 'active'),
(28, 'Niconi', '$2y$10$mIOJFIufgS6fLNpl4KFX2.IOUhD4MSO6NAwT0K2iuzAHxgxL3qRwu', 'nicO@GMAIL.COM', 'Nico', 'Deiparine', 'Minglanilla', 'member', '2025-11-13 06:47:01', 'active'),
(29, 'Rasheed', '$2y$10$Tkw7.3qr78vHF1HFqGnmwuU.AlvLdaY0nKNgn.cPqRXnsh9J8ENty', 'Rasheed@gym.local', 'Rasheed', '', '', 'guest', '2025-11-13 06:49:20', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `workouts`
--

CREATE TABLE `workouts` (
  `workout_id` int(11) NOT NULL,
  `routine_id` int(11) NOT NULL,
  `workout_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workouts`
--

INSERT INTO `workouts` (`workout_id`, `routine_id`, `workout_name`, `created_at`, `end_time`) VALUES
(1, 1, 'Workout Nov 3, 2025', '2025-11-03 12:27:38', '2025-11-03 12:31:19'),
(2, 1, 'Workout Nov 3, 2025', '2025-11-03 12:32:17', '2025-11-03 12:32:31'),
(4, 2, 'Workout Nov 12, 2025', '2025-11-12 04:01:28', '2025-11-12 04:01:49'),
(5, 3, 'Workout Nov 12, 2025', '2025-11-12 13:16:39', '2025-11-12 13:16:47'),
(7, 3, 'Workout Nov 12, 2025', '2025-11-12 13:35:56', '2025-11-12 14:18:57'),
(11, 4, 'Workout Nov 12, 2025', '2025-11-12 13:56:55', '2025-11-12 13:57:02'),
(12, 4, 'Workout Nov 12, 2025', '2025-11-12 13:59:08', '2025-11-12 13:59:09'),
(13, 4, 'Workout Nov 12, 2025', '2025-11-12 14:17:29', '2025-11-12 14:18:38'),
(16, 7, 'Workout Nov 13, 2025', '2025-11-13 06:19:15', '2025-11-13 06:20:26'),
(17, 3, 'Workout Nov 13, 2025', '2025-11-13 06:24:59', '2025-11-13 06:25:34'),
(18, 3, 'Workout Nov 13, 2025', '2025-11-13 06:26:02', '2025-11-13 06:26:33');

-- --------------------------------------------------------

--
-- Table structure for table `workout_exercises`
--

CREATE TABLE `workout_exercises` (
  `we_id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT 3,
  `reps` int(11) DEFAULT 10,
  `weight` decimal(10,2) DEFAULT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_exercises`
--

INSERT INTO `workout_exercises` (`we_id`, `workout_id`, `exercise_id`, `sets`, `reps`, `weight`, `is_done`) VALUES
(1, 1, 3, 3, 10, NULL, 0),
(2, 1, 335, 3, 10, NULL, 0),
(4, 2, 3, 3, 10, NULL, 0),
(5, 2, 335, 3, 10, NULL, 0),
(6, 2, 336, 3, 10, NULL, 0),
(7, 2, 352, 3, 10, NULL, 0),
(8, 2, 351, 3, 10, NULL, 0),
(9, 2, 343, 3, 10, NULL, 0),
(10, 2, 344, 3, 10, NULL, 0),
(18, 4, 377, 3, 10, NULL, 0),
(19, 4, 376, 3, 10, NULL, 0),
(21, 5, 354, 3, 10, NULL, 0),
(22, 6, 362, 3, 10, NULL, 0),
(23, 6, 354, 3, 10, NULL, 0),
(24, 6, 356, 3, 10, NULL, 0),
(25, 6, 355, 3, 10, NULL, 0),
(29, 7, 354, 3, 10, NULL, 0),
(30, 7, 356, 3, 10, NULL, 0),
(31, 7, 355, 3, 10, NULL, 0),
(32, 7, 357, 3, 10, NULL, 0),
(33, 7, 362, 3, 10, NULL, 0),
(57, 11, 362, 3, 10, NULL, 0),
(58, 11, 354, 3, 10, NULL, 0),
(59, 11, 356, 3, 10, NULL, 0),
(60, 11, 355, 3, 10, NULL, 0),
(64, 12, 362, 3, 10, NULL, 0),
(65, 12, 354, 3, 10, NULL, 0),
(66, 12, 356, 3, 10, NULL, 0),
(67, 12, 355, 3, 10, NULL, 0),
(71, 13, 362, 3, 10, NULL, 0),
(72, 13, 354, 3, 10, NULL, 0),
(73, 13, 356, 3, 10, NULL, 0),
(74, 13, 355, 3, 10, NULL, 0),
(78, 16, 341, 3, 10, NULL, 1),
(79, 16, 340, 3, 10, NULL, 1),
(81, 17, 354, 3, 10, NULL, 0),
(82, 17, 356, 3, 10, NULL, 0),
(83, 17, 355, 3, 10, NULL, 0),
(84, 17, 357, 3, 10, NULL, 0),
(85, 17, 362, 3, 10, NULL, 0),
(88, 18, 354, 3, 10, NULL, 0),
(89, 18, 356, 3, 10, NULL, 0),
(90, 18, 355, 3, 10, NULL, 0),
(91, 18, 357, 3, 10, NULL, 0),
(92, 18, 362, 3, 10, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `workout_exercise_sets`
--

CREATE TABLE `workout_exercise_sets` (
  `wes_id` int(11) NOT NULL,
  `we_id` int(11) NOT NULL,
  `set_number` int(11) NOT NULL,
  `reps` int(11) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_exercise_sets`
--

INSERT INTO `workout_exercise_sets` (`wes_id`, `we_id`, `set_number`, `reps`, `weight`, `is_done`, `created_at`) VALUES
(1, 18, 1, NULL, NULL, 0, '2025-11-12 04:01:42'),
(2, 29, 1, NULL, NULL, 0, '2025-11-12 13:36:04'),
(3, 78, 1, 12, 50.00, 0, '2025-11-13 06:19:27'),
(4, 78, 2, 12, 55.00, 0, '2025-11-13 06:19:47'),
(5, 79, 1, 12, 40.00, 0, '2025-11-13 06:20:08'),
(6, 81, 1, 0, 0.00, 0, '2025-11-13 06:25:18'),
(7, 81, 2, NULL, NULL, 0, '2025-11-13 06:25:26'),
(8, 81, 3, NULL, NULL, 0, '2025-11-13 06:25:29');

-- --------------------------------------------------------

--
-- Table structure for table `workout_logs`
--

CREATE TABLE `workout_logs` (
  `log_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `workout_id` int(11) DEFAULT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 0,
  `log_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_logs`
--

INSERT INTO `workout_logs` (`log_id`, `member_id`, `workout_id`, `exercise_id`, `sets`, `reps`, `weight`, `duration`, `log_date`) VALUES
(1, 19, 2, 3, 3, 10, NULL, 1, '2025-11-03'),
(2, 19, 2, 335, 3, 10, NULL, 1, '2025-11-03'),
(3, 19, 2, 336, 3, 10, NULL, 1, '2025-11-03'),
(4, 19, 2, 352, 3, 10, NULL, 1, '2025-11-03'),
(5, 19, 2, 351, 3, 10, NULL, 1, '2025-11-03'),
(6, 19, 2, 343, 3, 10, NULL, 1, '2025-11-03'),
(7, 19, 2, 344, 3, 10, NULL, 1, '2025-11-03'),
(8, 25, 4, 377, 1, NULL, NULL, 1, '2025-11-12'),
(9, 4, 3, 3, 3, 10, NULL, 12861, '2025-11-12'),
(10, 4, 3, 335, 3, 10, NULL, 12861, '2025-11-12'),
(11, 4, 3, 336, 3, 10, NULL, 12861, '2025-11-12'),
(12, 4, 3, 352, 3, 10, NULL, 12861, '2025-11-12'),
(13, 4, 3, 351, 3, 10, NULL, 12861, '2025-11-12'),
(14, 4, 3, 343, 3, 10, NULL, 12861, '2025-11-12'),
(15, 4, 3, 344, 3, 10, NULL, 12861, '2025-11-12'),
(16, 4, 5, 354, 3, 10, NULL, 1, '2025-11-12'),
(17, 4, 7, 354, 1, NULL, NULL, 44, '2025-11-12'),
(18, 4, 17, 354, 1, 0, 0.00, 1, '2025-11-13'),
(19, 4, 17, 354, 1, NULL, NULL, 1, '2025-11-13'),
(20, 4, 17, 354, 1, NULL, NULL, 1, '2025-11-13'),
(21, 4, 18, 354, 3, 10, NULL, 1, '2025-11-13'),
(22, 4, 18, 356, 3, 10, NULL, 1, '2025-11-13'),
(23, 4, 18, 355, 3, 10, NULL, 1, '2025-11-13'),
(24, 4, 18, 357, 3, 10, NULL, 1, '2025-11-13'),
(25, 4, 18, 362, 3, 10, NULL, 1, '2025-11-13');

-- --------------------------------------------------------

--
-- Table structure for table `workout_routines`
--

CREATE TABLE `workout_routines` (
  `routine_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routine_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_routines`
--

INSERT INTO `workout_routines` (`routine_id`, `user_id`, `routine_name`, `description`, `is_public`, `created_at`) VALUES
(1, 19, 'Push Day', '', 0, '2025-11-03 12:26:20'),
(2, 25, 'Leg Die', '', 0, '2025-11-12 03:57:00'),
(3, 19, 'Pull Day', '', 0, '2025-11-12 11:27:45'),
(4, 23, 'Pull Day', '', 0, '2025-11-12 12:22:37'),
(5, 18, 'Pull Day', '', 0, '2025-11-12 13:23:51'),
(6, 19, 'Leg Die', '', 0, '2025-11-12 14:19:32'),
(7, 23, 'Push Day', '', 0, '2025-11-13 06:18:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`exercise_id`);

--
-- Indexes for table `food_database`
--
ALTER TABLE `food_database`
  ADD PRIMARY KEY (`food_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `name_2` (`name`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `nutrition_logs`
--
ALTER TABLE `nutrition_logs`
  ADD PRIMARY KEY (`nutrition_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `routine_exercises`
--
ALTER TABLE `routine_exercises`
  ADD PRIMARY KEY (`re_id`),
  ADD KEY `routine_id` (`routine_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `supplement_logs`
--
ALTER TABLE `supplement_logs`
  ADD PRIMARY KEY (`supplement_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`workout_id`),
  ADD KEY `routine_id` (`routine_id`);

--
-- Indexes for table `workout_exercises`
--
ALTER TABLE `workout_exercises`
  ADD PRIMARY KEY (`we_id`),
  ADD KEY `workout_id` (`workout_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `workout_exercise_sets`
--
ALTER TABLE `workout_exercise_sets`
  ADD PRIMARY KEY (`wes_id`),
  ADD KEY `we_id` (`we_id`);

--
-- Indexes for table `workout_logs`
--
ALTER TABLE `workout_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `workout_id` (`workout_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- Indexes for table `workout_routines`
--
ALTER TABLE `workout_routines`
  ADD PRIMARY KEY (`routine_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=387;

--
-- AUTO_INCREMENT for table `food_database`
--
ALTER TABLE `food_database`
  MODIFY `food_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nutrition_logs`
--
ALTER TABLE `nutrition_logs`
  MODIFY `nutrition_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `routine_exercises`
--
ALTER TABLE `routine_exercises`
  MODIFY `re_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `supplement_logs`
--
ALTER TABLE `supplement_logs`
  MODIFY `supplement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `workout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `workout_exercises`
--
ALTER TABLE `workout_exercises`
  MODIFY `we_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `workout_exercise_sets`
--
ALTER TABLE `workout_exercise_sets`
  MODIFY `wes_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `workout_logs`
--
ALTER TABLE `workout_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `workout_routines`
--
ALTER TABLE `workout_routines`
  MODIFY `routine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `nutrition_logs`
--
ALTER TABLE `nutrition_logs`
  ADD CONSTRAINT `nutrition_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `supplement_logs`
--
ALTER TABLE `supplement_logs`
  ADD CONSTRAINT `supplement_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
