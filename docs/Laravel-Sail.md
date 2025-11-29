# Laravel Sail — Docker Development Environment

## Overview

Laravel Sail is a light-weight command-line interface for interacting with Laravel's default Docker development environment. It provides a simple way to run Laravel applications using Docker without requiring prior Docker experience.

**Version**: Laravel 12.x compatible  
**Purpose**: Docker-based local development environment

## Installation

### New Laravel Project

```bash
curl -s https://laravel.build/ictserve | bash
cd ictserve
./vendor/bin/sail up
```

### Existing Project

```bash
composer require laravel/sail --dev
php artisan sail:install
```

Select services when prompted:

- MySQL
- Redis
- Mailpit
- Meilisearch (optional)

## Starting Sail

```bash
./vendor/bin/sail up
```

Run in background:

```bash
./vendor/bin/sail up -d
```

Stop Sail:

```bash
./vendor/bin/sail down
```

## Shell Alias

Add to `~/.bashrc` or `~/.zshrc`:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Then use:

```bash
sail up
sail down
sail artisan migrate
```

## Available Services

### MySQL

Default configuration:

- Host: `mysql`
- Port: `3306`
- Database: `ictserve`
- Username: `sail`
- Password: `password`

### Redis

Default configuration:

- Host: `redis`
- Port: `6379`

### Mailpit

Web interface: `http://localhost:8025`

Catches all outgoing emails for testing.

### Meilisearch

Search engine interface: `http://localhost:7700`

## Executing Commands

### Artisan Commands

```bash
sail artisan migrate
sail artisan db:seed
sail artisan queue:work
sail artisan test
```

### Composer Commands

```bash
sail composer install
sail composer require laravel/sanctum
sail composer update
```

### NPM Commands

```bash
sail npm install
sail npm run dev
sail npm run build
```

### PHP Commands

```bash
sail php --version
sail php artisan tinker
```

### Running Tests

```bash
sail test
sail test --filter=UserTest
sail test --coverage
```

## Database Management

### MySQL CLI

```bash
sail mysql
```

Or with specific database:

```bash
sail mysql ictserve
```

### Database Migrations

```bash
sail artisan migrate
sail artisan migrate:fresh --seed
sail artisan migrate:rollback
```

### Database Backups

```bash
sail exec mysql mysqldump -u sail -ppassword ictserve > backup.sql
```

Restore:

```bash
sail exec -T mysql mysql -u sail -ppassword ictserve < backup.sql
```

## Redis CLI

```bash
sail redis
```

Common Redis commands:

```bash
# Inside Redis CLI
KEYS *
GET key_name
FLUSHALL
```

## Queue Workers

```bash
sail artisan queue:work
sail artisan queue:work --queue=high,default
sail artisan queue:listen
```

## Customizing Services

### Publishing Configuration

```bash
sail artisan sail:publish
```

This creates `docker-compose.yml` in project root.

### Adding Services

Edit `docker-compose.yml`:

```yaml
services:
    postgres:
        image: 'postgres:15'
        ports:
            - '${FORWARD_DB_PORT:-5432}:5432'
        environment:
            POSTGRES_DB: '${DB_DATABASE}'
            POSTGRES_USER: '${DB_USERNAME}'
            POSTGRES_PASSWORD: '${DB_PASSWORD}'
        volumes:
            - 'sail-postgres:/var/lib/postgresql/data'
        networks:
            - sail

volumes:
    sail-postgres:
        driver: local
```

### Changing PHP Version

Edit `docker-compose.yml`:

```yaml
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.3
            dockerfile: Dockerfile
```

Available versions: 8.1, 8.2, 8.3

## Environment Variables

### Port Configuration

```env
APP_PORT=80
FORWARD_DB_PORT=3306
FORWARD_REDIS_PORT=6379
FORWARD_MAILPIT_PORT=1025
FORWARD_MAILPIT_DASHBOARD_PORT=8025
```

### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=sail
DB_PASSWORD=password
```

### Redis Configuration

```env
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Sharing Sites

### Expose Local Site

```bash
sail share
```

This creates a public URL using ngrok.

## Debugging

### Xdebug

Enable Xdebug:

```bash
sail debug
```

Disable Xdebug:

```bash
sail debug off
```

Configure in `.env`:

```env
SAIL_XDEBUG_MODE=develop,debug,coverage
```

### VS Code Configuration

Create `.vscode/launch.json`:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            }
        }
    ]
}
```

## Running Multiple Projects

### Custom APP_PORT

Project 1:

```env
APP_PORT=80
```

Project 2:

```env
APP_PORT=81
```

### Site Isolation

Each project runs in isolated Docker network.

## Shell Access

### Application Container

```bash
sail shell
```

Or:

```bash
sail bash
```

### Root Shell

```bash
sail root-shell
```

### Specific Service

```bash
sail exec mysql bash
sail exec redis sh
```

## File Permissions

Sail runs as user `sail` (UID 1000) to match host user permissions.

Fix permissions if needed:

```bash
sail exec laravel.test chown -R sail:sail /var/www/html
```

## Performance Optimization

### Disable Xdebug

```bash
sail debug off
```

### Use Redis for Cache

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Optimize Composer

```bash
sail composer install --optimize-autoloader --no-dev
```

## ICTServe Docker Setup

### Custom docker-compose.yml

```yaml
services:
    laravel.test:
        build:
            context: ./vendor/laravel/sail/runtimes/8.2
            dockerfile: Dockerfile
        ports:
            - '${APP_PORT:-80}:80'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - mysql
            - redis
            - mailpit

    mysql:
        image: 'mysql:8.0'
        ports:
            - '${FORWARD_DB_PORT:-3306}:3306'
        environment:
            MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
            MYSQL_DATABASE: '${DB_DATABASE}'
            MYSQL_USER: '${DB_USERNAME}'
            MYSQL_PASSWORD: '${DB_PASSWORD}'
        volumes:
            - 'sail-mysql:/var/lib/mysql'
        networks:
            - sail

    redis:
        image: 'redis:alpine'
        ports:
            - '${FORWARD_REDIS_PORT:-6379}:6379'
        volumes:
            - 'sail-redis:/data'
        networks:
            - sail

    mailpit:
        image: 'axllent/mailpit:latest'
        ports:
            - '${FORWARD_MAILPIT_PORT:-1025}:1025'
            - '${FORWARD_MAILPIT_DASHBOARD_PORT:-8025}:8025'
        networks:
            - sail

networks:
    sail:
        driver: bridge

volumes:
    sail-mysql:
        driver: local
    sail-redis:
        driver: local
```

## Common Commands

```bash
# Start services
sail up -d

# Stop services
sail down

# View logs
sail logs
sail logs -f laravel.test

# Restart services
sail restart

# Rebuild containers
sail build --no-cache

# Run migrations
sail artisan migrate

# Run tests
sail test

# Install dependencies
sail composer install
sail npm install

# Clear cache
sail artisan cache:clear
sail artisan config:clear
sail artisan view:clear
```

## Troubleshooting

### Port Already in Use

Change port in `.env`:

```env
APP_PORT=8080
```

### Permission Denied

```bash
sudo chown -R $USER:$USER .
```

### Container Won't Start

```bash
sail down
docker system prune -a
sail up -d
```

### Database Connection Failed

Verify `.env` settings:

```env
DB_HOST=mysql
DB_PORT=3306
```

## Best Practices

1. **Use Sail Alias**: Set up shell alias for convenience
2. **Version Control**: Don't commit `docker-compose.yml` if customized
3. **Environment Files**: Use `.env` for configuration
4. **Stop When Not Using**: Stop containers to free resources
5. **Regular Updates**: Keep Sail updated with `composer update`

## CI/CD Integration

### GitHub Actions

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      
      - name: Install Dependencies
        run: composer install
      
      - name: Run Tests
        run: php artisan test
```

## References

- Official Documentation: <https://laravel.com/docs/12.x/sail>
- GitHub Repository: <https://github.com/laravel/sail>
- Docker Documentation: <https://docs.docker.com>
