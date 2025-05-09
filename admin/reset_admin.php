<?php
require_once(__DIR__ . '/../db_config.php');
require_once(__DIR__ . '/../config.php');

$conn = get_db_connection();

// Remove the default admin account only
$query = "DELETE FROM admins WHERE username = 'admin' AND email = 'admin@rgu.ac'";

if (mysqli_query($conn, $query)) {
    echo "Successfully removed default admin account. Your changes will now persist.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>