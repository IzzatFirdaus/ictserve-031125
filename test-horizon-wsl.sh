#!/bin/bash

# Test Laravel Horizon in WSL
cd /mnt/c/XAMPP/htdocs/ictserve-031125

echo "Testing Laravel Horizon in WSL..."
echo "PHP Version: $(php --version | head -n1)"
echo "Extensions: $(php -m | grep -E '(pcntl|posix|redis)' | tr '\n' ' ')"
echo "Redis Status: $(redis-cli ping 2>/dev/null || echo 'Not connected')"

# Test Horizon for 3 seconds
echo "Starting Horizon test..."
timeout 3 php artisan horizon &
HORIZON_PID=$!

sleep 1
echo "Horizon process started with PID: $HORIZON_PID"

# Check if process is running
if ps -p $HORIZON_PID > /dev/null; then
    echo "✅ Horizon is running successfully!"
else
    echo "❌ Horizon failed to start"
fi

# Clean up
kill $HORIZON_PID 2>/dev/null
echo "Test completed."