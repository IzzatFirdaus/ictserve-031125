#!/bin/bash
# Automated XAMPP deployment setup for ICTServe (Bash version)

set -e

# Color functions
print_success() { echo -e "\033[32m$1\033[0m"; }
print_warning() { echo -e "\033[33m$1\033[0m"; }
print_error() { echo -e "\033[31m$1\033[0m"; }
print_info() { echo -e "\033[36m$1\033[0m"; }
print_step() { echo -e "\n\033[35m=== $1 ===\033[0m"; }

# Parse arguments
SKIP_DEPENDENCIES=false
SKIP_DATABASE=false
FORCE=false
REDIS_SETUP=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-dependencies)
            SKIP_DEPENDENCIES=true
            shift
            ;;
        --skip-database)
            SKIP_DATABASE=true
            shift
            ;;
        --force)
            FORCE=true
            shift
            ;;
        --redis-setup)
            REDIS_SETUP=true
            shift
            ;;
        --help)
            echo "ICTServe XAMPP Deployment Setup"
            echo ""
            echo "Usage: $0 [options]"
            echo ""
            echo "Options:"
            echo "  --skip-dependencies    Skip composer and npm installation"
            echo "  --skip-database        Skip database creation and migration"
            echo "  --force               Force overwrite without confirmation"
            echo "  --redis-setup         Setup Redis for enhanced performance"
            echo "  --help                Show this help message"
            echo ""
            echo "Examples:"
            echo "  $0                    Standard XAMPP setup"
            echo "  $0 --redis-setup --force    Setup with Redis and force overwrite"
            exit 0
            ;;
        *)
            print_error "Unknown option: $1"
            echo "Use --help for usage information"
            exit 1
            ;;
    esac
done

print_info "ICTServe XAMPP Deployment Setup"
print_info "Version: 3.6.0"
print_info "Target: Non-Workspace XAMPP Environment"
print_info "Date: $(date '+%Y-%m-%d %H:%M:%S')"

# Check if we're in the correct directory
if [ ! -f "composer.json" ]; then
    print_error "Error: Please run this script from the ICTServe root directory"
    print_info "Expected: ./deployment/xampp/setup-xampp.sh"
    exit 1
fi

# Check prerequisites
print_step "Checking Prerequisites"

# Check for common XAMPP locations (Linux)
XAMPP_PATHS=("/opt/lampp" "/usr/local/xampp" "/Applications/XAMPP")
XAMPP_PATH=""

for path in "${XAMPP_PATHS[@]}"; do
    if [ -d "$path" ]; then
        XAMPP_PATH="$path"
        break
    fi
done

if [ -n "$XAMPP_PATH" ]; then
    print_success "XAMPP found at: $XAMPP_PATH"
else
    print_warning "XAMPP not found in common locations"
    print_info "Please ensure XAMPP is installed and accessible"
fi

# Check Composer
if command -v composer >/dev/null 2>&1; then
    COMPOSER_VERSION=$(composer --version 2>/dev/null | cut -d' ' -f1-2)
    print_success "Composer: $COMPOSER_VERSION"
else
    print_error "Composer not found. Please install Composer first."
    print_info "Download from: https://getcomposer.org/"
    exit 1
fi

# Check Node.js
if command -v node >/dev/null 2>&1; then
    NODE_VERSION=$(node --version 2>/dev/null)
    NPM_VERSION=$(npm --version 2>/dev/null)
    print_success "Node.js: $NODE_VERSION, npm: $NPM_VERSION"
    
    # Check Node.js version (require 22.12+)
    NODE_MAJOR=$(echo "$NODE_VERSION" | sed 's/v//' | cut -d'.' -f1)
    NODE_MINOR=$(echo "$NODE_VERSION" | sed 's/v//' | cut -d'.' -f2)
    
    if [ "$NODE_MAJOR" -lt 22 ] || ([ "$NODE_MAJOR" -eq 22 ] && [ "$NODE_MINOR" -lt 12 ]); then
        print_warning "Node.js version $NODE_VERSION is below required 22.12+"
        print_info "Please update Node.js from: https://nodejs.org/"
    fi
else
    print_error "Node.js not found. Please install Node.js 22.12+ first."
    print_info "Download from: https://nodejs.org/"
    exit 1
fi

# Check PHP
if command -v php >/dev/null 2>&1; then
    PHP_VERSION=$(php --version 2>/dev/null | head -n1 | cut -d' ' -f1-2)
    print_success "PHP: $PHP_VERSION"
    
    # Check PHP version (require 8.4+)
    PHP_VERSION_NUMBER=$(php --version | head -n1 | cut -d' ' -f2 | cut -d'-' -f1)
    PHP_MAJOR=$(echo "$PHP_VERSION_NUMBER" | cut -d'.' -f1)
    PHP_MINOR=$(echo "$PHP_VERSION_NUMBER" | cut -d'.' -f2)
    
    if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 4 ]); then
        print_warning "PHP version $PHP_VERSION_NUMBER is below required 8.4+"
        print_info "Please update PHP or use XAMPP with PHP 8.4+"
    fi
else
    print_error "PHP not found in PATH. Please ensure XAMPP PHP is accessible."
    exit 1
fi

# Backup existing .env if it exists
print_step "Environment Configuration"

if [ -f ".env" ]; then
    if [ "$FORCE" = false ]; then
        print_warning "Existing .env file found"
        read -p "Backup and replace with XAMPP configuration? (y/N): " confirm
        if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
            print_info "Setup cancelled"
            exit 0
        fi
    fi
    
    timestamp=$(date +"%Y%m%d_%H%M%S")
    backup_file=".env.backup.xampp.$timestamp"
    cp ".env" "$backup_file"
    print_success "Backed up existing .env to: $backup_file"
fi

# Copy XAMPP environment configuration
cp "deployment/xampp/.env.xampp" ".env"
print_success "Deployed XAMPP environment configuration"

# Generate application key if needed
if ! grep -q "APP_KEY=base64:" ".env" 2>/dev/null; then
    print_info "Generating application key..."
    php artisan key:generate --force
    print_success "Application key generated"
fi

# Install dependencies
if [ "$SKIP_DEPENDENCIES" = false ]; then
    print_step "Installing Dependencies"
    
    print_info "Installing Composer dependencies..."
    if composer install --no-interaction --prefer-dist --optimize-autoloader; then
        print_success "Composer dependencies installed"
    else
        print_error "Failed to install Composer dependencies"
        print_info "Try running: composer install --no-interaction"
        exit 1
    fi
    
    print_info "Installing NPM dependencies..."
    if npm ci --prefer-offline --no-audit 2>/dev/null; then
        print_success "NPM dependencies installed"
    else
        print_warning "npm ci failed, trying npm install..."
        if npm install --prefer-offline --no-audit; then
            print_success "NPM dependencies installed"
        else
            print_error "Failed to install NPM dependencies"
            print_info "Try running: npm install"
            exit 1
        fi
    fi
else
    print_info "Skipping dependency installation (--skip-dependencies)"
fi

# Database setup
if [ "$SKIP_DATABASE" = false ]; then
    print_step "Database Setup"
    
    print_info "Checking MySQL connection..."
    if mysql -u root -e "SELECT 1;" >/dev/null 2>&1; then
        print_success "MySQL connection successful"
        
        print_info "Creating database if not exists..."
        mysql -u root -e "CREATE DATABASE IF NOT EXISTS ictserve;" 2>/dev/null
        print_success "Database 'ictserve' ready"
        
        print_info "Running migrations..."
        php artisan migrate --force
        print_success "Database migrations completed"
        
        print_info "Seeding database..."
        php artisan db:seed --force
        print_success "Database seeding completed"
        
    else
        print_warning "MySQL connection failed"
        print_info "Please ensure XAMPP MySQL is running and try:"
        print_info "1. Start XAMPP Control Panel"
        print_info "2. Start MySQL service"
        print_info "3. Run: mysql -u root -e 'CREATE DATABASE ictserve;'"
        print_info "4. Run: php artisan migrate --seed"
    fi
else
    print_info "Skipping database setup (--skip-database)"
fi

# Redis setup (optional)
if [ "$REDIS_SETUP" = true ]; then
    print_step "Redis Setup (Optional)"
    
    print_info "Checking for Redis..."
    if command -v redis-cli >/dev/null 2>&1 && redis-cli ping >/dev/null 2>&1; then
        print_success "Redis is running"
        
        # Update .env to use Redis
        sed -i 's/CACHE_STORE=file/CACHE_STORE=redis/' ".env"
        sed -i 's/SESSION_DRIVER=file/SESSION_DRIVER=redis/' ".env"
        sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=redis/' ".env"
        
        print_success "Updated configuration to use Redis"
    else
        print_warning "Redis not found or not running"
        print_info "To install Redis:"
        print_info "1. Ubuntu/Debian: sudo apt install redis-server"
        print_info "2. CentOS/RHEL: sudo yum install redis"
        print_info "3. macOS: brew install redis"
        print_info "4. Or continue with file-based cache (current setup)"
    fi
fi

# Clear caches and optimize
print_step "Optimization"

print_info "Clearing caches..."
php artisan optimize:clear >/dev/null 2>&1
print_success "Caches cleared"

print_info "Optimizing autoloader..."
composer dump-autoload --optimize >/dev/null 2>&1
print_success "Autoloader optimized"

# Build frontend assets
print_info "Building frontend assets..."
if npm run build >/dev/null 2>&1; then
    print_success "Frontend assets built"
else
    print_warning "Frontend build failed"
    print_info "You can build assets later with: npm run build"
fi

# Create storage directories
print_info "Setting up storage directories..."
STORAGE_DIRS=(
    "storage/app/public"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "storage/logs"
    "storage/mcp"
)

for dir in "${STORAGE_DIRS[@]}"; do
    mkdir -p "$dir"
done
print_success "Storage directories ready"

# Create symbolic link for storage
print_info "Creating storage link..."
if php artisan storage:link --force >/dev/null 2>&1; then
    print_success "Storage link created"
else
    print_warning "Storage link creation failed"
fi

# Final setup
print_step "Final Configuration"

print_info "Verifying installation..."
if php artisan about --only=environment >/dev/null 2>&1; then
    print_success "Laravel application ready"
else
    print_warning "Laravel verification failed"
fi

# Display completion message
print_step "Setup Complete!"

print_success "ICTServe XAMPP deployment completed successfully!"
print_info ""
print_info "Next Steps:"
print_info "1. Start XAMPP services (Apache, MySQL)"
print_info "2. Start Laravel development server:"
print_info "   php artisan serve"
print_info ""
print_info "3. Start Vite development server (new terminal):"
print_info "   npm run dev"
print_info ""
print_info "4. Optional - Start WebSocket server (new terminal):"
print_info "   php artisan reverb:start"
print_info ""
print_info "Access URLs:"
print_info "- Application: http://127.0.0.1:8000"
print_info "- Admin Panel: http://127.0.0.1:8000/admin"
print_info "- Telescope: http://127.0.0.1:8000/telescope"
print_info "- Pulse: http://127.0.0.1:8000/pulse"
print_info ""
print_info "Default Credentials:"
print_info "- Superuser: superuser@motac.gov.my / password"
print_info "- Admin: admin@motac.gov.my / password"
print_info "- Staff: staff@motac.gov.my / password"
print_info ""

if [ "$REDIS_SETUP" = true ] && grep -q "CACHE_STORE=redis" ".env" 2>/dev/null; then
    print_info "Redis Configuration: Enabled"
    print_info "- Horizon Dashboard: http://127.0.0.1:8000/horizon"
fi

print_info "For service management, use:"
print_info "./deployment/xampp/scripts/start-services.sh"
print_info "./deployment/xampp/scripts/health-check.sh"

print_success "\nDeployment completed! 🎉"