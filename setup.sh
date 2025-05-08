#!/bin/bash

# Exit on error
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if script is run with sudo
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run this script with sudo${NC}"
    exit 1
fi

echo -e "${GREEN}Starting RGU Portal Setup...${NC}\n"

# Function to check prerequisites
check_prerequisite() {
    if ! command -v "$1" >/dev/null 2>&1; then
        echo -e "${RED}$1 is required but not installed. Please install $1 first.${NC}" >&2
        exit 1
    fi
}

# Check for PHP and MySQL only
echo -e "${YELLOW}Checking prerequisites...${NC}"
check_prerequisite "php"
check_prerequisite "mysql"

# Create mandatory directories
echo -e "${YELLOW}Creating required directories...${NC}"
for dir in uploads storage logs; do
    mkdir -p "$dir"
    chmod 777 "$dir"
    chown -R www-data:www-data "$dir"
done

# Create configuration files
echo -e "${YELLOW}Setting up configuration files...${NC}"

# Copy example config files if they exist
if [ -f "config.example.php" ] && [ ! -f "config.php" ]; then
    cp "config.example.php" "config.php"
    chown $(logname):$(logname) "config.php"
    echo -e "${GREEN}Created config.php file${NC}"
fi

# Default database settings
dbname="rgu_portal"
dbuser="rgu_user"
dbpass="rgu_password123"  # Default password

# Database setup
echo -e "${YELLOW}Setting up database...${NC}"

# Create database and user with error handling
echo -e "${YELLOW}Creating database and user...${NC}"
mysql << EOF
CREATE DATABASE IF NOT EXISTS \`$dbname\`;
CREATE USER IF NOT EXISTS '$dbuser'@'localhost' IDENTIFIED BY '$dbpass';
GRANT ALL PRIVILEGES ON \`$dbname\`.* TO '$dbuser'@'localhost';
FLUSH PRIVILEGES;
EOF

# Update config file with database credentials
echo -e "${YELLOW}Updating configuration...${NC}"
if [ -f "config.php" ]; then
    sed -i.bak "s/'DB_NAME',.*/'DB_NAME', '$dbname'/" "config.php"
    sed -i.bak "s/'DB_USER',.*/'DB_USER', '$dbuser'/" "config.php"
    sed -i.bak "s/'DB_PASS',.*/'DB_PASS', '$dbpass'/" "config.php"
    rm -f "config.php.bak"
fi

# Create upload directories
echo -e "${YELLOW}Creating upload directories...${NC}"
for dir in students faculty departments; do
    mkdir -p "uploads/$dir"
done
chmod -R 777 uploads
chown -R www-data:www-data uploads

# Import base schema
if [ -f "schema.sql" ]; then
    echo -e "${YELLOW}Importing database schema...${NC}"
    if ! mysql -u "$dbuser" -p"$dbpass" "$dbname" < schema.sql; then
        echo -e "${RED}Failed to import database schema. Please check the schema.sql file.${NC}"
        exit 1
    fi
fi

# Fix final permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 777 uploads storage logs

# Setup complete
echo -e "\n${GREEN}Setup completed successfully!${NC}"
echo -e "\n${YELLOW}Default Database Credentials:${NC}"
echo -e "Database Name: ${GREEN}$dbname${NC}"
echo -e "Database User: ${GREEN}$dbuser${NC}"
echo -e "Database Password: ${GREEN}$dbpass${NC}"
echo -e "\n${YELLOW}Default Admin Credentials:${NC}"
echo -e "Username: ${GREEN}admin${NC}"
echo -e "Password: ${GREEN}admin123${NC}"
echo -e "\n${YELLOW}Next steps:${NC}"
echo -e "1. Start the PHP server: ${YELLOW}sudo php -S localhost:8000${NC}"
echo -e "2. Access the portal at: ${YELLOW}http://localhost:8000${NC}"
echo -e "\n${RED}IMPORTANT: Change these default passwords in production!${NC}"