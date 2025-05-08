# Installation Guide for RGU Portal

## Prerequisites

Before installing the RGU Portal, ensure you have the following:

- PHP 7.4 or higher
- MySQL 5.7 or higher
- MySQL root password
- Sudo privileges

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

   During setup you will be prompted for:
   - MySQL root password (required for database creation)

The setup script will automatically:
- Create necessary directories
- Configure database with default credentials
- Import database schema
- Set proper file permissions

No additional configuration is needed for basic setup.

## Default Credentials

### Database Settings
- Database Name: rgu_portal
- Database User: rgu_user
- Database Password: rgu_password123

### Admin Access
- Username: admin
- Password: admin123

## Directory Structure

After installation, the following directories will be created with proper permissions:
```
argu-portal/
├── uploads/          (777, www-data:www-data)
│   ├── students/
│   ├── faculty/
│   ├── departments/
│   └── events/
├── storage/          (777, www-data:www-data)
└── logs/            (777, www-data:www-data)
```

## Starting the Server

After installation, start the PHP development server:
```bash
sudo php -S localhost:8000
```

Access the portal at: http://localhost:8000

## Troubleshooting

### Common Issues

1. **MySQL Access Denied**
   - Make sure you have the correct MySQL root password
   - Verify MySQL is running: `sudo systemctl status mysql`
   - Check MySQL root user permissions

2. **Permission Denied Errors**
   - Verify you're running setup script with sudo
   - Check directory permissions with `ls -la`
   - Ensure www-data user exists on your system

3. **Directory Permission Issues**
   - Fix permissions manually if needed:
     ```bash
     sudo chmod -R 777 uploads storage logs
     sudo chown -R www-data:www-data uploads storage logs
     ```

## Production Deployment

For production environments, take these additional steps:

1. Change Default Passwords
   - Update admin password through the interface
   - Change MySQL user password
   - Update config.php with new credentials

2. Secure the Installation
   - Set restrictive file permissions
   - Configure HTTPS
   - Enable MySQL SSL connections
   - Set up proper firewall rules

3. Configure Web Server
   - Set up Apache/Nginx
   - Configure virtual hosts
   - Enable SSL certificates

4. Regular Maintenance
   - Set up automated backups
   - Configure log rotation
   - Monitor system resources

## Support

If you encounter issues:

1. Check error.log in the logs directory
2. Review MySQL error logs
3. Verify system requirements
4. Ensure proper file permissions

For academic project inquiries:
- Email: thokchomdayananda54@gmail.com
- GitHub: [OmeshThokchom](https://github.com/OmeshThokchom)