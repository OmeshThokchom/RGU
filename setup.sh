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
for config in "config.php"; do
    if [ -f "config.example.php" ] && [ ! -f "$config" ]; then
        cp "config.example.php" "$config"
        chown $(logname):$(logname) "$config"
        echo -e "${GREEN}Created $config file${NC}"
    fi
done

# Database setup
echo -e "${YELLOW}Setting up database...${NC}"
read -s -p "Enter MySQL root password: " rootpass
echo
[ -z "$rootpass" ] && { echo -e "${RED}MySQL root password cannot be empty${NC}"; exit 1; }

read -p "Enter desired database name (default: rgu_portal): " dbname
dbname=${dbname:-rgu_portal}
read -p "Enter desired database user (default: rgu_user): " dbuser
dbuser=${dbuser:-rgu_user}
read -s -p "Enter desired database password: " dbpass
echo
[ -z "$dbpass" ] && { echo -e "${RED}Database password cannot be empty${NC}"; exit 1; }

# Test MySQL connection
if ! mysql -u root -p"$rootpass" -e "SELECT 1" >/dev/null 2>&1; then
    echo -e "${RED}Failed to connect to MySQL. Please check your root password and MySQL service.${NC}"
    exit 1
fi

# Create database and user
echo -e "${YELLOW}Creating database and user...${NC}"
if ! mysql -u root -p"$rootpass" << EOF
CREATE DATABASE IF NOT EXISTS \`$dbname\`;
CREATE USER IF NOT EXISTS '$dbuser'@'localhost' IDENTIFIED BY '$dbpass';
GRANT ALL PRIVILEGES ON \`$dbname\`.* TO '$dbuser'@'localhost';
FLUSH PRIVILEGES;
EOF
then
    echo -e "${RED}Failed to setup database. Please check your MySQL permissions.${NC}"
    exit 1
fi

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
echo -e "\n${YELLOW}Next steps:${NC}"
echo -e "1. Start the PHP server: ${YELLOW}sudo php -S localhost:8000${NC}"
echo -e "2. Access the portal at: ${YELLOW}http://localhost:8000${NC}"
echo -e "3. Default admin credentials:"
echo -e "   Username: ${YELLOW}admin${NC}"
echo -e "   Password: ${YELLOW}admin123${NC}"