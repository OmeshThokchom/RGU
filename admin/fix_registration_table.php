<?php
require_once(__DIR__ . '/../db_config.php');
require_once(__DIR__ . '/../config.php');

$conn = get_db_connection();

// Create temporary table without unique constraints
$create_temp = "CREATE TABLE admin_registration_requests_temp (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX username_status_idx (username, status),
    INDEX email_status_idx (email, status)
)";

// Copy data to temporary table
$copy_data = "INSERT INTO admin_registration_requests_temp 
              SELECT * FROM admin_registration_requests";

// Drop original table and rename temp
$drop_original = "DROP TABLE admin_registration_requests";
$rename_temp = "RENAME TABLE admin_registration_requests_temp TO admin_registration_requests";

try {
    mysqli_query($conn, "DROP TABLE IF EXISTS admin_registration_requests_temp");
    
    if (!mysqli_query($conn, $create_temp)) {
        throw new Exception("Failed to create temporary table: " . mysqli_error($conn));
    }
    
    if (!mysqli_query($conn, $copy_data)) {
        throw new Exception("Failed to copy data: " . mysqli_error($conn));
    }
    
    if (!mysqli_query($conn, $drop_original)) {
        throw new Exception("Failed to drop original table: " . mysqli_error($conn));
    }
    
    if (!mysqli_query($conn, $rename_temp)) {
        throw new Exception("Failed to rename table: " . mysqli_error($conn));
    }
    
    echo "Successfully restructured admin_registration_requests table";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

mysqli_close($conn);
?>