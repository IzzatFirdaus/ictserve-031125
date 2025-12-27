#!/bin/bash
cd /mnt/c/XAMPP/htdocs/ictserve-031125

# Start Redis if not running
if ! pgrep -x "redis-server" > /dev/null; then
    sudo systemctl start redis-server
fi

# Start Laravel Horizon
php artisan horizon
