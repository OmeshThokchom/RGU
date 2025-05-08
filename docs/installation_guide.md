# Installation Guide for RGU Portal

## Prerequisites

Before installing the RGU Portal, ensure you have the following software installed on your system:

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Python 3.8 or higher
- Composer (PHP package manager)
- OpenSSL

## Installation Steps

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd argu-portal
   ```

2. Make the setup script executable:
   ```bash
   chmod +x setup.sh
   ```

3. Run the setup script:
   ```bash
   ./setup.sh
   ```

   The setup script will:
   - Check for required software
   - Create necessary directories
   - Install PHP dependencies
   - Set up Python virtual environment
   - Create configuration files
   - Set up the database
   - Generate encryption keys
   - Import database schema

4. During setup, you will be prompted for:
   - MySQL root password
   - Database name (default: rgu_portal)
   - Database user (default: rgu_user)
   - Database password

## Troubleshooting

### Common Issues

1. **MySQL Connection Failed**
   - Verify MySQL is running: `systemctl status mysql`
   - Check root password is correct
   - Ensure MySQL user has proper permissions

2. **PHP Dependencies Installation Failed**
   - Check internet connection
   - Run `composer install --no-dev` manually
   - Verify PHP version compatibility

3. **Python Virtual Environment Issues**
   - Ensure python3-venv is installed
   - Try creating venv manually: `python3 -m venv venv`
   - Activate venv: `source venv/bin/activate`

4. **Permission Issues**
   - Ensure proper permissions on directories:
     ```bash
     chmod -R 777 uploads storage logs sessions
     ```
   - Check file ownership matches web server user

### Directory Structure

After installation, verify these directories exist and have proper permissions:
```
argu-portal/
├── uploads/
│   ├── students/
│   ├── faculty/
│   ├── departments/
│   └── events/
├── storage/
├── logs/
└── sessions/
```

## Post-Installation

1. Start the development server:
   ```bash
   python server.py
   ```

2. Access the portal at: http://localhost:8000

3. Default admin credentials:
   - Username: admin
   - Password: admin123

   **IMPORTANT**: Change the default admin password immediately after first login!

## Security Recommendations

1. Update default admin password
2. Set proper file permissions in production
3. Configure secure MySQL passwords
4. Enable HTTPS in production
5. Regular backups of database and uploads

## Production Deployment

For production deployment, additional steps are recommended:

1. Update .env file:
   - Set APP_ENV=production
   - Set APP_DEBUG=false
   - Configure proper APP_URL

2. Set up a proper web server (Apache/Nginx)
3. Configure SSL certificate
4. Set up regular backups
5. Configure proper file permissions
6. Set up monitoring

## Support

If you encounter any issues during installation:

1. Check the error.log file in the logs directory
2. Verify system information matches requirements
3. Contact system administrator or refer to technical documentation