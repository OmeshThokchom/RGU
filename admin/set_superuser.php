<?php
require_once(__DIR__ . '/../db_config.php');
require_once(__DIR__ . '/../config.php');

$conn = get_db_connection();

// Set the specified email as superuser
$email = 'thokchomdayananda54@gmail.com';
$query = "UPDATE admins SET is_superuser = 1 WHERE email = '$email'";

if (mysqli_query($conn, $query)) {
    echo "Successfully set $email as a superuser admin.";
    
    // Count superusers
    $count_query = "SELECT COUNT(*) as count FROM admins WHERE is_superuser = 1";
    $result = mysqli_query($conn, $count_query);
    $superuser_count = mysqli_fetch_assoc($result)['count'];
    echo "\nTotal superuser admins: $superuser_count";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>