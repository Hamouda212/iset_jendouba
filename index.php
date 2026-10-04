<?php
// Home.php - Main Landing Page
require_once 'db.php';
$conn = getDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISET Jendouba | Official Events Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="brand">
            <div class="logo">ISET</div>
            <span>ISET Jendouba</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
                <li><span style="color: white;">👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="adminevent.php" class="btn-primary">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="btn-outline">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-primary">Login</a></li>
                <li><a href="register.php" class="btn-outline">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="hero">
    <div class="hero-content">
        <h1>Shaping the Future <br><span>at ISET Jendouba</span></h1>
        <p>Stay updated with the latest workshops, seminars, and cultural activities happening on campus.</p>
        <a href="events.php" class="btn-primary" style="display: inline-block; padding: 12px 35px;">Explore Events →</a>
    </div>
</div>

<div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
        <div>
            <span style="background: #FFD700; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem;">About Us</span>
            <h2 style="font-size: 2.5rem; margin: 1rem 0;">A Tradition of <span style="border-bottom: 4px solid #FFD700;">Excellence</span></h2>
            <p>The Higher Institute of Technological Studies of Jendouba is dedicated to providing students with practical knowledge and industry-standard skills.</p>
            <p style="margin-top: 1rem;">Our event platform connects students with professional opportunities, fostering a community of innovation and technological advancement.</p>
        </div>
        <div style="background: #FFD700; border-radius: 30px; height: 300px; display: flex; align-items: center; justify-content: center; font-size: 5rem;">🏛️</div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div>
            <h3>ISET Jendouba</h3>
            <p>Leading the way in technological education and student innovation in the Jendouba region.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <ul style="list-style: none;">
                <li><a href="events.php" style="color: #ccc;">Events</a></li>
                <li><a href="#" style="color: #ccc;">Academic Calendar</a></li>
                <li><a href="#" style="color: #ccc;">Student Guide</a></li>
            </ul>
        </div>
        <div>
            <h4>Contact</h4>
            <p>Avenue de l'UMA, Jendouba</p>
            <p>Email: contact@isetj.rnu.tn</p>
            <p>Tel: +216 78 123 456</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> ISET Jendouba. All rights reserved.</p>
    </div>
</footer>

<script src="script.js"></script>
<!-- ISET Chatbot Integration -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="chatbot.js"></script>
</body>
</html>