<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch trainers from database
require_once '../../models/Trainer.php';
$trainerModel = new Trainer();

// Handle search and filter
$search = $_GET['search'] ?? '';
$specialtyFilter = $_GET['specialty'] ?? '';

// Get all trainers with optional filters
$trainers = $trainerModel->getAllTrainers($specialtyFilter ?: null);

// Filter by search term if provided
if (!empty($search)) {
    $trainers = array_filter($trainers, function($trainer) use ($search) {
        return stripos($trainer['full_name'], $search) !== false || 
               stripos($trainer['specialty'], $search) !== false;
    });
}

// Transform trainer data to match the display format
$coaches = [];
foreach ($trainers as $trainer) {
    // Parse specialties from specialty field (if comma-separated) or use as single item
    $specialties = !empty($trainer['specialty']) ? explode(',', $trainer['specialty']) : ['General Training'];
    $specialties = array_map('trim', $specialties);
    
    $coaches[] = [
        'id' => $trainer['trainer_id'],
        'name' => $trainer['full_name'],
        'specialty' => $trainer['specialty'] ?? 'General Training',
        'experience' => $trainer['experience'] ?? 'N/A', // Missing field
        'rating' => $trainer['rating'] ?? '4.5', // Missing field
        'clients' => $trainer['total_clients'] ?? '0', // Missing field
        'sessions' => $trainer['total_sessions'] ?? '0', // Missing field
        'description' => $trainer['description'] ?? 'Certified fitness professional dedicated to helping you achieve your fitness goals.', // Missing field
        'specialties' => $specialties,
        'email' => $trainer['email'] ?? '',
        'phone' => $trainer['phone'] ?? '',
        'image' => $trainer['image'] ?? 'default_trainer.png'
    ];
}
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
                <form class="search-form" method="get">
                    <div class="form-group">
                        <label for="search">Search Coaches</label>
                        <input type="text" id="search" name="search" placeholder="Search by name or specialty..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="form-group">
                        <label for="specialty">Specialty</label>
                        <select id="specialty" name="specialty">
                            <option value="">All Specialties</option>
                            <?php
                            // Get unique specialties from trainers
                            $allTrainers = $trainerModel->getAllTrainers();
                            $specialties = array_unique(array_filter(array_column($allTrainers, 'specialty')));
                            foreach ($specialties as $spec): ?>
                                <option value="<?= htmlspecialchars($spec) ?>" <?= $specialtyFilter === $spec ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($spec) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-search'></i>
                            Search
                        </button>
                        <?php if (!empty($search) || !empty($specialtyFilter)): ?>
                            <a href="coaches.php" class="btn btn-secondary" style="margin-left: 10px;">
                                <i class='bx bx-x'></i>
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Coaches Grid -->
            <?php if (empty($coaches)): ?>
                <div class="no-data">
                    <i class='bx bx-user-x'></i>
                    <p>No coaches found</p>
                    <p style="font-size: 0.9rem; font-weight: normal; margin-top: 0.5rem;">
                        <?php if (!empty($search) || !empty($specialtyFilter)): ?>
                            Try adjusting your search or filter criteria.
                        <?php else: ?>
                            There are currently no coaches available.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
            <div class="coaches-grid">
                <?php foreach ($coaches as $coach): ?>
                <div class="coach-card">
                    <div class="coach-header">
                        <div class="coach-avatar">
                            <?php
                            $imagePath = '';
                            if (!empty($coach['image']) && trim($coach['image']) !== '' && $coach['image'] !== 'default_trainer.png') {
                                $img = trim($coach['image']);
                                // Check if it's already a full path or just filename
                                $isAbsolute = stripos($img, 'http://') === 0 || stripos($img, 'https://') === 0 || substr($img, 0, 1) === '/';
                                $alreadyPrefixed = strpos($img, 'assets/images/trainers/') === 0;
                                $imagePath = $isAbsolute || $alreadyPrefixed ? $img : ('../../assets/images/trainers/' . $img);
                                
                                // Check if file actually exists
                                $fullPath = __DIR__ . '/../../assets/images/trainers/' . basename($img);
                                if (file_exists($fullPath)) {
                                    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($coach['name']) . '" />';
                                } else {
                                    echo strtoupper(substr($coach['name'], 0, 1));
                                }
                            } else {
                                echo strtoupper(substr($coach['name'], 0, 1));
                            }
                            ?>
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
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
