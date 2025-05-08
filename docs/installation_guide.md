# Installation Guide for RGU Portal

## Prerequisites

Before installing the RGU Portal, ensure you have the following software installed on your system:

- PHP 7.4 or higher
- MySQL 5.7 or higher

The setup script will automatically install Composer if it's not already present.

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

3. Run the setup script with sudo:
   ```bash
   sudo ./setup.sh
   ```

   The script requires sudo privileges to:
   - Install Composer globally
   - Create and set permissions on directories
   - Set proper file ownership
   - Configure the database

4. During setup, you will be prompted for:
   - MySQL root password
   - Database name (default: rgu_portal)
   - Database user (default: rgu_user)
   - Database password

## Starting the Server

After installation, start the PHP development server with:
```bash
sudo php -S localhost:8000
```

## Troubleshooting

### Common Issues

1. **Permission Denied Errors**
   - Ensure you're running the setup script with sudo
   - Check if www-data user exists on your system
   - Verify directory ownership: `ls -la`

2. **Composer Installation Failed**
   - Check your internet connection
   - Ensure PHP is properly installed
   - Try running manually with sudo:
     ```bash
     sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
     sudo php composer-setup.php
     sudo mv composer.phar /usr/local/bin/composer
     ```

3. **MySQL Connection Failed**
   - Verify MySQL is running: `sudo systemctl status mysql`
   - Check root password is correct
   - Ensure MySQL user has proper permissions

4. **Directory Permission Issues**
   - Verify permissions are set correctly:
     ```bash
     sudo chmod -R 777 uploads storage logs sessions
     sudo chown -R www-data:www-data uploads
     ```

### Directory Structure

After installation, verify these directories exist and have proper permissions:
```
argu-portal/
├── uploads/          (777, www-data:www-data)
│   ├── students/
│   ├── faculty/
│   ├── departments/
│   └── events/
├── storage/          (777, www-data:www-data)
├── logs/            (777, www-data:www-data)
└── sessions/        (777, www-data:www-data)
```

## Post-Installation

1. Access the portal at: http://localhost:8000

2. Default admin credentials:
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
3. Check MySQL logs for database-related issues