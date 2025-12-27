#!/bin/bash
# Switch between Docker and XAMPP environment configurations for ICTServe

set -e

# Color functions
print_success() { echo -e "\033[32m$1\033[0m"; }
print_warning() { echo -e "\033[33m$1\033[0m"; }
print_error() { echo -e "\033[31m$1\033[0m"; }
print_info() { echo -e "\033[36m$1\033[0m"; }

# Show usage
show_usage() {
    echo "Usage: $0 {docker|xampp} [--force]"
    echo ""
    echo "Switch between Docker and XAMPP environment configurations"
    echo ""
    echo "Arguments:"
    echo "  docker    Switch to Docker configuration (workspace default)"
    echo "  xampp     Switch to XAMPP configuration (non-workspace)"
    echo "  --force   Force overwrite without confirmation"
    echo ""
    echo "Examples:"
    echo "  $0 docker"
    echo "  $0 xampp --force"
    exit 1
}

# Check arguments
if [ $# -lt 1 ]; then
    show_usage
fi

ENV=$1
FORCE=false

if [ "$2" = "--force" ]; then
    FORCE=true
fi

# Validate environment
if [ "$ENV" != "docker" ] && [ "$ENV" != "xampp" ]; then
    print_error "Error: Environment must be 'docker' or 'xampp'"
    show_usage
fi

print_info "=== ICTServe Environment Switcher ==="
print_info "Switching to: $ENV environment"

# Check if we're in the correct directory
if [ ! -f "composer.json" ]; then
    print_error "Error: Please run this script from the ICTServe root directory"
    exit 1
fi

# Backup current .env if it exists
if [ -f ".env" ]; then
    timestamp=$(date +"%Y%m%d_%H%M%S")
    backup_file=".env.backup.$timestamp"
    
    if [ "$FORCE" = false ]; then
        print_warning "Current .env file will be backed up to: $backup_file"
        read -p "Continue? (y/N): " confirm
        if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
            print_info "Operation cancelled"
            exit 0
        fi
    fi
    
    cp ".env" "$backup_file"
    print_success "Backed up current .env to: $backup_file"
fi

# Switch environment
case $ENV in
    docker)
        print_info "Switching to Docker configuration..."
        print_info "- Database: db (container)"
        print_info "- Redis: redis (container)"
        print_info "- URL: http://localhost:8000"
        print_info "- Services: docker compose up -d"
        
        if [ -f ".env.docker" ]; then
            cp ".env.docker" ".env"
            print_success "Switched to Docker configuration (.env.docker → .env)"
        else
            print_warning ".env.docker not found, current .env should already be Docker-configured"
        fi
        ;;
        
    xampp)
        print_info "Switching to XAMPP configuration..."
        print_info "- Database: 127.0.0.1 (local MySQL)"
        print_info "- Redis: 127.0.0.1 (optional local Redis)"
        print_info "- URL: http://127.0.0.1:8000"
        print_info "- Services: XAMPP Control Panel"
        
        if [ -f ".env.xampp" ]; then
            cp ".env.xampp" ".env"
            print_success "Switched to XAMPP configuration (.env.xampp → .env)"
        else
            print_error "Error: .env.xampp file not found!"
            print_info "Please ensure .env.xampp exists in the project root"
            exit 1
        fi
        ;;
esac

# Show next steps
print_info ""
print_info "=== Next Steps ==="
case $ENV in
    docker)
        print_info "1. Start Docker services:"
        print_info "   docker compose up -d"
        print_info ""
        print_info "2. Or use the rebuild script:"
        print_info "   ./scripts/docker-rebuild.ps1"
        print_info ""
        print_info "3. Access application:"
        print_info "   http://localhost:8000"
        ;;
        
    xampp)
        print_info "1. Start XAMPP services (Apache, MySQL)"
        print_info "2. Create database if needed:"
        print_info "   mysql -u root -p -e 'CREATE DATABASE ictserve;'"
        print_info ""
        print_info "3. Run migrations:"
        print_info "   php artisan migrate --seed"
        print_info ""
        print_info "4. Start Laravel server:"
        print_info "   php artisan serve"
        print_info ""
        print_info "5. Access application:"
        print_info "   http://127.0.0.1:8000"
        ;;
esac

print_success ""
print_success "Environment switched successfully to: $ENV"