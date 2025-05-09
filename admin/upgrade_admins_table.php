<?php
require_once(__DIR__ . '/../db_config.php');
require_once(__DIR__ . '/../config.php');

$conn = get_db_connection();

// Create temporary table with new structure
$create_temp = "CREATE TABLE admins_temp (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    is_superuser BOOLEAN DEFAULT FALSE,
    reset_token VARCHAR(64),
    reset_token_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Copy data to temporary table
$copy_data = "INSERT INTO admins_temp (admin_id, username, password, email, reset_token, reset_token_expiry, created_at)
              SELECT admin_id, username, password, email, reset_token, reset_token_expiry, created_at FROM admins";

// Drop original table and rename temp
$drop_original = "DROP TABLE admins";
$rename_temp = "RENAME TABLE admins_temp TO admins";

try {
    mysqli_query($conn, "DROP TABLE IF EXISTS admins_temp");
    
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
    
    // Set the specified email as superuser
    $email = 'thokchomdayananda54@gmail.com';
    $query = "UPDATE admins SET is_superuser = 1 WHERE email = '$email'";
    if (!mysqli_query($conn, $query)) {
        throw new Exception("Failed to set superuser: " . mysqli_error($conn));
    }
    
    echo "Successfully upgraded admins table and set superuser privileges.";
    
    // Show current superusers
    $result = mysqli_query($conn, "SELECT username, email FROM admins WHERE is_superuser = 1");
    echo "\n\nCurrent superusers:";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "\n- {$row['username']} ({$row['email']})";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

mysqli_close($conn);
?>