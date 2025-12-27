#!/bin/bash

# Laravel Horizon WSL Setup Script
echo "Setting up Laravel Horizon in WSL..."

# Update system
sudo apt update

# Install PHP 8.4 with required extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and extensions (pcntl and posix are built into PHP core on Linux)
sudo apt install -y \
    php8.4-cli \
    php8.4-fpm \
    php8.4-mysql \
    php8.4-redis \
    php8.4-curl \
    php8.4-gd \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-zip \
    php8.4-bcmath \
    php8.4-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Create WSL project directory
WSL_PROJECT_DIR="/mnt/c/XAMPP/htdocs/ictserve-031125"
cd "$WSL_PROJECT_DIR"

# Install Laravel dependencies
composer install --no-dev --optimize-autoloader

# Create Horizon service script
cat > start-horizon-wsl.sh << 'EOF'
#!/bin/bash
cd /mnt/c/XAMPP/htdocs/ictserve-031125

# Start Redis if not running
if ! pgrep -x "redis-server" > /dev/null; then
    sudo systemctl start redis-server
fi

# Start Laravel Horizon
php artisan horizon
EOF

chmod +x start-horizon-wsl.sh

echo "WSL Laravel Horizon setup complete!"
echo "To start Horizon: ./start-horizon-wsl.sh"