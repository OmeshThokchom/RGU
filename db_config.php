<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'argu_user');
define('DB_PASS', 'argu_pass');
define('DB_NAME', 'argu_portal');

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
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    
    // Insert default admin if not exists
    $check_admin = mysqli_query($conn, "SELECT * FROM admins WHERE username = 'admin'");
    if (mysqli_num_rows($check_admin) == 0) {
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