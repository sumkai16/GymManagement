<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

// Sample coaches data - in a real app, this would come from a database
$coaches = [
    [
        'id' => 1,
        'name' => 'Sarah Johnson',
        'specialty' => 'Strength Training',
        'experience' => '8 years',
        'rating' => 4.9,
        'clients' => 150,
        'sessions' => 1200,
        'description' => 'Certified personal trainer specializing in strength training and muscle building. Passionate about helping clients achieve their fitness goals.',
        'specialties' => ['Strength Training', 'Muscle Building', 'Powerlifting']
    ],
    [
        'id' => 2,
        'name' => 'Mike Chen',
        'specialty' => 'Cardio & Weight Loss',
        'experience' => '6 years',
        'rating' => 4.8,
        'clients' => 120,
        'sessions' => 900,
        'description' => 'Expert in cardiovascular training and weight loss programs. Focuses on sustainable lifestyle changes.',
        'specialties' => ['Cardio', 'Weight Loss', 'HIIT', 'Nutrition']
    ],
    [
        'id' => 3,
        'name' => 'Emma Rodriguez',
        'specialty' => 'Yoga & Flexibility',
        'experience' => '10 years',
        'rating' => 4.9,
        'clients' => 200,
        'sessions' => 1500,
        'description' => 'Certified yoga instructor with expertise in flexibility training and stress management.',
        'specialties' => ['Yoga', 'Flexibility', 'Meditation', 'Stress Relief']
    ],
    [
        'id' => 4,
        'name' => 'David Thompson',
        'specialty' => 'Sports Performance',
        'experience' => '12 years',
        'rating' => 4.9,
        'clients' => 180,
        'sessions' => 2000,
        'description' => 'Former professional athlete specializing in sports performance and athletic training.',
        'specialties' => ['Sports Performance', 'Athletic Training', 'Injury Prevention']
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coaches - FitNexus</title>
    <link rel="stylesheet" href="../../assets/css/member_styles.css">
    <link rel="stylesheet" href="../../assets/css/coaches_styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Dynamic Sidebar -->
    <?php include '../components/dynamic_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-header">
                <h1>Our Coaches</h1>
                <p>Meet our certified fitness professionals and find the perfect coach for your goals.</p>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-section">
                <form class="search-form">
                    <div class="form-group">
                        <label for="search">Search Coaches</label>
                        <input type="text" id="search" name="search" placeholder="Search by name or specialty...">
                    </div>
                    <div class="form-group">
                        <label for="specialty">Specialty</label>
                        <select id="specialty" name="specialty">
                            <option value="">All Specialties</option>
                            <option value="strength">Strength Training</option>
                            <option value="cardio">Cardio & Weight Loss</option>
                            <option value="yoga">Yoga & Flexibility</option>
                            <option value="sports">Sports Performance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-search'></i>
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Coaches Grid -->
            <div class="coaches-grid">
                <?php foreach ($coaches as $coach): ?>
                <div class="coach-card">
                    <div class="coach-header">
                        <div class="coach-avatar">
                            <?= strtoupper(substr($coach['name'], 0, 1)) ?>
                        </div>
                        <div class="coach-info">
                            <h3><?= htmlspecialchars($coach['name']) ?></h3>
                            <p><?= htmlspecialchars($coach['specialty']) ?></p>
                        </div>
                    </div>
                    
                    <div class="coach-details">
                        <p><?= htmlspecialchars($coach['description']) ?></p>
                        
                        <div class="coach-specialties">
                            <?php foreach ($coach['specialties'] as $specialty): ?>
                                <span class="specialty-tag"><?= htmlspecialchars($specialty) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="coach-stats">
                        <div class="stat-item">
                            <h4><?= $coach['experience'] ?></h4>
                            <p>Experience</p>
                        </div>
                        <div class="stat-item">
                            <h4><?= $coach['rating'] ?></h4>
                            <p>Rating</p>
                        </div>
                        <div class="stat-item">
                            <h4><?= $coach['sessions'] ?></h4>
                            <p>Sessions</p>
                        </div>
                    </div>
                    
                    <div class="coach-actions">
                        <button class="btn btn-primary">
                            <i class='bx bx-calendar'></i>
                            Book Session
                        </button>
                        <button class="btn btn-secondary">
                            <i class='bx bx-message'></i>
                            Contact
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
