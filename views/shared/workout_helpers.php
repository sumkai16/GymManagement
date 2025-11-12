<?php
// Helper functions for workout routines (shared)
if (!function_exists('calculateTotalSets')) {
    function calculateTotalSets($exercises) {
        $total = 0;
        foreach ($exercises as $exercise) {
            $total += $exercise['sets'] ?? 0;
        }
        return $total;
    }
}
if (!function_exists('calculateTotalReps')) {
    function calculateTotalReps($exercises) {
        $total = 0;
        foreach ($exercises as $exercise) {
            $total += ($exercise['sets'] ?? 0) * ($exercise['reps'] ?? 0);
        }
        return $total;
    }
}
if (!function_exists('calculateDifficulty')) {
    function calculateDifficulty($exercises) {
        if (empty($exercises)) return 'N/A';
        $totalScore = 0;
        foreach ($exercises as $exercise) {
            $score = 0;
            $score += ($exercise['sets'] ?? 0) * 2;
            $score += ($exercise['reps'] ?? 0) * 1;
            $score += ($exercise['weight'] ?? 0) * 0.5;
            $totalScore += $score;
        }
        $avgScore = $totalScore / count($exercises);
        if ($avgScore < 20) return 'Beginner';
        if ($avgScore < 40) return 'Intermediate';
        if ($avgScore < 60) return 'Advanced';
        return 'Expert';
    }
}
if (!function_exists('estimateWorkoutTime')) {
    function estimateWorkoutTime($exercises) {
        if (empty($exercises)) return 0;
        $totalTime = 0;
        foreach ($exercises as $exercise) {
            $sets = $exercise['sets'] ?? 0;
            $reps = $exercise['reps'] ?? 0;
            $duration = $exercise['duration'] ?? 0;
            if ($duration > 0) {
                $totalTime += $duration;
            } else {
                $totalTime += ($sets * $reps * 3) + ($sets * 60);
            }
        }
        return round($totalTime / 60); // Convert to minutes
    }
}
if (!function_exists('estimateCalories')) {
    function estimateCalories($exercises) {
        if (empty($exercises)) return 0;
        $totalCalories = 0;
        foreach ($exercises as $exercise) {
            $sets = $exercise['sets'] ?? 0;
            $reps = $exercise['reps'] ?? 0;
            $weight = $exercise['weight'] ?? 0;
            $calories = ($sets * $reps * 0.1) + ($weight * $sets * 0.05);
            $totalCalories += $calories;
        }
        return round($totalCalories);
    }
}
