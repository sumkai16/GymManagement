-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 06:09 AM
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
(3, 'Barbell Bench Press', 'Lie on bench and press barbell from chest to full extension', 'Chest', 'Barbell'),
(5, 'Decline Bench Press', 'Bench press on declined angle targeting lower chest', 'Chest', 'Barbell'),
(7, 'Push-ups', 'Classic bodyweight chest exercise', 'Chest', 'Bodyweight'),
(8, 'Cable Crossover', 'Standing cable fly motion crossing at center', 'Chest', 'Cable'),
(9, 'Chest Dips', 'Bodyweight dips leaning forward for chest emphasis', 'Chest', 'Bodyweight'),
(10, 'Pec Deck Machine', 'Machine-based chest fly movement', 'Chest', 'Machine'),
(11, 'Incline Cable Flyes', 'Cable flyes on inclined bench', 'Chest', 'Cable'),
(12, 'Landmine Press', 'Single-arm press using landmine setup', 'Chest', 'Barbell'),
(13, 'Deadlift', 'Hip hinge movement lifting barbell from ground', 'Back', 'Barbell'),
(14, 'Pull-ups', 'Bodyweight vertical pull to bar', 'Back', 'Bodyweight'),
(15, 'Bent-Over Barbell Row', 'Rowing motion with barbell bent at hips', 'Back', 'Barbell'),
(16, 'Lat Pulldown', 'Vertical pulling motion on cable machine', 'Back', 'Cable'),
(17, 'Seated Cable Row', 'Horizontal rowing with cable', 'Back', 'Cable'),
(18, 'T-Bar Row', 'Rowing with T-bar or landmine', 'Back', 'Barbell'),
(20, 'Face Pulls', 'Cable pull to face level for rear delts and upper back', 'Back', 'Cable'),
(21, 'Inverted Row', 'Bodyweight horizontal pull under bar', 'Back', 'Bodyweight'),
(22, 'Hyperextensions', 'Lower back extension on hyperextension bench', 'Back', 'Bodyweight'),
(23, 'Chin-ups', 'Underhand grip pull-up variation', 'Back', 'Bodyweight'),
(24, 'Pendlay Row', 'Dead-stop barbell row from floor', 'Back', 'Barbell'),
(25, 'Straight-Arm Pulldown', 'Lat isolation with straight arms', 'Back', 'Cable'),
(26, 'Rack Pulls', 'Partial deadlift from elevated position', 'Back', 'Barbell'),
(27, 'Overhead Press', 'Standing barbell press overhead', 'Shoulders', 'Barbell'),
(33, 'Upright Row', 'Vertical pull along body to chin level', 'Shoulders', 'Barbell'),
(34, 'Cable Lateral Raises', 'Lateral raises using cable', 'Shoulders', 'Cable'),
(35, 'Machine Shoulder Press', 'Overhead press on machine', 'Shoulders', 'Machine'),
(36, 'Pike Push-ups', 'Bodyweight shoulder press variation', 'Shoulders', 'Bodyweight'),
(38, 'Barbell Back Squat', 'Classic squat with barbell on upper back', 'Legs', 'Barbell'),
(39, 'Front Squat', 'Squat with barbell on front delts', 'Legs', 'Barbell'),
(40, 'Romanian Deadlift', 'Hip hinge targeting hamstrings', 'Legs', 'Barbell'),
(41, 'Leg Press', 'Machine-based compound leg movement', 'Legs', 'Machine'),
(42, 'Leg Extension', 'Quadriceps isolation on machine', 'Legs', 'Machine'),
(43, 'Leg Curl', 'Hamstring isolation on machine', 'Legs', 'Machine'),
(47, 'Hack Squat', 'Machine squat with back support', 'Legs', 'Machine'),
(48, 'Calf Raises', 'Standing calf raise for gastrocnemius', 'Legs', 'Machine'),
(49, 'Seated Calf Raise', 'Seated calf isolation for soleus', 'Legs', 'Machine'),
(50, 'Sumo Deadlift', 'Wide-stance deadlift variation', 'Legs', 'Barbell'),
(51, 'Box Jumps', 'Plyometric jump onto elevated platform', 'Legs', 'Bodyweight'),
(53, 'Sissy Squat', 'Quad isolation bodyweight movement', 'Legs', 'Bodyweight'),
(54, 'Nordic Hamstring Curl', 'Eccentric hamstring bodyweight exercise', 'Legs', 'Bodyweight'),
(55, 'Hip Thrust', 'Glute-focused bridge with barbell', 'Legs', 'Barbell'),
(56, 'Glute Bridge', 'Bodyweight hip extension', 'Legs', 'Bodyweight'),
(57, 'Barbell Curl', 'Standing bicep curl with barbell', 'Arms', 'Barbell'),
(60, 'Preacher Curl', 'Bicep curl on preacher bench', 'Arms', 'Barbell'),
(61, 'Cable Curl', 'Bicep curl using cable machine', 'Arms', 'Cable'),
(63, 'Close-Grip Bench Press', 'Narrow grip bench press for triceps', 'Arms', 'Barbell'),
(64, 'Tricep Dips', 'Bodyweight dips for triceps', 'Arms', 'Bodyweight'),
(66, 'Tricep Pushdown', 'Cable pushdown for triceps', 'Arms', 'Cable'),
(67, 'Skull Crushers', 'Lying tricep extension to forehead', 'Arms', 'Barbell'),
(68, 'Diamond Push-ups', 'Close-hand push-up for triceps', 'Arms', 'Bodyweight'),
(69, 'Cable Overhead Tricep Extension', 'Overhead tricep work with cable', 'Arms', 'Cable'),
(71, 'Plank', 'Isometric core hold in push-up position', 'Core', 'Bodyweight'),
(72, 'Crunches', 'Basic abdominal crunch', 'Core', 'Bodyweight'),
(73, 'Bicycle Crunches', 'Alternating elbow-to-knee crunches', 'Core', 'Bodyweight'),
(74, 'Leg Raises', 'Lying leg raise for lower abs', 'Core', 'Bodyweight'),
(75, 'Russian Twists', 'Rotational core exercise', 'Core', 'Bodyweight'),
(76, 'Mountain Climbers', 'Dynamic plank with alternating knee drives', 'Core', 'Bodyweight'),
(77, 'Ab Wheel Rollout', 'Core extension using ab wheel', 'Core', 'Equipment'),
(78, 'Hanging Knee Raises', 'Hanging leg raise variation', 'Core', 'Bodyweight'),
(79, 'Cable Woodchoppers', 'Rotational cable movement', 'Core', 'Cable'),
(80, 'Side Plank', 'Lateral plank for obliques', 'Core', 'Bodyweight'),
(81, 'Pallof Press', 'Anti-rotation press with cable or band', 'Core', 'Cable'),
(82, 'Dead Bug', 'Supine core stability exercise', 'Core', 'Bodyweight'),
(83, 'Hollow Body Hold', 'Gymnastics core position hold', 'Core', 'Bodyweight'),
(84, 'V-ups', 'Simultaneous upper and lower body crunch', 'Core', 'Bodyweight'),
(85, 'Dragon Flag', 'Advanced core exercise popularized by Bruce Lee', 'Core', 'Bodyweight'),
(86, 'Clean and Press', 'Olympic lift from floor to overhead', 'Full Body', 'Barbell'),
(87, 'Thruster', 'Front squat to overhead press combination', 'Full Body', 'Barbell'),
(88, 'Burpees', 'Full body conditioning movement', 'Full Body', 'Bodyweight'),
(89, 'Power Clean', 'Explosive pull from floor to shoulders', 'Full Body', 'Barbell'),
(90, 'Snatch', 'Single motion lift from floor to overhead', 'Full Body', 'Barbell'),
(91, 'Turkish Get-up', 'Complex movement from lying to standing', 'Full Body', 'Kettlebell'),
(93, 'Kettlebell Swing', 'Hip hinge swing with kettlebell', 'Full Body', 'Kettlebell'),
(95, 'Battle Ropes', 'Conditioning with heavy ropes', 'Full Body', 'Equipment'),
(96, 'Clean', 'Olympic lift to shoulder level', 'Full Body', 'Barbell'),
(97, 'Jerk', 'Overhead press from shoulders', 'Full Body', 'Barbell'),
(98, 'Split Jerk', 'Jerk with split stance', 'Full Body', 'Barbell'),
(99, 'Hang Clean', 'Clean from hanging position', 'Full Body', 'Barbell'),
(100, 'Hang Snatch', 'Snatch from hanging position', 'Full Body', 'Barbell'),
(101, 'Box Squats', 'Squat to box for depth and power', 'Legs', 'Barbell'),
(102, 'Sled Push', 'Pushing loaded sled for power', 'Full Body', 'Equipment'),
(103, 'Sled Pull', 'Pulling loaded sled', 'Full Body', 'Equipment'),
(104, 'Tire Flips', 'Flipping large tire for power', 'Full Body', 'Equipment'),
(105, 'Medicine Ball Slams', 'Explosive overhead slam', 'Full Body', 'Medicine Ball'),
(106, 'Wall Balls', 'Squat and throw to wall target', 'Full Body', 'Medicine Ball'),
(259, 'Dumbbell Bench Press', 'Flat bench press with dumbbells for greater range of motion', 'Chest', 'Dumbbells'),
(260, 'Dumbbell Pullover', 'Lying pullover motion stretching chest and lats', 'Chest', 'Dumbbells'),
(261, 'Incline Dumbbell Flyes', 'Flyes on incline bench targeting upper chest', 'Chest', 'Dumbbells'),
(262, 'Decline Dumbbell Press', 'Dumbbell press on decline bench for lower chest', 'Chest', 'Dumbbells'),
(263, 'Single-Arm Dumbbell Press', 'Unilateral chest press with dumbbell', 'Chest', 'Dumbbells'),
(264, 'Dumbbell Squeeze Press', 'Pressing dumbbells together while pressing up', 'Chest', 'Dumbbells'),
(265, 'Dumbbell Floor Press', 'Bench press performed lying on floor', 'Chest', 'Dumbbells'),
(266, 'Dumbbell Deadlift', 'Deadlift variation using dumbbells', 'Back', 'Dumbbells'),
(267, 'Two-Arm Dumbbell Row', 'Bent-over row with both dumbbells simultaneously', 'Back', 'Dumbbells'),
(268, 'Dumbbell Pullover', 'Cross-bench pullover for lats', 'Back', 'Dumbbells'),
(269, 'Dumbbell Shrugs', 'Trap exercise with dumbbells at sides', 'Back', 'Dumbbells'),
(270, 'Incline Dumbbell Row', 'Chest-supported row on incline bench', 'Back', 'Dumbbells'),
(271, 'Renegade Row', 'Plank position alternating dumbbell rows', 'Back', 'Dumbbells'),
(272, 'Dumbbell Romanian Deadlift', 'RDL with dumbbells for hamstrings and back', 'Back', 'Dumbbells'),
(273, 'Seal Row', 'Face-down bench-supported dumbbell row', 'Back', 'Dumbbells'),
(274, 'Seated Dumbbell Press', 'Shoulder press performed seated for stability', 'Shoulders', 'Dumbbells'),
(275, 'Standing Dumbbell Press', 'Overhead press standing with dumbbells', 'Shoulders', 'Dumbbells'),
(276, 'Dumbbell Front Raise', 'Anterior delt isolation raising to front', 'Shoulders', 'Dumbbells'),
(277, 'Bent-Over Lateral Raise', 'Rear delt raise bent at hips', 'Shoulders', 'Dumbbells'),
(278, 'Dumbbell Upright Row', 'Vertical pull with dumbbells to shoulder height', 'Shoulders', 'Dumbbells'),
(279, 'Single-Arm Dumbbell Press', 'Unilateral overhead press', 'Shoulders', 'Dumbbells'),
(280, 'Dumbbell Y-Raise', 'Raising dumbbells in Y formation overhead', 'Shoulders', 'Dumbbells'),
(281, 'Dumbbell W-Raise', 'External rotation raise for rear delts', 'Shoulders', 'Dumbbells'),
(282, 'Leaning Lateral Raise', 'Side raises while holding support for balance', 'Shoulders', 'Dumbbells'),
(283, 'Six-Way Shoulders', 'Combination of front, side, and rear raises', 'Shoulders', 'Dumbbells'),
(284, 'Dumbbell Clean', 'Explosive pull of dumbbells to shoulders', 'Shoulders', 'Dumbbells'),
(285, 'Dumbbell Push Press', 'Overhead press with leg drive assistance', 'Shoulders', 'Dumbbells'),
(286, 'Dumbbell Squat', 'Squat holding dumbbells at sides', 'Legs', 'Dumbbells'),
(287, 'Dumbbell Front Squat', 'Squat with dumbbells at shoulder level', 'Legs', 'Dumbbells'),
(288, 'Dumbbell Lunges', 'Forward or reverse lunges with dumbbells', 'Legs', 'Dumbbells'),
(289, 'Dumbbell Sumo Squat', 'Wide-stance squat with single dumbbell', 'Legs', 'Dumbbells'),
(290, 'Single-Leg Dumbbell Deadlift', 'Unilateral RDL with dumbbell', 'Legs', 'Dumbbells'),
(291, 'Dumbbell Step-Up', 'Stepping onto platform with dumbbells', 'Legs', 'Dumbbells'),
(292, 'Dumbbell Calf Raise', 'Standing calf raise holding dumbbells', 'Legs', 'Dumbbells'),
(293, 'Dumbbell Side Lunge', 'Lateral lunge with dumbbells', 'Legs', 'Dumbbells'),
(294, 'Dumbbell Reverse Lunge', 'Backward lunge with dumbbells', 'Legs', 'Dumbbells'),
(295, 'Dumbbell Curtsy Lunge', 'Cross-behind lunge for glutes', 'Legs', 'Dumbbells'),
(296, 'Single-Leg Squat with Dumbbell', 'Pistol squat holding dumbbell', 'Legs', 'Dumbbells'),
(297, 'Dumbbell Stiff-Leg Deadlift', 'Straight-leg deadlift for hamstrings', 'Legs', 'Dumbbells'),
(298, 'Dumbbell Jump Squat', 'Explosive squat with dumbbells', 'Legs', 'Dumbbells'),
(299, 'Dumbbell Goblet Reverse Lunge', 'Goblet position reverse lunge', 'Legs', 'Dumbbells'),
(300, 'Alternating Dumbbell Curl', 'Bicep curls alternating arms', 'Arms', 'Dumbbells'),
(301, 'Seated Dumbbell Curl', 'Bicep curls performed seated', 'Arms', 'Dumbbells'),
(302, 'Incline Dumbbell Curl', 'Curls on incline bench for bicep stretch', 'Arms', 'Dumbbells'),
(303, 'Standing Hammer Curl', 'Neutral grip curls standing', 'Arms', 'Dumbbells'),
(304, 'Cross-Body Hammer Curl', 'Hammer curl across body', 'Arms', 'Dumbbells'),
(305, 'Spider Curl', 'Bicep curl on incline bench chest-down', 'Arms', 'Dumbbells'),
(306, 'Dumbbell Preacher Curl', 'Preacher curl with single dumbbell', 'Arms', 'Dumbbells'),
(307, '21s (Dumbbell)', 'Bicep curl method with three ranges', 'Arms', 'Dumbbells'),
(308, 'Dumbbell Kickback', 'Tricep extension kicking dumbbell back', 'Arms', 'Dumbbells'),
(309, 'Two-Arm Dumbbell Extension', 'Overhead tricep extension with both arms', 'Arms', 'Dumbbells'),
(310, 'Single-Arm Overhead Extension', 'Unilateral overhead tricep work', 'Arms', 'Dumbbells'),
(311, 'Lying Dumbbell Extension', 'Skull crusher variation with dumbbells', 'Arms', 'Dumbbells'),
(312, 'Tate Press', 'Tricep press with elbows flared', 'Arms', 'Dumbbells'),
(313, 'Dumbbell JM Press', 'Hybrid between close-grip press and extension', 'Arms', 'Dumbbells'),
(314, 'Reverse Curl', 'Overhand grip curl for brachialis and forearms', 'Arms', 'Dumbbells'),
(315, 'Wrist Curl', 'Forearm flexion with dumbbell', 'Arms', 'Dumbbells'),
(316, 'Reverse Wrist Curl', 'Forearm extension with dumbbell', 'Arms', 'Dumbbells'),
(317, 'Dumbbell Side Bend', 'Lateral flexion holding dumbbell', 'Core', 'Dumbbells'),
(318, 'Dumbbell Russian Twist', 'Rotational abs with dumbbell', 'Core', 'Dumbbells'),
(319, 'Weighted Crunch', 'Crunch holding dumbbell at chest', 'Core', 'Dumbbells'),
(320, 'Dumbbell Woodchopper', 'Diagonal chop motion with dumbbell', 'Core', 'Dumbbells'),
(321, 'Dumbbell Dead Bug', 'Dead bug variation holding dumbbell', 'Core', 'Dumbbells'),
(322, 'Weighted Plank', 'Plank with dumbbell on back', 'Core', 'Dumbbells'),
(323, 'Dumbbell V-Up', 'V-up passing dumbbell from hands to feet', 'Core', 'Dumbbells'),
(324, 'Dumbbell Toe Touch', 'Reaching dumbbell to elevated toes', 'Core', 'Dumbbells'),
(325, 'Dumbbell Thruster', 'Squat to overhead press with dumbbells', 'Full Body', 'Dumbbells'),
(326, 'Dumbbell Snatch', 'Single-arm explosive lift overhead', 'Full Body', 'Dumbbells'),
(327, 'Dumbbell Clean and Press', 'Clean to shoulders then press overhead', 'Full Body', 'Dumbbells'),
(328, 'Dumbbell Burpee', 'Burpee holding dumbbells', 'Full Body', 'Dumbbells'),
(329, 'Dumbbell Turkish Get-Up', 'Complex get-up movement with dumbbell', 'Full Body', 'Dumbbells'),
(330, 'Dumbbell Complex', 'Series of movements without putting weight down', 'Full Body', 'Dumbbells'),
(331, 'Dumbbell Farmers Carry', 'Heavy dumbbell carry for distance', 'Full Body', 'Dumbbells'),
(332, 'Dumbbell Bear Crawl', 'Crawling while holding dumbbells', 'Full Body', 'Dumbbells'),
(333, 'Devils Press', 'Burpee into double dumbbell snatch', 'Full Body', 'Dumbbells'),
(334, 'Dumbbell High Pull', 'Explosive upward pull to shoulder height', 'Full Body', 'Dumbbells');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workout_exercises`
--

CREATE TABLE `workout_exercises` (
  `we_id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT 3,
  `reps` int(11) DEFAULT 10
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
  `log_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workout_routines`
--

CREATE TABLE `workout_routines` (
  `routine_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routine_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `exercise_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=335;

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
  MODIFY `workout_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_exercises`
--
ALTER TABLE `workout_exercises`
  MODIFY `we_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_logs`
--
ALTER TABLE `workout_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_routines`
--
ALTER TABLE `workout_routines`
  MODIFY `routine_id` int(11) NOT NULL AUTO_INCREMENT;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
