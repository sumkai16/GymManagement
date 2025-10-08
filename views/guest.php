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
    <title>Guest - FitNexus</title>
    <link rel="stylesheet" href="../assets/css/member_styles.css">
    <link rel="stylesheet" href="../assets/css/guest_dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Include Alert System -->
    <?php include 'utilities/alert.php'; ?>
    
    <!-- Include Dynamic Sidebar -->
    <?php include 'components/dynamic_sidebar.php'; ?>


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
                    <div class="plan-card featured">
                            <div class="plan-badge">Most Popular</div>
                            <div class="plan-header">
                                <h3>Monthly Plan</h3>
                                <div class="price">
                                    <span class="currency">₱</span>
                                    <span class="amount">550</span>
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
                                    <span>Nutrition Plan</span>
                                </div>
                            </div>
                            <button class="btn-primary">Visit Admin to Pay</button>
                        </div> 
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3>Annual Plan</h3>
                            <div class="price">
                                <span class="currency">₱</span>
                                <span class="amount">2500</span>
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
                                <span>Basic Equipment</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Locker Room</span>
                            </div>
                            <div class="feature">
                                <i class="fas fa-check"></i>
                                <span>Nutrition Plan</span>
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
                            <img src="../assets/images/trainers/axcee.png" alt="Trainer 1">
                        </div>
                        <div class="trainer-info">
                            <h3>Axcee Cabusas</h3>
                            <p class="trainer-title">Semi Calisthenic and Free Weight Trainer</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">Semi Calisthenic</span>
                                <span class="expertise-tag">Free Weight</span>
                            </div>
                            <p class="trainer-description">2 years experience in calisthenic and free weight training</p>
                        </div>
                    </div>

                    <div class="trainer-card">
                        <div class="trainer-image">
                            <img src="https://via.placeholder.com/150" alt="Trainer 2">
                        </div>
                        <div class="trainer-info">
                            <h3>Joseph Anthony Arambala</h3>
                            <p class="trainer-title">Strength and Conditioning Coach</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">Strength Training</span>
                                <span class="expertise-tag">Body Building</span>
                            </div>
                            <p class="trainer-description">5+ years experience in strength training and bodybuilding</p>
                        </div>
                    </div>

                    <div class="trainer-card">
                        <div class="trainer-image">
                            <img src="../assets/images/trainers/klyde.jpg" alt="Trainer 3">
                        </div>
                        <div class="trainer-info">
                            <h3>Jan Klyde Bulagao</h3>
                            <p class="trainer-title">Strength and Nutrition Coach</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">Strength Training</span>
                                <span class="expertise-tag">Nutrition</span>
                            </div>
                            <p class="trainer-description">2 years experience in strength training and nutrition</p>
                        </div>
                    </div>

                    <div class="trainer-card">
                        <div class="trainer-image">
                            <img src="https://via.placeholder.com/150" alt="Trainer 4">
                        </div>
                        <div class="trainer-info">
                            <h3>Jerkean Gabrina</h3>
                            <p class="trainer-title">Body Composition and Nutrition Coach</p>
                            <div class="trainer-expertise">
                                <span class="expertise-tag">Body Composition</span>
                                <span class="expertise-tag">Nutrition</span>
                            </div>
                            <p class="trainer-description">2 years experience in body composition and nutrition</p>
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
                            <h3>Gym Staff</h3>
                            <p>Visit for Membership Payment</p>
                            <span>Mon-Fri: 9AM-6PM</span>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Gym Staff Phone</h3>
                            <p>+63 917 123-4567</p>
                            <span>Mon-Fri: 9AM-6PM</span>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Gym Location</h3>
                            <p>Gea Fitness Gym</p>
                            <span>Pob. Ward 3, National Hiway, Minglanilla, Cebu</span>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Payment</h3>
                            <p>Cash only</p>
                            <span>No other payment methods accepted</span>
                        </div>
                    </div>
                </div>

                <div class="support-actions">
                    <a href="tel:+63 917 123-4567" class="action-btn">
                        <i class="fas fa-phone"></i>
                        Call Admin
                    </a>
                    <a href="mailto:geafitnessgym@gmail.com" class="action-btn">
                        <i class="fas fa-envelope"></i>
                        Email Admin
                    </a>
                    <a href="#subscription" class="action-btn">
                        <i class="fas fa-credit-card"></i>
                        View Membership Plans
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic sidebar JavaScript is already included in the sidebar component -->
</body>
</html>
