-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 03, 2025 at 01:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
(4, 19, 'Arl Sison', 'arlsison@gmail.com', '9877654325', 'san vicente 4, tunghaan, minglanilla, cebu', 'monthly', '2025-12-25', '2026-12-06', 'active');

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
(7, 1, 344, 3, 10, NULL, '', 0, '2025-11-03 12:28:25', NULL);

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
(9, 18, 'Axcee Cabusas', 'Martial Arts', '3 years', 4.50, 0, 0, NULL, '9914082061', 'axceelfelis03@gmail.com', 'trainer_69048b8b7b60e.png');

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
(1, 4, 8, '2025-11-04', '22:00:00', 'cancelled', '2025-11-02 04:06:38', '2025-11-02 04:12:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','trainer','member','guest') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive','','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `status`) VALUES
(14, 'member', '$2y$10$IDbNhnZOSBnu9McC6s/TzeAHA8MYwA1ieaysH.pUq/iawcIB.VtLS', 'member', '2025-10-07 14:23:59', 'active'),
(15, 'admin1', '$2y$10$fUXk2ECJmDMgLFkrCVnPG.bz5CvF9zCURCZy7zk3YK1qfYBcjG5IC', 'admin', '2025-10-08 07:37:53', 'active'),
(18, 'axcee', '$2y$10$M8RjJ/r9lBMEvZAuzvVuU.P.hauTrA8gSUTHG8/IdBGc6xbTtD6FC', 'trainer', '2025-10-26 05:40:40', 'active'),
(19, 'arl', '$2y$10$duEoAKgWAMfKy8MbrbEuNeGNh0radDnLP9w3PEGoWmEk/hZ/DRA4m', 'member', '2025-10-26 05:41:48', 'active'),
(20, 'testtrainer', '', 'trainer', '2025-10-26 09:22:50', 'active'),
(21, 'Jerkean', '$2y$10$FA4MQoWAVj8Fd9teyUJHi.osq4CV3RhDSDKotrV3tKoLo0dn7WQda', 'trainer', '2025-10-30 12:19:00', 'active'),
(22, 'Joseph', '$2y$10$FA4MQoWAVj8Fd9teyUJHi.osq4CV3RhDSDKotrV3tKoLo0dn7WQda', 'trainer', '2025-10-30 12:19:00', 'active'),
(23, 'Klyde', '$2y$10$FA4MQoWAVj8Fd9teyUJHi.osq4CV3RhDSDKotrV3tKoLo0dn7WQda', 'trainer', '2025-10-30 12:19:00', 'active');

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
(3, 1, 'Workout Nov 3, 2025', '2025-11-03 12:33:01', NULL);

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
(11, 3, 3, 3, 10, NULL, 0),
(12, 3, 335, 3, 10, NULL, 0),
(13, 3, 336, 3, 10, NULL, 0),
(14, 3, 352, 3, 10, NULL, 0),
(15, 3, 351, 3, 10, NULL, 0),
(16, 3, 343, 3, 10, NULL, 0),
(17, 3, 344, 3, 10, NULL, 0);

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
(7, 19, 2, 344, 3, 10, NULL, 1, '2025-11-03');

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
(1, 19, 'Push Day', '', 0, '2025-11-03 12:26:20');

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
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nutrition_logs`
--
ALTER TABLE `nutrition_logs`
  MODIFY `nutrition_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routine_exercises`
--
ALTER TABLE `routine_exercises`
  MODIFY `re_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `supplement_logs`
--
ALTER TABLE `supplement_logs`
  MODIFY `supplement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `workout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workout_exercises`
--
ALTER TABLE `workout_exercises`
  MODIFY `we_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `workout_exercise_sets`
--
ALTER TABLE `workout_exercise_sets`
  MODIFY `wes_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_logs`
--
ALTER TABLE `workout_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `workout_routines`
--
ALTER TABLE `workout_routines`
  MODIFY `routine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `routine_exercises`
--
ALTER TABLE `routine_exercises`
  ADD CONSTRAINT `routine_exercises_ibfk_1` FOREIGN KEY (`routine_id`) REFERENCES `workout_routines` (`routine_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `routine_exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`exercise_id`);

--
-- Constraints for table `supplement_logs`
--
ALTER TABLE `supplement_logs`
  ADD CONSTRAINT `supplement_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  ADD CONSTRAINT `trainer_bookings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `trainer_bookings_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`);

--
-- Constraints for table `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`routine_id`) REFERENCES `workout_routines` (`routine_id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_exercise_sets`
--
ALTER TABLE `workout_exercise_sets`
  ADD CONSTRAINT `workout_exercise_sets_ibfk_1` FOREIGN KEY (`we_id`) REFERENCES `workout_exercises` (`we_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
