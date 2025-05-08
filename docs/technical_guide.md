# RGU Portal Technical Implementation Guide

## Architecture Overview

### 1. System Components
```
Frontend (Client-Side)
├── HTML5 + CSS3
├── Vanilla JavaScript
├── Glass Morphism UI
└── AJAX Communication

Backend (Server-Side)
├── PHP 7.4+
├── MySQL Database
├── Python Server
└── ngrok Tunneling
```

## Core System Components Explained

### 1. Database Layer

#### Connection Management
```php
// db_config.php
$conn = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

if (!$conn) {
    error_log("Connection failed: " . mysqli_connect_error());
    die("Connection failed");
}
```

#### Query Builder Pattern
```php
class QueryBuilder {
    private $conn;
    private $query;
    
    public function select($fields) {
        $this->query = "SELECT " . implode(", ", $fields);
        return $this;
    }
    
    public function from($table) {
        $this->query .= " FROM " . $table;
        return $this;
    }
    
    // Additional query building methods...
}
```

### 2. Authentication System

#### Session Management
```php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
```

#### Password Security
- Bcrypt hashing
- Salt generation
- Password verification

### 3. AJAX Implementation

#### Request Handler
```php
// ajax_handlers.php
switch($_POST['action']) {
    case 'create':
        handleCreate($_POST['data']);
        break;
    case 'update':
        handleUpdate($_POST['id'], $_POST['data']);
        break;
    case 'delete':
        handleDelete($_POST['id']);
        break;
}
```

#### Response Format
```json
{
    "status": "success|error",
    "message": "Operation result message",
    "data": {}
}
```

### 4. UI Components

#### Glass Effect Implementation
```css
.glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
}
```

#### Dynamic Table Generation
```javascript
function generateTable(data) {
    const table = document.createElement('table');
    table.className = 'data-table glass';
    
    // Generate headers
    const headers = Object.keys(data[0]);
    const thead = generateTableHeader(headers);
    table.appendChild(thead);
    
    // Generate rows
    const tbody = generateTableBody(data);
    table.appendChild(tbody);
    
    return table;
}
```

### 5. File Upload System

#### Image Processing
```php
function processImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type');
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('File too large');
    }
    
    // Process and store image
    $newFilename = generateUniqueFilename($file['name']);
    move_uploaded_file($file['tmp_name'], "uploads/" . $newFilename);
    
    return $newFilename;
}
```

### 6. Search Implementation

#### Full-Text Search
```sql
CREATE FULLTEXT INDEX idx_search 
ON students (first_name, last_name, roll_number);

SELECT * FROM students 
WHERE MATCH(first_name, last_name, roll_number) 
AGAINST('search_term' IN BOOLEAN MODE);
```

#### Search API
```php
function searchRecords($term, $table) {
    $term = mysqli_real_escape_string($conn, $term);
    $query = "SELECT * FROM $table WHERE ";
    
    // Add search conditions based on table
    switch($table) {
        case 'students':
            $query .= "MATCH(first_name, last_name, roll_number) 
                      AGAINST('$term' IN BOOLEAN MODE)";
            break;
        // Add cases for other tables
    }
    
    return mysqli_query($conn, $query);
}
```

### 7. Error Handling

#### Global Error Handler
```php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile on line $errline");
    
    if (DEBUG_MODE) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    
    // Show user-friendly error
    require_once('error.php');
    exit();
});
```

#### AJAX Error Handling
```javascript
async function handleAjaxRequest(url, data) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        showErrorToast('An error occurred while processing your request');
    }
}
```

### 8. Security Measures

#### Input Sanitization
```php
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    return htmlspecialchars(strip_tags(trim($input)));
}
```

#### CSRF Protection
```php
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || 
        $token !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }
}
```

### 9. Performance Optimization

#### Query Optimization
```php
function optimizeQuery($query) {
    // Add EXPLAIN analysis
    $explain = mysqli_query($conn, "EXPLAIN $query");
    $plan = mysqli_fetch_assoc($explain);
    
    // Log slow queries
    if ($plan['rows'] > 1000) {
        error_log("Potentially slow query: $query");
    }
    
    return $query;
}
```

#### Asset Caching
```php
// Set cache headers
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
```

## Deployment Guide

### 1. Development Setup
```bash
# Clone repository
git clone https://github.com/yourusername/rgu-portal.git

# Install dependencies
composer install

# Configure database
mysql -u root -p < schema.sql

# Start development server
python server.py
```

### 2. Production Deployment
```bash
# Set production configs
cp config.example.php config.php
vim config.php

# Set file permissions
chmod -R 755 .
chmod -R 777 uploads/

# Configure Apache virtual host
<VirtualHost *:80>
    ServerName portal.rgu.ac.in
    DocumentRoot /var/www/rgu-portal
    
    <Directory /var/www/rgu-portal>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Testing Guidelines

### 1. Unit Testing
```php
class StudentTest extends TestCase {
    public function testStudentCreation() {
        $student = new Student([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'roll_number' => 'CSE001'
        ]);
        
        $this->assertEquals('John Doe', $student->getFullName());
    }
}
```

### 2. Integration Testing
```php
class DatabaseTest extends TestCase {
    public function testDatabaseConnection() {
        $conn = new DatabaseConnection();
        $this->assertTrue($conn->isConnected());
    }
}
```

## Maintenance Procedures

### 1. Database Backup
```php
function backupDatabase() {
    $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $command = "mysqldump -u " . DB_USER . " -p" . DB_PASS . " " 
             . DB_NAME . " > backups/" . $filename;
    
    exec($command, $output, $returnVar);
    return $returnVar === 0;
}
```

### 2. Log Rotation
```php
function rotateLogs() {
    $logFile = 'error.log';
    if (filesize($logFile) > 5 * 1024 * 1024) { // 5MB
        rename($logFile, $logFile . '.' . date('Y-m-d'));
    }
}
```