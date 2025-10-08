-- Workout System Database Schema
-- This file contains the database tables needed for the workout tracking system

-- Exercises table - stores all available exercises
CREATE TABLE IF NOT EXISTS exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    muscle_groups JSON,
    equipment VARCHAR(100),
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Workout routines table - stores user-created workout routines
CREATE TABLE IF NOT EXISTS workout_routines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Routine exercises table - stores exercises within routines
CREATE TABLE IF NOT EXISTS routine_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    routine_id INT NOT NULL,
    exercise_id INT NOT NULL,
    sets INT NOT NULL DEFAULT 1,
    reps INT NOT NULL DEFAULT 1,
    weight VARCHAR(50),
    duration INT, -- in minutes
    notes TEXT,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (routine_id) REFERENCES workout_routines(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
);

-- Workouts table - stores individual workout sessions
CREATE TABLE IF NOT EXISTS workouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    routine_id INT NULL,
    name VARCHAR(255) NOT NULL,
    notes TEXT,
    workout_date DATE NOT NULL,
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (routine_id) REFERENCES workout_routines(id) ON DELETE SET NULL
);

-- Workout exercises table - stores exercises performed in a workout
CREATE TABLE IF NOT EXISTS workout_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workout_id INT NOT NULL,
    exercise_id INT NOT NULL,
    sets INT NOT NULL DEFAULT 1,
    reps INT NOT NULL DEFAULT 1,
    weight VARCHAR(50),
    duration INT, -- in minutes
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (workout_id) REFERENCES workouts(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE
);

-- Insert sample exercises
INSERT INTO exercises (name, category, description, muscle_groups, equipment, difficulty_level) VALUES
-- Chest Exercises
('Push-ups', 'Chest', 'Bodyweight chest exercise', '["chest", "triceps", "shoulders"]', 'Bodyweight', 'beginner'),
('Bench Press', 'Chest', 'Barbell chest press exercise', '["chest", "triceps", "shoulders"]', 'Barbell', 'intermediate'),
('Incline Dumbbell Press', 'Chest', 'Dumbbell press on inclined bench', '["chest", "triceps", "shoulders"]', 'Dumbbells', 'intermediate'),
('Chest Flyes', 'Chest', 'Dumbbell flye exercise for chest', '["chest"]', 'Dumbbells', 'beginner'),

-- Back Exercises
('Pull-ups', 'Back', 'Bodyweight back exercise', '["lats", "rhomboids", "biceps"]', 'Pull-up Bar', 'intermediate'),
('Bent-over Rows', 'Back', 'Barbell rowing exercise', '["lats", "rhomboids", "biceps"]', 'Barbell', 'intermediate'),
('Lat Pulldowns', 'Back', 'Cable lat pulldown exercise', '["lats", "biceps"]', 'Cable Machine', 'beginner'),
('Deadlifts', 'Back', 'Full body compound exercise', '["lats", "glutes", "hamstrings", "core"]', 'Barbell', 'advanced'),

-- Shoulder Exercises
('Overhead Press', 'Shoulders', 'Barbell overhead press', '["shoulders", "triceps"]', 'Barbell', 'intermediate'),
('Lateral Raises', 'Shoulders', 'Dumbbell lateral raise', '["shoulders"]', 'Dumbbells', 'beginner'),
('Rear Delt Flyes', 'Shoulders', 'Rear deltoid exercise', '["shoulders"]', 'Dumbbells', 'beginner'),
('Face Pulls', 'Shoulders', 'Cable face pull exercise', '["shoulders", "rhomboids"]', 'Cable Machine', 'beginner'),

-- Arm Exercises
('Bicep Curls', 'Arms', 'Dumbbell bicep curl', '["biceps"]', 'Dumbbells', 'beginner'),
('Tricep Dips', 'Arms', 'Bodyweight tricep exercise', '["triceps"]', 'Bodyweight', 'intermediate'),
('Hammer Curls', 'Arms', 'Dumbbell hammer curl', '["biceps", "forearms"]', 'Dumbbells', 'beginner'),
('Tricep Extensions', 'Arms', 'Dumbbell tricep extension', '["triceps"]', 'Dumbbells', 'beginner'),

-- Leg Exercises
('Squats', 'Legs', 'Bodyweight squat exercise', '["quadriceps", "glutes", "hamstrings"]', 'Bodyweight', 'beginner'),
('Lunges', 'Legs', 'Bodyweight lunge exercise', '["quadriceps", "glutes", "hamstrings"]', 'Bodyweight', 'beginner'),
('Leg Press', 'Legs', 'Machine leg press', '["quadriceps", "glutes"]', 'Leg Press Machine', 'beginner'),
('Romanian Deadlifts', 'Legs', 'Barbell RDL exercise', '["hamstrings", "glutes"]', 'Barbell', 'intermediate'),

-- Core Exercises
('Plank', 'Core', 'Isometric core exercise', '["core", "shoulders"]', 'Bodyweight', 'beginner'),
('Crunches', 'Core', 'Abdominal crunch exercise', '["abs"]', 'Bodyweight', 'beginner'),
('Russian Twists', 'Core', 'Rotational core exercise', '["abs", "obliques"]', 'Bodyweight', 'beginner'),
('Mountain Climbers', 'Core', 'Dynamic core exercise', '["core", "shoulders", "legs"]', 'Bodyweight', 'intermediate'),

-- Cardio Exercises
('Running', 'Cardio', 'Outdoor or treadmill running', '["legs", "cardiovascular"]', 'Treadmill', 'beginner'),
('Cycling', 'Cardio', 'Stationary or outdoor cycling', '["legs", "cardiovascular"]', 'Bike', 'beginner'),
('Rowing', 'Cardio', 'Rowing machine exercise', '["full body", "cardiovascular"]', 'Rowing Machine', 'intermediate'),
('Jump Rope', 'Cardio', 'Jump rope exercise', '["legs", "cardiovascular"]', 'Jump Rope', 'beginner');
