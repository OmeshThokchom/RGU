#!/bin/bash

# Exit on error
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}Starting RGU Portal Setup...${NC}\n"

# Check for required software
echo -e "${YELLOW}Checking prerequisites...${NC}"

# Function to check prerequisites
check_prerequisite() {
    if ! command -v "$1" >/dev/null 2>&1; then
        echo -e "${RED}$1 is required but not installed. Please install $1 first.${NC}" >&2
        exit 1
    fi
}

# Check all required software
check_prerequisite "php"
check_prerequisite "composer"
check_prerequisite "mysql"
check_prerequisite "python3"

echo -e "${GREEN}All prerequisites met!${NC}\n"

# Create necessary directories safely
echo -e "${YELLOW}Creating required directories...${NC}"
for dir in uploads storage logs sessions; do
    mkdir -p "$dir"
    chmod 777 "$dir"
done

# Install PHP dependencies with error handling
echo -e "${YELLOW}Installing PHP dependencies...${NC}"
if ! composer install --no-dev; then
    echo -e "${RED}Failed to install PHP dependencies. Please check your internet connection and try again.${NC}"
    exit 1
fi

# Setup Python virtual environment with error handling
echo -e "${YELLOW}Setting up Python environment...${NC}"
if ! python3 -m venv venv; then
    echo -e "${RED}Failed to create Python virtual environment. Please check your Python installation.${NC}"
    exit 1
fi

source venv/bin/activate
if ! pip install -r requirements.txt; then
    echo -e "${RED}Failed to install Python requirements. Please check your internet connection and try again.${NC}"
    exit 1
fi

# Create configuration files
echo -e "${YELLOW}Setting up configuration files...${NC}"

# Copy example config files if they exist
for config in ".env" "config.php"; do
    example="${config%.php}.example${config##*.}"
    if [ ! -f "$config" ] && [ -f "$example" ]; then
        cp "$example" "$config"
        echo -e "${GREEN}Created $config file${NC}"
    elif [ ! -f "$example" ]; then
        echo -e "${RED}Missing $example file. Please ensure all example configuration files are present.${NC}"
        exit 1
    fi
done

# Database setup with secure password input and validation
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

# Test MySQL connection first
if ! mysql -u root -p"$rootpass" -e "SELECT 1" >/dev/null 2>&1; then
    echo -e "${RED}Failed to connect to MySQL. Please check your root password and MySQL service.${NC}"
    exit 1
fi

# Create database and user with error handling
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

# Update config files with database credentials
echo -e "${YELLOW}Updating configuration files...${NC}"
for file in ".env" "config.php"; do
    if [ -f "$file" ]; then
        sed -i.bak "s/DB_NAME=.*/DB_NAME=$dbname/" "$file" 2>/dev/null || true
        sed -i.bak "s/DB_USER=.*/DB_USER=$dbuser/" "$file" 2>/dev/null || true
        sed -i.bak "s/DB_PASS=.*/DB_PASS=$dbpass/" "$file" 2>/dev/null || true
        sed -i.bak "s/'DB_NAME',.*/'DB_NAME', '$dbname'/" "$file" 2>/dev/null || true
        sed -i.bak "s/'DB_USER',.*/'DB_USER', '$dbuser'/" "$file" 2>/dev/null || true
        sed -i.bak "s/'DB_PASS',.*/'DB_PASS', '$dbpass'/" "$file" 2>/dev/null || true
        rm -f "$file.bak"
    fi
done

# Create uploads directory structure safely
echo -e "${YELLOW}Creating upload directories...${NC}"
for dir in students faculty departments events; do
    mkdir -p "uploads/$dir"
done
chmod -R 777 uploads

# Generate encryption key securely
echo -e "${YELLOW}Generating encryption key...${NC}"
if ! ENCRYPTION_KEY=$(openssl rand -base64 32); then
    echo -e "${RED}Failed to generate encryption key. Please check if openssl is installed.${NC}"
    exit 1
fi
sed -i.bak "s/ENCRYPTION_KEY=.*/ENCRYPTION_KEY=$ENCRYPTION_KEY/" .env
rm -f .env.bak

# Import database schema if exists
if [ -f "schema.sql" ]; then
    echo -e "${YELLOW}Importing database schema...${NC}"
    if ! mysql -u "$dbuser" -p"$dbpass" "$dbname" < schema.sql; then
        echo -e "${RED}Failed to import database schema. Please check the schema.sql file.${NC}"
        exit 1
    fi
fi

# Verify setup
echo -e "${YELLOW}Verifying setup...${NC}"
if ! php -r "include 'config.php'; \$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Database connection failed');" 2>/dev/null; then
    echo -e "${RED}Database connection verification failed. Please check your configuration.${NC}"
    exit 1
fi

# Setup complete with success verification
echo -e "\n${GREEN}Setup completed successfully!${NC}"
echo -e "\n${YELLOW}Next steps:${NC}"
echo -e "1. Start the development server: ${YELLOW}python server.py${NC}"
echo -e "2. Access the portal at: ${YELLOW}http://localhost:8000${NC}"
echo -e "3. Default admin credentials:"
echo -e "   Username: ${YELLOW}admin${NC}"
echo -e "   Password: ${YELLOW}admin123${NC}"
echo -e "\n${RED}IMPORTANT: Change the default admin password after first login!${NC}"

# Optional: Display system info for debugging
echo -e "\n${YELLOW}System Information:${NC}"
echo -e "PHP Version: $(php -v | head -n1)"
echo -e "Python Version: $(python3 --version)"
echo -e "MySQL Version: $(mysql --version)"