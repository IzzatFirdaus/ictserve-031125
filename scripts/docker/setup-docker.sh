#!/bin/bash
set -euo pipefail

# ICTServe Docker Setup Script - Addresses composer install issues
# This script sets up the ICTServe Docker environment with proper composer dependency management.

# Default values
ENVIRONMENT="development"
REBUILD=false

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -e|--environment)
            ENVIRONMENT="$2"
            shift 2
            ;;
        -r|--rebuild)
            REBUILD=true
            shift
            ;;
        -h|--help)
            echo "Usage: $0 [OPTIONS]"
            echo "Options:"
            echo "  -e, --environment    Environment to set up: 'production' or 'development' (default: development)"
            echo "  -r, --rebuild        Force rebuild of Docker images"
            echo "  -h, --help          Show this help message"
            exit 0
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
done

# Validate environment
if [[ "$ENVIRONMENT" != "development" && "$ENVIRONMENT" != "production" ]]; then
    echo "❌ Invalid environment: $ENVIRONMENT. Must be 'development' or 'production'"
    exit 1
fi

echo "🐳 ICTServe Docker Setup"
echo "Environment: $ENVIRONMENT"

# Check prerequisites
echo ""
echo "📋 Checking prerequisites..."

# Check Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker not found. Please install Docker."
    exit 1
fi
echo "✅ Docker: $(docker --version)"

# Check Docker Compose
if ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose not found. Please update Docker."
    exit 1
fi
echo "✅ Docker Compose: $(docker compose version)"

# Verify required files
required_files=(
    "Dockerfile"
    "compose.yaml"
    "composer.json"
    "composer.lock"
    "package.json"
    "package-lock.json"
    ".env.docker"
)

for file in "${required_files[@]}"; do
    if [[ ! -f "$file" ]]; then
        echo "❌ Required file missing: $file"
        exit 1
    fi
done
echo "✅ All required files present"

# Stop existing containers
echo ""
echo "🛑 Stopping existing containers..."
docker compose down -v 2>/dev/null || true

# Clean up if rebuild requested
if [[ "$REBUILD" == "true" ]]; then
    echo "🧹 Cleaning up existing images..."
    docker compose down --rmi all -v 2>/dev/null || true
    docker system prune -f 2>/dev/null || true
fi

# Build and start services based on environment
echo ""
echo "🔨 Building and starting services..."

if [[ "$ENVIRONMENT" == "development" ]]; then
    echo "Starting development environment with dev dependencies..."
    
    # Build with development dependencies
    docker compose -f compose.yaml -f compose.dev.yaml build --no-cache
    
    # Start services
    docker compose -f compose.yaml -f compose.dev.yaml up -d
    
    COMPOSE_FILES="-f compose.yaml -f compose.dev.yaml"
else
    echo "Starting production environment..."
    
    # Build production image
    docker compose build --no-cache
    
    # Start services
    docker compose up -d
    
    COMPOSE_FILES=""
fi

# Wait for services to be ready
echo ""
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check service status
echo ""
echo "📊 Checking service status..."
docker compose ps

# Initialize Laravel application
echo ""
echo "🚀 Initializing Laravel application..."

# Generate application key if needed
echo "Generating application key..."
docker compose exec app php artisan key:generate --force

# Run database migrations
echo "Running database migrations..."
docker compose exec app php artisan migrate --force

# Seed database if development
if [[ "$ENVIRONMENT" == "development" ]]; then
    echo "Seeding database..."
    docker compose exec app php artisan db:seed --force
fi

# Clear and cache configuration
echo "Optimizing application..."
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

if [[ "$ENVIRONMENT" == "production" ]]; then
    docker compose exec app php artisan config:cache
    docker compose exec app php artisan route:cache
    docker compose exec app php artisan view:cache
fi

# Build frontend assets
echo ""
echo "🎨 Building frontend assets..."

if [[ "$ENVIRONMENT" == "development" ]]; then
    echo "Installing npm dependencies..."
    docker compose exec app npm ci
    
    echo "Building development assets..."
    docker compose exec app npm run build
else
    echo "Building production assets..."
    docker compose exec app npm ci --only=production
    docker compose exec app npm run build
fi

# Create admin user for development
if [[ "$ENVIRONMENT" == "development" ]]; then
    echo ""
    echo "👤 Creating admin user..."
    echo "Creating default admin user (admin@motac.gov.my / password)..."
    
    docker compose exec app php artisan tinker --execute="
        use App\Models\User;
        use Illuminate\Support\Facades\Hash;
        
        \$user = User::firstOrCreate(
            ['email' => 'admin@motac.gov.my'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        \$user->assignRole('superuser');
        echo 'Admin user created: admin@motac.gov.my / password\n';
    "
fi

# Display service information
echo ""
echo "🌐 Service Information:"
echo "Application: http://localhost:8000"
echo "Admin Panel: http://localhost:8000/admin"

if [[ "$ENVIRONMENT" == "development" ]]; then
    echo "Vite Dev Server: http://localhost:5173"
    echo "Reverb WebSocket: ws://localhost:8080"
fi

# Display credentials
if [[ "$ENVIRONMENT" == "development" ]]; then
    echo ""
    echo "🔑 Development Credentials:"
    echo "Admin: admin@motac.gov.my / password"
    echo "Staff: staff@motac.gov.my / password"
    echo "Approver: approver@motac.gov.my / password"
fi

# Display useful commands
echo ""
echo "🛠️  Useful Commands:"
echo "View logs: docker compose logs -f app"
echo "Shell access: docker compose exec app sh"
echo "Run artisan: docker compose exec app php artisan <command>"
echo "Stop services: docker compose down"

if [[ "$ENVIRONMENT" == "development" ]]; then
    echo "Start Vite: docker compose exec vite npm run dev"
fi

echo ""
echo "✅ Docker setup complete!"
echo "The application is now running with proper composer dependency management."