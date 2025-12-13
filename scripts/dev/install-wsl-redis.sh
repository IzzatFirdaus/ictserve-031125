#!/usr/bin/env bash
# WSL Redis Installer — idempotent installer for Ubuntu (WSL)
# Usage: sudo ./install-wsl-redis.sh OR run as root: wsl.exe --user root bash -ic "bash /path/to/install-wsl-redis.sh"

set -euo pipefail

PROJECT_ROOT="/mnt/c/laragon/www/ictserve-031125"
SCRIPT_DIR="${PROJECT_ROOT}/scripts/dev"

echo "WSL Redis Installer: Starting (project root: ${PROJECT_ROOT})"

function has_cmd() {
    command -v "$1" >/dev/null 2>&1
}

function install_redis_apt() {
    echo "Using apt/dpkg to install Redis..."
    apt update
    apt install -y redis-server
}

function configure_redis() {
    echo "Configuring Redis to listen on 0.0.0.0 (Windows accessible)..."
    CONF=/etc/redis/redis.conf
    if grep -q "^bind 127.0.0.1" "$CONF"; then
        sed -i "s/^bind 127.0.0.1.*/bind 0.0.0.0 ::1/" "$CONF" || true
    fi
    # keep protected-mode
    sed -i "s/^protected-mode .*/protected-mode yes/" "$CONF" || true
}

function start_redis() {
    echo "Starting Redis (systemctl or service fallback)..."
    if has_cmd systemctl; then
        systemctl enable --now redis-server || true
    elif has_cmd service; then
        service redis-server start || true
    else
        # last resort: run redis-server in the background
        redis-server --daemonize yes || true
    fi
}

function verify_redis() {
    if has_cmd redis-cli; then
        if redis-cli ping | grep -q PONG; then
            echo "Redis is up (redis-cli ping -> PONG)"
        else
            echo "Redis appears to be installed but is not responding (redis-cli ping). Check logs: sudo journalctl -u redis-server -n 20"
            exit 1
        fi
    else
        echo "redis-cli not found, installation probably failed or client not installed. Check 'apt install redis-server' or install redis-tools package." >&2
        exit 1
    fi
}

#: Main
echo "Checking for root..."
if [ "$(id -u)" -ne 0 ]; then
    echo "Please run this script as root inside WSL (try: 'wsl.exe --user root bash -ic \"bash /path/to/install-wsl-redis.sh\"')." >&2
    exit 1
fi

if has_cmd redis-server; then
    echo "Redis already installed. Checking status..."
    start_redis || true
    verify_redis
    echo "Redis is already installed and running. Exiting."
    exit 0
fi

echo "Preparing to install Redis..."
if has_cmd apt >/dev/null 2>&1; then
    install_redis_apt
else
    echo "No apt found. Unsupported distribution. Please install Redis manually for your distro." >&2
    exit 1
fi

configure_redis
start_redis
verify_redis
echo "WSL Redis installation completed successfully. Remember to disable Laragon Redis if you prefer WSL Redis to avoid port conflicts (Laragon UI -> Redis -> Stop)."
