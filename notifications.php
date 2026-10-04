<?php
// notifications.php - User Notifications
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = getDB();
$user_id = $_SESSION['user_id'];

// Mark notification as read
if (isset($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $id AND user_id = $user_id");
    header('Location: notifications.php');
    exit();
}

$notifications = $conn->query("
    SELECT n.*, e.title as event_title 
    FROM notifications n 
    LEFT JOIN events e ON n.event_id = e.id 
    WHERE n.user_id = $user_id 
    ORDER BY n.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications - ISET Jendouba</title>
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
            <li><a href="notifications.php" class="active"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><span style="color: white;">👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li><a href="adminevent.php" class="btn-primary">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="btn-outline">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="section-title"><i class="fas fa-bell"></i> My Notifications</h1>
    
    <?php if ($notifications->num_rows == 0): ?>
        <div class="form-card" style="text-align: center;">
            <i class="fas fa-bell-slash" style="font-size: 3rem; color: #ccc;"></i>
            <p style="margin-top: 1rem;">No notifications yet.</p>
        </div>
    <?php else: ?>
        <?php while ($notif = $notifications->fetch_assoc()): ?>
            <div class="form-card" style="margin-bottom: 1rem; background: <?php echo $notif['is_read'] ? 'white' : '#fffef0'; ?>; border-left: 4px solid <?php echo $notif['is_read'] ? '#ccc' : '#FFD700'; ?>;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h3><?php echo htmlspecialchars($notif['title']); ?></h3>
                        <p><?php echo htmlspecialchars($notif['message']); ?></p>
                        <?php if ($notif['event_title']): ?>
                            <small><i class="fas fa-calendar"></i> Related to: <?php echo htmlspecialchars($notif['event_title']); ?></small><br>
                        <?php endif; ?>
                        <small style="color: #888;"><?php echo date('M d, Y H:i', strtotime($notif['created_at'])); ?></small>
                    </div>
                    <?php if (!$notif['is_read']): ?>
                        <a href="?mark_read=<?php echo $notif['id']; ?>" class="btn-info" style="padding: 5px 15px; text-decoration: none; border-radius: 20px;">Mark as Read</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<footer class="footer">
    <div class="footer-container">
        <div><h3>ISET Jendouba</h3><p>Leading technological education.</p></div>
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