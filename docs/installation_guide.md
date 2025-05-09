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
   git clone https://github.com/OmeshThokchom/RGU.git
   cd RGU
   ```

2. Run the setup script with sudo:
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

After installation, you will have the following directory structure:
```
RGU/
├── admin/          
│   ├── includes/
│   ├── ajax_handlers.php
│   ├── departments.php
│   ├── documentation.php
│   ├── events.php
│   ├── faculty.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── notices.php
│   └── students.php
├── assets/ 
│   ├── css/
│   │   ├── glass.css
│   │   └── style.css
│   ├── images/
│   │   ├── campus.jpg
│   │   ├── favicon.ico
│   │   └── logo-dark.png
│   └── js/
│       ├── admin.js
│       ├── glass-ui.js
│       └── main.js
├── docs/
│   ├── api_documentation.md
│   ├── CHANGELOG.md
│   ├── CODE_OF_CONDUCT.md
│   ├── CONTRIBUTING.md
│   ├── CONTRIBUTORS.md
│   ├── dbms_documentation.md
│   ├── development_standards.md
│   ├── documentation.md
│   ├── installation_guide.md
│   ├── SECURITY.md
│   ├── technical_guide.md
│   ├── user_guide.md
│   ├── viva_preparation.md
│   └── workflow.md
├── includes/
│   ├── header.php
│   └── footer.php
├── pages/
│   ├── departments.php
│   ├── events.php
│   ├── faculties.php
│   ├── notices.php
│   └── students.php
|── vendor/
|    └── [vendor dependencies]
├── .env.example
├── config.example.php
├── index.php
├── README.md
|── composer.json
|── composer.lock
├── db_config.php
├── error.log
├── requirements.txt
├── server.py
├── setup.sh
└── .gitignore
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