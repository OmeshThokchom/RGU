# RGU Portal - DBMS Implementation Documentation

## Overview
This document details the Database Management System implementation for the RGU Portal, developed as part of the 4th semester DBMS course project.

## Database Schema

### Entity-Relationship Model

#### Primary Entities
1. Students
2. Departments
3. Faculty
4. Notices
5. Events

#### Relationships
- Department -(1:N)-> Students
- Department -(1:N)-> Faculty
- Faculty -(M:N)-> Courses
- Department -(1:N)-> Notices

### Table Structures

#### 1. Students Table
```sql
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    roll_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dept_id INT NOT NULL,
    semester INT NOT NULL CHECK (semester BETWEEN 1 AND 8),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    INDEX idx_student_name (first_name, last_name),
    INDEX idx_roll_number (roll_number)
);
```

#### 2. Departments Table
```sql
CREATE TABLE departments (
    dept_id INT PRIMARY KEY AUTO_INCREMENT,
    dept_name VARCHAR(100) UNIQUE NOT NULL,
    dept_code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    hod_name VARCHAR(100),
    student_count INT DEFAULT 0,
    faculty_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_dept_code (dept_code)
);
```

#### 3. Faculty Table
```sql
CREATE TABLE faculty (
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
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    INDEX idx_faculty_name (first_name, last_name)
);
```

## Normalization Implementation

### First Normal Form (1NF)
- All tables have primary keys
- No repeating groups
- All columns contain atomic values

### Second Normal Form (2NF)
- All non-key attributes are fully functionally dependent on the primary key
- No partial dependencies
Example:
```sql
-- Before 2NF
CREATE TABLE student_courses (
    student_id INT,
    course_id INT,
    course_name VARCHAR(100),  -- Depends only on course_id
    PRIMARY KEY (student_id, course_id)
);

-- After 2NF
CREATE TABLE courses (
    course_id INT PRIMARY KEY,
    course_name VARCHAR(100)
);

CREATE TABLE student_courses (
    student_id INT,
    course_id INT,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
);
```

### Third Normal Form (3NF)
- No transitive dependencies
Example:
```sql
-- Before 3NF
CREATE TABLE students (
    student_id INT PRIMARY KEY,
    dept_id INT,
    dept_name VARCHAR(100),  -- Depends on dept_id
    dept_hod VARCHAR(100)    -- Depends on dept_id
);

-- After 3NF
CREATE TABLE departments (
    dept_id INT PRIMARY KEY,
    dept_name VARCHAR(100),
    dept_hod VARCHAR(100)
);

CREATE TABLE students (
    student_id INT PRIMARY KEY,
    dept_id INT,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
);
```

## Query Optimization

### 1. Indexing Strategy
```sql
-- Primary indexes (automatically created)
PRIMARY KEY on all id columns

-- Secondary indexes
CREATE INDEX idx_student_name ON students(first_name, last_name);
CREATE INDEX idx_roll_number ON students(roll_number);
CREATE INDEX idx_dept_code ON departments(dept_code);
```

### 2. Query Examples

#### Efficient JOIN Operations
```sql
-- Optimized query using indexes
SELECT s.*, d.dept_name
FROM students s
INNER JOIN departments d ON s.dept_id = d.dept_id
WHERE d.dept_code = 'CSE'
AND s.semester = 4;
```

#### Aggregate Functions
```sql
-- Using indexes for grouping
SELECT d.dept_name, COUNT(s.student_id) as student_count
FROM departments d
LEFT JOIN students s ON d.dept_id = s.dept_id
GROUP BY d.dept_id, d.dept_name;
```

## Transaction Management

### 1. ACID Properties Implementation

#### Atomicity
```php
function transferStudent($studentId, $fromDept, $toDept) {
    mysqli_begin_transaction($conn);
    try {
        // Update student department
        $stmt1 = mysqli_prepare($conn, 
            "UPDATE students SET dept_id = ? WHERE student_id = ?");
        mysqli_stmt_execute($stmt1, [$toDept, $studentId]);

        // Update department counts
        $stmt2 = mysqli_prepare($conn,
            "UPDATE departments SET student_count = student_count - 1 
             WHERE dept_id = ?");
        mysqli_stmt_execute($stmt2, [$fromDept]);

        $stmt3 = mysqli_prepare($conn,
            "UPDATE departments SET student_count = student_count + 1 
             WHERE dept_id = ?");
        mysqli_stmt_execute($stmt3, [$toDept]);

        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}
```

#### Consistency
```php
function addStudent($data) {
    // Check department capacity
    $stmt = mysqli_prepare($conn,
        "SELECT student_count, max_students 
         FROM departments 
         WHERE dept_id = ?");
    mysqli_stmt_execute($stmt, [$data['dept_id']]);
    $result = mysqli_stmt_get_result($stmt);
    $dept = mysqli_fetch_assoc($result);

    if ($dept['student_count'] >= $dept['max_students']) {
        throw new Exception("Department capacity exceeded");
    }

    // Proceed with student addition
    // ...
}
```

#### Isolation
```sql
-- Set transaction isolation level
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;
```

#### Durability
```php
// Enable binary logging
ini_set('mysqli.allow_local_infile', 1);
mysqli_query($conn, "SET GLOBAL binlog_format = 'ROW'");
```

### 2. Concurrency Control

#### Row-Level Locking
```sql
-- Lock rows for update
SELECT * FROM students 
WHERE dept_id = ? 
FOR UPDATE;
```

#### Deadlock Prevention
```php
function updateMultipleRecords($records) {
    // Sort record IDs to prevent deadlocks
    sort($records);
    
    foreach ($records as $record) {
        // Process in consistent order
        updateRecord($record);
    }
}
```

## Data Security

### 1. Access Control
```sql
-- Create user with limited privileges
CREATE USER 'portal_user'@'localhost' 
IDENTIFIED BY 'secure_password';

GRANT SELECT, INSERT, UPDATE 
ON rgu_portal.students 
TO 'portal_user'@'localhost';
```

### 2. Input Validation
```php
function validateStudentData($data) {
    $errors = [];
    
    // Validate roll number format
    if (!preg_match('/^[A-Z]{3}\d{3}$/', $data['roll_number'])) {
        $errors[] = "Invalid roll number format";
    }
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    return $errors;
}
```

### 3. SQL Injection Prevention
```php
function searchStudents($searchTerm) {
    $stmt = mysqli_prepare($conn,
        "SELECT * FROM students 
         WHERE first_name LIKE ? 
         OR last_name LIKE ?");
    
    $searchPattern = "%$searchTerm%";
    mysqli_stmt_bind_param($stmt, "ss", 
        $searchPattern, $searchPattern);
    
    return mysqli_stmt_execute($stmt);
}
```

## Backup and Recovery

### 1. Automated Backups
```php
function scheduleBackups() {
    // Daily incremental backup
    if (date('H:i') === '00:00') {
        $filename = 'backup_' . date('Y-m-d') . '.sql';
        $command = "mysqldump --single-transaction " .
                  "--quick --no-autocommit " .
                  "--skip-extended-insert " .
                  "-u {$user} -p{$pass} {$db} > {$filename}";
        exec($command);
    }
}
```

### 2. Point-in-Time Recovery
```sql
-- Enable binary logging
SET GLOBAL binlog_format = 'ROW';
SET GLOBAL expire_logs_days = 14;
```

## Performance Optimization

### 1. Query Cache
```php
function getCachedResult($key) {
    $cache_file = "cache/{$key}.cache";
    
    if (file_exists($cache_file) && 
        (time() - filemtime($cache_file) < 3600)) {
        return unserialize(file_get_contents($cache_file));
    }
    
    return false;
}
```

### 2. Connection Pooling
```php
class DatabaseConnection {
    private static $connections = [];
    
    public static function getConnection() {
        if (empty(self::$connections)) {
            self::$connections[] = mysqli_connect(
                DB_HOST, DB_USER, DB_PASS, DB_NAME
            );
        }
        
        return self::$connections[
            array_rand(self::$connections)
        ];
    }
}
```

## Examination Guidelines

### Key Points to Remember
1. Database Normalization (1NF, 2NF, 3NF)
2. Transaction Management (ACID properties)
3. Index Implementation
4. Query Optimization
5. Security Measures
6. Backup Strategies

### Common Questions
1. Explain the database schema design choices
2. Demonstrate transaction handling
3. Show how data integrity is maintained
4. Discuss security implementations
5. Explain backup and recovery procedures

### Project Demonstration
1. Show the ER diagram
2. Demonstrate CRUD operations
3. Execute complex queries
4. Show transaction rollback
5. Demonstrate backup process

## References
1. Database Design Principles
2. MySQL Documentation
3. PHP PDO Documentation
4. ACID Properties in DBMS
5. SQL Optimization Techniques