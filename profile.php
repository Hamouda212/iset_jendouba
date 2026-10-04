<?php
// profile.php - User Profile Management
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = getDB();
$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Get user's registered events
$myEvents = $conn->query("
    SELECT e.*, r.registration_date 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    WHERE r.user_id = $user_id 
    ORDER BY e.event_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ISET Jendouba</title>
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
            <li><a href="index.php">Home</a></li>
            <li><a href="events.php">Events</a></li>
            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><a href="profile.php" class="active">👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li><a href="adminevent.php" class="btn-primary">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="btn-outline">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem;">
        <!-- Profile Info -->
        <div class="form-card">
            <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 100px; height: 100px; background: #FFD700; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 3rem;">👤</div>
            </div>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <span class="badge" style="background: #FFD700;"><?php echo ucfirst($user['role']); ?></span></p>
            <p><strong>Member since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
        </div>
        
        <!-- Registered Events -->
        <div class="form-card">
            <h2><i class="fas fa-ticket-alt"></i> My Registered Events</h2>
            <?php if ($myEvents->num_rows == 0): ?>
                <p style="text-align: center; color: #666;">You haven't registered for any events yet.</p>
                <a href="events.php" class="btn-warning" style="display: inline-block; margin-top: 1rem;">Browse Events</a>
            <?php else: ?>
                <?php while ($event = $myEvents->fetch_assoc()): ?>
                    <div style="border-bottom: 1px solid #eee; padding: 1rem 0;">
                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('h:i A', strtotime($event['event_time'])); ?></p>
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['place']); ?></p>
                        <small>Registered on: <?php echo date('M d, Y', strtotime($event['registration_date'])); ?></small>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div><h3>ISET Jendouba</h3></div>
        <div><h4>Quick Links</h4><ul style="list-style: none;"><li><a href="events.php" style="color: #ccc;">Events</a></li></ul></div>
        <div><h4>Contact</h4><p>Email: contact@isetj.rnu.tn</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; <?php echo date('Y'); ?> ISET Jendouba.</p></div>
</footer>
<!-- ISET Chatbot Integration -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="chatbot.js"></script>
</body>
</html>