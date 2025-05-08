# RGU Portal Documentation

## Table of Contents
1. [Introduction](#introduction)
2. [System Architecture](#system-architecture)
3. [Database Structure](#database-structure)
4. [Core Components](#core-components)
5. [File Structure Documentation](#file-structure-documentation)
6. [Authentication & Security](#authentication--security)
7. [User Interfaces](#user-interfaces)
8. [API Documentation](#api-documentation)
9. [Setup & Installation](#setup--installation)

## Introduction

RGU Portal is a comprehensive PHP-based web application designed for educational institutions to manage their academic operations. The system provides a secure, user-friendly interface for managing departments, students, faculty, notices, and events.

## System Architecture

The application follows a traditional LAMP (Linux, Apache, MySQL, PHP) stack architecture with the following components:

```
RGU-portal/
├── admin/           # Administrative interface
├── assets/         # Static resources (CSS, JS, images)
├── includes/       # Shared PHP components
├── pages/          # Public-facing pages
└── server.py      # Python-based server with ngrok integration
```

### Key Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Security**: ngrok for secure tunneling
- **Server**: Python-based development server

## Database Structure

The system uses a relational database with the following core tables:

### 1. Departments Table
```sql
CREATE TABLE departments (
    dept_id INT PRIMARY KEY AUTO_INCREMENT,
    dept_name VARCHAR(100) UNIQUE NOT NULL,
    dept_code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    hod_name VARCHAR(100),
    created_at TIMESTAMP
)
```
This table stores department information, with each department having a unique code and name.

### 2. Students Table
```sql
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    roll_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dept_id INT NOT NULL,
    semester INT NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
)
```
Manages student records with department associations.

### 3. Faculty Table
```sql
CREATE TABLE faculties (
    faculty_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    dept_id INT NOT NULL,
    designation VARCHAR(100) NOT NULL,
    qualification VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
)
```
Stores faculty information with their respective departments.

### 4. Notices Table
```sql
CREATE TABLE notices (
    notice_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    posted_date DATE NOT NULL,
    expiry_date DATE,
    important BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP
)
```
Manages institutional notices with importance flags and expiry dates.

### 5. Events Table
```sql
CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(200) NOT NULL,
    organizer VARCHAR(100) NOT NULL,
    created_at TIMESTAMP
)
```
Handles institution events with venue and organizer information.

## Core Components

### 1. Database Configuration (db_config.php)
- Centralizes database connection management
- Provides security functions like `sanitize_input()`
- Handles automatic table creation and updates
- Manages default admin account creation

### 2. Admin Panel (admin/)
- **Login System**: Secure authentication for administrators
- **Dashboard**: Overview of system statistics
- **CRUD Operations**: 
  - Department management
  - Student records
  - Faculty information
  - Notice board
  - Event calendar

### 3. Public Interface (pages/)
- Department listings
- Faculty directory
- Student information
- Notice board
- Event calendar

### 4. Security Layer

#### Database Security
- Prepared statements prevent SQL injection
- Input sanitization through `sanitize_input()` function
- Password hashing using PHP's `password_hash()`

#### Access Control
```php
function check_admin_auth() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit();
    }
}
```

#### AJAX Handlers
The system uses secure AJAX handlers for dynamic operations:
- Form validation
- Real-time updates
- Data filtering
- Search functionality

## File Structure Documentation

### Admin Directory (`/admin/`)

#### 1. index.php
The administrative dashboard that provides:
- System statistics overview
- Quick access to all administrative functions
- Real-time data visualization
- Recent activity logs

```php
Key functions:
- getDepartmentStats(): Returns department-wise statistics
- getRecentActivity(): Fetches latest system activities
- generateDashboardCards(): Creates dashboard UI components
```

#### 2. login.php
Secure authentication interface for administrators:
- Session-based authentication
- Brute force protection
- Password hashing with salt
- Remember me functionality

```php
Key functions:
- validateAdminLogin(): Authenticates admin credentials
- createLoginSession(): Initializes admin session
- checkBruteForce(): Prevents multiple failed attempts
```

#### 3. documentation.php
Interactive documentation system:
- Markdown parsing with Parsedown
- Code syntax highlighting
- Section navigation
- Mobile-responsive layout

#### 4. departments.php
Department management interface:
- CRUD operations for departments
- HOD assignment
- Department statistics
- Faculty and student overview

```php
Key functions:
- createDepartment(): Adds new department
- updateDepartment(): Modifies department details
- getDepartmentDetails(): Fetches department information
- assignHOD(): Assigns Head of Department
```

#### 5. students.php
Student records management:
- Bulk import/export functionality
- Advanced search and filtering
- Semester-wise categorization
- Academic progress tracking

```php
Key functions:
- addStudent(): Creates new student record
- updateStudentDetails(): Modifies student information
- searchStudents(): Advanced student search
- generateStudentReport(): Creates student reports
```

#### 6. faculty.php
Faculty management system:
- Faculty profiles
- Department assignment
- Qualification management
- Contact information

```php
Key functions:
- addFaculty(): Creates faculty profile
- updateFacultyInfo(): Modifies faculty details
- assignDepartment(): Associates faculty with department
- getFacultyLoadStatus(): Checks teaching load
```

#### 7. notices.php
Notice board management:
- Priority-based notices
- Expiry date management
- Department-specific notices
- Notice categories

```php
Key functions:
- postNotice(): Creates new notice
- updateNotice(): Modifies notice content
- expireNotice(): Manages notice validity
- filterNotices(): Categorizes notices
```

#### 8. events.php
Event management system:
- Event scheduling
- Venue management
- Organizer assignment
- Event categories

```php
Key functions:
- createEvent(): Adds new event
- updateEvent(): Modifies event details
- getUpcomingEvents(): Lists future events
- categorizeEvents(): Organizes events by type
```

### Includes Directory (`/includes/`)

#### 1. header.php
Global header component:
- Navigation menu
- User session management
- Responsive design elements
- Brand identity

#### 2. footer.php
Global footer component:
- Copyright information
- Quick links
- Social media integration
- Contact details

### Pages Directory (`/pages/`)

Each public-facing page follows a consistent structure:
- Clean UI with glass morphism effects
- Responsive design
- Search functionality
- Filtered views

#### Common Features
```php
- filterContent(): Applies content filters
- searchRecords(): Implements search functionality
- paginateResults(): Handles result pagination
- renderGlassUI(): Applies glass morphism effects
```

### Assets Directory (`/assets/`)

#### CSS Files
1. **glass.css**
   - Glass morphism effects
   - UI component styles
   - Animation definitions
   - Responsive breakpoints

2. **style.css**
   - Global styles
   - Typography
   - Layout structures
   - Color schemes

#### JavaScript Files
1. **admin.js**
   - AJAX handlers
   - Form validation
   - Dynamic UI updates
   - Data manipulation

2. **glass-ui.js**
   - UI animations
   - Glass effect calculations
   - Interactive elements
   - Responsive behaviors

3. **main.js**
   - Public page functionality
   - Search implementations
   - Filter handlers
   - UI interactions

## Authentication & Security

### Database Security
- Prepared statements prevent SQL injection
- Input sanitization through `sanitize_input()` function
- Password hashing using PHP's `password_hash()`

### Access Control
```php
function check_admin_auth() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit();
    }
}
```

### AJAX Handlers
The system uses secure AJAX handlers for dynamic operations:
- Form validation
- Real-time updates
- Data filtering
- Search functionality

## User Interfaces

### 1. Public Pages
- Clean, responsive design
- Glass morphism UI effects
- Intuitive navigation
- Mobile-friendly layout

### 2. Admin Dashboard
- Comprehensive statistics
- Quick access to all modules
- Real-time data updates
- Search and filter capabilities

## API Documentation

### AJAX Endpoints (ajax_handlers.php)

1. Create/Update Records
```php
POST /admin/ajax_handlers.php
{
    "action": "create|update",
    "table": "departments|students|faculties|notices|events",
    "data": {...}
}
```

2. Delete Records
```php
POST /admin/ajax_handlers.php
{
    "action": "delete",
    "table": "table_name",
    "id": "record_id"
}
```

3. Search Functionality
```php
POST /admin/ajax_handlers.php
{
    "action": "search",
    "term": "search_term"
}
```

## Setup & Installation

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Python 3.6+
- ngrok account

### Installation Steps
1. Clone the repository
2. Run setup.sh for database configuration
3. Configure ngrok authentication
4. Start the Python server
5. Access through generated ngrok URL

### Default Credentials
- Username: admin
- Password: admin123