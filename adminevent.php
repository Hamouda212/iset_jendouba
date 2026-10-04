<?php
// adminevent.php - Admin Event Management (Full CRUD + Notifications)
require_once 'db.php';

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$conn = getDB();
$message = '';
$messageType = '';

// Handle all POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // CREATE Event
    if ($action === 'create_event') {
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $speaker = $conn->real_escape_string($_POST['speaker']);
        $place = $conn->real_escape_string($_POST['place']);
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'];
        $capacity = (int)$_POST['capacity'];
        $certification = $_POST['certification'];
        $status = $_POST['status'];
        $created_by = $_SESSION['user_id'];
        
        $conn->query("
            INSERT INTO events (title, description, speaker, place, event_date, event_time, capacity, remaining_places, certification, status, created_by)
            VALUES ('$title', '$description', '$speaker', '$place', '$event_date', '$event_time', $capacity, $capacity, '$certification', '$status', $created_by)
        ");
        
        $eventId = $conn->insert_id;
        
        // Notify all students about new event
        $users = $conn->query("SELECT id FROM users WHERE role = 'student'");
        while ($user = $users->fetch_assoc()) {
            $conn->query("
                INSERT INTO notifications (user_id, event_id, title, message)
                VALUES ({$user['id']}, $eventId, 'New Event: $title', 'A new event \"$title\" has been added. Register now!')
            ");
        }
        
        $message = "Event created successfully!";
        $messageType = "success";
    }
    
    // UPDATE Event
    if ($action === 'update_event') {
        $id = (int)$_POST['event_id'];
        $title = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $speaker = $conn->real_escape_string($_POST['speaker']);
        $place = $conn->real_escape_string($_POST['place']);
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'];
        $capacity = (int)$_POST['capacity'];
        $certification = $_POST['certification'];
        $status = $_POST['status'];
        
        // Adjust remaining places if capacity changed
        $current = $conn->query("SELECT remaining_places, capacity FROM events WHERE id = $id")->fetch_assoc();
        $registeredCount = $current['capacity'] - $current['remaining_places'];
        $newRemaining = $capacity - $registeredCount;
        if ($newRemaining < 0) $newRemaining = 0;
        
        $conn->query("
            UPDATE events SET 
                title = '$title', description = '$description', speaker = '$speaker', 
                place = '$place', event_date = '$event_date', event_time = '$event_time',
                capacity = $capacity, remaining_places = $newRemaining, 
                certification = '$certification', status = '$status'
            WHERE id = $id
        ");
        
        $message = "Event updated successfully!";
        $messageType = "success";
    }
    
    // DELETE Event
    if ($action === 'delete_event') {
        $id = (int)$_POST['event_id'];
        $conn->query("DELETE FROM events WHERE id = $id");
        $message = "Event deleted successfully!";
        $messageType = "success";
    }
    
    // SEND Notifications to registered users
    if ($action === 'notify_users') {
        $event_id = (int)$_POST['event_id'];
        $title = $conn->real_escape_string($_POST['notification_title']);
        $messageText = $conn->real_escape_string($_POST['notification_message']);
        
        $registrations = $conn->query("SELECT user_id FROM registrations WHERE event_id = $event_id");
        $count = 0;
        while ($reg = $registrations->fetch_assoc()) {
            $conn->query("
                INSERT INTO notifications (user_id, event_id, title, message)
                VALUES ({$reg['user_id']}, $event_id, '$title', '$messageText')
            ");
            $count++;
        }
        $message = "Notifications sent to $count registered users!";
        $messageType = "success";
    }
}

// Get all events with registration counts
$events = $conn->query("
    SELECT e.*, COUNT(r.id) as registered_count 
    FROM events e 
    LEFT JOIN registrations r ON e.id = r.event_id 
    GROUP BY e.id 
    ORDER BY e.event_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ISET Jendouba</title>
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
            <li><span style="color: white;">👋 Admin: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
            <li><a href="adminevent.php" class="btn-primary active">Admin Panel</a></li>
            <li><a href="logout.php" class="btn-outline">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="section-title"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <!-- ==================== CREATE EVENT FORM ==================== -->
    <div class="form-card" style="margin-bottom: 2rem;">
        <h2><i class="fas fa-plus-circle"></i> Create New Event</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create_event">
            <div class="form-grid">
                <div class="form-group"><label>Event Title *</label><input type="text" name="title" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="2"></textarea></div>
                <div class="form-group"><label>Speaker *</label><input type="text" name="speaker" required></div>
                <div class="form-group"><label>Place *</label><input type="text" name="place" required></div>
                <div class="form-group"><label>Event Date *</label><input type="date" name="event_date" required></div>
                <div class="form-group"><label>Event Time *</label><input type="time" name="event_time" required></div>
                <div class="form-group"><label>Capacity *</label><input type="number" name="capacity" value="50" min="1" required></div>
                <div class="form-group">
                    <label>Certification</label>
                    <select name="certification"><option>Yes</option><option>No</option></select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-warning"><i class="fas fa-save"></i> Publish Event</button>
        </form>
    </div>
    
    <!-- ==================== EVENTS LIST WITH EDIT/DELETE ==================== -->
    <h2><i class="fas fa-list"></i> Manage Events</h2>
    <table class="data-table">
        <thead>
            <tr><th>ID</th><th>Title</th><th>Speaker</th><th>Date</th><th>Place</th><th>Capacity</th><th>Registered</th><th>Status</th><th>Actions</th</tr>
        </thead>
        <tbody>
            <?php while ($event = $events->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $event['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($event['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($event['speaker']); ?></td>
                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?><br><small><?php echo date('h:i A', strtotime($event['event_time'])); ?></small></td>
                <td><?php echo htmlspecialchars($event['place']); ?></td>
                <td><?php echo $event['capacity']; ?></td>
                <td><?php echo $event['registered_count']; ?></td>
                <td><span class="badge badge-<?php echo $event['status']; ?>"><?php echo ucfirst($event['status']); ?></span></td>
                <td>
                    <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="btn-warning" style="padding: 5px 12px;">✏️ Edit</button>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this event permanently?')">
                        <input type="hidden" name="action" value="delete_event">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" class="btn-danger" style="padding: 5px 12px;">🗑️ Delete</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <!-- ==================== NOTIFY USERS SECTION ==================== -->
    <div class="form-card" style="margin-top: 2rem;">
        <h2><i class="fas fa-bell"></i> Notify Registered Users</h2>
        <form method="POST">
            <input type="hidden" name="action" value="notify_users">
            <div class="form-group">
                <label>Select Event</label>
                <select name="event_id" required>
                    <option value="">Choose an event...</option>
                    <?php
                    $eventsForNotify = $conn->query("SELECT id, title FROM events ORDER BY event_date DESC");
                    while ($ev = $eventsForNotify->fetch_assoc()):
                    ?>
                    <option value="<?php echo $ev['id']; ?>"><?php echo htmlspecialchars($ev['title']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Notification Title</label>
                <input type="text" name="notification_title" required placeholder="e.g., Important Update">
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="notification_message" rows="3" required placeholder="Your message to all registered participants..."></textarea>
            </div>
            <button type="submit" class="btn-warning"><i class="fas fa-paper-plane"></i> Send Notifications</button>
        </form>
    </div>
</div>

<!-- ==================== EDIT MODAL ==================== -->
<div id="editModal" class="modal">
    <div class="modal-content form-card">
        <h2><i class="fas fa-edit"></i> Edit Event</h2>
        <form method="POST" id="editForm">
            <input type="hidden" name="action" value="update_event">
            <input type="hidden" name="event_id" id="edit_event_id">
            <div class="form-group"><label>Title</label><input type="text" name="title" id="edit_title" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" id="edit_description" rows="2"></textarea></div>
            <div class="form-group"><label>Speaker</label><input type="text" name="speaker" id="edit_speaker" required></div>
            <div class="form-group"><label>Place</label><input type="text" name="place" id="edit_place" required></div>
            <div class="form-group"><label>Date</label><input type="date" name="event_date" id="edit_date" required></div>
            <div class="form-group"><label>Time</label><input type="time" name="event_time" id="edit_time" required></div>
            <div class="form-group"><label>Capacity</label><input type="number" name="capacity" id="edit_capacity" required></div>
            <div class="form-group">
                <label>Certification</label>
                <select name="certification" id="edit_certification"><option>Yes</option><option>No</option></select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status">
                    <option>upcoming</option><option>ongoing</option><option>completed</option><option>cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn-warning">Update Event</button>
            <button type="button" onclick="closeModal('editModal')" class="btn-danger">Cancel</button>
        </form>
    </div>
</div>

<script>
function openEditModal(event) {
    document.getElementById('edit_event_id').value = event.id;
    document.getElementById('edit_title').value = event.title;
    document.getElementById('edit_description').value = event.description || '';
    document.getElementById('edit_speaker').value = event.speaker;
    document.getElementById('edit_place').value = event.place;
    document.getElementById('edit_date').value = event.event_date;
    document.getElementById('edit_time').value = event.event_time;
    document.getElementById('edit_capacity').value = event.capacity;
    document.getElementById('edit_certification').value = event.certification;
    document.getElementById('edit_status').value = event.status;
    document.getElementById('editModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>

<footer class="footer">
    <div class="footer-container">
        <div><h3>ISET Jendouba</h3><p>Admin Event Management System</p></div>
        <div><h4>Quick Links</h4><ul style="list-style: none;"><li><a href="events.php" style="color: #ccc;">Events</a></li></ul></div>
        <div><h4>Contact</h4><p>Email: contact@isetj.rnu.tn</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; <?php echo date('Y'); ?> ISET Jendouba. Admin Dashboard.</p></div>
</footer>

</body>
</html>