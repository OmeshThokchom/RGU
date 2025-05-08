<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'rgu_portal');
define('DB_USER', 'rgu_user');
define('DB_PASS', '');

// File Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/uploads');

// Logging Configuration
define('LOG_PATH', __DIR__ . '/logs');
define('ERROR_LOG', LOG_PATH . '/error.log');

// Make sure important directories exist
$required_dirs = [
    UPLOAD_PATH,
    LOG_PATH
];

foreach ($required_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}