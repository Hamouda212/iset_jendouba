<?php
// login.php - User Authentication
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDB();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            
            if ($user['role'] === 'admin') {
                header('Location: adminevent.php');
            } else {
                header('Location: events.php');
            }
            exit();
        }
    }
    $error = "Invalid email or password";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ISET Jendouba</title>
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
        <h2 style="margin-bottom: 1.5rem;"><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful! Please login.</div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="admin@isetj.rnu.tn">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••">
            </div>
            <button type="submit" class="btn-warning" style="width: 100%;">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
        <hr style="margin: 1rem 0;">
        <p style="text-align: center; font-size: 0.85rem; color: #666;">
            Demo Admin: admin@isetj.rnu.tn / admin123
        </p>
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