#!/bin/bash

# =============================================================================
# ICTServe Ollama AI Integration - Production Deployment Script
# Versi: 3.6.0 (D11 v3.6.0 Technical Design Documentation)
# Tarikh: 12 Disember 2025
# Penulis: Pasukan Pembangunan BPM MOTAC
# =============================================================================

set -e  # Exit on any error

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="/var/www/ictserve"
BACKUP_DIR="/backup/ictserve-releases"
LOG_FILE="/var/log/ictserve-deployment.log"
DEPLOYMENT_DATE=$(date +%Y%m%d_%H%M%S)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

# Check if running as root
check_permissions() {
    if [[ $EUID -ne 0 ]]; then
        error "Script ini mesti dijalankan sebagai root atau dengan sudo"
    fi
}

# Validate environment
validate_environment() {
    log "Mengesahkan persekitaran sistem..."
    
    # Check required commands
    local required_commands=("php" "composer" "npm" "mysql" "redis-cli" "nginx" "supervisorctl")
    for cmd in "${required_commands[@]}"; do
        if ! command -v "$cmd" &> /dev/null; then
            error "Arahan diperlukan tidak dijumpai: $cmd"
        fi
    done
    
    # Check PHP version
    local php_version=$(php -r "echo PHP_VERSION;")
    if [[ ! "$php_version" =~ ^8\.2\. ]]; then
        error "PHP 8.2.x diperlukan. Versi semasa: $php_version"
    fi
    
    # Check disk space (minimum 5GB free)
    local free_space=$(df "$PROJECT_ROOT" | awk 'NR==2 {print $4}')
    if [[ $free_space -lt 5242880 ]]; then  # 5GB in KB
        error "Ruang disk tidak mencukupi. Minimum 5GB diperlukan."
    fi
    
    success "Persekitaran sistem sah"
}

# Check system resources
check_system_resources() {
    log "Memeriksa sumber sistem..."
    
    # Check memory (minimum 16GB)
    local total_mem=$(free -m | awk 'NR==2{print $2}')
    if [[ $total_mem -lt 16384 ]]; then
        warning "Memori sistem kurang dari 16GB. Prestasi mungkin terjejas."
    fi
    
    # Check CPU cores (minimum 4)
    local cpu_cores=$(nproc)
    if [[ $cpu_cores -lt 4 ]]; then
        warning "CPU cores kurang dari 4. Prestasi mungkin terjejas."
    fi
    
    # Check load average
    local load_avg=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
    if (( $(echo "$load_avg > $cpu_cores" | bc -l) )); then
        warning "Load average tinggi: $load_avg (CPU cores: $cpu_cores)"
    fi
    
    success "Pemeriksaan sumber sistem selesai"
}

# Backup current deployment
backup_current_deployment() {
    log "Membuat backup deployment semasa..."
    
    # Create backup directory
    mkdir -p "$BACKUP_DIR"
    
    # Backup application files
    if [[ -d "$PROJECT_ROOT" ]]; then
        tar -czf "$BACKUP_DIR/ictserve_backup_$DEPLOYMENT_DATE.tar.gz" \
            -C "$(dirname "$PROJECT_ROOT")" \
            "$(basename "$PROJECT_ROOT")" \
            --exclude="node_modules" \
            --exclude="vendor" \
            --exclude="storage/logs/*" \
            --exclude="storage/framework/cache/*"
        
        success "Backup aplikasi selesai: $BACKUP_DIR/ictserve_backup_$DEPLOYMENT_DATE.tar.gz"
    fi
    
    # Backup database
    local db_name=$(grep "DB_DATABASE=" "$PROJECT_ROOT/.env" | cut -d'=' -f2)
    local db_user=$(grep "DB_USERNAME=" "$PROJECT_ROOT/.env" | cut -d'=' -f2)
    local db_pass=$(grep "DB_PASSWORD=" "$PROJECT_ROOT/.env" | cut -d'=' -f2)
    
    if [[ -n "$db_name" && -n "$db_user" && -n "$db_pass" ]]; then
        mysqldump -u "$db_user" -p"$db_pass" \
            --single-transaction \
            --routines \
            --triggers \
            "$db_name" > "$BACKUP_DIR/database_backup_$DEPLOYMENT_DATE.sql"
        
        success "Backup database selesai: $BACKUP_DIR/database_backup_$DEPLOYMENT_DATE.sql"
    fi
}

# Check services status
check_services() {
    log "Memeriksa status perkhidmatan..."
    
    local services=("nginx" "mysql" "redis-server" "php8.2-fpm")
    for service in "${services[@]}"; do
        if systemctl is-active --quiet "$service"; then
            success "Perkhidmatan $service: AKTIF"
        else
            error "Perkhidmatan $service: TIDAK AKTIF"
        fi
    done
    
    # Check Ollama service
    if pgrep -f "ollama serve" > /dev/null; then
        success "Perkhidmatan Ollama: AKTIF"
    else
        error "Perkhidmatan Ollama: TIDAK AKTIF"
    fi
    
    # Check Supervisor programs
    local supervisor_programs=("ictserve-horizon" "ictserve-reverb")
    for program in "${supervisor_programs[@]}"; do
        if supervisorctl status "$program" | grep -q "RUNNING"; then
            success "Program Supervisor $program: BERJALAN"
        else
            warning "Program Supervisor $program: TIDAK BERJALAN"
        fi
    done
}

# Deploy application code
deploy_application() {
    log "Melaksanakan deployment aplikasi..."
    
    cd "$PROJECT_ROOT"
    
    # Pull latest code from production branch
    log "Mengambil kod terkini dari branch production..."
    git fetch origin
    git checkout production
    git pull origin production
    
    # Install PHP dependencies
    log "Memasang dependencies PHP..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Install Node.js dependencies and build assets
    log "Memasang dependencies Node.js dan membina assets..."
    npm ci --production
    npm run build
    
    # Set proper permissions
    log "Menetapkan permissions yang betul..."
    chown -R www-data:www-data "$PROJECT_ROOT"
    chmod -R 755 "$PROJECT_ROOT"
    chmod -R 775 "$PROJECT_ROOT/storage"
    chmod -R 775 "$PROJECT_ROOT/bootstrap/cache"
    
    success "Deployment aplikasi selesai"
}

# Run database migrations
run_migrations() {
    log "Menjalankan database migrations..."
    
    cd "$PROJECT_ROOT"
    
    # Check if there are pending migrations
    local pending_migrations=$(php artisan migrate:status | grep -c "Ran?" || true)
    
    if [[ $pending_migrations -gt 0 ]]; then
        log "Terdapat $pending_migrations migrations yang belum dijalankan"
        
        # Run migrations
        php artisan migrate --force --no-interaction
        
        success "Database migrations selesai"
    else
        log "Tiada migrations baru untuk dijalankan"
    fi
}

# Configure environment
configure_environment() {
    log "Mengkonfigurasi persekitaran..."
    
    cd "$PROJECT_ROOT"
    
    # Check if .env.production exists
    if [[ ! -f ".env.production" ]]; then
        error "Fail .env.production tidak dijumpai"
    fi
    
    # Backup current .env
    if [[ -f ".env" ]]; then
        cp ".env" ".env.backup.$DEPLOYMENT_DATE"
    fi
    
    # Copy production environment
    cp ".env.production" ".env"
    
    # Generate application key if not set
    if ! grep -q "APP_KEY=base64:" ".env"; then
        php artisan key:generate --force
    fi
    
    # Cache configuration
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    
    success "Konfigurasi persekitaran selesai"
}

# Setup Ollama models
setup_ollama_models() {
    log "Menyediakan model Ollama..."
    
    # Check if Ollama is running
    if ! curl -s http://127.0.0.1:11434/api/tags > /dev/null; then
        error "Pelayan Ollama tidak dapat dihubungi"
    fi
    
    # Check if required model exists
    local required_model="llama3.1:8b-instruct-q4_K_M"
    if ! ollama list | grep -q "$required_model"; then
        log "Memuat turun model yang diperlukan: $required_model"
        ollama pull "$required_model"
    fi
    
    # Test model
    local test_response=$(ollama run "$required_model" "Hello" --timeout 30s)
    if [[ -n "$test_response" ]]; then
        success "Model Ollama berfungsi dengan baik"
    else
        error "Model Ollama tidak memberikan respons"
    fi
}

# Restart services
restart_services() {
    log "Memulakan semula perkhidmatan..."
    
    # Restart PHP-FPM
    systemctl restart php8.2-fpm
    
    # Restart Nginx
    systemctl restart nginx
    
    # Restart Supervisor programs
    supervisorctl restart all
    
    # Wait for services to start
    sleep 10
    
    # Verify services are running
    check_services
    
    success "Perkhidmatan telah dimulakan semula"
}

# Run health checks
run_health_checks() {
    log "Menjalankan pemeriksaan kesihatan..."
    
    # Wait for application to warm up
    sleep 30
    
    # Check API health endpoint
    local api_health=$(curl -s https://ictserve.motac.gov.my/api/v1/ollama/health | jq -r '.data.status' 2>/dev/null || echo "error")
    
    if [[ "$api_health" == "healthy" ]]; then
        success "API kesihatan: SIHAT"
    else
        error "API kesihatan: TIDAK SIHAT ($api_health)"
    fi
    
    # Check database connectivity
    cd "$PROJECT_ROOT"
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';" 2>/dev/null | grep -q "Database OK"; then
        success "Sambungan database: OK"
    else
        error "Sambungan database: GAGAL"
    fi
    
    # Check Redis connectivity
    if redis-cli ping | grep -q "PONG"; then
        success "Sambungan Redis: OK"
    else
        error "Sambungan Redis: GAGAL"
    fi
    
    # Check Ollama connectivity
    if curl -s http://127.0.0.1:11434/api/tags > /dev/null; then
        success "Sambungan Ollama: OK"
    else
        error "Sambungan Ollama: GAGAL"
    fi
}

# Send deployment notification
send_notification() {
    log "Menghantar notifikasi deployment..."
    
    local status="$1"
    local subject="ICTServe AI Integration Deployment - $status"
    local message="Deployment ICTServe AI Integration v3.6.0 telah $status pada $(date)."
    
    # Add deployment details
    message+="\n\nButiran Deployment:"
    message+="\n- Tarikh: $DEPLOYMENT_DATE"
    message+="\n- Branch: production"
    message+="\n- Commit: $(cd "$PROJECT_ROOT" && git rev-parse --short HEAD)"
    message+="\n- Status API: $(curl -s https://ictserve.motac.gov.my/api/v1/ollama/health | jq -r '.data.status' 2>/dev/null || echo 'unknown')"
    
    # Send email notification
    echo -e "$message" | mail -s "$subject" admin@motac.gov.my
    
    success "Notifikasi dihantar"
}

# Cleanup old backups
cleanup_old_backups() {
    log "Membersihkan backup lama..."
    
    # Keep only last 10 backups
    find "$BACKUP_DIR" -name "ictserve_backup_*.tar.gz" -type f | sort -r | tail -n +11 | xargs -r rm -f
    find "$BACKUP_DIR" -name "database_backup_*.sql" -type f | sort -r | tail -n +11 | xargs -r rm -f
    
    success "Pembersihan backup selesai"
}

# Rollback function
rollback_deployment() {
    local backup_file="$1"
    
    if [[ -z "$backup_file" ]]; then
        error "Fail backup tidak dinyatakan untuk rollback"
    fi
    
    if [[ ! -f "$backup_file" ]]; then
        error "Fail backup tidak dijumpai: $backup_file"
    fi
    
    log "Memulakan rollback menggunakan: $backup_file"
    
    # Stop services
    supervisorctl stop all
    
    # Extract backup
    tar -xzf "$backup_file" -C "$(dirname "$PROJECT_ROOT")"
    
    # Restore permissions
    chown -R www-data:www-data "$PROJECT_ROOT"
    chmod -R 755 "$PROJECT_ROOT"
    chmod -R 775 "$PROJECT_ROOT/storage"
    
    # Restart services
    restart_services
    
    success "Rollback selesai"
}

# Main deployment function
main() {
    log "=== Memulakan Deployment ICTServe AI Integration v3.6.0 ==="
    
    # Check if rollback is requested
    if [[ "$1" == "rollback" ]]; then
        if [[ -n "$2" ]]; then
            rollback_deployment "$2"
        else
            error "Penggunaan: $0 rollback <backup_file>"
        fi
        return
    fi
    
    # Pre-deployment checks
    check_permissions
    validate_environment
    check_system_resources
    check_services
    
    # Create backup before deployment
    backup_current_deployment
    
    # Deploy application
    deploy_application
    configure_environment
    run_migrations
    setup_ollama_models
    
    # Restart services and verify
    restart_services
    run_health_checks
    
    # Post-deployment tasks
    cleanup_old_backups
    send_notification "BERJAYA"
    
    success "=== Deployment ICTServe AI Integration v3.6.0 SELESAI ==="
    log "Masa deployment: $(date)"
    log "Log lengkap: $LOG_FILE"
    log "Backup: $BACKUP_DIR/ictserve_backup_$DEPLOYMENT_DATE.tar.gz"
    log "Monitoring: https://pulse.ictserve.motac.gov.my"
    log "API Health: https://ictserve.motac.gov.my/api/v1/ollama/health"
}

# Error handling
trap 'error "Deployment gagal pada baris $LINENO. Sila semak log: $LOG_FILE"' ERR

# Run main function with all arguments
main "$@"