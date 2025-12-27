#!/bin/bash

# =============================================================================
# ICTServe Ollama AI Integration - Health Check Script
# Versi: 3.6.0 (D11 v3.6.0 Technical Design Documentation)
# Tarikh: 12 Disember 2025
# Penulis: Pasukan Pembangunan BPM MOTAC
# =============================================================================

set -e

# Configuration
PROJECT_ROOT="/var/www/ictserve"
LOG_FILE="/var/log/ictserve-health.log"
ALERT_EMAIL="admin@motac.gov.my"
API_BASE_URL="https://ictserve.motac.gov.my/api/v1/ollama"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Health check results
HEALTH_STATUS="healthy"
ISSUES_FOUND=()
WARNINGS_FOUND=()

# Logging function
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    HEALTH_STATUS="unhealthy"
    ISSUES_FOUND+=("$1")
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
    if [[ "$HEALTH_STATUS" == "healthy" ]]; then
        HEALTH_STATUS="degraded"
    fi
    WARNINGS_FOUND+=("$1")
}

success() {
    echo -e "${GREEN}[OK]${NC} $1" | tee -a "$LOG_FILE"
}

# Check system resources
check_system_resources() {
    log "Memeriksa sumber sistem..."
    
    # Check CPU usage
    local cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)
    if (( $(echo "$cpu_usage > 80" | bc -l) )); then
        warning "Penggunaan CPU tinggi: ${cpu_usage}%"
    else
        success "Penggunaan CPU: ${cpu_usage}%"
    fi
    
    # Check memory usage
    local mem_usage=$(free | grep Mem | awk '{printf("%.1f", $3/$2 * 100.0)}')
    if (( $(echo "$mem_usage > 90" | bc -l) )); then
        error "Penggunaan memori kritikal: ${mem_usage}%"
    elif (( $(echo "$mem_usage > 80" | bc -l) )); then
        warning "Penggunaan memori tinggi: ${mem_usage}%"
    else
        success "Penggunaan memori: ${mem_usage}%"
    fi
    
    # Check disk usage
    local disk_usage=$(df "$PROJECT_ROOT" | awk 'NR==2 {print $5}' | cut -d'%' -f1)
    if [[ $disk_usage -gt 90 ]]; then
        error "Penggunaan disk kritikal: ${disk_usage}%"
    elif [[ $disk_usage -gt 80 ]]; then
        warning "Penggunaan disk tinggi: ${disk_usage}%"
    else
        success "Penggunaan disk: ${disk_usage}%"
    fi
    
    # Check load average
    local load_avg=$(uptime | awk -F'load average:' '{print $2}' | awk '{print $1}' | sed 's/,//')
    local cpu_cores=$(nproc)
    if (( $(echo "$load_avg > $cpu_cores" | bc -l) )); then
        warning "Load average tinggi: $load_avg (CPU cores: $cpu_cores)"
    else
        success "Load average: $load_avg"
    fi
}

# Check services status
check_services() {
    log "Memeriksa status perkhidmatan..."
    
    # Check system services
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
        
        # Test Ollama API
        if curl -s --max-time 10 http://127.0.0.1:11434/api/tags > /dev/null; then
            success "API Ollama: BOLEH DIAKSES"
        else
            error "API Ollama: TIDAK BOLEH DIAKSES"
        fi
    else
        error "Perkhidmatan Ollama: TIDAK AKTIF"
    fi
    
    # Check Supervisor programs
    local supervisor_programs=("ictserve-horizon" "ictserve-reverb")
    for program in "${supervisor_programs[@]}"; do
        local status=$(supervisorctl status "$program" 2>/dev/null | awk '{print $2}' || echo "UNKNOWN")
        if [[ "$status" == "RUNNING" ]]; then
            success "Program Supervisor $program: BERJALAN"
        else
            error "Program Supervisor $program: $status"
        fi
    done
}

# Check database connectivity and performance
check_database() {
    log "Memeriksa database..."
    
    cd "$PROJECT_ROOT"
    
    # Check database connectivity
    if php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null | grep -q "OK"; then
        success "Sambungan database: OK"
        
        # Check database performance
        local query_time=$(php artisan tinker --execute="
            \$start = microtime(true);
            DB::table('users')->count();
            echo round((microtime(true) - \$start) * 1000, 2);
        " 2>/dev/null | tail -1)
        
        if (( $(echo "$query_time > 1000" | bc -l) )); then
            warning "Prestasi database perlahan: ${query_time}ms"
        else
            success "Prestasi database: ${query_time}ms"
        fi
        
        # Check database size
        local db_size=$(mysql -e "
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB'
            FROM information_schema.tables
            WHERE table_schema = DATABASE();
        " --skip-column-names 2>/dev/null || echo "0")
        
        success "Saiz database: ${db_size}MB"
        
    else
        error "Sambungan database: GAGAL"
    fi
}

# Check Redis connectivity and performance
check_redis() {
    log "Memeriksa Redis..."
    
    # Check Redis connectivity
    if redis-cli ping 2>/dev/null | grep -q "PONG"; then
        success "Sambungan Redis: OK"
        
        # Check Redis memory usage
        local redis_memory=$(redis-cli info memory | grep "used_memory_human" | cut -d':' -f2 | tr -d '\r')
        success "Penggunaan memori Redis: $redis_memory"
        
        # Check Redis hit rate
        local hits=$(redis-cli info stats | grep "keyspace_hits" | cut -d':' -f2 | tr -d '\r')
        local misses=$(redis-cli info stats | grep "keyspace_misses" | cut -d':' -f2 | tr -d '\r')
        
        if [[ $hits -gt 0 && $misses -gt 0 ]]; then
            local hit_rate=$(echo "scale=2; $hits / ($hits + $misses) * 100" | bc)
            if (( $(echo "$hit_rate < 80" | bc -l) )); then
                warning "Kadar hit Redis rendah: ${hit_rate}%"
            else
                success "Kadar hit Redis: ${hit_rate}%"
            fi
        fi
        
    else
        error "Sambungan Redis: GAGAL"
    fi
}

# Check API endpoints
check_api_endpoints() {
    log "Memeriksa endpoint API..."
    
    # Check health endpoint
    local health_response=$(curl -s --max-time 30 "$API_BASE_URL/health" 2>/dev/null || echo "")
    if [[ -n "$health_response" ]]; then
        local api_status=$(echo "$health_response" | jq -r '.data.status' 2>/dev/null || echo "unknown")
        
        case "$api_status" in
            "healthy")
                success "Status API: SIHAT"
                ;;
            "degraded")
                warning "Status API: TERDEGRADASI"
                ;;
            "unhealthy")
                error "Status API: TIDAK SIHAT"
                ;;
            *)
                error "Status API: TIDAK DIKETAHUI ($api_status)"
                ;;
        esac
        
        # Check response time
        local response_time=$(curl -s -w "%{time_total}" -o /dev/null --max-time 30 "$API_BASE_URL/health" 2>/dev/null || echo "999")
        local response_time_ms=$(echo "$response_time * 1000" | bc | cut -d'.' -f1)
        
        if [[ $response_time_ms -gt 5000 ]]; then
            warning "Masa respons API perlahan: ${response_time_ms}ms"
        else
            success "Masa respons API: ${response_time_ms}ms"
        fi
        
    else
        error "Endpoint API kesihatan: TIDAK BOLEH DIAKSES"
    fi
    
    # Check FAQ endpoint (with authentication)
    # Note: This would require a valid API token in production
    # For now, just check if the endpoint responds to unauthenticated requests
    local faq_response=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$API_BASE_URL/faq/query" 2>/dev/null || echo "000")
    if [[ "$faq_response" == "401" ]]; then
        success "Endpoint FAQ: MEMERLUKAN PENGESAHAN (seperti yang dijangka)"
    elif [[ "$faq_response" == "000" ]]; then
        error "Endpoint FAQ: TIDAK BOLEH DIAKSES"
    else
        success "Endpoint FAQ: BOLEH DIAKSES (HTTP $faq_response)"
    fi
}

# Check SSL certificate
check_ssl_certificate() {
    log "Memeriksa sijil SSL..."
    
    local domain="ictserve.motac.gov.my"
    local cert_info=$(echo | openssl s_client -servername "$domain" -connect "$domain:443" 2>/dev/null | openssl x509 -noout -dates 2>/dev/null || echo "")
    
    if [[ -n "$cert_info" ]]; then
        local not_after=$(echo "$cert_info" | grep "notAfter" | cut -d'=' -f2)
        local expiry_date=$(date -d "$not_after" +%s 2>/dev/null || echo "0")
        local current_date=$(date +%s)
        local days_until_expiry=$(( (expiry_date - current_date) / 86400 ))
        
        if [[ $days_until_expiry -lt 7 ]]; then
            error "Sijil SSL akan tamat tempoh dalam $days_until_expiry hari"
        elif [[ $days_until_expiry -lt 30 ]]; then
            warning "Sijil SSL akan tamat tempoh dalam $days_until_expiry hari"
        else
            success "Sijil SSL sah untuk $days_until_expiry hari lagi"
        fi
    else
        error "Tidak dapat memeriksa sijil SSL"
    fi
}

# Check log files for errors
check_log_files() {
    log "Memeriksa fail log untuk ralat..."
    
    # Check Laravel logs
    local laravel_log="$PROJECT_ROOT/storage/logs/laravel.log"
    if [[ -f "$laravel_log" ]]; then
        local recent_errors=$(tail -1000 "$laravel_log" | grep -c "ERROR" || echo "0")
        local recent_criticals=$(tail -1000 "$laravel_log" | grep -c "CRITICAL" || echo "0")
        
        if [[ $recent_criticals -gt 0 ]]; then
            error "Terdapat $recent_criticals ralat kritikal dalam log Laravel"
        elif [[ $recent_errors -gt 10 ]]; then
            warning "Terdapat $recent_errors ralat dalam log Laravel"
        else
            success "Log Laravel: $recent_errors ralat ditemui"
        fi
    else
        warning "Fail log Laravel tidak dijumpai"
    fi
    
    # Check Nginx error logs
    local nginx_log="/var/log/nginx/error.log"
    if [[ -f "$nginx_log" ]]; then
        local nginx_errors=$(tail -1000 "$nginx_log" | grep -c "error" || echo "0")
        if [[ $nginx_errors -gt 5 ]]; then
            warning "Terdapat $nginx_errors ralat dalam log Nginx"
        else
            success "Log Nginx: $nginx_errors ralat ditemui"
        fi
    fi
    
    # Check system logs for critical issues
    local system_criticals=$(journalctl --since "1 hour ago" --priority=crit --no-pager -q | wc -l)
    if [[ $system_criticals -gt 0 ]]; then
        error "Terdapat $system_criticals mesej kritikal dalam log sistem"
    else
        success "Log sistem: Tiada mesej kritikal"
    fi
}

# Check queue status
check_queue_status() {
    log "Memeriksa status queue..."
    
    cd "$PROJECT_ROOT"
    
    # Check failed jobs
    local failed_jobs=$(php artisan queue:failed --format=json 2>/dev/null | jq length 2>/dev/null || echo "0")
    if [[ $failed_jobs -gt 10 ]]; then
        warning "Terdapat $failed_jobs kerja queue yang gagal"
    else
        success "Kerja queue yang gagal: $failed_jobs"
    fi
    
    # Check Horizon status
    local horizon_status=$(php artisan horizon:status 2>/dev/null || echo "inactive")
    if [[ "$horizon_status" == "running" ]]; then
        success "Status Horizon: BERJALAN"
    else
        error "Status Horizon: $horizon_status"
    fi
}

# Generate health report
generate_health_report() {
    log "Menjana laporan kesihatan..."
    
    local report_file="/tmp/ictserve-health-report-$(date +%Y%m%d_%H%M%S).txt"
    
    {
        echo "=== Laporan Kesihatan ICTServe AI Integration ==="
        echo "Tarikh: $(date)"
        echo "Status Keseluruhan: $HEALTH_STATUS"
        echo
        
        if [[ ${#ISSUES_FOUND[@]} -gt 0 ]]; then
            echo "MASALAH KRITIKAL:"
            for issue in "${ISSUES_FOUND[@]}"; do
                echo "- $issue"
            done
            echo
        fi
        
        if [[ ${#WARNINGS_FOUND[@]} -gt 0 ]]; then
            echo "AMARAN:"
            for warning in "${WARNINGS_FOUND[@]}"; do
                echo "- $warning"
            done
            echo
        fi
        
        echo "BUTIRAN SISTEM:"
        echo "- CPU: $(top -bn1 | grep "Cpu(s)" | awk '{print $2}')"
        echo "- Memori: $(free -h | grep Mem | awk '{print $3 "/" $2}')"
        echo "- Disk: $(df -h "$PROJECT_ROOT" | awk 'NR==2 {print $3 "/" $2 " (" $5 ")"}')"
        echo "- Load: $(uptime | awk -F'load average:' '{print $2}')"
        echo
        
        echo "STATUS PERKHIDMATAN:"
        systemctl is-active nginx mysql redis-server php8.2-fpm | paste <(echo -e "Nginx\nMySQL\nRedis\nPHP-FPM") - | column -t
        echo
        
        echo "API KESIHATAN:"
        curl -s "$API_BASE_URL/health" | jq '.data' 2>/dev/null || echo "Tidak dapat mengakses API"
        
    } > "$report_file"
    
    echo "$report_file"
}

# Send alert if issues found
send_alert() {
    if [[ "$HEALTH_STATUS" != "healthy" ]]; then
        log "Menghantar alert untuk status: $HEALTH_STATUS"
        
        local report_file=$(generate_health_report)
        local subject="ICTServe AI Health Alert - Status: $HEALTH_STATUS"
        
        # Send email with report
        mail -s "$subject" -a "$report_file" "$ALERT_EMAIL" < "$report_file"
        
        # Clean up report file
        rm -f "$report_file"
        
        success "Alert dihantar ke $ALERT_EMAIL"
    fi
}

# Main health check function
main() {
    log "=== Memulakan Pemeriksaan Kesihatan ICTServe AI Integration ==="
    
    # Run all health checks
    check_system_resources
    check_services
    check_database
    check_redis
    check_api_endpoints
    check_ssl_certificate
    check_log_files
    check_queue_status
    
    # Generate final status
    case "$HEALTH_STATUS" in
        "healthy")
            success "=== STATUS KESELURUHAN: SIHAT ==="
            ;;
        "degraded")
            warning "=== STATUS KESELURUHAN: TERDEGRADASI ==="
            ;;
        "unhealthy")
            error "=== STATUS KESELURUHAN: TIDAK SIHAT ==="
            ;;
    esac
    
    # Send alert if needed
    send_alert
    
    log "Pemeriksaan kesihatan selesai pada $(date)"
    
    # Exit with appropriate code
    case "$HEALTH_STATUS" in
        "healthy") exit 0 ;;
        "degraded") exit 1 ;;
        "unhealthy") exit 2 ;;
    esac
}

# Run main function
main "$@"