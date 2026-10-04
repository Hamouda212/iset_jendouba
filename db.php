<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private $host = '127.0.0.1';
    private $port = 3306;
    private $db_name = 'iset_events_db';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {     
            $conn = new mysqli($this->host, $this->username, $this->password, '', $this->port);
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
           
            $conn->query("CREATE DATABASE IF NOT EXISTS {$this->db_name}");
            $conn->select_db($this->db_name);
            
        
            $conn->query("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    role ENUM('admin', 'student') DEFAULT 'student',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
           
            $conn->query("
                CREATE TABLE IF NOT EXISTS events (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(200) NOT NULL,
                    description TEXT,
                    speaker VARCHAR(100) NOT NULL,
                    place VARCHAR(200) NOT NULL,
                    event_date DATE NOT NULL,
                    event_time TIME NOT NULL,
                    capacity INT DEFAULT 50,
                    remaining_places INT DEFAULT 50,
                    certification ENUM('Yes', 'No') DEFAULT 'Yes',
                    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
                    created_by INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
                )
            ");
            
            
            $conn->query("
                CREATE TABLE IF NOT EXISTS registrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    event_id INT NOT NULL,
                    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_registration (user_id, event_id)
                )
            ");
            
         
            $conn->query("
                CREATE TABLE IF NOT EXISTS notifications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    event_id INT,
                    title VARCHAR(200),
                    message TEXT,
                    is_read BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
                )
            ");
            
           
            $adminEmail = 'admin@isetj.rnu.tn';
            $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
            $conn->query("
                INSERT IGNORE INTO users (name, email, password, role) 
                VALUES ('Administrator', '$adminEmail', '$adminPass', 'admin')
            ");
            
            // Insert sample events if none exist
            $checkEvents = $conn->query("SELECT COUNT(*) as count FROM events");
            $eventCount = $checkEvents->fetch_assoc()['count'];
            
            if ($eventCount == 0) {
                $conn->query("
                    INSERT INTO events (title, description, speaker, place, event_date, event_time, capacity, remaining_places, certification, status) VALUES
                    ('International Conference on Artificial Intelligence', 'Join us for a full day of AI insights, workshops, and networking with industry leaders.', 'Dr. Sarah Johnson', 'Grand Amphitheater', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '09:00:00', 200, 200, 'Yes', 'upcoming'),
                    ('Web Development Bootcamp', 'Learn HTML, CSS, JavaScript, and React in this hands-on workshop.', 'Prof. Mohamed Ali', 'Computer Lab B101', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '14:00:00', 40, 40, 'Yes', 'upcoming'),
                    ('Cybersecurity Summit', 'Discuss the latest trends in cybersecurity and ethical hacking.', 'Dr. Emma Watson', 'Conference Hall', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '10:30:00', 150, 150, 'Yes', 'upcoming'),
                    ('Data Science Workshop', 'Introduction to Python, Pandas, and Machine Learning basics.', 'Prof. Karim Ben Ahmed', 'Lab C202', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '13:00:00', 35, 35, 'No', 'upcoming')
                ");
            }
            
            $this->connection = $conn;
            
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Global function for easy access
function getDB() {
    return Database::getInstance()->getConnection();
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>