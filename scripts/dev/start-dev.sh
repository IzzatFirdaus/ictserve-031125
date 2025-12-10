#!/bin/bash
# ICTServe Development Environment Startup Script (Git Bash Version)
# This script launches all required services in separate terminal windows

echo -e "\033[1;36mStarting ICTServe Development Environment...\033[0m"
echo -e "\033[1;36m=============================================\033[0m"
echo ""

# Get the current directory (project root)
PROJECT_ROOT=$(pwd)

# Convert Unix path to Windows path for Git Bash on Windows
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
    # Convert /c/XAMPP/htdocs/... to C:\XAMPP\htdocs\...
    PROJECT_ROOT_WIN=$(cygpath -w "$PROJECT_ROOT")
else
    PROJECT_ROOT_WIN="$PROJECT_ROOT"
fi

# Function to start a service in a new terminal window
start_service() {
    local title=$1
    local command=$2
    local color=$3

    echo -e "\033[1;33m$title\033[0m"

    # For Windows Git Bash, use 'start' command to open new cmd windows
    if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
        cmd.exe /c start "$title" cmd /k "cd /d \"$PROJECT_ROOT_WIN\" && $command"
    else
        # For Linux/Mac, use gnome-terminal or xterm
        if command -v gnome-terminal &> /dev/null; then
            gnome-terminal --title="$title" -- bash -c "cd '$PROJECT_ROOT' && $command; exec bash"
        elif command -v xterm &> /dev/null; then
            xterm -title "$title" -e "cd '$PROJECT_ROOT' && $command; bash" &
        else
            echo "No suitable terminal emulator found. Please install gnome-terminal or xterm."
            exit 1
        fi
    fi

    sleep 1
}

# 1. Start Redis Server (WSL)
echo ""
echo -e "\033[1;33m[1/5] Starting Redis Server (WSL)...\033[0m"
start_service "Redis Server (WSL)" "wsl.exe --user root systemctl start redis-server && wsl.exe redis-cli ping && echo 'Redis is running!' && wsl.exe redis-cli monitor"

sleep 2

# 2. Start Laravel Server
echo ""
echo -e "\033[1;33m[2/5] Starting Laravel Server...\033[0m"
start_service "Laravel Server (Port 8000)" "php artisan serve"

sleep 2

# 3. Start Laravel Reverb (WebSocket Server)
echo ""
echo -e "\033[1;33m[3/5] Starting Laravel Reverb...\033[0m"
start_service "Laravel Reverb (WebSocket)" "php artisan reverb:start"

sleep 2

# 4. Start Queue Worker
echo ""
echo -e "\033[1;33m[4/5] Starting Queue Worker...\033[0m"
start_service "Laravel Queue Worker" "php artisan queue:work --tries=3 --timeout=90"

sleep 2

# 5. Start Vite Dev Server
echo ""
echo -e "\033[1;33m[5/5] Starting Vite Dev Server...\033[0m"
start_service "Vite Dev Server (HMR)" "npm run dev"

sleep 2

# Summary
echo ""
echo -e "\033[1;36m=============================================\033[0m"
echo -e "\033[1;32mAll services started successfully!\033[0m"
echo -e "\033[1;36m=============================================\033[0m"
echo ""
echo -e "\033[1;37mRunning Services:\033[0m"
echo -e "  \033[1;31m1. Redis Server (WSL)       - Monitoring mode\033[0m"
echo -e "  \033[1;34m2. Laravel Server           - http://127.0.0.1:8000\033[0m"
echo -e "  \033[1;35m3. Laravel Reverb           - ws://127.0.0.1:6001\033[0m"
echo -e "  \033[1;36m4. Queue Worker             - Processing jobs\033[0m"
echo -e "  \033[1;32m5. Vite Dev Server          - Hot Module Replacement\033[0m"
echo ""
echo -e "\033[1;33mClose this window to keep services running.\033[0m"
echo -e "\033[1;33mTo stop all services, close each window individually.\033[0m"
echo ""
read -p "Press any key to exit..."
