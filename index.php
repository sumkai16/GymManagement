<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'member';
    if ($role === 'admin') {
        header("Location: views/admin/admin_dashboard.php");
        exit;
    } elseif ($role === 'trainer') {
        header("Location: views/trainers/trainers_dashboard.php");
        exit;
    } elseif ($role === 'member') {
        header("Location: views/member/member_dashboard.php");
        exit;
    } else {
        header("Location: views/guest.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitNexus - The Ultimate Gym Experience</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>
  <header class="header">
    <div class="container">
      <nav class="navbar">
        <a href="#" class="logo"><i class='bx bx-dumbbell'></i><span>FitNexus</span></a>
        <ul class="nav-menu">
          <li><a href="#about">About</a></li>
          <li><a href="#plans">Membership</a></li>
          <li><a href="#trainers">Trainers</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <div class="nav-actions">
          <a href="views/auth/login.php" class="btn">Login</a>
          <a href="views/auth/register.php" class="btn btn-signup">Sign Up</a>
        </div>
      </nav>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="container hero-container">
        <div class="hero-content">
          <h1 class="hero-title">Start Your Fitness Journey with <span>FitNexus</span></h1>
          <p class="hero-desc">Stronger. Fitter. Healthier. Join FitNexus — the ultimate gym management platform. Modern amenities, expert trainers, and a supportive community—all in one place.</p>
          <a href="views/auth/register.php" class="btn btn-primary hero-cta">Get Started</a>
        </div>
        <div class="hero-img-wrap">
          <img src="assets/images/hero.webp" alt="FitNexus Gym" class="hero-img" loading="lazy"/>
        </div>
      </div>
    </section>

    <section id="about" class="section about-section">
      <div class="container about-container">
        <div class="about-img-wrap">
          <img src="assets/images/gyminterior.jpg" alt="Gym interior" class="about-img" loading="lazy">
        </div>
        <div class="about-content">
          <h2 class="section-title">About FitNexus</h2>
          <p>Welcome to FitNexus—the hub for fitness enthusiasts determined to reach their best shape! From state-of-the-art equipment and personal training to energizing group classes, we provide everything you need in a premium fitness facility. Discover flexible memberships, advanced wellness tracking, and a friendly, motivating community!</p>
        </div>
      </div>
    </section>

    <section id="plans" class="section plans-section">
      <div class="container">
        <h2 class="section-title">Membership Plans</h2>
        <div class="plans-grid">
          <div class="plan-card">
            <h4 class="plan-name">Basic</h4>
            <div class="plan-price">₱1,500<span>/mo</span></div>
            <ul class="plan-features">
              <li>Unlimited Gym Access</li>
              <li>Locker Room</li>
              <li>Free Wi-Fi</li>
            </ul>
            <a href="views/auth/register.php" class="btn btn-secondary">Join Now</a>
          </div>
          <div class="plan-card">
            <h4 class="plan-name">Premium</h4>
            <div class="plan-price">₱2,200<span>/mo</span></div>
            <ul class="plan-features">
              <li>Group Fitness Classes</li>
              <li>Pool & Sauna</li>
              <li>All Basic Features</li>
            </ul>
            <a href="views/auth/register.php" class="btn btn-secondary">Go Premium</a>
          </div>
          <div class="plan-card premium-plan">
            <h4 class="plan-name">Elite</h4>
            <div class="plan-price">₱3,300<span>/mo</span></div>
            <ul class="plan-features">
              <li>Personal Trainer</li>
              <li>Nutritionist Consultation</li>
              <li>All Premium Features</li>
            </ul>
            <a href="views/auth/register.php" class="btn btn-secondary">Go Elite</a>
          </div>
        </div>
      </div>
    </section>

    <section id="trainers" class="section trainers-section">
      <div class="container">
        <h2 class="section-title">Meet Our Trainers</h2>
        <div class="trainers-grid">
          <div class="trainer-card">
            <div class="trainer-avatar"><i class='bx bx-user-voice'></i></div>
            <h3>Jay Aquino</h3>
            <span class="trainer-role">Strength & Conditioning</span>
            <p>10+ Years Experience. NASM Certified.</p>
          </div>
          <div class="trainer-card">
            <div class="trainer-avatar"><i class='bx bx-user-voice'></i></div>
            <h3>Alyssa Cruz</h3>
            <span class="trainer-role">Yoga Instructor</span>
            <p>Registered Yoga Teacher. Mindfulness Specialist.</p>
          </div>
          <div class="trainer-card">
            <div class="trainer-avatar"><i class='bx bx-user-voice'></i></div>
            <h3>Enrico Alvarez</h3>
            <span class="trainer-role">HIIT & Functional Training</span>
            <p>Certified Group Fitness Leader.</p>
          </div>
          <div class="trainer-card">
            <div class="trainer-avatar"><i class='bx bx-user-voice'></i></div>
            <h3>Karen Santos</h3>
            <span class="trainer-role">Sports Nutrition</span>
            <p>Body Transformation Coach.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" class="section contact-section">
      <div class="container contact-container">
        <h2 class="section-title">Contact Us</h2>
        <p class="contact-prompt">Ready to start? Got questions? Reach out and join the FitNexus community!</p>
        <div class="contact-row">
          <div><i class='bx bxs-phone' style="color:var(--blue-main);"></i> 0912-345-6789</div>
          <div><i class='bx bx-envelope' style="color:var(--blue-accent);"></i> info@fitnexus.com</div>
        </div>
        <a href="mailto:info@fitnexus.com" class="btn btn-tertiary">Send Email</a>
      </div>
    </section>
  </main>
  
</body>
</html>
