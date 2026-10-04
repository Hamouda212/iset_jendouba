<?php
// events.php - Student Event Listing and Registration
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = getDB();
$user_id = $_SESSION['user_id'];

// Handle event registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_event'])) {
    $event_id = (int)$_POST['event_id'];
    
    // Check if already registered
    $check = $conn->query("SELECT * FROM registrations WHERE user_id = $user_id AND event_id = $event_id");
    if ($check->num_rows === 0) {
        $event = $conn->query("SELECT remaining_places, title FROM events WHERE id = $event_id")->fetch_assoc();
        if ($event['remaining_places'] > 0) {
            $conn->query("INSERT INTO registrations (user_id, event_id) VALUES ($user_id, $event_id)");
            $conn->query("UPDATE events SET remaining_places = remaining_places - 1 WHERE id = $event_id");
            
            // Create notification
            $conn->query("
                INSERT INTO notifications (user_id, event_id, title, message)
                VALUES ($user_id, $event_id, 'Registration Confirmed', 'You have successfully registered for \"{$event['title']}\"')
            ");
            $_SESSION['message'] = 'Successfully registered for event!';
        } else {
            $_SESSION['error'] = 'Event is full!';
        }
    } else {
        $_SESSION['error'] = 'You are already registered for this event!';
    }
    header('Location: events.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Events - ISET Jendouba</title>
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
            <li><a href="events.php" class="active">Events</a></li>
            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a></li>
            <li><span style="color: white;">👋 <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li><a href="adminevent.php" class="btn-primary">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="btn-outline">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="section-title"><i class="fas fa-calendar-alt"></i> Available Events</h1>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="events-grid">
        <?php
        $result = $conn->query("SELECT * FROM events WHERE status != 'cancelled' ORDER BY event_date ASC");
        while ($event = $result->fetch_assoc()):
            $registered = $conn->query("SELECT * FROM registrations WHERE user_id = $user_id AND event_id = {$event['id']}")->num_rows > 0;
            $remaining = $event['remaining_places'];
            $capacityClass = $remaining == 0 ? 'capacity-low' : ($remaining < 10 ? 'capacity-medium' : 'capacity-high');
        ?>
        <div class="event-card">
            <div class="event-card-body">
                <span class="badge" style="background: #FFD700; color: #1a1a2e;"><?php echo $event['certification']; ?> Certification</span>
                <h3 style="margin-top: 0.75rem;"><?php echo htmlspecialchars($event['title']); ?></h3>
                <p style="color: #666; font-size: 0.85rem; margin: 0.5rem 0;"><?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?>...</p>
                <div class="event-meta">
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($event['speaker']); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo date('h:i A', strtotime($event['event_time'])); ?></span>
                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['place']); ?></span>
                </div>
                <span class="badge <?php echo $capacityClass; ?>"><i class="fas fa-users"></i> <?php echo $remaining; ?> / <?php echo $event['capacity']; ?> spots left</span>
                
                <?php if (!$registered && $remaining > 0): ?>
                    <form method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="register_event" value="1">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" class="btn-warning" style="width: 100%;">📝 Register Now</button>
                    </form>
                <?php elseif ($registered): ?>
                    <button class="btn-success" style="width: 100%; margin-top: 1rem;" disabled>✅ Already Registered</button>
                <?php else: ?>
                    <button class="btn-danger" style="width: 100%; margin-top: 1rem;" disabled>❌ Full Capacity</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
        <?php if ($result->num_rows == 0): ?>
            <div class="form-card" style="text-align: center;">No events available at the moment.</div>
        <?php endif; ?>
    </div>
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