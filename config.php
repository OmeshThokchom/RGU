<?php
// Load environment variables from .env file if it exists
function loadEnv($path = '.env') {
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim(trim($value), '"');
            if (!empty($name)) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }
}

// Load environment variables if .env exists
loadEnv();

// Database Configuration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'argu_portal');
if (!defined('DB_USER')) define('DB_USER', 'argu_user');
if (!defined('DB_PASS')) define('DB_PASS', 'argu_pass');

// File Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/uploads');

// Logging Configuration
define('LOG_PATH', __DIR__ . '/logs');
define('ERROR_LOG', LOG_PATH . '/error.log');

// Email Configuration with defaults
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: '587');
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: 'thokchomdayananda54@gmail.com');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: 'nemnjrecdnvctvmd');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: 'thokchomdayananda54@gmail.com');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'RGU Portal');

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