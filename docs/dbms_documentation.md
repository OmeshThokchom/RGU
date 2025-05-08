# RGU Portal - DBMS Implementation Documentation

## Overview
This document details the Database Management System implementation for the RGU Portal, developed as part of the 4th semester DBMS course project.

## Database Schema

### Entity-Relationship Model

#### Primary Entities
1. Admin Users
2. Departments
3. Students
4. Faculty
5. Notices
6. Events

#### Relationships
- Department -(1:N)-> Students
- Department -(1:N)-> Faculty
- Admin Users -(1:N)-> Notices
- Admin Users -(1:N)-> Events

### Table Structures

#### 1. Admin Users Table
```sql
CREATE TABLE IF NOT EXISTS admin_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 2. Departments Table
```sql
CREATE TABLE IF NOT EXISTS departments (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL,
    dept_code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    hod_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 3. Faculty Table
```sql
CREATE TABLE IF NOT EXISTS faculty (
    faculty_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dept_id INT,
    designation VARCHAR(100),
    qualification TEXT,
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE SET NULL
);
```

#### 4. Students Table
```sql
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    roll_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dept_id INT,
    semester INT CHECK (semester >= 1 AND semester <= 8),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id) ON DELETE SET NULL
);
```

#### 5. Notices Table
```sql
CREATE TABLE IF NOT EXISTS notices (
    notice_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    important BOOLEAN DEFAULT FALSE,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 6. Events Table
```sql
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(200),
    organizer VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Normalization Implementation

### First Normal Form (1NF)
- All tables have primary keys (user_id, dept_id, etc.)
- No repeating groups
- Atomic values in all columns

### Second Normal Form (2NF)
- Full functional dependency on primary keys
- No partial dependencies
- Separate tables for departments and faculty

### Third Normal Form (3NF)
- No transitive dependencies
- Department details separated from student/faculty records
- Events and notices in separate tables

## Query Optimization

### Indexing Strategy
- Primary keys automatically indexed
- Foreign keys indexed (dept_id in students and faculty)
- Frequently searched fields indexed:
  - roll_number in students
  - dept_code in departments
  - event_date in events

### Performance Considerations
- Appropriate data types chosen
- TEXT for large content
- VARCHAR with reasonable limits
- Timestamp for all temporal data

## Data Security

### Access Control
- Admin authentication required
- Prepared statements for all queries
- Input validation and sanitization
- Password hashing with bcrypt

### Data Integrity
- Foreign key constraints
- CHECK constraints for valid values
- NOT NULL constraints where needed
- UNIQUE constraints for codes/numbers

## Backup Strategy

### Automated Backups
```sql
-- Create backup
mysqldump -u [user] -p [database] > backup_[date].sql

-- Restore from backup
mysql -u [user] -p [database] < backup_file.sql
```

### Backup Schedule
- Daily incremental backups
- Weekly full backups
- Monthly archives

## Examination Guidelines

### Key Points for Viva
1. Database Normalization (1NF, 2NF, 3NF)
2. Entity Relationships
3. Data Integrity Implementation
4. Security Measures
5. Backup and Recovery
6. Query Optimization

### Common Questions
1. Explain the database schema design
2. Demonstrate CRUD operations
3. Show data integrity enforcement
4. Explain security implementations
5. Describe backup procedures