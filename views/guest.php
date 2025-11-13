<?php
session_start();

// Check if user is logged in and has guest role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header("Location: ../views/auth/login.php");
    exit;
}

// Get user information
$username = $_SESSION['username'] ?? 'Guest';

// Fetch trainers from database
require_once __DIR__ . '/../models/Trainer.php';
$trainerModel = new Trainer();
$trainers = $trainerModel->getAllTrainers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest - FitNexus</title>
    <link rel="stylesheet" href="../assets/css/member_styles.css">
    <link rel="stylesheet" href="../assets/css/guest_dashboard.css">
    <link rel="stylesheet" href="../assets/css/modal_styles.css">
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

    <!-- Guest Sidebar -->
    <nav class="sidebar close" id="guestSidebar">
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
                            <i class='bx bx-home icon'></i>
                            <span class="text nav-text">Home</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#subscription">
                            <i class='bx bx-credit-card icon'></i>
                            <span class="text nav-text">Membership Plans</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#trainers">
                            <i class='bx bx-user icon'></i>
                            <span class="text nav-text">Trainers</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#trial">
                            <i class='bx bx-dumbbell icon'></i>
                            <span class="text nav-text">Trial Workout</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#contact">
                            <i class='bx bx-phone icon'></i>
                            <span class="text nav-text">Contact</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logout Section -->
            <div class="bottom-content">
                <li class="nav-link logout">
                    <a href="#" onclick="openLogoutModal()">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <i class='bx bx-log-out'></i>
                <h3>Logout Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout?</p>
                <p class="modal-subtext">You will need to sign in again to access your account.</p>
            </div>
            <div class="modal-actions">
                <button onclick="closeLogoutModal()" class="btn btn-secondary">Cancel</button>
                <button onclick="confirmLogout()" class="btn btn-primary">Logout</button>
            </div>
        </div>
    </div>


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
                    <?php if (empty($trainers)): ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class='bx bx-user-x' style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                            <p>No trainers available at the moment.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($trainers as $trainer): ?>
                            <?php
                            // Parse specialty to create expertise tags
                            $specialty = $trainer['specialty'] ?? '';
                            $expertiseTags = [];
                            if (!empty($specialty)) {
                                // Split by common delimiters: "and", ",", "&"
                                $parts = preg_split('/\s*(?:and|,|&)\s*/i', $specialty);
                                foreach ($parts as $part) {
                                    $part = trim($part);
                                    if (!empty($part)) {
                                        $expertiseTags[] = $part;
                                    }
                                }
                            }
                            // If no tags created, use the specialty as a single tag
                            if (empty($expertiseTags) && !empty($specialty)) {
                                $expertiseTags[] = $specialty;
                            }
                            
                            // Handle image path
                            $img = isset($trainer['image']) ? trim($trainer['image']) : '';
                            $imageSrc = '../assets/images/trainers/default_trainer.png';
                            if (!empty($img) && $img !== 'default_trainer.png') {
                                $isAbsolute = stripos($img, 'http://') === 0 || stripos($img, 'https://') === 0 || substr($img, 0, 1) === '/';
                                $alreadyPrefixed = strpos($img, 'assets/images/trainers/') === 0;
                                if ($isAbsolute || $alreadyPrefixed) {
                                    $imageSrc = $img;
                                } else {
                                    $imageSrc = '../assets/images/trainers/' . $img;
                                }
                            }
                            
                            // Build description
                            $experience = $trainer['experience'] ?? '';
                            $description = '';
                            if (!empty($experience)) {
                                $description = $experience . ' experience';
                                if (!empty($specialty)) {
                                    $description .= ' in ' . strtolower($specialty);
                                }
                            } else if (!empty($specialty)) {
                                $description = 'Expert in ' . strtolower($specialty);
                            } else {
                                $description = 'Certified fitness professional';
                            }
                            ?>
                            <div class="trainer-card">
                                <div class="trainer-image">
                                    <img src="<?php echo htmlspecialchars($imageSrc); ?>" 
                                         alt="<?php echo htmlspecialchars($trainer['full_name'] ?? 'Trainer'); ?>"
                                         onerror="this.src='../assets/images/trainers/default_trainer.png'">
                                </div>
                                <div class="trainer-info">
                                    <h3><?php echo htmlspecialchars($trainer['full_name'] ?? 'Trainer'); ?></h3>
                                    <p class="trainer-title"><?php echo htmlspecialchars($specialty ?: 'Fitness Trainer'); ?></p>
                                    <?php if (!empty($expertiseTags)): ?>
                                        <div class="trainer-expertise">
                                            <?php foreach ($expertiseTags as $tag): ?>
                                                <span class="expertise-tag"><?php echo htmlspecialchars($tag); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <p class="trainer-description"><?php echo htmlspecialchars($description); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

    <!-- Guest Sidebar JavaScript -->
    <script>
    // GuestSidebar JavaScript - Inline to avoid path issues
    class GuestSidebar {
        constructor() {
            this.sidebar = document.getElementById('guestSidebar');
            this.toggle = document.querySelector('.toggle');
            this.mobileMenuToggle = document.getElementById('mobileMenuToggle');
            this.sidebarOverlay = document.getElementById('sidebarOverlay');
            this.body = document.body;

            this.init();
        }

        init() {
            this.setupEventListeners();
            this.setupMobileResponsiveness();
            this.setupActiveStates();
            this.loadSidebarState();
        }

        setupEventListeners() {
            // Desktop sidebar toggle
            if (this.toggle) {
                this.toggle.addEventListener('click', () => {
                    this.toggleSidebar();
                });
            }

            // Mobile menu toggle
            if (this.mobileMenuToggle) {
                this.mobileMenuToggle.addEventListener('click', () => {
                    this.toggleMobileSidebar();
                });
            }

            // Close sidebar when clicking overlay
            if (this.sidebarOverlay) {
                this.sidebarOverlay.addEventListener('click', () => {
                    this.closeMobileSidebar();
                });
            }

            // Close mobile sidebar when clicking outside
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 &&
                    !this.sidebar.contains(e.target) &&
                    !this.mobileMenuToggle.contains(e.target)) {
                    this.closeMobileSidebar();
                }
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                this.handleResize();
            });

            // Setup dropdown functionality (none for guest)
            // this.setupDropdowns();
        }

        setupMobileResponsiveness() {
            // Check if we're on mobile on load
            if (window.innerWidth <= 768) {
                this.closeMobileSidebar();
            }
        }

        setupActiveStates() {
            // Update active states based on current hash
            const currentHash = window.location.hash;
            const menuLinks = document.querySelectorAll('.menu-links .nav-link');

            menuLinks.forEach(link => {
                const href = link.querySelector('a').getAttribute('href');
                if (href && currentHash === href) {
                    // Remove active class from all links
                    menuLinks.forEach(l => l.classList.remove('active'));
                    // Add active class to current link
                    link.classList.add('active');
                }
            });
        }

        toggleSidebar() {
            if (this.sidebar) {
                this.sidebar.classList.toggle('close');
                this.saveSidebarState();
            }
        }

        toggleMobileSidebar() {
            if (this.sidebar) {
                this.sidebar.classList.toggle('mobile-open');
                this.sidebarOverlay.classList.toggle('active');
                this.body.classList.toggle('sidebar-open');
            }
        }

        closeMobileSidebar() {
            if (this.sidebar) {
                this.sidebar.classList.remove('mobile-open');
                this.sidebarOverlay.classList.remove('active');
                this.body.classList.remove('sidebar-open');
            }
        }

        handleResize() {
            if (window.innerWidth > 768) {
                // Desktop view - remove mobile classes
                this.sidebar.classList.remove('mobile-open');
                this.sidebarOverlay.classList.remove('active');
                this.body.classList.remove('sidebar-open');
            } else {
                // Mobile view - ensure sidebar is closed by default
                this.closeMobileSidebar();
            }
        }

        saveSidebarState() {
            const isClosed = this.sidebar.classList.contains('close');
            localStorage.setItem('sidebarState', isClosed ? 'closed' : 'open');
        }

        loadSidebarState() {
            const savedState = localStorage.getItem('sidebarState');
            if (savedState === 'closed') {
                this.sidebar.classList.add('close');
            } else if (savedState === 'open') {
                this.sidebar.classList.remove('close');
            }
        }

        // Public method to programmatically toggle sidebar
        toggle() {
            this.toggleSidebar();
        }

        // Public method to set sidebar state
        setState(isOpen) {
            if (isOpen) {
                this.sidebar.classList.remove('close');
            } else {
                this.sidebar.classList.add('close');
            }
            this.saveSidebarState();
        }
    }

    // Initialize the guest sidebar when DOM is loaded
    document.addEventListener('DOMContentLoaded', () => {
        window.guestSidebar = new GuestSidebar();

        // Navigation functionality
        const navLinks = document.querySelectorAll('.nav-link a');
        const dashboardSections = document.querySelectorAll('.dashboard-section');

        // Handle navigation clicks
        navLinks.forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href').substring(1);

                // Remove active class from all nav links
                navLinks.forEach(navLink => {
                    navLink.parentElement.classList.remove('active');
                });

                // Add active class to clicked link
                link.parentElement.classList.add('active');

                // Hide all sections
                dashboardSections.forEach(section => {
                    section.classList.remove('active');
                });

                // Show target section
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.add('active');
                }

                // Close mobile menu if open
                const sidebar = document.getElementById('guestSidebar');
                const sidebarOverlay = document.getElementById('sidebarOverlay');
                if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.remove('active');
                    }
                    document.body.classList.remove('sidebar-open');
                }
            });
        });

        // Set initial active section
        const homeSection = document.getElementById('home');
        if (homeSection) {
            homeSection.classList.add('active');
        }
    });

    // Logout Modal Functions
    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'block';
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    function confirmLogout() {
        window.location.href = '../controllers/AuthController.php?action=logout';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('logoutModal');
        if (event.target === modal) {
            closeLogoutModal();
        }
    }
    </script>
</body>
</html>
