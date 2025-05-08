# RGU Portal

A comprehensive web-based portal system for Royal Global University, developed as a Database Management Systems (DBMS) course project.

## Project Status

![Build Status](https://github.com/dayananda/rgu-portal/workflows/CI/badge.svg)
![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Version](https://img.shields.io/badge/version-1.0.0-green.svg)

## Screenshots

### Admin Dashboard
![Admin Dashboard](screenshots/dashboards.png)

### Student Portal
![Student Portal](screenshots/terminal.png)

## Project Information

### Academic Context
- **Course**: B.Tech Computer Science & Engineering
- **Semester**: 4th Semester
- **Subject**: Database Management Systems (DBMS)
- **Institution**: Royal Global University

### Developer
- **Name**: THOKCHOM DAYANANDA
- **Role**: Student Developer
- **GitHub**: [OmeshThokchom](https://github.com/OmeshThokchom)
- **Email**: thokchomdayananda54@gmail.com

### Course Instructor
- **Name**: Dr. Sourabh
- **Department**: Computer Science & Engineering

## Features

### 🎓 Academic Management
- Department organization
- Student records
- Faculty profiles
- Course tracking
- Academic calendar

### 📢 Communication
- Notice board system
- Event management
- Email notifications
- Department announcements
- Important updates

### 🎨 Modern UI
- Glass morphism design
- Responsive layout
- Dark theme
- Interactive components
- Smooth animations

### 🔒 Security
- Secure authentication
- Role-based access
- Data encryption
- Activity logging
- Regular backups

## Implementation Details

### Database Design
- MySQL database implementation
- Normalized table structures
- Efficient indexing
- Referential integrity
- Transaction management

### Technical Stack
- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3
- JavaScript
- Python (Development Server)

## Quick Start

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Composer
- Python 3.6+ (for development)

### Installation

1. Clone the repository
```bash
git clone https://github.com/dayananda/rgu-portal.git
cd portal
```

2. Install dependencies
```bash
composer install
```

3. Configure environment
```bash
cp .env.example .env
cp config.example.php config.php
```

4. Set up database
```sql
CREATE DATABASE rgu_portal;
CREATE USER 'rgu_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON rgu_portal.* TO 'rgu_user'@'localhost';
```

5. Start development server
```bash
python server.py
```

## Documentation

- [User Guide](docs/user_guide.md)
- [Installation Guide](docs/installation_guide.md)
- [API Documentation](docs/api_documentation.md)
- [Development Standards](docs/development_standards.md)
- [Technical Guide](docs/technical_guide.md)

## Database Schema

### Core Tables
1. **departments**
   - Department information
   - HOD details
   - Course offerings

2. **students**
   - Student records
   - Academic history
   - Department association

3. **faculty**
   - Faculty profiles
   - Department assignment
   - Teaching load

4. **notices**
   - Announcements
   - Important updates
   - Department notices

5. **events**
   - Academic events
   - Department functions
   - University activities

## Academic Objectives

This project demonstrates proficiency in:
1. Database design and normalization
2. SQL query optimization
3. Transaction management
4. Web application development
5. System security implementation
6. Documentation practices

## Acknowledgments

Special thanks to:
- Dr. Sumit Kumar (Course Instructor)
- Department of Computer Science & Engineering
- Royal Global University

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Project Timeline
- **Start Date**: 4th Semester
- **Completion**: May 2025