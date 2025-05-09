<?php
require_once(__DIR__ . '/config.php');

// Create connection
function get_db_connection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Create tables if they don't exist
    $tables = [
        // Admin table
        "CREATE TABLE IF NOT EXISTS admins (
            admin_id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            is_superuser BOOLEAN DEFAULT FALSE,
            reset_token VARCHAR(64),
            reset_token_expiry DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Admin registration requests table
        "CREATE TABLE IF NOT EXISTS admin_registration_requests (
            request_id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX username_status_idx (username, status),
            INDEX email_status_idx (email, status)
        )",
        
        // Departments table
        "CREATE TABLE IF NOT EXISTS departments (
            dept_id INT PRIMARY KEY AUTO_INCREMENT,
            dept_name VARCHAR(100) UNIQUE NOT NULL,
            dept_code VARCHAR(10) UNIQUE NOT NULL,
            description TEXT,
            hod_name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Students table
        "CREATE TABLE IF NOT EXISTS students (
            student_id INT PRIMARY KEY AUTO_INCREMENT,
            roll_number VARCHAR(20) UNIQUE NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            dept_id INT NOT NULL,
            semester INT NOT NULL,
            email VARCHAR(100),
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
        )",
        
        // Faculty table
        "CREATE TABLE IF NOT EXISTS faculties (
            faculty_id INT PRIMARY KEY AUTO_INCREMENT,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            dept_id INT NOT NULL,
            designation VARCHAR(100) NOT NULL,
            qualification VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
        )",
        
        // Notices table
        "CREATE TABLE IF NOT EXISTS notices (
            notice_id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            posted_date DATE NOT NULL,
            expiry_date DATE,
            important BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Events table
        "CREATE TABLE IF NOT EXISTS events (
            event_id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            description TEXT NOT NULL,
            event_date DATE NOT NULL,
            venue VARCHAR(200) NOT NULL,
            organizer VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $sql) {
        if (!mysqli_query($conn, $sql)) {
            error_log("Error creating table: " . mysqli_error($conn));
        }
    }
    
    // Only insert default admin if there are no admin accounts at all
    $check_admins = mysqli_query($conn, "SELECT COUNT(*) as count FROM admins");
    $admin_count = mysqli_fetch_assoc($check_admins)['count'];
    
    if ($admin_count == 0) {
        $default_password = password_hash('admin123', PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO admins (username, password, email) 
                            VALUES ('admin', '$default_password', 'admin@rgu.ac')");
    }
    
    return $conn;
}

// Function to safely escape user input
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Function to generate a secure random token
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

// Function to get base URL of the application
function get_base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    return rtrim($protocol . $host . $path, '/');
}

// Function to handle database errors
function handle_db_error($conn) {
    die("Database error: " . mysqli_error($conn));
}

// Function to check if user is logged in as admin
function check_admin_auth() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit();
    }
}
?>