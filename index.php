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
          <div class="logo">
            <i class='bx bx-dumbbell'></i>
            <a href="#">FitNexus</a>
          </div>
          <ul class="nav-menu">
            <li><a href="#about">About</a></li>
            <li><a href="#plans">Membership</a></li>
            <li><a href="#trainers">Trainers</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
          <div class="nav-actions">
            <a href="views/auth/login.php" class="btn">Login</a>
            <a href="views/auth/register.php" class="btn" style="background: var(--blue-accent);">Sign Up</a>
          </div>
        </nav>
      </div>
    </header>
    <main>
      <section class="hero">
        <div class="hero-content">
          <h1>Start Your Fitness Journey with FitNexus</h1>
          <p>Stronger. Fitter. Healthier. Join FitNexus—the ultimate gym management platform connecting you to a healthier & better you. Modern amenities, expert trainers, and a supportive community—all in one place.</p>
          <a href="views/auth/register.php" class="btn">Get Started</a>
        </div>
        <div class="hero-img">
          <img src="assets/images/hero.webp" alt="FitNexus Gym" loading="lazy"/>
        </div>
      </section>
      <section class="section" id="about">
        <h2 class="section-title">About FitNexus</h2>
        <div class="info-section">
          <div class="info-img">
            <img src="https://images.unsplash.com/photo-1554284126-aa88f22d8b72?auto=format&fit=crop&w=600&q=80" alt="Gym interior" loading="lazy">
          </div>
          <div class="info-content">
            <p>Welcome to FitNexus—the hub for fitness enthusiasts determined to reach their best shape! From state-of-the-art equipment and personal training to energizing group classes, we provide everything you need in a premium fitness facility. Discover flexible memberships, advanced wellness tracking, and a friendly, motivating community!</p>
          </div>
        </div>
      </section>
      <section class="section" id="plans">
        <h2 class="section-title">Membership Plans</h2>
        <div class="plans">
          <div class="plan-card">
            <h4>Basic</h4>
            <div class="price">₱1,500<span style="font-size:.95rem;font-weight:400;">/mo</span></div>
            <ul style="list-style:none;color:var(--gray);padding:0;margin-bottom:1.4rem;margin-top:.9rem;">
              <li>Unlimited Gym Access</li>
              <li>Locker Room</li>
              <li>Free Wi-Fi</li>
            </ul>
            <a href="views/auth/register.php" class="btn">Join Now</a>
          </div>
          <div class="plan-card">
            <h4>Premium</h4>
            <div class="price">₱2,200<span style="font-size:.95rem;font-weight:400;">/mo</span></div>
            <ul style="list-style:none;color:var(--gray);padding:0;margin-bottom:1.4rem;margin-top:.9rem;">
              <li>Group Fitness Classes</li>
              <li>Pool & Sauna</li>
              <li>All Basic Features</li>
            </ul>
            <a href="views/auth/register.php" class="btn">Go Premium</a>
          </div>
          <div class="plan-card">
            <h4>Elite</h4>
            <div class="price">₱3,300<span style="font-size:.95rem;font-weight:400;">/mo</span></div>
            <ul style="list-style:none;color:var(--gray);padding:0;margin-bottom:1.4rem;margin-top:.9rem;">
              <li>Personal Trainer</li>
              <li>Nutritionist Consultation</li>
              <li>All Premium Features</li>
            </ul>
            <a href="views/auth/register.php" class="btn">Go Elite</a>
          </div>
        </div>
      </section>
      <section class="section" id="trainers">
        <h2 class="section-title">Meet Our Trainers</h2>
        <div class="features">
          <div class="feature-card">
            <h3>Jay Aquino</h3>
            <p>Strength & Conditioning Coach. 10+ Years Experience. NASM Certified.</p>
          </div>
          <div class="feature-card">
            <h3>Alyssa Cruz</h3>
            <p>Yoga Instructor. Registered Yoga Teacher. Mindfulness Specialist.</p>
          </div>
          <div class="feature-card">
            <h3>Enrico Alvarez</h3>
            <p>HIIT & Functional Training. Certified Group Fitness Leader.</p>
          </div>
          <div class="feature-card">
            <h3>Karen Santos</h3>
            <p>Sports Nutrition and Body Transformation Coach.</p>
          </div>
        </div>
      </section>
      <section class="section" id="contact">
        <div class="contact">
          <h2>Contact Us</h2>
          <p>Ready to start? Got questions? Reach out and join the FitNexus community!</p>
          <div style="margin-bottom:.7rem;font-size:1.09em;"><i class='bx bxs-phone' style="color:var(--blue-main);"></i> 0912-345-6789 | <i class='bx bx-envelope' style="color:var(--blue-accent);"></i> info@fitnexus.com</div>
          <a href="mailto:info@fitnexus.com" class="btn">Send Email</a>
        </div>
      </section>
    </main>
</body>
</html>
