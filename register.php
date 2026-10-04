<?php
// register.php - User Registration
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDB();
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
    
    // Check if email exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')");
        header('Location: login.php?registered=1');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ISET Jendouba</title>
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
            <li><a href="login.php" class="btn-primary">Login</a></li>
            <li><a href="register.php" class="btn-outline">Register</a></li>
        </ul>
    </div>
</nav>

<div class="container" style="max-width: 500px;">
    <div class="form-card">
        <h2 style="margin-bottom: 1.5rem;"><i class="fas fa-user-plus"></i> Create Account</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-warning" style="width: 100%;">Register</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
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