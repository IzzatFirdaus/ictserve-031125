#!/bin/bash

# =============================================================================
# ICTServe Ollama AI Integration - Rollback Script
# Versi: 3.6.0 (D11 v3.6.0 Technical Design Documentation)
# Tarikh: 12 Disember 2025
# Penulis: Pasukan Pembangunan BPM MOTAC
# =============================================================================

set -e

# Configuration
PROJECT_ROOT="/var/www/ictserve"
BACKUP_DIR="/backup/ictserve-releases"
LOG_FILE="/var/log/ictserve-rollback.log"
ROLLBACK_DATE=$(date +%Y%m%d_%H%M%S)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

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

# List available backups
list_backups() {
    log "Senarai backup yang tersedia:"
    echo
    
    if [[ ! -d "$BACKUP_DIR" ]]; then
        error "Direktori backup tidak dijumpai: $BACKUP_DIR"
    fi
    
    local app_backups=($(find "$BACKUP_DIR" -name "ictserve_backup_*.tar.gz" -type f | sort -r))
    local db_backups=($(find "$BACKUP_DIR" -name "database_backup_*.sql" -type f | sort -r))
    
    if [[ ${#app_backups[@]} -eq 0 ]]; then
        error "Tiada backup aplikasi dijumpai"
    fi
    
    echo "BACKUP APLIKASI:"
    for i in "${!app_backups[@]}"; do
        local backup_file="${app_backups[$i]}"
        local backup_name=$(basename "$backup_file")
        local backup_date=$(echo "$backup_name" | sed 's/ictserve_backup_\(.*\)\.tar\.gz/\1/')
        local backup_size=$(du -h "$backup_file" | cut -f1)
        local backup_time=$(date -d "${backup_date:0:8} ${backup_date:9:2}:${backup_date:11:2}:${backup_date:13:2}" '+%d/%m/%Y %H:%M:%S' 2>/dev/null || echo "Unknown")
        
        echo "  $((i+1)). $backup_name"
        echo "     Tarikh: $backup_time"
        echo "     Saiz: $backup_size"
        echo "     Path: $backup_file"
        echo
    done
    
    echo "BACKUP DATABASE:"
    for i in "${!db_backups[@]}"; do
        local backup_file="${db_backups[$i]}"
        local backup_name=$(basename "$backup_file")
        local backup_date=$(echo "$backup_name" | sed 's/database_backup_\(.*\)\.sql/\1/')
        local backup_size=$(du -h "$backup_file" | cut -f1)
        local backup_time=$(date -d "${backup_date:0:8} ${backup_date:9:2}:${backup_date:11:2}:${backup_date:13:2}" '+%d/%m/%Y %H:%M:%S' 2>/dev/null || echo "Unknown")
        
        echo "  $((i+1)). $backup_name"
        echo "     Tarikh: $backup_time"
        echo "     Saiz: $backup_size"
        echo "     Path: $backup_file"
        echo
    done
}

# Validate backup files
validate_backup() {
    local app_backup="$1"
    local db_backup="$2"
    
    log "Mengesahkan fail backup..."
    
    # Check application backup
    if [[ ! -f "$app_backup" ]]; then
        error "Fail backup aplikasi tidak dijumpai: $app_backup"
    fi
    
    # Test if backup file is valid
    if ! tar -tzf "$app_backup" > /dev/null 2>&1; then
        error "Fail backup aplikasi rosak atau tidak sah: $app_backup"
    fi
    
    success "Backup aplikasi sah: $app_backup"
    
    # Check database backup if provided
    if [[ -n "$db_backup" ]]; then
        if [[ ! -f "$db_backup" ]]; then
            error "Fail backup database tidak dijumpai: $db_backup"
        fi
        
        # Basic SQL file validation
        if ! head -10 "$db_backup" | grep -q "MySQL dump"; then
            warning "Fail backup database mungkin tidak sah: $db_backup"
        else
            success "Backup database sah: $db_backup"
        fi
    fi
}

# Create pre-rollback backup
create_pre_rollback_backup() {
    log "Membuat backup sebelum rollback..."
    
    local pre_rollback_backup="$BACKUP_DIR/pre_rollback_backup_$ROLLBACK_DATE.tar.gz"
    
    # Backup current application
    tar -czf "$pre_rollback_backup" \
        -C "$(dirname "$PROJECT_ROOT")" \
        "$(basename "$PROJECT_ROOT")" \
        --exclude="node_modules" \
        --exclude="vendor" \
        --exclude="storage/logs/*" \
        --exclude="storage/framework/cache/*" 2>/dev/null || true
    
    if [[ -f "$pre_rollback_backup" ]]; then
        success "Pre-rollback backup dibuat: $pre_rollback_backup"
    else
        warning "Gagal membuat pre-rollback backup"
    fi
    
    # Backup current database
    local db_name=$(grep "DB_DATABASE=" "$PROJECT_ROOT/.env" | cut -d'=' -f2 2>/dev/null || echo "")
    local db_user=$(grep "DB_USERNAME=" "$PROJECT_ROOT/.env" | cut -d'=' -f2 2>/dev/null || echo "")
    local db_pass=$(grep "DB_PASSWORD=" "$PROJECT_ROOT/.env" | cut -d'=' -f2 2>/dev/null || echo "")
    
    if [[ -n "$db_name" && -n "$db_user" && -n "$db_pass" ]]; then
        local pre_rollback_db="$BACKUP_DIR/pre_rollback_database_$ROLLBACK_DATE.sql"
        
        mysqldump -u "$db_user" -p"$db_pass" \
            --single-transaction \
            --routines \
            --triggers \
            "$db_name" > "$pre_rollback_db" 2>/dev/null || true
        
        if [[ -f "$pre_rollback_db" ]]; then
            success "Pre-rollback database backup dibuat: $pre_rollback_db"
        else
            warning "Gagal membuat pre-rollback database backup"
        fi
    fi
}

# Stop services
stop_services() {
    log "Menghentikan perkhidmatan..."
    
    # Stop Supervisor programs
    supervisorctl stop all 2>/dev/null || true
    
    # Stop web server
    systemctl stop nginx 2>/dev/null || true
    
    # Stop PHP-FPM
    systemctl stop php8.2-fpm 2>/dev/null || true
    
    success "Perkhidmatan dihentikan"
}

# Start services
start_services() {
    log "Memulakan perkhidmatan..."
    
    # Start PHP-FPM
    systemctl start php8.2-fpm
    
    # Start web server
    systemctl start nginx
    
    # Start Supervisor programs
    supervisorctl start all
    
    # Wait for services to start
    sleep 10
    
    success "Perkhidmatan dimulakan"
}

# Rollback application
rollback_application() {
    local app_backup="$1"
    
    log "Melakukan rollback aplikasi dari: $app_backup"
    
    # Remove current application (keep backup)
    if [[ -d "$PROJECT_ROOT" ]]; then
        rm -rf "${PROJECT_ROOT}.old" 2>/dev/null || true
        mv "$PROJECT_ROOT" "${PROJECT_ROOT}.old"
    fi
    
    # Extract backup
    mkdir -p "$(dirname "$PROJECT_ROOT")"
    tar -xzf "$app_backup" -C "$(dirname "$PROJECT_ROOT")"
    
    # Set proper permissions
    chown -R www-data:www-data "$PROJECT_ROOT"
    chmod -R 755 "$PROJECT_ROOT"
    chmod -R 775 "$PROJECT_ROOT/storage" 2>/dev/null || true
    chmod -R 775 "$PROJECT_ROOT/bootstrap/cache" 2>/dev/null || true
    
    success "Rollback aplikasi selesai"
}

# Rollback database
rollback_database() {
    local db_backup="$1"
    
    if [[ -z "$db_backup" ]]; then
        log "Tiada backup database dinyatakan, melangkau rollback database"
        return
    fi
    
    log "Melakukan rollback database dari: $db_backup"
    
    # Get database credentials
    local db_name=$(grep "DB_DATABASE=" "$PROJECT_ROOT/.env" | cut -d'=' -f2 2>/dev/null || echo "")
    local db_user=$(grep "DB_USERNAME=" "$PROJECT_ROOT/.env" | cut -d'=' -f2 2>/dev/null || echo "")
    local db_pass=$(grep "DB_PASSWORD=" "$PROJECT_ROOT/.env" | cut -d'=' -f2 2>/dev/null || echo "")
    
    if [[ -z "$db_name" || -z "$db_user" || -z "$db_pass" ]]; then
        error "Kredential database tidak dijumpai dalam fail .env"
    fi
    
    # Restore database
    mysql -u "$db_user" -p"$db_pass" "$db_name" < "$db_backup"
    
    success "Rollback database selesai"
}

# Clear caches after rollback
clear_caches() {
    log "Membersihkan cache selepas rollback..."
    
    cd "$PROJECT_ROOT"
    
    # Clear Laravel caches
    php artisan config:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    
    # Rebuild caches
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    
    success "Cache dibersihkan dan dibina semula"
}

# Run post-rollback checks
run_post_rollback_checks() {
    log "Menjalankan pemeriksaan selepas rollback..."
    
    # Wait for application to warm up
    sleep 30
    
    # Check if application is accessible
    local health_check=$(curl -s --max-time 30 https://ictserve.motac.gov.my/api/v1/ollama/health 2>/dev/null || echo "")
    
    if [[ -n "$health_check" ]]; then
        local status=$(echo "$health_check" | jq -r '.data.status' 2>/dev/null || echo "unknown")
        
        case "$status" in
            "healthy")
                success "Aplikasi berfungsi dengan baik selepas rollback"
                ;;
            "degraded")
                warning "Aplikasi berfungsi tetapi terdegradasi selepas rollback"
                ;;
            *)
                error "Aplikasi tidak berfungsi dengan baik selepas rollback: $status"
                ;;
        esac
    else
        error "Tidak dapat mengakses aplikasi selepas rollback"
    fi
    
    # Check database connectivity
    cd "$PROJECT_ROOT"
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null | grep -q "OK"; then
        success "Sambungan database berfungsi selepas rollback"
    else
        error "Sambungan database gagal selepas rollback"
    fi
}

# Send rollback notification
send_notification() {
    local status="$1"
    local app_backup="$2"
    local db_backup="$3"
    
    log "Menghantar notifikasi rollback..."
    
    local subject="ICTServe AI Integration Rollback - $status"
    local message="Rollback ICTServe AI Integration telah $status pada $(date)."
    
    message+="\n\nButiran Rollback:"
    message+="\n- Tarikh: $ROLLBACK_DATE"
    message+="\n- Backup aplikasi: $(basename "$app_backup")"
    if [[ -n "$db_backup" ]]; then
        message+="\n- Backup database: $(basename "$db_backup")"
    fi
    message+="\n- Status API: $(curl -s https://ictserve.motac.gov.my/api/v1/ollama/health | jq -r '.data.status' 2>/dev/null || echo 'unknown')"
    
    echo -e "$message" | mail -s "$subject" admin@motac.gov.my
    
    success "Notifikasi dihantar"
}

# Interactive rollback selection
interactive_rollback() {
    log "Mod interaktif - Pilih backup untuk rollback"
    
    list_backups
    
    echo "Pilih backup aplikasi untuk rollback:"
    read -p "Masukkan nombor pilihan (atau 'q' untuk keluar): " app_choice
    
    if [[ "$app_choice" == "q" ]]; then
        log "Rollback dibatalkan oleh pengguna"
        exit 0
    fi
    
    local app_backups=($(find "$BACKUP_DIR" -name "ictserve_backup_*.tar.gz" -type f | sort -r))
    
    if [[ ! "$app_choice" =~ ^[0-9]+$ ]] || [[ $app_choice -lt 1 ]] || [[ $app_choice -gt ${#app_backups[@]} ]]; then
        error "Pilihan tidak sah"
    fi
    
    local selected_app_backup="${app_backups[$((app_choice-1))]}"
    
    echo
    echo "Adakah anda juga mahu rollback database? (y/n)"
    read -p "Pilihan: " db_choice
    
    local selected_db_backup=""
    if [[ "$db_choice" == "y" || "$db_choice" == "Y" ]]; then
        echo "Pilih backup database untuk rollback:"
        local db_backups=($(find "$BACKUP_DIR" -name "database_backup_*.sql" -type f | sort -r))
        
        for i in "${!db_backups[@]}"; do
            local backup_name=$(basename "${db_backups[$i]}")
            echo "  $((i+1)). $backup_name"
        done
        
        read -p "Masukkan nombor pilihan: " db_num_choice
        
        if [[ "$db_num_choice" =~ ^[0-9]+$ ]] && [[ $db_num_choice -ge 1 ]] && [[ $db_num_choice -le ${#db_backups[@]} ]]; then
            selected_db_backup="${db_backups[$((db_num_choice-1))]}"
        else
            error "Pilihan database tidak sah"
        fi
    fi
    
    echo
    echo "RINGKASAN ROLLBACK:"
    echo "- Backup aplikasi: $(basename "$selected_app_backup")"
    if [[ -n "$selected_db_backup" ]]; then
        echo "- Backup database: $(basename "$selected_db_backup")"
    else
        echo "- Backup database: Tidak"
    fi
    echo
    
    read -p "Adakah anda pasti mahu meneruskan rollback? (yes/no): " confirm
    
    if [[ "$confirm" != "yes" ]]; then
        log "Rollback dibatalkan oleh pengguna"
        exit 0
    fi
    
    # Perform rollback
    perform_rollback "$selected_app_backup" "$selected_db_backup"
}

# Perform the actual rollback
perform_rollback() {
    local app_backup="$1"
    local db_backup="$2"
    
    log "=== Memulakan Rollback ICTServe AI Integration ==="
    
    # Validate backups
    validate_backup "$app_backup" "$db_backup"
    
    # Create pre-rollback backup
    create_pre_rollback_backup
    
    # Stop services
    stop_services
    
    # Perform rollback
    rollback_application "$app_backup"
    
    if [[ -n "$db_backup" ]]; then
        rollback_database "$db_backup"
    fi
    
    # Clear caches
    clear_caches
    
    # Start services
    start_services
    
    # Run checks
    run_post_rollback_checks
    
    # Send notification
    send_notification "BERJAYA" "$app_backup" "$db_backup"
    
    success "=== Rollback ICTServe AI Integration SELESAI ==="
    log "Masa rollback: $(date)"
    log "Log lengkap: $LOG_FILE"
    log "Monitoring: https://pulse.ictserve.motac.gov.my"
    log "API Health: https://ictserve.motac.gov.my/api/v1/ollama/health"
}

# Show usage
show_usage() {
    echo "Penggunaan: $0 [OPTIONS]"
    echo
    echo "OPTIONS:"
    echo "  -a, --app-backup FILE     Path ke backup aplikasi"
    echo "  -d, --db-backup FILE      Path ke backup database (opsyen)"
    echo "  -i, --interactive         Mod interaktif untuk memilih backup"
    echo "  -l, --list               Senaraikan backup yang tersedia"
    echo "  -h, --help               Paparkan bantuan ini"
    echo
    echo "CONTOH:"
    echo "  $0 --interactive"
    echo "  $0 --list"
    echo "  $0 --app-backup /backup/ictserve-releases/ictserve_backup_20251212_120000.tar.gz"
    echo "  $0 --app-backup /backup/app.tar.gz --db-backup /backup/db.sql"
}

# Main function
main() {
    local app_backup=""
    local db_backup=""
    local interactive_mode=false
    local list_mode=false
    
    # Parse command line arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            -a|--app-backup)
                app_backup="$2"
                shift 2
                ;;
            -d|--db-backup)
                db_backup="$2"
                shift 2
                ;;
            -i|--interactive)
                interactive_mode=true
                shift
                ;;
            -l|--list)
                list_mode=true
                shift
                ;;
            -h|--help)
                show_usage
                exit 0
                ;;
            *)
                error "Pilihan tidak diketahui: $1"
                ;;
        esac
    done
    
    # Check permissions
    check_permissions
    
    # Handle different modes
    if [[ "$list_mode" == true ]]; then
        list_backups
        exit 0
    elif [[ "$interactive_mode" == true ]]; then
        interactive_rollback
    elif [[ -n "$app_backup" ]]; then
        perform_rollback "$app_backup" "$db_backup"
    else
        show_usage
        exit 1
    fi
}

# Error handling
trap 'error "Rollback gagal pada baris $LINENO. Sila semak log: $LOG_FILE"' ERR

# Run main function with all arguments
main "$@"