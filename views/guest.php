<?php
session_start();

// Check if user is logged in and has guest role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header("Location: ../views/auth/login.php");
    exit;
}

// Get user information
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Dashboard</title>
    <link rel="stylesheet" href="../assets/css/member_styles.css">
    <link rel="stylesheet" href="../assets/css/guest_dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Alert System -->
    <?php include 'utilities/alert.php'; ?>
    
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class='bx bx-menu'></i>
    </button>
    
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <nav class="sidebar close">
        <header>
            <div class="logo">
                <img src="../assets/images/logo.png" alt="Logo" width="150">
                <div class="text">
                    <span class="welcome">Welcome,</span>
                    <span class="member-name"><?php echo htmlspecialchars($username); ?>!</span>
                </div>
            </div>
            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link active">
                        <a href="#home">
                            <i class='bx bx-home-alt icon'></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#subscription">
                            <i class='bx bx-credit-card icon'></i>
                            <span class="text nav-text">Subscription Plans</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#trainers">
                            <i class='bx bx-dumbbell icon'></i>
                            <span class="text nav-text">Available Trainers</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#trial">
                            <i class='bx bx-clipboard icon'></i>
                            <span class="text nav-text">Trial Workout Log</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#contact">
                            <i class='bx bx-phone icon'></i>
                            <span class="text nav-text">Contact & Support</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logout Section -->
            <div class="bottom-cont">
                <li>
                    <a href="../../controllers/AuthController.php?action=logout" id="logoutBtn">
                        <i class='bx bx-log-out-circle icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>


    <!-- Main Content Area -->
    <div class="main-content">
        <div class="content-wrapper">
            <!-- Home Section -->
            <div id="home" class="dashboard-section active">
            <div class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>You have an account but need to visit our admin to pay and become a full member. Explore our facilities and membership options below.</p>
            </div>

                <!-- Hero Section -->
                <div class="hero-section">
                    <h1>Ready to Become a Member?</h1>
                    <p>Visit our admin office to complete your membership payment and unlock full gym access</p>
                    <a href="#subscription" class="btn-subscribe">View Membership Plans</a>
                </div>

                <!-- Quick Stats -->
                <div class="quick-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Pending</h3>
                            <p>Membership Status</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>5+</h3>
                            <p>Available Coaches</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Pay In-Person</h3>
                            <p>Visit Admin Office</p>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Grid -->
                <div class="dashboard-grid">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h2>Why Choose Our Gym?</h2>
                        </div>
                        <div class="facilities-overview">
                            <div class="facility-item">
                                <i class="fas fa-dumbbell"></i>
                                <div>
                                    <h4>State-of-the-Art Equipment</h4>
                                    <p>Latest fitness machines and free weights</p>
                                </div>
                            </div>
                            <div class="facility-item">
                                <i class="fas fa-user-tie"></i>
                                <div>
                                    <h4>Expert Trainers</h4>
                                    <p>Certified professionals to guide your journey</p>
                                </div>
                            </div>
                            <div class="facility-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h4>24/7 Access</h4>
                                    <p>Work out anytime that suits your schedule</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-section">
                        <div class="section-header">
                            <h2>Limited Trial Features</h2>
                            <a href="#subscription" class="view-all">Upgrade Now</a>
                        </div>
                        <div class="trial-features">
                            <div class="feature-item">
                                <i class="fas fa-eye"></i>
                                <span>View trainer profiles</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-info-circle"></i>
                                <span>Gym information & facilities</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-credit-card"></i>
                                <span>Membership plans & pricing</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Plans Section -->
            <div id="subscription" class="dashboard-section">
                <div class="dashboard-header">
                    <h1>Membership Plans</h1>
                    <p>Choose your membership plan and visit our admin office to complete payment</p>
                </div>

                <div class="subscription-plans">
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3>Basic Plan</h3>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">29</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Gym Access</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Basic Equipment</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Locker Room</span>
                            </div>
                        </div>
                        <button class="btn-primary">Visit Admin to Pay</button>
                    </div>

                    <div class="plan-card featured">
                        <div class="plan-badge">Most Popular</div>
                        <div class="plan-header">
                            <h3>Premium Plan</h3>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">49</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Full Gym Access</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Personal Trainer</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Nutrition Plan</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Group Classes</span>
                            </div>
                        </div>
                        <button class="btn-primary">Visit Admin to Pay</button>
                    </div>

                    <div class="plan-card">
                        <div class="plan-header">
                            <h3>VIP Plan</h3>
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">79</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="plan-features">
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>All Premium Features</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>1-on-1 Training</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Nutrition Coaching</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Priority Booking</span>
                            </div>
                        </div>
                        <button class="btn-primary">Visit Admin to Pay</button>
                    </div>
                </div>
            </div>

            <!-- Trainers Section -->
            <div id="trainers" class="dashboard-section">
                <div class="dashboard-header">
                    <h1>Meet Our Trainers</h1>
                    <p>Professional trainers ready to help you achieve your fitness goals</p>
                </div>

                <div class="trainers-grid">
                    <div class="trainer-card">
                        <div class="trainer-image">
                            <img src="https://via.placeholder.com/150" alt="Trainer 1">
                        </div>
                        <div class="trainer-info">
                            <h3>John Smith</h3>
                            <p class="trainer-title">Senior Personal Trainer</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">Weight Training</span>
                                <span class="expertise-tag">Cardio</span>
                            </div>
                            <p class="trainer-description">5+ years experience in strength training and bodybuilding</p>
                        </div>
                    </div>

                    <div class="trainer-card">
                        <div class="trainer-image">
                            <img src="https://via.placeholder.com/150" alt="Trainer 2">
                        </div>
                        <div class="trainer-info">
                            <h3>Sarah Johnson</h3>
                            <p class="trainer-title">Fitness Specialist</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">Yoga</span>
                                <span class="expertise-tag">Pilates</span>
                            </div>
                            <p class="trainer-description">Certified yoga instructor with focus on flexibility and mindfulness</p>
                        </div>
                    </div>

                    <div class="trainer-card">
                        <div class="trainer-image">
                            <img src="https://via.placeholder.com/150" alt="Trainer 3">
                        </div>
                        <div class="trainer-info">
                            <h3>Mike Davis</h3>
                            <p class="trainer-title">Nutrition Coach</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">Nutrition</span>
                                <span class="expertise-tag">Weight Loss</span>
                            </div>
                            <p class="trainer-description">Specialized in nutrition planning and weight management</p>
                        </div>
                    </div>

                    <div class="trainer-card">
                        <div class="trainer-image">
                            <img src="https://via.placeholder.com/150" alt="Trainer 4">
                        </div>
                        <div class="trainer-info">
                            <h3>Lisa Brown</h3>
                            <p class="trainer-title">Group Fitness Instructor</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">HIIT</span>
                                <span class="expertise-tag">CrossFit</span>
                            </div>
                            <p class="trainer-description">High-intensity training specialist with group fitness expertise</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trial Workout Log Section -->
            <div id="trial" class="dashboard-section">
                <div class="dashboard-header">
                    <h1>Trial Workout Log</h1>
                    <p>Experience a limited version of our workout tracking system</p>
                </div>

                <div class="trial-workout-demo">
                    <div class="demo-notice">
                        <i class="fas fa-info-circle"></i>
                        <p>This is a demo version. Visit our admin office to pay and become a member for full workout tracking features!</p>
                    </div>

                    <div class="workout-demo">
                        <div class="demo-workout-card">
                            <h3>Sample Workout: Upper Body Strength</h3>
                            <div class="demo-exercises">
                                <div class="demo-exercise">
                                    <span class="exercise-name">Bench Press</span>
                                    <span class="exercise-sets">3 sets x 8 reps</span>
                                </div>
                                <div class="demo-exercise">
                                    <span class="exercise-name">Pull-ups</span>
                                    <span class="exercise-sets">3 sets x 6 reps</span>
                                </div>
                                <div class="demo-exercise">
                                    <span class="exercise-name">Shoulder Press</span>
                                    <span class="exercise-sets">3 sets x 10 reps</span>
                                </div>
                            </div>
                            <div class="demo-status">
                                <span class="status-demo">Demo Mode</span>
                            </div>
                        </div>
                    </div>

                    <div class="upgrade-prompt">
                        <h3>Become a Full Member</h3>
                        <p>Visit our admin office to pay and unlock complete workout tracking, progress monitoring, and personalized plans</p>
                        <a href="#contact" class="btn-subscribe">Contact Admin</a>
                    </div>
                </div>
            </div>

            <!-- Contact & Support Section -->
            <div id="contact" class="dashboard-section">
                <div class="dashboard-header">
                    <h1>Admin Office & Support</h1>
                    <p>Visit our admin office to complete your membership payment or get assistance</p>
                </div>

                <div class="contact-grid">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Admin Office</h3>
                            <p>Visit for Membership Payment</p>
                            <span>Mon-Fri: 9AM-6PM</span>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Admin Phone</h3>
                            <p>+1 (555) 123-4567</p>
                            <span>Mon-Fri: 9AM-6PM</span>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Admin Office Location</h3>
                            <p>123 Fitness Street, Admin Office</p>
                            <span>City, State 12345</span>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Payment Methods</h3>
                            <p>Cash, Card, Check</p>
                            <span>All major cards accepted</span>
                        </div>
                    </div>
                </div>

                <div class="support-actions">
                    <a href="tel:+15551234567" class="action-btn">
                        <i class="fas fa-phone"></i>
                        Call Admin
                    </a>
                    <a href="mailto:admin@gymmanagement.com" class="action-btn">
                        <i class="fas fa-envelope"></i>
                        Email Admin
                    </a>
                    <a href="#subscription" class="action-btn">
                        <i class="fas fa-credit-card"></i>
                        View Plans
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/dashboard_guest.js"></script>
</body>
</html>
