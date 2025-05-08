# Contributing to ARGU Portal

## Project Context
This is a personal academic project developed for the Database Management Systems (DBMS) course at Royal Global University.

## Developer Contact
- **Name**: THOKCHOM DAYANANDA
- **GitHub**: [OmeshThokchom](https://github.com/OmeshThokchom)
- **Email**: thokchomdayananda54@gmail.com

## Getting Started

### Prerequisites
1. PHP 7.4+
2. MySQL 5.7+
3. Composer
4. Git
5. Python 3.6+ (for development server)

### Development Setup
```bash
# Clone the repository
git clone https://github.com/OmeshThokchom/RGU.git
cd RGU

# Create branch
git checkout -b feature/your-feature-name

# Install dependencies
composer install

# Configure environment
cp .env.example .env
cp config.example.php config.php

# Start development server
python server.py
```

## Development Process

### 1. Making Changes
1. Create a feature branch
2. Write clean, well-documented code
3. Test your changes thoroughly
4. Update documentation as needed

### 2. Commit Guidelines

Use clear commit messages that describe your changes:
```
feat: add bulk import feature
fix: resolve login validation issue
docs: update setup instructions
```

### 3. Pull Request Process
1. Ensure your code is tested
2. Update relevant documentation
3. Create a clear PR description
4. Wait for review

## Testing

Run tests using:
```bash
vendor/bin/phpunit
```

## Questions and Support

For questions or issues:
1. Check existing issues
2. Create a new issue with clear details
3. Use discussions for general questions

## License
This project is under MIT License - see the [LICENSE](LICENSE) file.

## Version
Contributing Guide v1.0.0
Last Updated: 2023-12-08
Project Timeline: May 2025

### Database Configuration
- Database Name: rgu_portal
- Database User: rgu_user
- Database Password: rgu_password123

### Admin Access
- Username: admin
- Password: admin123