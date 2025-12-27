#!/bin/bash
# WSL Redis Control Script for ICTServe
# Bash script to manage Redis service within WSL

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Functions
print_info() {
    echo -e "${CYAN}$1${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

check_redis_installed() {
    if ! command -v redis-server &> /dev/null; then
        print_error "Redis is not installed"
        exit 1
    fi
}

start_redis() {
    print_info "Starting Redis service..."
    
    if sudo systemctl start redis-server 2>/dev/null; then
        print_success "Redis started via systemctl"
    elif sudo service redis-server start 2>/dev/null; then
        print_success "Redis started via service command"
    else
        print_error "Failed to start Redis"
        exit 1
    fi
    
    sleep 2
    test_connectivity
}

stop_redis() {
    print_info "Stopping Redis service..."
    
    if sudo systemctl stop redis-server 2>/dev/null; then
        print_success "Redis stopped via systemctl"
    elif sudo service redis-server stop 2>/dev/null; then
        print_success "Redis stopped via service command"
    else
        print_warning "Failed to stop Redis gracefully, force killing..."
        sudo pkill -f redis-server || true
        print_success "Redis processes terminated"
    fi
}

restart_redis() {
    print_info "Restarting Redis service..."
    stop_redis
    sleep 1
    start_redis
}

status_redis() {
    print_info "Redis service status:"
    
    # Check systemctl status
    if sudo systemctl is-active redis-server &>/dev/null; then
        print_success "Redis is active (systemctl)"
    else
        print_warning "Redis is not active (systemctl)"
    fi
    
    # Check processes
    if pgrep -f redis-server > /dev/null; then
        print_success "Redis processes are running"
        ps aux | grep redis-server | grep -v grep
    else
        print_warning "No Redis processes found"
    fi
    
    # Check port
    if netstat -tlnp 2>/dev/null | grep :6379 > /dev/null; then
        print_success "Redis is listening on port 6379"
    else
        print_warning "Redis is not listening on port 6379"
    fi
}

test_connectivity() {
    print_info "Testing Redis connectivity..."
    
    if redis-cli ping | grep -q PONG; then
        print_success "Redis responds to ping"
        return 0
    else
        print_error "Redis is not responding"
        return 1
    fi
}

show_logs() {
    local lines=${1:-50}
    print_info "Showing last $lines lines of Redis logs:"
    sudo tail -n "$lines" /var/log/redis/redis-server.log
}

show_config() {
    print_info "Redis configuration (non-comment lines):"
    sudo cat /etc/redis/redis.conf | grep -v "^#" | grep -v "^$"
}

show_info() {
    print_info "Redis server information:"
    redis-cli info server
    echo ""
    print_info "Memory usage:"
    redis-cli info memory
    echo ""
    print_info "Connected clients:"
    redis-cli info clients
}

# Main script
case "${1:-}" in
    start)
        check_redis_installed
        start_redis
        ;;
    stop)
        check_redis_installed
        stop_redis
        ;;
    restart)
        check_redis_installed
        restart_redis
        ;;
    status)
        check_redis_installed
        status_redis
        ;;
    test)
        check_redis_installed
        test_connectivity
        ;;
    logs)
        check_redis_installed
        show_logs "${2:-50}"
        ;;
    config)
        check_redis_installed
        show_config
        ;;
    info)
        check_redis_installed
        show_info
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|test|logs|config|info}"
        echo ""
        echo "Commands:"
        echo "  start   - Start Redis service"
        echo "  stop    - Stop Redis service"
        echo "  restart - Restart Redis service"
        echo "  status  - Show Redis service status"
        echo "  test    - Test Redis connectivity"
        echo "  logs    - Show Redis logs (optional: number of lines)"
        echo "  config  - Show Redis configuration"
        echo "  info    - Show Redis server information"
        exit 1
        ;;
esac