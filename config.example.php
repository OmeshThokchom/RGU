<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'rgu_portal');
define('DB_USER', 'rgu_user');
define('DB_PASS', '');

// Application Settings
define('APP_NAME', 'RGU Portal');
define('APP_ENV', 'development');
define('APP_DEBUG', true);
define('APP_URL', 'http://localhost:8000');

// File Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/uploads');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);

// Session Configuration
define('SESSION_PATH', __DIR__ . '/sessions');
define('SESSION_LIFETIME', 7200); // 2 hours

// Logging Configuration
define('LOG_PATH', __DIR__ . '/logs');
define('ERROR_LOG', LOG_PATH . '/error.log');

// Make sure important directories exist
$required_dirs = [
    UPLOAD_PATH,
    SESSION_PATH,
    LOG_PATH
];

foreach ($required_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}