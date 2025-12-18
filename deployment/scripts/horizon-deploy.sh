#!/bin/bash

# ICTServe Laravel Horizon Deployment Script
#
# This script handles Horizon deployment including graceful shutdown,
# configuration updates, and health verification.
#
# Requirements: 23.1, 23.4
# Usage: ./horizon-deploy.sh [environment]

set -e

# Configuration
ENVIRONMENT=${1:-production}
APP_PATH="/var/www/ictserve"
SUPERVISOR_CONFIG="/etc/supervisor/conf.d/ictserve-horizon.conf"
LOG_FILE="/var/www/ictserve/storage/logs/horizon-deploy.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[$(date '+%Y-%m-%d %H:%M:%S')] ERROR:${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[$(date '+%Y-%m-%d %H:%M:%S')] WARNING:${NC} $1" | tee -a "$LOG_FILE"
}

# Check if running as root or with sudo
check_permissions() {
    if [[ $EUID -ne 0 ]]; then
        error "This script must be run as root or with sudo"
        exit 1
    fi
}

# Verify application path exists
check_app_path() {
    if [[ ! -d "$APP_PATH" ]]; then
        error "Application path $APP_PATH does not exist"
        exit 1
    fi
}

# Check if Horizon is currently running
check_horizon_status() {
    log "Checking current Horizon status..."
    
    if supervisorctl status ictserve-horizon >/dev/null 2>&1; then
        log "Horizon supervisor process found"
        return 0
    else
        warning "Horizon supervisor process not found"
        return 1
    fi
}

# Gracefully terminate Horizon
terminate_horizon() {
    log "Terminating Horizon gracefully..."
    
    cd "$APP_PATH"
    
    # Send terminate signal to Horizon
    if php artisan horizon:terminate 2>/dev/null; then
        log "Horizon terminate signal sent successfully"
    else
        warning "Failed to send Horizon terminate signal"
    fi
    
    # Wait for graceful shutdown
    local timeout=60
    local count=0
    
    while [[ $count -lt $timeout ]]; do
        if ! pgrep -f "horizon" >/dev/null 2>&1; then
            log "Horizon processes terminated gracefully"
            return 0
        fi
        
        sleep 1
        ((count++))
    done
    
    warning "Horizon did not terminate gracefully within ${timeout}s"
    return 1
}

# Stop supervisor process
stop_supervisor() {
    log "Stopping Horizon supervisor process..."
    
    if supervisorctl stop ictserve-horizon 2>/dev/null; then
        log "Supervisor process stopped successfully"
    else
        warning "Failed to stop supervisor process or process was not running"
    fi
}

# Update supervisor configuration
update_supervisor_config() {
    log "Updating supervisor configuration..."
    
    # Copy new configuration if it exists
    if [[ -f "$APP_PATH/deployment/supervisor/ictserve-horizon.conf" ]]; then
        cp "$APP_PATH/deployment/supervisor/ictserve-horizon.conf" "$SUPERVISOR_CONFIG"
        log "Supervisor configuration updated"
    else
        warning "Supervisor configuration file not found in deployment directory"
    fi
    
    # Reload supervisor configuration
    supervisorctl reread
    supervisorctl update
    log "Supervisor configuration reloaded"
}

# Start Horizon
start_horizon() {
    log "Starting Horizon..."
    
    # Start supervisor process
    if supervisorctl start ictserve-horizon; then
        log "Horizon supervisor process started"
    else
        error "Failed to start Horizon supervisor process"
        return 1
    fi
    
    # Wait for startup
    sleep 10
    
    # Verify Horizon is running
    if verify_horizon_health; then
        log "Horizon started successfully and is healthy"
        return 0
    else
        error "Horizon failed health check after startup"
        return 1
    fi
}

# Verify Horizon health
verify_horizon_health() {
    log "Verifying Horizon health..."
    
    cd "$APP_PATH"
    
    # Check supervisor status
    if ! supervisorctl status ictserve-horizon | grep -q "RUNNING"; then
        error "Horizon supervisor process is not running"
        return 1
    fi
    
    # Check Horizon status via artisan command
    if php artisan horizon:status 2>/dev/null | grep -q "running"; then
        log "Horizon is running and healthy"
        return 0
    else
        error "Horizon health check failed"
        return 1
    fi
}

# Create health check endpoint
create_health_check() {
    log "Creating Horizon health check endpoint..."
    
    cd "$APP_PATH"
    
    # Create a simple health check script
    cat > storage/app/horizon-health-check.php << 'EOF'
<?php
// ICTServe Horizon Health Check
// Returns HTTP 200 if Horizon is healthy, 503 if not

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Check if Horizon is running
    $output = null;
    $returnCode = null;
    exec('php ' . __DIR__ . '/../../artisan horizon:status 2>&1', $output, $returnCode);
    
    if ($returnCode === 0 && strpos(implode(' ', $output), 'running') !== false) {
        http_response_code(200);
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => date('c'),
            'horizon' => 'running'
        ]);
    } else {
        http_response_code(503);
        echo json_encode([
            'status' => 'unhealthy',
            'timestamp' => date('c'),
            'horizon' => 'not running',
            'output' => $output
        ]);
    }
} catch (Exception $e) {
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'timestamp' => date('c'),
        'error' => $e->getMessage()
    ]);
}
EOF

    log "Health check endpoint created at storage/app/horizon-health-check.php"
}

# Main deployment function
deploy_horizon() {
    log "Starting ICTServe Horizon deployment for environment: $ENVIRONMENT"
    
    # Pre-deployment checks
    check_permissions
    check_app_path
    
    # Create log directory if it doesn't exist
    mkdir -p "$(dirname "$LOG_FILE")"
    
    # Deployment steps
    if check_horizon_status; then
        terminate_horizon
        stop_supervisor
    fi
    
    update_supervisor_config
    create_health_check
    start_horizon
    
    # Final verification
    if verify_horizon_health; then
        log "✅ ICTServe Horizon deployment completed successfully!"
        
        # Display status
        echo ""
        log "Horizon Status:"
        supervisorctl status ictserve-horizon
        
        echo ""
        log "Health Check URL: http://your-domain.com/storage/horizon-health-check.php"
        log "Horizon Dashboard: http://your-domain.com/horizon"
        
        return 0
    else
        error "❌ Horizon deployment failed - health check unsuccessful"
        return 1
    fi
}

# Rollback function
rollback_horizon() {
    log "Rolling back Horizon deployment..."
    
    # Stop current process
    supervisorctl stop ictserve-horizon 2>/dev/null || true
    
    # Restore previous configuration if backup exists
    if [[ -f "${SUPERVISOR_CONFIG}.backup" ]]; then
        mv "${SUPERVISOR_CONFIG}.backup" "$SUPERVISOR_CONFIG"
        log "Supervisor configuration restored from backup"
    fi
    
    # Reload and restart
    supervisorctl reread
    supervisorctl update
    supervisorctl start ictserve-horizon
    
    log "Rollback completed"
}

# Script execution
case "${2:-deploy}" in
    "deploy")
        deploy_horizon
        ;;
    "rollback")
        rollback_horizon
        ;;
    "health")
        verify_horizon_health
        ;;
    "status")
        check_horizon_status
        supervisorctl status ictserve-horizon
        ;;
    *)
        echo "Usage: $0 [environment] [deploy|rollback|health|status]"
        echo "  environment: production, staging, etc. (default: production)"
        echo "  action: deploy, rollback, health, status (default: deploy)"
        exit 1
        ;;
esac

exit $?