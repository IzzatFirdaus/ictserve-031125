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
        # On Windows, ensure Laragon Node v22 is prioritized in PATH so npm/vite uses Node v22
        cmd.exe /c start "$title" cmd /k "cd /d \"$PROJECT_ROOT_WIN\" && set PATH=C:\\laragon\\bin\\nodejs\\node-v22;%PATH% && $command"
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

# Verify a TCP port on localhost is accepting connections (retries)
check_port() {
    local port=$1
    local attempts=${2:-5}
    local delay=${3:-1}
    local serviceName=${4:-"Port $port"}

    for ((i=1;i<=attempts;i++)); do
        if command -v nc >/dev/null 2>&1; then
            if nc -z 127.0.0.1 "$port"; then
                echo -e "\033[1;32m[OK] $serviceName is reachable on 127.0.0.1:$port\033[0m"
                return 0
            fi
        else
            if command -v curl >/dev/null 2>&1; then
                if curl -sS --fail http://127.0.0.1:"$port" >/dev/null 2>&1; then
                    echo -e "\033[1;32m[OK] $serviceName is reachable on 127.0.0.1:$port\033[0m"
                    return 0
                fi
            fi
        fi
        echo -e "\033[1;33m[WAIT] $serviceName not reachable (attempt $i/$attempts). Retrying in $delay sec...\033[0m"
        sleep $delay
    done
    echo -e "\033[1;33m[WARN] $serviceName not reachable after $attempts attempts\033[0m"
    return 1
}

# 1. Start Redis Server (WSL) - only if WSL + systemctl + redis-cli available
echo ""
echo -e "\033[1;33m[1/5] Starting Redis Server (WSL)...\033[0m"
if command -v wsl.exe >/dev/null 2>&1; then
    sysctl_present=$(wsl.exe -e bash -lc "command -v systemctl >/dev/null && echo 1 || echo 0" 2>/dev/null)
    redis_cli_present=$(wsl.exe -e bash -lc "command -v redis-cli >/dev/null && echo 1 || echo 0" 2>/dev/null)
    if [[ "$sysctl_present" == "1" && "$redis_cli_present" == "1" ]]; then
        start_service "Redis Server (WSL)" "wsl.exe --user root systemctl start redis-server && wsl.exe redis-cli ping && echo 'Redis is running!' && wsl.exe redis-cli monitor"
        check_port 6379 10 1 "Redis (WSL)"
    else
        echo -e "\033[1;33mSkipping WSL Redis start: systemctl or redis-cli not available in WSL.\033[0m"
        if command -v wsl.exe >/dev/null 2>&1; then
            read -p "WSL detected but redis not installed. Install Redis in WSL now? (Y/n): " yn
            case "$yn" in
                [Yy]* | "" )
                    echo "Installing Redis in WSL (requires sudo inside WSL)..."
                    wsl.exe -e bash -lc "sudo bash /mnt/c/laragon/www/ictserve-031125/scripts/dev/install-wsl-redis.sh"
                    ;;
                * )
                    echo "Skipping automated WSL Redis install." ;;
            esac
        fi
        # Check local port 6379 as fallback
        if command -v nc >/dev/null 2>&1; then
            if nc -z 127.0.0.1 6379; then
                echo -e "\033[1;32mLaragon Redis is running on 127.0.0.1:6379\033[0m"
                check_port 6379 5 1 "Laragon Redis"
            else
                echo -e "\033[1;33mNo Redis found on 127.0.0.1:6379. Start Laragon's Redis or install Redis in WSL.\033[0m"
            fi
        fi
    fi
else
    echo -e "\033[1;33mWSL not detected; skipping WSL Redis start. If you need WSL Redis, install WSL and redis-server inside it.\033[0m"
fi

sleep 2

# 2. Start Laravel Server
echo ""
echo -e "\033[1;33m[2/5] Starting Laravel Server...\033[0m"
start_service "Laravel Server (Port 8000)" "php artisan serve"
check_port 8000 10 1 "Laravel Server"

sleep 2

# 3. Start Laravel Reverb (WebSocket Server)
echo ""
echo -e "\033[1;33m[3/5] Starting Laravel Reverb...\033[0m"
start_service "Laravel Reverb (WebSocket)" "php artisan reverb:start"
check_port 6001 10 1 "Laravel Reverb"

sleep 2

# 4. Start Laravel Horizon or Queue Worker
echo ""
echo -e "\033[1;33m[4/5] Starting Laravel Horizon/Queue Worker...\033[0m"

# Check if Horizon is available and Redis is running
horizon_available=false
redis_running=false

# Check if Redis is accessible
if command -v wsl.exe >/dev/null 2>&1; then
    redis_ping=$(wsl.exe -e redis-cli ping 2>/dev/null)
    if [[ "$redis_ping" == "PONG" ]]; then
        redis_running=true
    fi
elif command -v redis-cli >/dev/null 2>&1; then
    redis_ping=$(redis-cli ping 2>/dev/null)
    if [[ "$redis_ping" == "PONG" ]]; then
        redis_running=true
    fi
fi

# Check if Horizon command exists
if php artisan horizon:status >/dev/null 2>&1; then
    horizon_available=true
fi

if [[ "$redis_running" == true && "$horizon_available" == true ]]; then
    echo -e "\033[1;36m  └─ Using Laravel Horizon (Redis + Horizon detected)\033[0m"
    
    # Check current Horizon status
    horizon_status=$(php artisan horizon:status 2>/dev/null)
    if [[ "$horizon_status" == *"running"* ]]; then
        echo -e "\033[1;32m  └─ Horizon already running\033[0m"
    else
        if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
            # Use WSL for better Horizon compatibility on Windows
            if command -v wsl.exe >/dev/null 2>&1; then
                start_service "Laravel Horizon (WSL)" "wsl.exe -e bash -c 'cd /mnt/c/laragon/www/ictserve-031125 && php artisan horizon'"
            else
                start_service "Laravel Horizon" "php artisan horizon"
            fi
        else
            start_service "Laravel Horizon" "php artisan horizon"
        fi
    fi
    
    # Verify Horizon is running
    horizon_check() {
        local attempts=${1:-10}
        local delay=${2:-2}
        for ((i=1;i<=attempts;i++)); do
            horizon_status=$(php artisan horizon:status 2>/dev/null)
            if [[ "$horizon_status" == *"running"* ]]; then
                echo -e "\033[1;32m[OK] Laravel Horizon running and managing queues\033[0m"
                return 0
            fi
            echo -e "\033[1;33m[WAIT] Horizon starting... (attempt $i/$attempts)\033[0m"
            sleep $delay
        done
        echo -e "\033[1;33m[WARN] Horizon not confirmed running after $attempts attempts\033[0m"
        echo -e "\033[1;37m  └─ Check manually: php artisan horizon:status\033[0m"
        return 1
    }
    horizon_check 10 2
    
else
    echo -e "\033[1;33m  └─ Using traditional Queue Worker (Horizon not available or Redis not running)\033[0m"
    start_service "Laravel Queue Worker" "php artisan queue:work --tries=3 --timeout=90 --sleep=3 --max-jobs=1000 --max-time=3600"
    
    # Check for Laravel queue process
    queue_check() {
        local attempts=${1:-8}
        local delay=${2:-1}
        for ((i=1;i<=attempts;i++)); do
            if pgrep -f "artisan queue:work" >/dev/null 2>&1 || ps aux | grep "artisan queue:work" | grep -v grep >/dev/null 2>&1; then
                echo -e "\033[1;32m[OK] Queue worker process detected\033[0m"
                return 0
            fi
            echo -e "\033[1;33m[WAIT] Queue worker not detected (attempt $i/$attempts). Retrying in $delay sec...\033[0m"
            sleep $delay
        done
        echo -e "\033[1;33m[WARN] Queue worker process not detected after $attempts attempts\033[0m"
        return 1
    }
    queue_check 8 1
fi

sleep 2

# 5. Start Vite Dev Server
echo ""
echo -e "\033[1;33m[5/5] Starting Vite Dev Server...\033[0m"
start_service "Vite Dev Server (HMR)" "npm run dev"
check_port 5173 10 1 "Vite Dev Server"

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
if [[ "$redis_running" == true && "$horizon_available" == true ]]; then
    echo -e "  \033[1;36m4. Laravel Horizon         - Queue management\033[0m"
else
    echo -e "  \033[1;36m4. Queue Worker             - Processing jobs\033[0m"
fi
echo -e "  \033[1;32m5. Vite Dev Server          - Hot Module Replacement\033[0m"
echo ""
echo -e "\033[1;33mQuick Access:\033[0m"
echo -e "  \033[1;37m• Application:       http://127.0.0.1:8000\033[0m"
echo -e "  \033[1;37m• Admin Panel:       http://127.0.0.1:8000/admin\033[0m"
if [[ "$redis_running" == true && "$horizon_available" == true ]]; then
    echo -e "  \033[1;37m• Horizon Dashboard: http://127.0.0.1:8000/horizon\033[0m"
fi
echo -e "  \033[1;37m• Vite Dev Server:   http://127.0.0.1:5173\033[0m"
echo ""
echo -e "\033[1;33mClose this window to keep services running.\033[0m"
echo -e "\033[1;33mTo stop all services, close each window individually.\033[0m"
echo ""
read -p "Press any key to exit..."
